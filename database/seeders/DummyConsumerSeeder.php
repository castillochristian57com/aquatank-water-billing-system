<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class DummyConsumerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $consumers = [
            ['Juan', 'Dela Cruz', 'Poblacion', '09171234501'],
            ['Maria', 'Santos', 'Caruhayon', '09171234502'],
            ['Pedro', 'Reyes', 'Poblacion', '09171234503'],
            ['Ana', 'Garcia', 'Caruhayon', '09171234504'],
            ['Jose', 'Mendoza', 'Poblacion', '09171234505'],
            ['Rosa', 'Cruz', 'Caruhayon', '09171234506'],
            ['Mark', 'Ramos', 'Poblacion', '09171234507'],
            ['Liza', 'Flores', 'Caruhayon', '09171234508'],
            ['Carlo', 'Bautista', 'Poblacion', '09171234509'],
            ['Sofia', 'Villanueva', 'Caruhayon', '09171234510'],
        ];

        foreach ($consumers as $index => $consumer) {
            $sequence = str_pad($index + 1, 4, '0', STR_PAD_LEFT);
            $accountNumber = 'AT-TEST-' . str_pad(
                $index + 1,
                4,
                '0',
                STR_PAD_LEFT
            );

            DB::table('consumers')->updateOrInsert(
                ['account_number' => $accountNumber],
                [
                    'first_name' => $consumer[0],
                    'last_name' => $consumer[1],
                    'address' => $consumer[2],
                    'contact_number' => $consumer[3],
                    'purok' => 'Purok ' . (($index % 7) + 1),
                    'meter_number' => 'MTR-TEST-' . $sequence,
                    'connection_date' => '2026-01-01',
                    'status' => 'active',
                    'deleted_at' => null,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        $this->command->info('10 dummy consumers generated successfully!');
    }
}
