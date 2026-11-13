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
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->string('bill_number')->unique();
            $table->foreignId('consumer_id')->constrained('consumers')->cascadeOnDelete();
            $table->decimal('previous_reading', 10, 2);
            $table->decimal('current_reading', 10, 2);
            $table->decimal('consumption', 10, 2);
            $table->string('billing_period');
            $table->date('billing_date');
            $table->date('due_date');
            $table->decimal('rate_per_cubic_meter', 10, 2);
            $table->decimal('amount_due', 10, 2);
            $table->string('status')->default('unpaid'); // 'unpaid', 'paid', 'overdue'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};
