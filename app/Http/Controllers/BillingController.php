<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\User;
use App\Models\Consumer;
use App\Models\MeterReading;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BillingController extends Controller
{
    // list bills with search and status filters
    public function index(Request $request): View
    {
        $query = Bill::with(['consumer', 'payments']);

        //status filter
        if ($status = $request->input('status')) {
            if (in_array($status, ['unpaid', 'paid', 'overdue'])) {
                $query->where('status', $status);
            }
        }

        //search by bill number, period, or consumer
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('bill_number', 'like', "%{$search}%")
                  ->orWhere('billing_period', 'like', "%{$search}%")
                  ->orWhereHas('consumer', function ($cq) use ($search) {
                      $cq->where('account_number', 'like', "%{$search}%")
                         ->orWhere('meter_number', 'like', "%{$search}%")
                         ->orWhere('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }

        $bills = $query->orderBy('billing_date', 'desc')->paginate(12)->withQueryString();

        return view('bills.index', compact('bills'));
    }

    // meter reading and bill creation form
    public function create(Request $request): View
    {
        $consumers = Consumer::where('status', 'active')->orderBy('last_name')->get();
        $selectedConsumerId = $request->input('consumer_id');
        $selectedConsumer = $selectedConsumerId ? Consumer::find($selectedConsumerId) : null;

        $previousReading = 0.00;
        $suggestedCurrentReading = null;
        $currentPeriod = date('F Y');

        if ($selectedConsumer) {
            // Check for unbilled meter reading first
            $unbilledReading = $selectedConsumer->meterReadings()
                ->where('status', 'recorded')
                ->whereNull('bill_id')
                ->latest('id')
                ->first();

            if ($unbilledReading) {
                $previousReading = (float) $unbilledReading->previous_reading;
                $suggestedCurrentReading = (float) $unbilledReading->current_reading;
                $currentPeriod = $unbilledReading->billing_period;
            } else {
                $previousReading = (float) $selectedConsumer->latest_reading;
            }
        }

        $defaultRate = (float) Setting::get('water_rate_per_cbm', 15.00);
        $defaultBillingDate = date('Y-m-d');
        $defaultDueDate = date('Y-m-d', strtotime('+15 days'));

        $seq = Bill::whereYear('created_at', date('Y'))->whereMonth('created_at', date('m'))->count() + 1;
        do {
            $suggestedBillNumber = sprintf('BILL-%s-%04d', date('Ym'), $seq);
            $seq++;
        } while (Bill::where('bill_number', $suggestedBillNumber)->exists());

        return view('bills.create', compact(
            'consumers',
            'selectedConsumer',
            'previousReading',
            'suggestedCurrentReading',
            'defaultRate',
            'currentPeriod',
            'defaultBillingDate',
            'defaultDueDate',
            'suggestedBillNumber'
        ));
    }

    // store meter reading and bill
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'consumer_id' => ['required', 'exists:consumers,id'],
            'previous_reading' => ['required', 'numeric', 'min:0'],
            'current_reading' => [
                'required',
                'numeric',
                'gte:previous_reading',
            ],
            'billing_period' => ['required', 'string', 'max:50'],
            'billing_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:billing_date'],
            'rate_per_cubic_meter' => ['required', 'numeric', 'min:0.01'],
        ], [
            'current_reading.gte' => 'Current reading cannot be lower than the previous reading (:value m³).',
            'due_date.after_or_equal' => 'Due date must be on or after the billing date.',
        ]);

        $consumer = Consumer::findOrFail($validated['consumer_id']);

        // Prevent duplicate bills for the same consumer and billing period
        $existingBill = Bill::where('consumer_id', $consumer->id)
            ->where('billing_period', $validated['billing_period'])
            ->first();

        if ($existingBill) {
            return back()->withErrors([
                'billing_period' => "A water bill ({$existingBill->bill_number}) has already been generated for {$consumer->full_name} for the period {$validated['billing_period']}."
            ])->withInput();
        }

        // calculate consumption & amount due
        $consumption = round((float) $validated['current_reading'] - (float) $validated['previous_reading'], 2);
        $rate = round((float) $validated['rate_per_cubic_meter'], 2);
        $amountDue = round($consumption * $rate, 2);

        // generate collision-proof unique bill number
        $seq = Bill::whereYear('created_at', date('Y'))->whereMonth('created_at', date('m'))->count() + 1;
        do {
            $billNumber = sprintf('BILL-%s-%04d', date('Ym'), $seq);
            $seq++;
        } while (Bill::where('bill_number', $billNumber)->exists());

        $bill = Bill::create([
            'bill_number' => $billNumber,
            'consumer_id' => $consumer->id,
            'previous_reading' => $validated['previous_reading'],
            'current_reading' => $validated['current_reading'],
            'consumption' => $consumption,
            'billing_period' => $validated['billing_period'],
            'billing_date' => $validated['billing_date'],
            'due_date' => $validated['due_date'],
            'rate_per_cubic_meter' => $rate,
            'amount_due' => $amountDue,
            'status' => 'unpaid',
        ]);

        // Link with recorded meter reading if one exists for this cycle, or create one to maintain sync
        $matchingReading = MeterReading::where('consumer_id', $consumer->id)
            ->where('billing_period', $validated['billing_period'])
            ->whereNull('bill_id')
            ->latest('id')
            ->first();

        if ($matchingReading) {
            $matchingReading->update([
                'bill_id' => $bill->id,
                'status' => 'billed',
            ]);
        } else {
            MeterReading::create([
                'consumer_id' => $consumer->id,
                'user_id' => auth()->id(),
                'previous_reading' => $validated['previous_reading'],
                'current_reading' => $validated['current_reading'],
                'consumption' => $consumption,
                'reading_date' => $validated['billing_date'],
                'billing_period' => $validated['billing_period'],
                'status' => 'billed',
                'bill_id' => $bill->id,
                'notes' => 'Synchronized during bill generation',
            ]);
        }

        return redirect()->route('bills.statement', $bill)
            ->with('success', "Water bill {$bill->bill_number} generated successfully for {$consumer->full_name}.");
    }

    // printable official billing statement
    public function statement(Bill $bill): View
    {
        $bill->load(['consumer', 'payments']);
        $barangayName = Setting::get('barangay_name', 'Barangay Caruhayon');
        $systemContact = Setting::get('system_contact', '0917-123-CARU (2278)');

        return view('bills.statement', compact('bill', 'barangayName', 'systemContact'));
    }

    // api endpoint for consumer latest reading
    public function getConsumerReading(Consumer $consumer): JsonResponse
    {
        $prevReading = (float) $consumer->latest_reading;
        $hasPrevious = $consumer->hasPreviousReading();

        $unbilledReading = $consumer->meterReadings()
            ->where('status', 'recorded')
            ->whereNull('bill_id')
            ->latest('id')
            ->first();

        return response()->json([
            'consumer_id' => $consumer->id,
            'name' => $consumer->full_name,
            'account_number' => $consumer->account_number,
            'meter_number' => $consumer->meter_number,
            'address' => $consumer->address,
            'previous_reading' => $prevReading,
            'has_previous_reading' => $hasPrevious,
            'unbilled_reading' => $unbilledReading ? [
                'id' => $unbilledReading->id,
                'previous_reading' => (float) $unbilledReading->previous_reading,
                'current_reading' => (float) $unbilledReading->current_reading,
                'consumption' => (float) $unbilledReading->consumption,
                'reading_date' => $unbilledReading->reading_date->format('Y-m-d'),
                'billing_period' => $unbilledReading->billing_period,
            ] : null,
        ]);
    }
}
