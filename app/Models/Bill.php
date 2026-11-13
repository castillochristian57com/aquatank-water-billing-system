<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Bill extends Model
{
    use HasFactory;

    protected $fillable = [
        'bill_number',
        'consumer_id',
        'previous_reading',
        'current_reading',
        'consumption',
        'billing_period',
        'billing_date',
        'due_date',
        'rate_per_cubic_meter',
        'amount_due',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'previous_reading' => 'decimal:2',
            'current_reading' => 'decimal:2',
            'consumption' => 'decimal:2',
            'billing_date' => 'date',
            'due_date' => 'date',
            'rate_per_cubic_meter' => 'decimal:2',
            'amount_due' => 'decimal:2',
        ];
    }

    public function consumer(): BelongsTo
    {
        return $this->belongsTo(Consumer::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function meterReading(): HasOne
    {
        return $this->hasOne(MeterReading::class);
    }

    public function getTotalPaidAttribute(): float
    {
        return (float) $this->payments()->sum('amount_paid');
    }

    public function getBalanceAttribute(): float
    {
        return max(0, (float) $this->amount_due - $this->total_paid);
    }
}
