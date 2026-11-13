<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Only essential operational settings are seeded.
     * No default users, consumers, or sample transactions are created.
     */
    public function run(): void
    {
        // Default System Settings
        Setting::updateOrCreate(
            ['key' => 'water_rate_per_cbm'],
            [
                'value' => '15.00',
                'description' => 'Water Rate per Cubic Meter (PHP)',
            ]
        );

        Setting::updateOrCreate(
            ['key' => 'barangay_name'],
            [
                'value' => 'Barangay Caruhayon',
                'description' => 'Local Barangay Jurisdiction',
            ]
        );

        Setting::updateOrCreate(
            ['key' => 'system_contact'],
            [
                'value' => '0917-123-CARU (2278)',
                'description' => 'Customer Care Hotline',
            ]
        );

        Setting::updateOrCreate(
            ['key' => 'due_days'],
            [
                'value' => '15',
                'description' => 'Days until bill is overdue',
            ]
        );
    }
}
