<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sms_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consumer_id')->nullable()->constrained('consumers')->nullOnDelete();
            $table->foreignId('bill_id')->nullable()->constrained('bills')->nullOnDelete();
            $table->string('recipient_phone', 30);
            $table->string('message_type', 50)->default('reminder'); // reminder, overdue_alert, payment_ack, custom
            $table->text('message');
            $table->string('gateway_provider', 50)->default('semaphore'); // semaphore, philsms, generic, simulation
            $table->string('status', 20)->default('sent'); // sent, simulated, failed
            $table->string('message_id')->nullable();
            $table->text('response_payload')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_logs');
    }
};
