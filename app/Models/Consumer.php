<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Consumer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'account_number',
        'first_name',
        'middle_name',
        'last_name',
        'address',
        'contact_number',
        'purok',
        'meter_number',
        'connection_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'connection_date' => 'date',
            'deleted_at' => 'datetime',
        ];
    }

    public function bills(): HasMany
    {
        return $this->hasMany(Bill::class);
    }

    public function meterReadings(): HasMany
    {
        return $this->hasMany(MeterReading::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function smsLogs(): HasMany
    {
        return $this->hasMany(SmsLog::class);
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->middle_name} {$this->last_name}");
    }

    public function getLatestBillAttribute(): ?Bill
    {
        return $this->bills()->latest('billing_date')->latest('id')->first();
    }

    public function getLatestMeterReadingAttribute(): ?MeterReading
    {
        return $this->meterReadings()->latest('reading_date')->latest('id')->first();
    }

    public function getLatestReadingRecord(): ?object
    {
        $latestReading = $this->meterReadings()->latest('reading_date')->latest('id')->first();
        if ($latestReading) {
            return $latestReading;
        }

        return $this->bills()->latest('billing_date')->latest('id')->first();
    }

    public function hasPreviousReading(): bool
    {
        return $this->getLatestReadingRecord() !== null;
    }

    public function getLatestReadingAttribute(): float
    {
        $latest = $this->getLatestReadingRecord();
        return $latest ? (float) $latest->current_reading : 0.0;
    }

    /**
     * Check if the consumer has associated historical billing, payment, or meter reading records.
     */
    public function hasFinancialHistory(): bool
    {
        return $this->bills()->exists() || $this->payments()->exists() || $this->meterReadings()->exists();
    }

    /**
     * Total water volume consumed across all billing records (in cubic meters).
     */
    public function getTotalConsumptionAttribute(): float
    {
        return (float) $this->bills()->sum('consumption');
    }

    /**
     * Total outstanding unpaid balance across all bills.
     */
    public function getOutstandingBalanceAttribute(): float
    {
        return (float) $this->bills()->whereIn('status', ['unpaid', 'overdue'])->sum('amount_due')
            - (float) $this->payments()->sum('amount_paid');
    }
}
