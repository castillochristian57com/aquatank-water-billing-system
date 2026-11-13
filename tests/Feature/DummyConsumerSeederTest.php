<?php

namespace Tests\Feature;

use App\Models\Consumer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DummyConsumerSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_dummy_consumers_can_be_seeded_repeatedly_and_cleaned_safely(): void
    {
        $existingConsumer = Consumer::create([
            'account_number' => 'REAL-0001',
            'first_name' => 'Existing',
            'last_name' => 'Consumer',
            'address' => 'Poblacion',
            'contact_number' => '09171230000',
            'meter_number' => 'MTR-REAL-0001',
        ]);

        $this->seed(\Database\Seeders\DummyConsumerSeeder::class);
        $this->seed(\Database\Seeders\DummyConsumerSeeder::class);

        $this->assertDatabaseCount('consumers', 11);
        $this->assertDatabaseHas('consumers', [
            'account_number' => 'AT-TEST-0001',
            'meter_number' => 'MTR-TEST-0001',
            'status' => 'active',
        ]);

        $this->artisan('app:clean-dummy-data', ['--force' => true])
            ->assertExitCode(0);

        $this->assertDatabaseCount('consumers', 1);
        $this->assertDatabaseHas('consumers', [
            'id' => $existingConsumer->id,
            'account_number' => 'REAL-0001',
        ]);
    }
}