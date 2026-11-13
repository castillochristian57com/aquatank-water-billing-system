<?php

namespace App\Console\Commands;

use App\Models\Bill;
use App\Services\SmsService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendAutomatedOverdueAlerts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms:send-overdue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scans and dispatches urgent overdue alerts to registered mobile numbers for overdue bills';

    /**
     * Execute the console command.
     */
    public function handle(SmsService $smsService): int
    {
        $today = Carbon::today();
        $this->info("Scanning for unpaid bills past due date ({$today->toDateString()})...");

        $bills = Bill::with('consumer')
            ->where(function ($q) use ($today) {
                $q->where('status', 'overdue')
                  ->orWhere(function ($sub) use ($today) {
                      $sub->where('status', 'unpaid')->whereDate('due_date', '<', $today);
                  });
            })
            ->get();

        if ($bills->isEmpty()) {
            $this->info("No overdue accounts found.");
            return Command::SUCCESS;
        }

        $sent = 0;
        foreach ($bills as $bill) {
            if ($bill->status === 'unpaid') {
                $bill->update(['status' => 'overdue']);
            }

            $res = $smsService->sendOverdueAlert($bill);
            if ($res['success']) {
                $sent++;
                $this->line("<info>[ALERT SENT]</info> Overdue Bill #{$bill->bill_number} to {$bill->consumer->full_name} ({$bill->consumer->contact_number})");
            } else {
                $this->warn("<comment>[FAILED]</comment> Bill #{$bill->bill_number}: {$res['message']}");
            }
        }

        $this->info("Dispatched {$sent} automated overdue alert SMS successfully.");
        return Command::SUCCESS;
    }
}
