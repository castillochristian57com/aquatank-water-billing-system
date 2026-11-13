<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Consumer;
use App\Models\Setting;
use App\Models\SmsLog;
use App\Services\SmsService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SmsController extends Controller
{
    protected SmsService $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    // sms notifications hub and dispatch logs
    public function index(Request $request): View
    {
        $query = SmsLog::with(['consumer', 'bill'])->orderBy('sent_at', 'desc');

        if ($type = $request->input('message_type')) {
            $query->where('message_type', $type);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('recipient_phone', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%")
                  ->orWhereHas('consumer', function ($cq) use ($search) {
                      $cq->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%")
                         ->orWhere('account_number', 'like', "%{$search}%");
                  });
            });
        }

        $logs = $query->paginate(15)->withQueryString();

        // dispatch statistics
        $totalSent = SmsLog::where('status', 'sent')->count();
        $totalSimulated = SmsLog::where('status', 'simulated')->count();
        $totalFailed = SmsLog::where('status', 'failed')->count();
        $totalLogs = SmsLog::count();

        // pending reminders & overdue count
        $today = Carbon::today();
        $pendingDueCount = Bill::where('status', 'unpaid')
            ->whereDate('due_date', '>=', $today)
            ->whereDate('due_date', '<=', $today->copy()->addDays(5))
            ->count();

        $overdueCount = Bill::where(function ($q) use ($today) {
            $q->where('status', 'overdue')
              ->orWhere(function ($sub) use ($today) {
                  $sub->where('status', 'unpaid')->whereDate('due_date', '<', $today);
              });
        })->count();

        // gateway settings
        $currentProvider = Setting::get('sms_gateway_provider', 'semaphore');
        $simulationMode = Setting::get('sms_simulation_mode', '1') === '1';
        $apiKey = Setting::get('sms_api_key', '');
        $senderName = Setting::get('sms_sender_name', 'AQUATANK');

        return view('sms.index', compact(
            'logs',
            'totalSent',
            'totalSimulated',
            'totalFailed',
            'totalLogs',
            'pendingDueCount',
            'overdueCount',
            'currentProvider',
            'simulationMode',
            'apiKey',
            'senderName'
        ));
    }

    // dispatch initial billing notice
    public function sendBillingNotice(Bill $bill): RedirectResponse
    {
        $result = $this->smsService->sendBillingNotice($bill);

        if ($result['success']) {
            return back()->with('success', $result['message']);
        }

        return back()->with('error', $result['message']);
    }

    // dispatch single bill reminder
    public function sendBillReminder(Bill $bill): RedirectResponse
    {
        $result = $this->smsService->sendBillReminder($bill);

        if ($result['success']) {
            return back()->with('success', $result['message']);
        }

        return back()->with('error', $result['message']);
    }

    // dispatch single overdue alert
    public function sendOverdueAlert(Bill $bill): RedirectResponse
    {
        $result = $this->smsService->sendOverdueAlert($bill);

        if ($result['success']) {
            return back()->with('success', $result['message']);
        }

        return back()->with('error', $result['message']);
    }

    // dispatch batch billing reminders
    public function batchReminders(Request $request): RedirectResponse
    {
        $days = (int) $request->input('days_ahead', 5);
        $today = Carbon::today();
        $targetDate = $today->copy()->addDays($days);

        $bills = Bill::with('consumer')
            ->where('status', 'unpaid')
            ->whereDate('due_date', '>=', $today)
            ->whereDate('due_date', '<=', $targetDate)
            ->get();

        if ($bills->isEmpty()) {
            return back()->with('info', 'No unpaid bills due within the next ' . $days . ' days.');
        }

        $sentCount = 0;
        foreach ($bills as $bill) {
            $res = $this->smsService->sendBillReminder($bill);
            if ($res['success']) {
                $sentCount++;
            }
        }

        return back()->with('success', "Batch billing reminders processed: {$sentCount} notices dispatched successfully.");
    }

    // dispatch batch overdue alerts
    public function batchOverdueAlerts(): RedirectResponse
    {
        $today = Carbon::today();

        $bills = Bill::with('consumer')
            ->where(function ($q) use ($today) {
                $q->where('status', 'overdue')
                  ->orWhere(function ($sub) use ($today) {
                      $sub->where('status', 'unpaid')->whereDate('due_date', '<', $today);
                  });
            })
            ->get();

        if ($bills->isEmpty()) {
            return back()->with('info', 'No overdue accounts found at this moment.');
        }

        $sentCount = 0;
        foreach ($bills as $bill) {
            // mark as overdue if still unpaid
            if ($bill->status === 'unpaid') {
                $bill->update(['status' => 'overdue']);
            }

            $res = $this->smsService->sendOverdueAlert($bill);
            if ($res['success']) {
                $sentCount++;
            }
        }

        return back()->with('success', "Batch overdue alerts dispatched: {$sentCount} overdue warning messages sent.");
    }

    // send custom or test sms
    public function sendTestSms(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'phone' => ['required', 'string', 'min:10', 'max:20'],
            'message' => ['required', 'string', 'max:160'],
        ]);

        $res = $this->smsService->sendCustom($validated['phone'], $validated['message']);

        if ($res['success']) {
            return back()->with('success', $res['message']);
        }

        return back()->with('error', $res['message']);
    }

    // update sms gateway settings
    public function updateSettings(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'sms_gateway_provider' => ['required', 'string', 'in:semaphore,philsms,generic'],
            'sms_sender_name' => ['required', 'string', 'max:15'],
            'sms_api_key' => ['nullable', 'string', 'max:255'],
            'sms_simulation_mode' => ['nullable', 'boolean'],
        ]);

        Setting::set('sms_gateway_provider', $validated['sms_gateway_provider']);
        Setting::set('sms_sender_name', $validated['sms_sender_name']);
        Setting::set('sms_api_key', $validated['sms_api_key'] ?? '');
        Setting::set('sms_simulation_mode', $request->has('sms_simulation_mode') ? '1' : '0');

        return back()->with('success', 'SMS Gateway configuration updated successfully.');
    }
}
