<?php

namespace Tests\Feature;

use App\Models\Bill;
use App\Models\Consumer;
use App\Models\MeterReading;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConsumerManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'name' => 'Barangay Water Admin',
            'email' => 'admin@aquatank.test',
        ]);
    }

    /**
     * Scenario 11: Unauthorized guest users cannot access consumer management.
     */
    public function test_guest_cannot_access_consumer_management(): void
    {
        $this->get('/consumers')->assertRedirect('/login');
        $this->get('/consumers/create')->assertRedirect('/login');
        $this->post('/consumers', [])->assertRedirect('/login');
    }

    /**
     * Scenario 1 & 2: Authorized administrator can create a new consumer and see it in list.
     */
    public function test_admin_can_create_new_consumer(): void
    {
        $response = $this->actingAs($this->admin)->post('/consumers', [
            'account_number' => 'CRH-2026-0001',
            'first_name' => 'Maria',
            'middle_name' => 'Clara',
            'last_name' => 'Ramos',
            'address' => 'Purok 2, Caruhayon, Juban',
            'contact_number' => '0917-123-4567',
            'purok' => 'Purok 2',
            'meter_number' => 'MTR-CAR-1001',
            'connection_date' => '2026-11-05',
            'status' => 'active',
        ]);

        $consumer = Consumer::where('account_number', 'CRH-2026-0001')->first();
        $this->assertNotNull($consumer);
        $this->assertEquals('Maria Clara Ramos', $consumer->full_name);
        $this->assertEquals('09171234567', $consumer->contact_number); // Normalized phone
        $this->assertEquals('Purok 2', $consumer->purok);

        $response->assertRedirect(route('consumers.show', $consumer));
        $response->assertSessionHas('success');

        // Check it appears in the list
        $indexResponse = $this->actingAs($this->admin)->get('/consumers');
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('CRH-2026-0001');
        $indexResponse->assertSee('Maria Clara Ramos');
    }

    /**
     * Scenario 3: Administrator can view consumer details.
     */
    public function test_admin_can_view_consumer_profile(): void
    {
        $consumer = Consumer::create([
            'account_number' => 'CRH-2026-0002',
            'first_name' => 'Jose',
            'last_name' => 'Rizal',
            'address' => 'Purok 1, Caruhayon',
            'contact_number' => '09181234567',
            'purok' => 'Purok 1',
            'meter_number' => 'MTR-CAR-1002',
            'connection_date' => '2026-11-01',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->admin)->get(route('consumers.show', $consumer));
        $response->assertStatus(200);
        $response->assertSee('CRH-2026-0002');
        $response->assertSee('Jose Rizal');
        $response->assertSee('MTR-CAR-1002');
    }

    /**
     * Scenario 4 & 5: Administrator can edit and save consumer information.
     */
    public function test_admin_can_edit_and_update_consumer(): void
    {
        $consumer = Consumer::create([
            'account_number' => 'CRH-2026-0003',
            'first_name' => 'Andres',
            'last_name' => 'Bonifacio',
            'address' => 'Purok 3, Caruhayon',
            'contact_number' => '09191234567',
            'purok' => 'Purok 3',
            'meter_number' => 'MTR-CAR-1003',
            'connection_date' => '2026-10-15',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->admin)->put(route('consumers.update', $consumer), [
            'account_number' => 'CRH-2026-0003',
            'first_name' => 'Andres',
            'middle_name' => 'Castro',
            'last_name' => 'Bonifacio',
            'address' => 'Purok 4, Caruhayon, Juban (Relocated)',
            'contact_number' => '09199998888',
            'purok' => 'Purok 4',
            'meter_number' => 'MTR-CAR-1003',
            'connection_date' => '2026-10-15',
            'status' => 'inactive',
        ]);

        $response->assertRedirect(route('consumers.show', $consumer));
        $response->assertSessionHas('success');

        $consumer->refresh();
        $this->assertEquals('Andres Castro Bonifacio', $consumer->full_name);
        $this->assertEquals('Purok 4, Caruhayon, Juban (Relocated)', $consumer->address);
        $this->assertEquals('09199998888', $consumer->contact_number);
        $this->assertEquals('inactive', $consumer->status);
    }

    /**
     * Scenario 6: Duplicate account numbers are rejected.
     */
    public function test_duplicate_account_number_is_rejected(): void
    {
        Consumer::create([
            'account_number' => 'CRH-2026-0004',
            'first_name' => 'Emilio',
            'last_name' => 'Aguinaldo',
            'address' => 'Purok 1',
            'contact_number' => '09201234567',
            'meter_number' => 'MTR-CAR-1004',
            'connection_date' => '2026-01-01',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->admin)->post('/consumers', [
            'account_number' => 'CRH-2026-0004', // duplicate
            'first_name' => 'Apolinario',
            'last_name' => 'Mabini',
            'address' => 'Purok 2',
            'contact_number' => '09207654321',
            'meter_number' => 'MTR-CAR-9999',
            'connection_date' => '2026-02-01',
            'status' => 'active',
        ]);

        $response->assertSessionHasErrors('account_number');
    }

    /**
     * Scenario 7: Duplicate meter numbers are rejected.
     */
    public function test_duplicate_meter_number_is_rejected(): void
    {
        Consumer::create([
            'account_number' => 'CRH-2026-0005',
            'first_name' => 'Gabriela',
            'last_name' => 'Silang',
            'address' => 'Purok 5',
            'contact_number' => '09211234567',
            'meter_number' => 'MTR-CAR-1005',
            'connection_date' => '2026-01-01',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->admin)->post('/consumers', [
            'account_number' => 'CRH-2026-0006',
            'first_name' => 'Diego',
            'last_name' => 'Silang',
            'address' => 'Purok 6',
            'contact_number' => '09217654321',
            'meter_number' => 'MTR-CAR-1005', // duplicate meter
            'connection_date' => '2026-02-01',
            'status' => 'active',
        ]);

        $response->assertSessionHasErrors('meter_number');
    }

    /**
     * Scenario 7b: Invalid mobile numbers are rejected.
     */
    public function test_invalid_contact_number_is_rejected(): void
    {
        $response = $this->actingAs($this->admin)->post('/consumers', [
            'account_number' => 'CRH-2026-0007',
            'first_name' => 'Juan',
            'last_name' => 'Luna',
            'address' => 'Purok 1',
            'contact_number' => '12345', // invalid phone
            'meter_number' => 'MTR-CAR-1007',
            'connection_date' => '2026-01-01',
            'status' => 'active',
        ]);

        $response->assertSessionHasErrors('contact_number');
    }

    /**
     * Scenario 8: Consumer search and purok filter return correct records.
     */
    public function test_consumer_search_and_purok_filter(): void
    {
        Consumer::create([
            'account_number' => 'CRH-2026-0010',
            'first_name' => 'Antonio',
            'last_name' => 'Luna',
            'address' => 'Centro Riverside',
            'contact_number' => '09221112222',
            'purok' => 'Purok 1',
            'meter_number' => 'MTR-CAR-2010',
            'connection_date' => '2026-03-01',
            'status' => 'active',
        ]);

        Consumer::create([
            'account_number' => 'CRH-2026-0020',
            'first_name' => 'Melchora',
            'last_name' => 'Aquino',
            'address' => 'Hilltop Road',
            'contact_number' => '09223334444',
            'purok' => 'Purok 7',
            'meter_number' => 'MTR-CAR-2020',
            'connection_date' => '2026-03-01',
            'status' => 'active',
        ]);

        // Search by name
        $searchResponse = $this->actingAs($this->admin)->get('/consumers?search=Antonio');
        $searchResponse->assertSee('Antonio Luna');
        $searchResponse->assertDontSee('Melchora Aquino');

        // Filter by Purok 7
        $purokResponse = $this->actingAs($this->admin)->get('/consumers?purok=Purok+7');
        $purokResponse->assertSee('Melchora Aquino');
        $purokResponse->assertDontSee('Antonio Luna');
    }

    /**
     * Scenario 9 & 10: Safe deletion protects billing, meter reading, and payment records.
     */
    public function test_deletion_preserves_records_when_bills_exist(): void
    {
        $consumer = Consumer::create([
            'account_number' => 'CRH-2026-0030',
            'first_name' => 'Marcelo',
            'last_name' => 'Del Pilar',
            'address' => 'Purok 2',
            'contact_number' => '09251112233',
            'meter_number' => 'MTR-CAR-2030',
            'connection_date' => '2026-01-01',
            'status' => 'active',
        ]);

        // Create a bill for this consumer
        $bill = Bill::create([
            'bill_number' => 'BILL-2026-0001',
            'consumer_id' => $consumer->id,
            'previous_reading' => 0.0,
            'current_reading' => 12.5,
            'consumption' => 12.5,
            'billing_period' => 'January 2026',
            'billing_date' => '2026-01-31',
            'due_date' => '2026-02-15',
            'rate_per_cubic_meter' => 15.00,
            'amount_due' => 187.50,
            'status' => 'unpaid',
        ]);

        // Delete the consumer
        $response = $this->actingAs($this->admin)->delete(route('consumers.destroy', $consumer));
        $response->assertRedirect(route('consumers.index'));
        $response->assertSessionHas('success');

        // The consumer record must be SOFT-DELETED, NOT physically erased
        $this->assertSoftDeleted('consumers', ['id' => $consumer->id]);

        // Bill must still exist in database
        $this->assertDatabaseHas('bills', ['id' => $bill->id, 'consumer_id' => $consumer->id]);
    }

    /**
     * Scenario 9b: Fresh consumer with no records is deleted permanently.
     */
    public function test_fresh_consumer_without_records_is_permanently_deleted(): void
    {
        $consumer = Consumer::create([
            'account_number' => 'CRH-2026-0040',
            'first_name' => 'Graciano',
            'last_name' => 'Lopez Jaena',
            'address' => 'Purok 1',
            'contact_number' => '09261112233',
            'meter_number' => 'MTR-CAR-2040',
            'connection_date' => '2026-01-01',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('consumers.destroy', $consumer));
        $response->assertRedirect(route('consumers.index'));

        $this->assertDatabaseMissing('consumers', ['id' => $consumer->id]);
    }

    /**
     * Scenario 12: Public consumer portal cannot access another person's bill statement by guessing ID.
     */
    public function test_consumer_portal_statement_access_protection(): void
    {
        $consumer = Consumer::create([
            'account_number' => 'CRH-2026-0050',
            'first_name' => 'Teresa',
            'last_name' => 'Magbanua',
            'address' => 'Purok 3',
            'contact_number' => '09271112233',
            'meter_number' => 'MTR-CAR-2050',
            'connection_date' => '2026-01-01',
            'status' => 'active',
        ]);

        $bill = Bill::create([
            'bill_number' => 'BILL-2026-0050',
            'consumer_id' => $consumer->id,
            'previous_reading' => 0.0,
            'current_reading' => 10.0,
            'consumption' => 10.0,
            'billing_period' => 'February 2026',
            'billing_date' => '2026-02-28',
            'due_date' => '2026-03-15',
            'rate_per_cubic_meter' => 15.00,
            'amount_due' => 150.00,
            'status' => 'unpaid',
        ]);

        // Direct unauthorized URL access without lookup credentials
        $response = $this->get(route('portal.statement', $bill));
        $response->assertRedirect(route('portal.my-water-bill'));
        $response->assertSessionHas('error');

        // Authorized access with verified account token in URL
        $authResponse = $this->get(route('portal.statement', [
            'bill' => $bill,
            'account' => $consumer->account_number,
        ]));
        $authResponse->assertStatus(200);
        $authResponse->assertSee($consumer->full_name);
        $authResponse->assertSee('BILL-2026-0050');
    }
}

