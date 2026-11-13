<?php

namespace App\Console\Commands;

use App\Models\Bill;
use App\Services\SmsService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendAutomatedBillingReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms:send-reminders {--days=3 : Days ahead of due date to notify}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatches automated SMS billing reminders to registered consumer mobile numbers for upcoming due bills';

    /**
     * Execute the console command.
     */
    public function handle(SmsService $smsService): int
    {
        $days = (int) $this->option('days');
        $today = Carbon::today();
        $targetDate = $today->copy()->addDays($days);

        $this->info("Scanning for unpaid bills due between {$today->toDateString()} and {$targetDate->toDateString()}...");

        $bills = Bill::with('consumer')
            ->where('status', 'unpaid')
            ->whereDate('due_date', '>=', $today)
            ->whereDate('due_date', '<=', $targetDate)
            ->get();

        if ($bills->isEmpty()) {
            $this->info("No unpaid bills due in the next {$days} days.");
            return Command::SUCCESS;
        }

        $sent = 0;
        foreach ($bills as $bill) {
            $res = $smsService->sendBillReminder($bill);
            if ($res['success']) {
                $sent++;
                $this->line("<info>[SENT]</info> Bill #{$bill->bill_number} to {$bill->consumer->full_name} ({$bill->consumer->contact_number})");
            } else {
                $this->warn("<comment>[FAILED]</comment> Bill #{$bill->bill_number}: {$res['message']}");
            }
        }

        $this->info("Dispatched {$sent} automated billing reminder SMS successfully.");
        return Command::SUCCESS;
    }
}
