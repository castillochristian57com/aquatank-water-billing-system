<?php

namespace App\Services;

use App\Models\Bill;
use App\Models\Consumer;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\SmsLog;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    // send initial billing notice
    public function sendBillingNotice(Bill $bill): array
    {
        $bill->loadMissing('consumer');
        $consumer = $bill->consumer;

        if (!$consumer || empty($consumer->contact_number)) {
            return [
                'success' => false,
                'status' => 'failed',
                'message' => 'Consumer does not have a registered contact number.',
            ];
        }

        $barangay = Setting::get('barangay_name', 'Barangay Caruhayon');
        $contact = Setting::get('system_contact', '0917-123-CARU (2278)');
        $dueDate = Carbon::parse($bill->due_date)->format('M d, Y');
        $amount = number_format($bill->amount_due, 2);

        $message = "AQUATANK BILL ISSUED: Hello {$consumer->first_name}, your water bill for {$bill->billing_period} has been generated. Amount: PHP {$amount}, Consumption: {$bill->consumption} m3. Due Date: {$dueDate}. Acct: {$consumer->account_number}. Pay at {$barangay} Hall. Info: {$contact}";

        return $this->dispatchSms(
            $consumer->contact_number,
            $message,
            'billing_notice',
            $consumer->id,
            $bill->id
        );
    }

    // send billing reminder
    public function sendBillReminder(Bill $bill): array
    {
        $bill->loadMissing('consumer');
        $consumer = $bill->consumer;

        if (!$consumer || empty($consumer->contact_number)) {
            return [
                'success' => false,
                'status' => 'failed',
                'message' => 'Consumer does not have a registered contact number.',
            ];
        }

        $barangay = Setting::get('barangay_name', 'Barangay Caruhayon');
        $contact = Setting::get('system_contact', '0917-123-CARU (2278)');
        $dueDate = Carbon::parse($bill->due_date)->format('M d, Y');
        $amount = number_format($bill->amount_due, 2);

        $message = "AQUATANK NOTICE: Hello {$consumer->first_name}, your water bill for {$bill->billing_period} is PHP {$amount}. Due date: {$dueDate}. Acct: {$consumer->account_number}, Meter: {$consumer->meter_number}. Settle at {$barangay} Hall to avoid penalties. Help: {$contact}";

        return $this->dispatchSms(
            $consumer->contact_number,
            $message,
            'reminder',
            $consumer->id,
            $bill->id
        );
    }

    // send overdue alert
    public function sendOverdueAlert(Bill $bill): array
    {
        $bill->loadMissing('consumer');
        $consumer = $bill->consumer;

        if (!$consumer || empty($consumer->contact_number)) {
            return [
                'success' => false,
                'status' => 'failed',
                'message' => 'Consumer does not have a registered contact number.',
            ];
        }

        $barangay = Setting::get('barangay_name', 'Barangay Caruhayon');
        $contact = Setting::get('system_contact', '0917-123-CARU (2278)');
        $dueDate = Carbon::parse($bill->due_date)->format('M d, Y');
        $balance = number_format($bill->balance, 2);

        $message = "AQUATANK OVERDUE ALERT: Notice for {$consumer->full_name} (Acct: {$consumer->account_number}). Bill {$bill->bill_number} of PHP {$balance} is PAST DUE since {$dueDate}. Please settle immediately at {$barangay} Hall to prevent service disconnection. Info: {$contact}";

        return $this->dispatchSms(
            $consumer->contact_number,
            $message,
            'overdue_alert',
            $consumer->id,
            $bill->id
        );
    }

    // send payment confirmation receipt
    public function sendPaymentConfirmation(Payment $payment): array
    {
        $payment->loadMissing(['bill.consumer']);
        $consumer = $payment->bill?->consumer;

        if (!$consumer || empty($consumer->contact_number)) {
            return [
                'success' => false,
                'status' => 'failed',
                'message' => 'Consumer does not have a registered contact number.',
            ];
        }

        $amount = number_format($payment->amount_paid, 2);
        $date = Carbon::parse($payment->payment_date)->format('M d, Y');
        $message = "AQUATANK PAYMENT ACKNOWLEDGMENT: Received PHP {$amount} for Acct: {$consumer->account_number} under OR #{$payment->reference_number} on {$date}. Thank you for your payment!";

        return $this->dispatchSms(
            $consumer->contact_number,
            $message,
            'payment_ack',
            $consumer->id,
            $payment->bill_id
        );
    }

    // send custom sms
    public function sendCustom(string $phone, string $message, ?int $consumerId = null, ?int $billId = null): array
    {
        return $this->dispatchSms($phone, $message, 'custom', $consumerId, $billId);
    }

    // core dispatch logic with gateway and simulation fallback
    protected function dispatchSms(string $phone, string $message, string $messageType, ?int $consumerId = null, ?int $billId = null): array
    {
        $normalizedPhone = $this->normalizePhoneNumber($phone);
        $provider = config('services.sms.provider') ?: Setting::get('sms_gateway_provider', 'semaphore');
        $envSim = config('services.sms.simulation_mode');
        $simulationMode = ($envSim !== null) ? filter_var($envSim, FILTER_VALIDATE_BOOLEAN) : (Setting::get('sms_simulation_mode', '1') === '1');
        $apiKey = config('services.sms.api_key') ?: Setting::get('sms_api_key', '');
        $senderName = config('services.sms.sender_name') ?: Setting::get('sms_sender_name', 'AQUATANK');

        // sandbox simulation mode
        if ($simulationMode || empty($apiKey)) {
            $log = SmsLog::create([
                'consumer_id' => $consumerId,
                'bill_id' => $billId,
                'recipient_phone' => $normalizedPhone,
                'message_type' => $messageType,
                'message' => $message,
                'gateway_provider' => $provider . ' (Sandbox/Simulated)',
                'status' => 'simulated',
                'message_id' => 'SIM-' . strtoupper(bin2hex(random_bytes(4))),
                'response_payload' => json_encode([
                    'mode' => 'simulated',
                    'note' => 'Dispatched in sandbox mode. Ready for production API key.',
                    'recipient' => $normalizedPhone,
                    'char_count' => strlen($message),
                ]),
                'sent_at' => now(),
            ]);

            return [
                'success' => true,
                'status' => 'simulated',
                'message' => "SMS dispatched successfully in simulation mode to {$normalizedPhone}.",
                'log' => $log,
            ];
        }

        // live third-party gateway dispatch
        try {
            if ($provider === 'philsms') {
                // philsms api v3
                $response = Http::withToken($apiKey)
                    ->timeout(10)
                    ->post('https://app.philsms.com/api/v3/sms/send', [
                        'recipient' => $normalizedPhone,
                        'sender_id' => $senderName,
                        'type' => 'plain',
                        'message' => $message,
                    ]);

                $data = $response->json();
                $isOk = $response->successful() && ($data['status'] ?? '') === 'success';
                $messageId = $data['data']['uid'] ?? null;
            } elseif ($provider === 'semaphore') {
                // semaphore sms api v4
                $response = Http::timeout(10)->post('https://api.semaphore.co/api/v4/messages', [
                    'apikey' => $apiKey,
                    'number' => $normalizedPhone,
                    'message' => $message,
                    'sendername' => $senderName,
                ]);

                $data = $response->json();
                $isOk = $response->successful() && !empty($data);
                $messageId = is_array($data) && isset($data[0]['message_id']) ? (string)$data[0]['message_id'] : null;
            } else {
                // generic webhook
                $endpoint = Setting::get('sms_custom_endpoint', 'https://httpbin.org/post');
                $response = Http::timeout(10)->post($endpoint, [
                    'to' => $normalizedPhone,
                    'message' => $message,
                    'sender' => $senderName,
                    'api_key' => $apiKey,
                ]);

                $data = $response->json();
                $isOk = $response->successful();
                $messageId = 'GEN-' . uniqid();
            }

            $log = SmsLog::create([
                'consumer_id' => $consumerId,
                'bill_id' => $billId,
                'recipient_phone' => $normalizedPhone,
                'message_type' => $messageType,
                'message' => $message,
                'gateway_provider' => $provider,
                'status' => $isOk ? 'sent' : 'failed',
                'message_id' => $messageId,
                'response_payload' => is_string($response->body()) ? $response->body() : json_encode($data),
                'error_message' => $isOk ? null : ($response->body() ?: 'Gateway response error'),
                'sent_at' => now(),
            ]);

            return [
                'success' => $isOk,
                'status' => $isOk ? 'sent' : 'failed',
                'message' => $isOk ? "SMS successfully delivered via {$provider}." : "SMS failed via {$provider}.",
                'log' => $log,
            ];
        } catch (Exception $e) {
            Log::error("SMS Gateway Exception ({$provider}): " . $e->getMessage());

            $log = SmsLog::create([
                'consumer_id' => $consumerId,
                'bill_id' => $billId,
                'recipient_phone' => $normalizedPhone,
                'message_type' => $messageType,
                'message' => $message,
                'gateway_provider' => $provider,
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'sent_at' => now(),
            ]);

            return [
                'success' => false,
                'status' => 'failed',
                'message' => 'Failed to connect to SMS Gateway: ' . $e->getMessage(),
                'log' => $log,
            ];
        }
    }

    // normalize philippine phone number
    public function normalizePhoneNumber(string $phone): string
    {
        $clean = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($clean, '639') && strlen($clean) === 12) {
            return '0' . substr($clean, 2);
        }

        if (str_starts_with($clean, '9') && strlen($clean) === 10) {
            return '0' . $clean;
        }

        return $clean;
    }
}
