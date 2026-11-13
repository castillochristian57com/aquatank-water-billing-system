<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Payment;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PaymentController extends Controller
{
    // list payment transactions with search and method filters
    public function index(Request $request): View
    {
        $query = Payment::with(['bill.consumer']);

        // search by reference, bill number, or consumer
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                  ->orWhereHas('bill', function ($bq) use ($search) {
                      $bq->where('bill_number', 'like', "%{$search}%")
                         ->orWhereHas('consumer', function ($cq) use ($search) {
                             $cq->where('account_number', 'like', "%{$search}%")
                                ->orWhere('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%");
                         });
                  });
            });
        }

        // payment method filter
        if ($method = $request->input('payment_method')) {
            if (in_array($method, ['Cash', 'Other'])) {
                $query->where('payment_method', $method);
            }
        }

        $payments = $query->orderBy('payment_date', 'desc')->paginate(12)->withQueryString();

        return view('payments.index', compact('payments'));
    }

    // over the counter payment form
    public function create(Request $request): View
    {
        $unpaidBills = Bill::with('consumer')
            ->whereIn('status', ['unpaid', 'overdue'])
            ->orderBy('due_date', 'asc')
            ->get();

        $selectedBillId = $request->input('bill_id');
        $selectedBill = $selectedBillId ? Bill::with(['consumer', 'payments'])->find($selectedBillId) : null;

        $lastPayment = Payment::latest('id')->first();
        $nextPaymentId = $lastPayment ? ($lastPayment->id + 1) : 1;
        $suggestedReferenceNo = sprintf('OR-%s-%04d', date('Ym'), $nextPaymentId);

        return view('payments.create', compact('unpaidBills', 'selectedBill', 'suggestedReferenceNo'));
    }

    // store payment and update bill status
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'bill_id' => ['required', 'exists:bills,id'],
            'amount_paid' => ['required', 'numeric', 'decimal:0,2', 'min:0.01'],
            'payment_date' => ['required', 'date'],
            'reference_number' => ['required', 'string', 'max:50', 'unique:payments,reference_number'],
            'payment_method' => ['required', 'in:Cash,Other'],
            'notes' => ['nullable', 'string', 'max:255'],
        ], [
            'reference_number.unique' => 'This Official Receipt / Reference Number has already been used.',
        ]);

        return DB::transaction(function () use ($validated): RedirectResponse {
            $bill = Bill::with('consumer')->lockForUpdate()->findOrFail($validated['bill_id']);
            $remainingBalance = $bill->balance;
            $amountPaidInCents = (int) round((float) $validated['amount_paid'] * 100);
            $remainingBalanceInCents = (int) round($remainingBalance * 100);

            if ($amountPaidInCents > $remainingBalanceInCents) {
                return back()->withInput()->withErrors([
                    'amount_paid' => "Payment amount (₱" . number_format($validated['amount_paid'], 2) . ") cannot exceed the remaining balance of ₱" . number_format($remainingBalance, 2) . "."
                ]);
            }

            $payment = Payment::create([
                'bill_id' => $bill->id,
                'consumer_id' => $bill->consumer_id,
                'reference_number' => $validated['reference_number'],
                'amount_paid' => $validated['amount_paid'],
                'payment_date' => $validated['payment_date'],
                'payment_method' => $validated['payment_method'],
                'notes' => $validated['notes'],
            ]);

            if ($bill->total_paid >= $bill->amount_due) {
                $bill->update(['status' => 'paid']);
            }

            return redirect()->route('payments.receipt', $payment)
                ->with('success', "Payment of ₱" . number_format($payment->amount_paid, 2) . " successfully recorded with OR #{$payment->reference_number}.");
        });
    }

    // official receipt view
    public function receipt(Payment $payment): View
    {
        $payment->load(['bill.consumer']);
        $barangayName = Setting::get('barangay_name', 'Barangay Caruhayon');
        $systemContact = Setting::get('system_contact', '0917-123-CARU (2278)');

        return view('payments.receipt', compact('payment', 'barangayName', 'systemContact'));
    }
}
