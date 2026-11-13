<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('meter_readings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consumer_id')->constrained('consumers')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('previous_reading', 10, 2);
            $table->decimal('current_reading', 10, 2);
            $table->decimal('consumption', 10, 2);
            $table->date('reading_date');
            $table->string('billing_period', 50);
            $table->string('status', 20)->default('recorded'); // 'recorded', 'billed', 'cancelled'
            $table->foreignId('bill_id')->nullable()->constrained('bills')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['consumer_id', 'reading_date']);
            $table->index('billing_period');
        });

        // Seed initial meter readings from existing bills if available
        if (Schema::hasTable('bills')) {
            $bills = DB::table('bills')->orderBy('id')->get();
            $defaultUserId = DB::table('users')->value('id');

            foreach ($bills as $bill) {
                DB::table('meter_readings')->insert([
                    'consumer_id' => $bill->consumer_id,
                    'user_id' => $defaultUserId,
                    'previous_reading' => $bill->previous_reading,
                    'current_reading' => $bill->current_reading,
                    'consumption' => $bill->consumption,
                    'reading_date' => $bill->billing_date,
                    'billing_period' => $bill->billing_period,
                    'status' => 'billed',
                    'bill_id' => $bill->id,
                    'notes' => 'Imported from existing bill record #' . $bill->bill_number,
                    'created_at' => $bill->created_at ?? now(),
                    'updated_at' => $bill->updated_at ?? now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meter_readings');
    }
};
