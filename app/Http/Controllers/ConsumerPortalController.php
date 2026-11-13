<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Consumer;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConsumerPortalController extends Controller
{
    /**
     * Public consumer lookup portal.
     * Consumers can enter their assigned Account Number or Meter Number to view service billing.
     */
    public function index(Request $request): View
    {
        $identifier = trim((string) $request->input('identifier', ''));
        $consumer = null;
        $activeBill = null;
        $bills = collect();
        $notFound = false;

        $tariffRate = (float) Setting::get('water_rate_per_cbm', 15.00);
        $barangayName = Setting::get('barangay_name', 'Barangay Caruhayon');
        $systemContact = Setting::get('system_contact', '0917-123-CARU (2278)');

        if ($identifier !== '') {
            $consumer = Consumer::where('account_number', $identifier)
                ->orWhere('meter_number', $identifier)
                ->with(['bills' => function ($q) {
                    $q->with('payments')->orderBy('billing_date', 'desc');
                }])
                ->first();

            if ($consumer) {
                // Record verified lookup in session to authorize access to their statements
                session(['portal_consumer_id' => $consumer->id]);

                $bills = $consumer->bills;
                $activeBill = $bills->first();
            } else {
                $notFound = true;
            }
        }

        return view('portal.my-water-bill', compact(
            'identifier',
            'consumer',
            'activeBill',
            'bills',
            'notFound',
            'tariffRate',
            'barangayName',
            'systemContact'
        ));
    }

    /**
     * Public read-only statement.
     * Protected against unauthorized enumeration by requiring verified lookup.
     */
    public function statement(Request $request, Bill $bill): View|RedirectResponse
    {
        $bill->load(['consumer', 'payments']);

        // Check authorization:
        // 1. Staff logged in
        // 2. Verified session matches consumer
        // 3. Request account or meter token matches the bill's consumer
        $isStaff = auth()->check();
        $sessionConsumerId = session('portal_consumer_id');
        $accountToken = $request->query('account');
        $meterToken = $request->query('meter');

        $isAuthorized = $isStaff
            || ($sessionConsumerId && $sessionConsumerId === $bill->consumer_id)
            || ($accountToken && $accountToken === $bill->consumer->account_number)
            || ($meterToken && $meterToken === $bill->consumer->meter_number);

        if (!$isAuthorized) {
            return redirect()->route('portal.my-water-bill')->with(
                'error',
                'Access restricted. Please search for your Account Number or Meter Number to view your billing statement.'
            );
        }

        $barangayName = Setting::get('barangay_name', 'Barangay Caruhayon');
        $systemContact = Setting::get('system_contact', '0917-123-CARU (2278)');

        return view('portal.statement', compact('bill', 'barangayName', 'systemContact'));
    }
}
