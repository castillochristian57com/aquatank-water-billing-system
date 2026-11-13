<?php

namespace Tests\Feature;

use App\Models\Bill;
use App\Models\Consumer;
use App\Models\MeterReading;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MeterReadingTest extends TestCase
{
    use RefreshDatabase;

    protected User $staff;

    protected function setUp(): void
    {
        parent::setUp();

        $this->staff = User::factory()->create([
            'name' => 'Staff Meter Reader',
            'email' => 'staff@aquatank.local',
        ]);

        Setting::updateOrCreate(
            ['key' => 'water_rate_per_cbm'],
            ['value' => '15.00', 'description' => 'Water Rate per Cubic Meter']
        );
    }

    /**
     * Test A: Existing consumer with previous reading 125 m3.
     * Staff enters 143 m3 -> consumption is 18 m3.
     * Saved to database, and the next entry shows 143 m3 as the previous reading.
     */
    public function test_a_existing_consumer_reading_and_subsequent_reading_progression(): void
    {
        $consumer = Consumer::create([
            'account_number' => 'CAR-001',
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'address' => 'Purok 1, Caruhayon',
            'contact_number' => '09171234501',
            'meter_number' => 'MTR-CAR-001',
            'status' => 'active',
        ]);

        // Historical reading: 125 m3
        MeterReading::create([
            'consumer_id' => $consumer->id,
            'user_id' => $this->staff->id,
            'previous_reading' => 100.00,
            'current_reading' => 125.00,
            'consumption' => 25.00,
            'reading_date' => '2026-09-01',
            'billing_period' => 'September 2026',
            'status' => 'billed',
        ]);

        // Verify previous reading is automatically 125.00
        $this->assertEquals(125.00, $consumer->fresh()->latest_reading);

        // API verification
        $apiResponse = $this->actingAs($this->staff)
            ->getJson(route('meter-readings.consumer', $consumer));

        $apiResponse->assertOk()
            ->assertJson([
                'id' => $consumer->id,
                'account_number' => 'CAR-001',
                'previous_reading' => 125.00,
                'has_previous_reading' => true,
            ]);

        // Staff submits current reading: 143 m3
        $response = $this->actingAs($this->staff)
            ->post(route('meter-readings.store'), [
                'consumer_id' => $consumer->id,
                'current_reading' => 143.00,
                'reading_date' => '2026-10-01',
                'billing_period' => 'October 2026',
                'notes' => 'Test A reading',
            ]);

        $response->assertRedirect(route('meter-readings.receipt', MeterReading::latest('id')->first()));
        $response->assertSessionHas('success');

        // Verify database persistence
        $this->assertDatabaseHas('meter_readings', [
            'consumer_id' => $consumer->id,
            'previous_reading' => 125.00,
            'current_reading' => 143.00,
            'consumption' => 18.00,
            'billing_period' => 'October 2026',
            'user_id' => $this->staff->id,
        ]);

        $reading = MeterReading::latest('id')->firstOrFail();
        $receiptResponse = $this->actingAs($this->staff)
            ->get(route('meter-readings.receipt', $reading));

        $receiptResponse->assertOk()
            ->assertSee('WATER METER READING RECEIPT')
            ->assertSee('October 2026')
            ->assertSee('270.00')
            ->assertSee('Print Receipt')
            ->assertSee('onclick="window.print()"', false);

        // Verify that the next entry automatically shows 143.00 as previous reading
        $this->assertEquals(143.00, $consumer->fresh()->latest_reading);

        $nextApiResponse = $this->actingAs($this->staff)
            ->getJson(route('meter-readings.consumer', $consumer));

        $nextApiResponse->assertOk()
            ->assertJson([
                'id' => $consumer->id,
                'previous_reading' => 143.00,
                'has_previous_reading' => true,
            ]);
    }

    /**
     * Test B: Different consumers have separate previous readings and are never mixed.
     */
    public function test_b_different_consumers_display_own_previous_readings(): void
    {
        $consumerA = Consumer::create([
            'account_number' => 'CAR-001',
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'address' => 'Purok 1',
            'contact_number' => '09171234501',
            'meter_number' => 'MTR-001',
            'status' => 'active',
        ]);

        $consumerB = Consumer::create([
            'account_number' => 'CAR-002',
            'first_name' => 'Maria',
            'last_name' => 'Clara',
            'address' => 'Purok 2',
            'contact_number' => '09181234502',
            'meter_number' => 'MTR-002',
            'status' => 'active',
        ]);

        MeterReading::create([
            'consumer_id' => $consumerA->id,
            'user_id' => $this->staff->id,
            'previous_reading' => 50.00,
            'current_reading' => 125.00,
            'consumption' => 75.00,
            'reading_date' => '2026-09-01',
            'billing_period' => 'September 2026',
            'status' => 'billed',
        ]);

        MeterReading::create([
            'consumer_id' => $consumerB->id,
            'user_id' => $this->staff->id,
            'previous_reading' => 200.00,
            'current_reading' => 240.00,
            'consumption' => 40.00,
            'reading_date' => '2026-09-01',
            'billing_period' => 'September 2026',
            'status' => 'billed',
        ]);

        $resA = $this->actingAs($this->staff)->getJson(route('meter-readings.consumer', $consumerA));
        $resA->assertOk()->assertJson(['previous_reading' => 125.00]);

        $resB = $this->actingAs($this->staff)->getJson(route('meter-readings.consumer', $consumerB));
        $resB->assertOk()->assertJson(['previous_reading' => 240.00]);

        // Check search endpoint
        $searchRes = $this->actingAs($this->staff)->getJson(route('meter-readings.search', ['q' => 'Maria']));
        $searchRes->assertOk();
        $this->assertEquals(240.00, $searchRes->json()[0]['previous_reading']);
    }

    /**
     * Test C: Invalid reading lower than previous reading is rejected with a clear error.
     */
    public function test_c_invalid_reading_lower_than_previous_is_rejected(): void
    {
        $consumer = Consumer::create([
            'account_number' => 'CAR-001',
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'address' => 'Purok 1',
            'contact_number' => '09171234501',
            'meter_number' => 'MTR-001',
            'status' => 'active',
        ]);

        MeterReading::create([
            'consumer_id' => $consumer->id,
            'user_id' => $this->staff->id,
            'previous_reading' => 100.00,
            'current_reading' => 125.00,
            'consumption' => 25.00,
            'reading_date' => '2026-09-01',
            'billing_period' => 'September 2026',
            'status' => 'billed',
        ]);

        // Staff enters 120.00 (lower than 125.00)
        $response = $this->actingAs($this->staff)
            ->post(route('meter-readings.store'), [
                'consumer_id' => $consumer->id,
                'current_reading' => 120.00,
                'reading_date' => '2026-10-01',
                'billing_period' => 'October 2026',
            ]);

        $response->assertSessionHasErrors('current_reading');

        // Confirm database did not record the invalid entry
        $this->assertDatabaseMissing('meter_readings', [
            'consumer_id' => $consumer->id,
            'current_reading' => 120.00,
        ]);
    }

    /**
     * Test D: Consumer with no previous reading is identified as initial reading.
     */
    public function test_d_initial_reading_for_new_consumer(): void
    {
        $newConsumer = Consumer::create([
            'account_number' => 'CAR-999',
            'first_name' => 'Bagong',
            'last_name' => 'Konsumidor',
            'address' => 'Purok 5',
            'contact_number' => '09179998888',
            'meter_number' => 'MTR-NEW-999',
            'status' => 'active',
        ]);

        $this->assertFalse($newConsumer->hasPreviousReading());
        $this->assertEquals(0.00, $newConsumer->latest_reading);

        $apiRes = $this->actingAs($this->staff)->getJson(route('meter-readings.consumer', $newConsumer));
        $apiRes->assertOk()->assertJson([
            'has_previous_reading' => false,
            'is_initial_reading' => true,
            'previous_reading' => 0.00,
        ]);

        // Initial reading entry
        $response = $this->actingAs($this->staff)->post(route('meter-readings.store'), [
            'consumer_id' => $newConsumer->id,
            'current_reading' => 15.00,
            'reading_date' => '2026-10-01',
            'billing_period' => 'October 2026',
        ]);

        $response->assertRedirect(route('meter-readings.receipt', MeterReading::latest('id')->first()));
        $this->assertDatabaseHas('meter_readings', [
            'consumer_id' => $newConsumer->id,
            'previous_reading' => 0.00,
            'current_reading' => 15.00,
            'consumption' => 15.00,
        ]);
    }

    /**
     * Test E: Mobile page renders properly with all required elements.
     */
    public function test_e_meter_reading_page_renders_with_mobile_friendly_elements(): void
    {
        $response = $this->actingAs($this->staff)->get(route('meter-readings.create'));

        $response->assertOk();
        $response->assertSee('Record Meter Reading');
        $response->assertSee('consumerSearchInput');
        $response->assertSee('current_reading');
        $response->assertSee('reading_date');
        $response->assertSee('billing_period');
        $response->assertSee('Save Reading');
        $response->assertSee('Smartphone Optimized');
    }

    /**
     * Test F: Database and Billing integration.
     * Prevents duplicate readings and connects to billing.
     */
    public function test_f_duplicate_prevention_and_billing_connection(): void
    {
        $consumer = Consumer::create([
            'account_number' => 'CAR-001',
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'address' => 'Purok 1',
            'contact_number' => '09171234501',
            'meter_number' => 'MTR-001',
            'status' => 'active',
        ]);

        // First submission succeeds
        $res1 = $this->actingAs($this->staff)->post(route('meter-readings.store'), [
            'consumer_id' => $consumer->id,
            'current_reading' => 143.00,
            'reading_date' => '2026-10-01',
            'billing_period' => 'October 2026',
        ]);
        $res1->assertSessionHasNoErrors();

        // Duplicate submission for same consumer and period fails
        $res2 = $this->actingAs($this->staff)->post(route('meter-readings.store'), [
            'consumer_id' => $consumer->id,
            'current_reading' => 145.00,
            'reading_date' => '2026-10-01',
            'billing_period' => 'October 2026',
        ]);
        $res2->assertSessionHasErrors('current_reading');

        // Check billing creation uses recorded reading
        $billCreateRes = $this->actingAs($this->staff)->get(route('bills.create', ['consumer_id' => $consumer->id]));
        $billCreateRes->assertOk();
        $billCreateRes->assertSee('143'); // Suggested current reading

        // Generate Bill
        $billStoreRes = $this->actingAs($this->staff)->post(route('bills.store'), [
            'consumer_id' => $consumer->id,
            'previous_reading' => 0.00,
            'current_reading' => 143.00,
            'billing_period' => 'October 2026',
            'billing_date' => '2026-10-01',
            'due_date' => '2026-10-16',
            'rate_per_cubic_meter' => 15.00,
        ]);
        $billStoreRes->assertSessionHasNoErrors();

        // Meter reading is now marked as billed
        $reading = MeterReading::where('consumer_id', $consumer->id)->where('billing_period', 'October 2026')->first();
        $this->assertEquals('billed', $reading->status);
        $this->assertNotNull($reading->bill_id);

        // Attempting to generate duplicate bill for same period is rejected
        $duplicateBillRes = $this->actingAs($this->staff)->post(route('bills.store'), [
            'consumer_id' => $consumer->id,
            'previous_reading' => 0.00,
            'current_reading' => 143.00,
            'billing_period' => 'October 2026',
            'billing_date' => '2026-10-01',
            'due_date' => '2026-10-16',
            'rate_per_cubic_meter' => 15.00,
        ]);
        $duplicateBillRes->assertSessionHasErrors('billing_period');
    }
}
