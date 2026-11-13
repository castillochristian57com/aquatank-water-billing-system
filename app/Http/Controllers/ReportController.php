<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Consumer;
use App\Models\Payment;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    // operational reports hub
    public function index(Request $request): View
    {
        $reportType = $request->input('report_type', 'billing_collection');
        $purokFilter = $request->input('purok');
        $dateFrom = $request->input('date_from', Carbon::now()->startOfMonth()->toDateString());
        $dateTo = $request->input('date_to', Carbon::now()->endOfMonth()->toDateString());

        $barangayName = Setting::get('barangay_name', 'Barangay Caruhayon');
        $systemContact = Setting::get('system_contact', '0917-123-CARU (2278)');
        $waterRate = (float) Setting::get('water_rate_per_cbm', 15.00);

        $purokList = ['Purok 1', 'Purok 2', 'Purok 3', 'Purok 4', 'Purok 5', 'Purok 6', 'Purok 7'];

        // billing & collection summary
        $billingQuery = Bill::with('consumer');
        if ($purokFilter) {
            $billingQuery->whereHas('consumer', fn($q) => $q->where('purok', $purokFilter));
        }
        $bills = $billingQuery->whereBetween('billing_date', [$dateFrom, $dateTo])->get();
        $totalBilled = (float) $bills->sum('amount_due');
        $totalCollected = (float) $bills->sum(fn($b) => $b->total_paid);
        $totalArrears = max(0, $totalBilled - $totalCollected);
        $collectionEfficiency = $totalBilled > 0 ? ($totalCollected / $totalBilled) * 100 : 0;

        // group by billing period
        $monthlyBillingSummary = Bill::select(
            'billing_period',
            DB::raw('COUNT(*) as total_bills'),
            DB::raw('SUM(amount_due) as total_amount_due'),
            DB::raw('SUM(consumption) as total_consumption')
        )->groupBy('billing_period')->orderByRaw('MAX(id) desc')->get();

        // water consumption & metering report
        $totalVolume = (float) $bills->sum('consumption');
        $avgHouseholdVolume = $bills->count() > 0 ? $totalVolume / $bills->count() : 0;

        // consumption by purok
        $consumptionQuery = Consumer::select('purok')
            ->selectRaw('COUNT(DISTINCT consumers.id) as consumers_count')
            ->selectRaw('COALESCE(SUM(bills.consumption), 0) as total_cbm')
            ->leftJoin('bills', function ($join) use ($dateFrom, $dateTo) {
                $join->on('consumers.id', '=', 'bills.consumer_id')
                     ->whereBetween('bills.billing_date', [$dateFrom, $dateTo]);
            });
        if ($purokFilter) {
            $consumptionQuery->where('consumers.purok', $purokFilter);
        }
        $consumptionByPurok = $consumptionQuery->groupBy('purok')->orderBy('purok')->get();

        // top water consumers
        $topConsumersQuery = Consumer::whereHas('bills', function ($q) use ($dateFrom, $dateTo) {
            $q->whereBetween('billing_date', [$dateFrom, $dateTo])
              ->where('consumption', '>', 0);
        });
        if ($purokFilter) {
            $topConsumersQuery->where('purok', $purokFilter);
        }
        $topConsumers = $topConsumersQuery
            ->withSum(['bills' => function ($q) use ($dateFrom, $dateTo) {
                $q->whereBetween('billing_date', [$dateFrom, $dateTo]);
            }], 'consumption')
            ->withSum(['bills' => function ($q) use ($dateFrom, $dateTo) {
                $q->whereBetween('billing_date', [$dateFrom, $dateTo]);
            }], 'amount_due')
            ->orderByDesc('bills_sum_consumption')
            ->take(10)
            ->get();

        // delinquency overdue report
        $delinquentQuery = Bill::with('consumer')
            ->where(function ($q) {
                $q->where('status', 'overdue')
                  ->orWhere(function ($sub) {
                      $sub->where('status', 'unpaid')->whereDate('due_date', '<', Carbon::today());
                  });
            });

        if ($purokFilter) {
            $delinquentQuery->whereHas('consumer', fn($q) => $q->where('purok', $purokFilter));
        }

        $delinquentBills = $delinquentQuery->orderBy('due_date', 'asc')->get();
        $totalOverdueAmount = (float) $delinquentBills->sum(fn($b) => $b->balance);

        // cashier collection report
        $paymentQuery = Payment::with(['bill.consumer']);
        if ($dateFrom && $dateTo) {
            $paymentQuery->whereBetween('payment_date', [$dateFrom, $dateTo]);
        }
        $payments = $paymentQuery->orderBy('payment_date', 'desc')->get();
        $cashTotal = (float) $payments->where('payment_method', 'Cash')->sum('amount_paid');
        $otherTotal = (float) $payments->where('payment_method', '!=', 'Cash')->sum('amount_paid');
        $grandPaymentsTotal = (float) $payments->sum('amount_paid');

        return view('reports.index', compact(
            'reportType',
            'purokFilter',
            'dateFrom',
            'dateTo',
            'barangayName',
            'systemContact',
            'waterRate',
            'purokList',
            'bills',
            'totalBilled',
            'totalCollected',
            'totalArrears',
            'collectionEfficiency',
            'monthlyBillingSummary',
            'totalVolume',
            'avgHouseholdVolume',
            'consumptionByPurok',
            'topConsumers',
            'delinquentBills',
            'totalOverdueAmount',
            'payments',
            'cashTotal',
            'otherTotal',
            'grandPaymentsTotal'
        ));
    }

    // printable official report layout
    public function print(Request $request): View
    {
        $reportType = $request->input('report_type', 'billing_collection');
        $purokFilter = $request->input('purok');
        $dateFrom = $request->input('date_from', Carbon::now()->startOfMonth()->toDateString());
        $dateTo = $request->input('date_to', Carbon::now()->endOfMonth()->toDateString());

        $barangayName = Setting::get('barangay_name', 'Barangay Caruhayon');
        $preparedBy = auth()->user()->name ?? 'Barangay Water Administrator';
        $waterRate = (float) Setting::get('water_rate_per_cbm', 15.00);

        // load data based on report type
        $bills = collect();
        $delinquentBills = collect();
        $payments = collect();
        $consumptionByPurok = collect();

        if ($reportType === 'billing_collection') {
            $q = Bill::with('consumer')->whereBetween('billing_date', [$dateFrom, $dateTo]);
            if ($purokFilter) $q->whereHas('consumer', fn($sub) => $sub->where('purok', $purokFilter));
            $bills = $q->get();
        } elseif ($reportType === 'delinquency') {
            $q = Bill::with('consumer')->where(function ($sub) {
                $sub->where('status', 'overdue')
                    ->orWhere(function ($s2) {
                        $s2->where('status', 'unpaid')->whereDate('due_date', '<', Carbon::today());
                    });
            });
            if ($purokFilter) $q->whereHas('consumer', fn($sub) => $sub->where('purok', $purokFilter));
            $delinquentBills = $q->get();
        } elseif ($reportType === 'cashier') {
            $payments = Payment::with(['bill.consumer'])->whereBetween('payment_date', [$dateFrom, $dateTo])->get();
        } else {
            // consumption by purok
            $consumptionQuery = Consumer::select('purok')
                ->selectRaw('COUNT(DISTINCT consumers.id) as consumers_count')
                ->selectRaw('COALESCE(SUM(bills.consumption), 0) as total_cbm')
                ->leftJoin('bills', function ($join) use ($dateFrom, $dateTo) {
                    $join->on('consumers.id', '=', 'bills.consumer_id')
                         ->whereBetween('bills.billing_date', [$dateFrom, $dateTo]);
                });
            if ($purokFilter) {
                $consumptionQuery->where('consumers.purok', $purokFilter);
            }
            $consumptionByPurok = $consumptionQuery->groupBy('purok')->orderBy('purok')->get();
        }

        return view('reports.print', compact(
            'reportType',
            'purokFilter',
            'dateFrom',
            'dateTo',
            'barangayName',
            'preparedBy',
            'waterRate',
            'bills',
            'delinquentBills',
            'payments',
            'consumptionByPurok'
        ));
    }

    // export operational report to csv
    public function exportCsv(Request $request): StreamedResponse
    {
        $reportType = $request->input('report_type', 'billing_collection');
        $purokFilter = $request->input('purok');
        $dateFrom = $request->input('date_from', Carbon::now()->startOfMonth()->toDateString());
        $dateTo = $request->input('date_to', Carbon::now()->endOfMonth()->toDateString());

        $filename = "aquatank_{$reportType}_{$dateFrom}_to_{$dateTo}.csv";

        return response()->streamDownload(function () use ($reportType, $purokFilter, $dateFrom, $dateTo) {
            $handle = fopen('php://output', 'w');

            if ($reportType === 'billing_collection') {
                fputcsv($handle, ['Bill Number', 'Account Number', 'Consumer Name', 'Purok', 'Billing Period', 'Consumption (m3)', 'Rate', 'Amount Due (PHP)', 'Total Paid', 'Balance', 'Status']);
                $q = Bill::with('consumer')->whereBetween('billing_date', [$dateFrom, $dateTo]);
                if ($purokFilter) {
                    $q->whereHas('consumer', fn($sub) => $sub->where('purok', $purokFilter));
                }
                $bills = $q->get();
                foreach ($bills as $b) {
                    fputcsv($handle, [
                        $b->bill_number,
                        $b->consumer->account_number ?? '',
                        $b->consumer->full_name ?? '',
                        $b->consumer->purok ?? '',
                        $b->billing_period,
                        $b->consumption,
                        $b->rate_per_cubic_meter,
                        $b->amount_due,
                        $b->total_paid,
                        $b->balance,
                        $b->status,
                    ]);
                }
            } elseif ($reportType === 'consumption') {
                fputcsv($handle, ['Purok / Zone', 'Active Connections', 'Total Consumption (m3)', 'Estimated Billed Value (PHP)']);
                $consumptionQuery = Consumer::select('purok')
                    ->selectRaw('COUNT(DISTINCT consumers.id) as consumers_count')
                    ->selectRaw('COALESCE(SUM(bills.consumption), 0) as total_cbm')
                    ->leftJoin('bills', function ($join) use ($dateFrom, $dateTo) {
                        $join->on('consumers.id', '=', 'bills.consumer_id')
                             ->whereBetween('bills.billing_date', [$dateFrom, $dateTo]);
                    });
                if ($purokFilter) {
                    $consumptionQuery->where('consumers.purok', $purokFilter);
                }
                $consumptionByPurok = $consumptionQuery->groupBy('purok')->orderBy('purok')->get();
                $waterRate = (float) Setting::get('water_rate_per_cbm', 15.00);
                foreach ($consumptionByPurok as $cp) {
                    fputcsv($handle, [
                        $cp->purok,
                        $cp->consumers_count,
                        $cp->total_cbm,
                        $cp->total_cbm * $waterRate,
                    ]);
                }
            } elseif ($reportType === 'delinquency') {
                fputcsv($handle, ['Bill Number', 'Account Number', 'Consumer Name', 'Purok', 'Contact Number', 'Due Date', 'Days Overdue', 'Total Overdue (PHP)']);
                $q = Bill::with('consumer')->where(function ($sub) {
                    $sub->where('status', 'overdue')
                        ->orWhere(function ($s2) {
                            $s2->where('status', 'unpaid')->whereDate('due_date', '<', Carbon::today());
                        });
                });
                if ($purokFilter) {
                    $q->whereHas('consumer', fn($sub) => $sub->where('purok', $purokFilter));
                }
                $overdue = $q->get();
                foreach ($overdue as $b) {
                    $days = Carbon::parse($b->due_date)->diffInDays(Carbon::today());
                    fputcsv($handle, [
                        $b->bill_number,
                        $b->consumer->account_number ?? '',
                        $b->consumer->full_name ?? '',
                        $b->consumer->purok ?? '',
                        $b->consumer->contact_number ?? '',
                        Carbon::parse($b->due_date)->format('Y-m-d'),
                        $days,
                        $b->balance,
                    ]);
                }
            } else {
                fputcsv($handle, ['OR Number', 'Payment Date', 'Account Number', 'Consumer Name', 'Bill Number', 'Amount Paid (PHP)', 'Payment Method', 'Notes']);
                $payments = Payment::with('bill.consumer')->whereBetween('payment_date', [$dateFrom, $dateTo])->get();
                foreach ($payments as $p) {
                    fputcsv($handle, [
                        $p->reference_number,
                        $p->payment_date,
                        $p->bill?->consumer?->account_number ?? '',
                        $p->bill?->consumer?->full_name ?? '',
                        $p->bill?->bill_number ?? '',
                        $p->amount_paid,
                        $p->payment_method,
                        $p->notes ?? '',
                    ]);
                }
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
