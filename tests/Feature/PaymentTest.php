<?php

namespace Tests\Feature;

use App\Models\Bill;
use App\Models\Consumer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
    }

    public function test_payment_one_cent_over_balance_is_rejected(): void
    {
        $bill = $this->createBill(100.00);

        $this->actingAs($this->admin)
            ->post(route('payments.store'), $this->paymentData($bill, '100.01'))
            ->assertSessionHasErrors('amount_paid');

        $this->assertDatabaseCount('payments', 0);
        $this->assertDatabaseHas('bills', [
            'id' => $bill->id,
            'status' => 'unpaid',
        ]);
    }

    public function test_exact_balance_payment_is_recorded_and_marks_bill_paid(): void
    {
        $bill = $this->createBill(100.00);

        $response = $this->actingAs($this->admin)
            ->post(route('payments.store'), $this->paymentData($bill, '100.00'));

        $response->assertRedirect(route('payments.receipt', ['payment' => 1]));
        $this->assertDatabaseHas('payments', [
            'bill_id' => $bill->id,
            'consumer_id' => $bill->consumer_id,
            'reference_number' => 'OR-TEST-0001',
            'amount_paid' => '100.00',
        ]);
        $this->assertDatabaseHas('bills', [
            'id' => $bill->id,
            'status' => 'paid',
        ]);
    }

    private function createBill(float $amountDue): Bill
    {
        $consumer = Consumer::create([
            'account_number' => 'PAY-0001',
            'first_name' => 'Test',
            'last_name' => 'Consumer',
            'address' => 'Poblacion',
            'contact_number' => '09171234567',
            'meter_number' => 'MTR-PAY-0001',
        ]);

        return Bill::create([
            'bill_number' => 'BILL-PAY-0001',
            'consumer_id' => $consumer->id,
            'previous_reading' => 0,
            'current_reading' => 10,
            'consumption' => 10,
            'billing_period' => 'January 2026',
            'billing_date' => '2026-01-31',
            'due_date' => '2026-02-15',
            'rate_per_cubic_meter' => 10,
            'amount_due' => $amountDue,
            'status' => 'unpaid',
        ]);
    }

    private function paymentData(Bill $bill, string $amount): array
    {
        return [
            'bill_id' => $bill->id,
            'amount_paid' => $amount,
            'payment_date' => '2026-11-11',
            'reference_number' => 'OR-TEST-0001',
            'payment_method' => 'Cash',
        ];
    }
}