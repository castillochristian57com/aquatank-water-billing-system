<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Consumer;
use App\Models\Payment;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    // admin dashboard metrics and activity
    public function index(): View
    {
        $tariffRate = (float) Setting::get('water_rate_per_cbm', 15.00);
        $barangayName = Setting::get('barangay_name', 'Barangay Caruhayon');
        $systemContact = Setting::get('system_contact', '0917-123-CARU (2278)');

        // consumer metrics
        $totalConsumers = Consumer::count();
        $activeConsumers = Consumer::where('status', 'active')->count();
        $inactiveConsumers = Consumer::where('status', 'inactive')->count();

        // bill status counts
        $totalBills = Bill::count();
        $paidBills = Bill::where('status', 'paid')->count();
        $unpaidBills = Bill::where('status', 'unpaid')->count();
        $overdueBills = Bill::where('status', 'overdue')->count();

        // financial collections and arrears
        $totalBilled = (float) Bill::sum('amount_due');
        $totalCollected = (float) Payment::sum('amount_paid');
        $outstandingBalance = max(0, $totalBilled - $totalCollected);

        $collectionRate = $totalBilled > 0
            ? round(($totalCollected / $totalBilled) * 100, 1)
            : 0;

        // recent activities
        $recentBills = Bill::with('consumer')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $recentPayments = Payment::with(['bill.consumer'])
            ->orderBy('payment_date', 'desc')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'tariffRate',
            'barangayName',
            'systemContact',
            'totalConsumers',
            'activeConsumers',
            'inactiveConsumers',
            'totalBills',
            'paidBills',
            'unpaidBills',
            'overdueBills',
            'totalBilled',
            'totalCollected',
            'outstandingBalance',
            'collectionRate',
            'recentBills',
            'recentPayments'
        ));
    }
}
