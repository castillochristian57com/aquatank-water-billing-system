<?php

namespace App\Console\Commands;

use App\Models\Bill;
use App\Models\Consumer;
use App\Models\MeterReading;
use App\Models\Payment;
use App\Models\SmsLog;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanDummyData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clean-dummy-data
                            {--all-consumers : Delete all consumers instead of just seeded dummy records}
                            {--reset-system : Perform a full fresh deployment reset (purges all consumers, transactions, and users)}
                            {--force : Force operation without asking confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Removes dummy customers, bills, payments, readings, SMS logs, and users for a clean system state';

    /**
     * Known seeded dummy account numbers.
     */
    protected array $dummyAccountNumbers = [
        'CRH-2026-0001',
        'CRH-2026-0002',
        'CRH-2026-0003',
        'CRH-2026-0004',
        'CRH-2026-0005',
        'CRH-2026-0006',
        'AT-TEST-0001',
        'AT-TEST-0002',
        'AT-TEST-0003',
        'AT-TEST-0004',
        'AT-TEST-0005',
        'AT-TEST-0006',
        'AT-TEST-0007',
        'AT-TEST-0008',
        'AT-TEST-0009',
        'AT-TEST-0010',
    ];

    /**
     * Known seeded dummy meter numbers.
     */
    protected array $dummyMeterNumbers = [
        'MTR-CAR-1001',
        'MTR-CAR-1002',
        'MTR-CAR-1003',
        'MTR-CAR-1004',
        'MTR-CAR-1005',
        'MTR-CAR-1006',
        'MTR-TEST-0001',
        'MTR-TEST-0002',
        'MTR-TEST-0003',
        'MTR-TEST-0004',
        'MTR-TEST-0005',
        'MTR-TEST-0006',
        'MTR-TEST-0007',
        'MTR-TEST-0008',
        'MTR-TEST-0009',
        'MTR-TEST-0010',
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $allConsumers = (bool) $this->option('all-consumers');
        $resetSystem = (bool) $this->option('reset-system');
        $force = (bool) $this->option('force');

        if ($resetSystem) {
            $this->warn('CRITICAL: --reset-system will wipe ALL consumer records, transaction history, and user accounts.');
        } else {
            $this->warn('This command will clean dummy customers, staff users, and their associated data.');
        }

        if (! $force && ! $this->confirm('Are you sure you want to proceed with this operation?')) {
            $this->info('Operation cancelled.');
            return Command::SUCCESS;
        }

        return DB::transaction(function () use ($allConsumers, $resetSystem) {
            if ($resetSystem) {
                $smsLogsDeleted = SmsLog::query()->delete();
                $paymentsDeleted = Payment::query()->delete();
                $meterReadingsDeleted = MeterReading::query()->delete();
                $billsDeleted = Bill::query()->delete();
                $consumersDeleted = Consumer::query()->delete();
                $usersDeleted = User::query()->delete();

                $this->newLine();
                $this->info('Full system reset completed successfully!');
                $this->table(
                    ['Record Type', 'Count Deleted'],
                    [
                        ['Consumers', $consumersDeleted],
                        ['Bills', $billsDeleted],
                        ['Payments', $paymentsDeleted],
                        ['Meter Readings', $meterReadingsDeleted],
                        ['SMS Logs', $smsLogsDeleted],
                        ['User Accounts', $usersDeleted],
                    ]
                );
                $this->comment('Database is clean and ready for initial administrator registration.');
                return Command::SUCCESS;
            }

            // 1. Identify target consumers
            if ($allConsumers) {
                $consumersQuery = Consumer::query();
            } else {
                $consumersQuery = Consumer::whereIn('account_number', $this->dummyAccountNumbers)
                    ->orWhereIn('meter_number', $this->dummyMeterNumbers);
            }

            $consumerIds = $consumersQuery->pluck('id')->toArray();

            // 2. Delete related SMS logs (including simulated logs)
            $smsLogsDeleted = SmsLog::whereIn('consumer_id', $consumerIds)
                ->orWhere('status', 'simulated')
                ->orWhere('message_id', 'like', 'SIM-%')
                ->delete();

            // 3. Delete related Meter Readings
            $meterReadingsDeleted = MeterReading::whereIn('consumer_id', $consumerIds)->delete();

            // 4. Delete related Payments
            $paymentsDeleted = Payment::whereIn('consumer_id', $consumerIds)
                ->orWhereHas('bill', function ($q) use ($consumerIds) {
                    $q->whereIn('consumer_id', $consumerIds);
                })
                ->delete();

            // 5. Delete related Bills
            $billsDeleted = Bill::whereIn('consumer_id', $consumerIds)->delete();

            // 6. Delete Consumers
            $consumersDeleted = Consumer::whereIn('id', $consumerIds)->delete();

            // 7. Delete dummy staff users
            $usersDeleted = User::whereIn('email', ['staff@aquatank.local', 'admin@aquatank.local'])->delete();

            $this->newLine();
            $this->info('Dummy data removal completed successfully!');
            $this->table(
                ['Record Type', 'Count Deleted'],
                [
                    ['Dummy Consumers', $consumersDeleted],
                    ['Associated Bills', $billsDeleted],
                    ['Associated Payments', $paymentsDeleted],
                    ['Associated Meter Readings', $meterReadingsDeleted],
                    ['Associated SMS Logs', $smsLogsDeleted],
                    ['Dummy Users', $usersDeleted],
                ]
            );

            return Command::SUCCESS;
        });
    }
}
