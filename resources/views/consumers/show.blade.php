@extends('adminlte::page')

@section('title', 'Consumer Profile - ' . $consumer->full_name)

@section('content_header')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
        <div>
            <h1 class="m-0 text-dark fw-bold" style="font-size: 1.75rem;">
                <i class="bi bi-person-badge-fill text-primary me-2"></i>{{ $consumer->full_name }}
            </h1>
            <p class="text-muted small mb-0">Account No: <strong class="font-monospace text-primary">{{ $consumer->account_number }}</strong> &bull; Meter No: <strong class="font-monospace">{{ $consumer->meter_number }}</strong></p>
        </div>
        <div class="mt-2 mt-md-0 d-flex flex-wrap gap-2">
            <a href="{{ route('meter-readings.create', ['consumer_id' => $consumer->id]) }}" class="btn btn-primary btn-sm px-3 shadow-sm" style="background: linear-gradient(135deg, #0284c7 0%, #155eef 100%); border: none;">
                <i class="bi bi-speedometer2 me-1"></i> Record Meter Reading
            </a>
            <a href="{{ route('bills.create', ['consumer_id' => $consumer->id]) }}" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-receipt me-1"></i> New Bill
            </a>
            <a href="{{ route('consumers.edit', $consumer) }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-pencil me-1"></i> Edit
            </a>
            <form method="POST" action="{{ route('consumers.destroy', $consumer) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete or archive this consumer ({{ $consumer->account_number }})?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger btn-sm" title="Delete or Archive Consumer">
                    <i class="bi bi-trash me-1"></i> Delete
                </button>
            </form>
            <a href="{{ route('consumers.index') }}" class="btn btn-light btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>
@stop

@section('content')
<div class="container-fluid px-0">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-3" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-3" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-bottom p-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold text-dark"><i class="bi bi-info-circle text-primary me-1"></i> Consumer & Service Details</h6>
                    <span class="badge {{ $consumer->status === 'active' ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }} text-uppercase" style="font-size: 0.72rem;">
                        {{ $consumer->status }}
                    </span>
                </div>
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted small">Account Number</span>
                        <span class="fw-bold font-monospace text-primary">{{ $consumer->account_number }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted small">Meter Serial Number</span>
                        <span class="fw-bold font-monospace text-dark">{{ $consumer->meter_number }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted small">Current Meter Reading</span>
                        <span class="fw-bold text-info">{{ number_format($consumer->latest_reading, 2) }} m³</span>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted small">Total Volume Consumed</span>
                        <span class="fw-bold text-primary">{{ number_format($consumer->total_consumption, 2) }} m³</span>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted small">Outstanding Balance</span>
                        <span class="fw-bold {{ $consumer->outstanding_balance > 0 ? 'text-danger' : 'text-success' }}">
                            ₱{{ number_format(max(0, $consumer->outstanding_balance), 2) }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted small">Mobile Contact</span>
                        <span class="fw-semibold">{{ $consumer->contact_number }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted small">Service Address</span>
                        <span class="fw-semibold text-end" style="max-width: 60%;">{{ $consumer->address }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted small">Purok / Zone</span>
                        <span class="fw-semibold">{{ $consumer->purok ?? 'N/A' }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted small">Connection Date</span>
                        <span class="fw-semibold">{{ $consumer->connection_date ? $consumer->connection_date->format('M d, Y') : 'N/A' }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-2">
                        <span class="text-muted small">Date Registered</span>
                        <span class="text-muted small">{{ $consumer->created_at->format('M d, Y') }}</span>
                    </div>

                    <div class="mt-3 pt-3 border-top d-flex gap-2">
                        <form method="POST" action="{{ route('consumers.toggle-status', $consumer) }}" class="w-100" onsubmit="return confirm('Toggle status for {{ $consumer->full_name }}?')">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-sm btn-outline-warning w-100">
                                <i class="bi bi-arrow-repeat me-1"></i> Switch to {{ $consumer->status === 'active' ? 'Inactive' : 'Active' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-bottom p-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold text-dark"><i class="bi bi-receipt text-primary me-1"></i> Billing & Meter Reading Ledger</h6>
                    <span class="text-muted small">{{ $consumer->bills->count() }} Statements</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                            <thead class="table-light">
                                <tr>
                                    <th>Bill #</th>
                                    <th>Period</th>
                                    <th>Prev &rarr; Curr</th>
                                    <th>Consumption</th>
                                    <th>Amount Due</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($consumer->bills as $bill)
                                    <tr>
                                        <td class="font-monospace fw-bold">{{ $bill->bill_number }}</td>
                                        <td>{{ $bill->billing_period }}</td>
                                        <td>{{ number_format($bill->previous_reading, 1) }} &rarr; {{ number_format($bill->current_reading, 1) }} m³</td>
                                        <td class="fw-bold text-primary">{{ number_format($bill->consumption, 1) }} m³</td>
                                        <td class="fw-bold">₱{{ number_format($bill->amount_due, 2) }}</td>
                                        <td>
                                            <span class="badge {{ $bill->status === 'paid' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} text-uppercase" style="font-size: 0.72rem;">
                                                {{ $bill->status }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('bills.statement', $bill) }}" target="_blank" class="btn btn-light" title="Print Statement">
                                                    <i class="bi bi-printer text-primary"></i>
                                                </a>
                                                @if($bill->status !== 'paid')
                                                    <a href="{{ route('payments.create', ['bill_id' => $bill->id]) }}" class="btn btn-light text-success" title="Record Payment">
                                                        <i class="bi bi-cash"></i>
                                                    </a>
                                                    <form action="{{ route('sms.send-bill-reminder', $bill) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-light text-primary" title="Dispatch SMS Reminder" onclick="return confirm('Send SMS billing reminder to {{ $consumer->full_name }}?')">
                                                            <i class="bi bi-chat-dots"></i>
                                                        </button>
                                                    </form>
                                                    @if($bill->status === 'overdue' || $bill->due_date < now())
                                                        <form action="{{ route('sms.send-overdue-alert', $bill) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-light text-danger" title="Dispatch SMS Overdue Alert" onclick="return confirm('Send urgent SMS overdue alert to {{ $consumer->full_name }}?')">
                                                                <i class="bi bi-exclamation-triangle"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">
                                            No bills generated yet for this consumer.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-bottom p-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 fw-bold text-dark">
                <i class="bi bi-chat-left-text-fill text-primary me-2"></i>SMS Notification & Alert History
            </h6>
            <span class="badge bg-light text-dark border">{{ $consumer->smsLogs->count() }} messages logged</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                    <thead class="table-light">
                        <tr class="small text-muted text-uppercase">
                            <th>Date & Time</th>
                            <th>Recipient Phone</th>
                            <th>Message Type</th>
                            <th>Message Content</th>
                            <th>Gateway / Mode</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($consumer->smsLogs as $sms)
                            <tr>
                                <td class="text-nowrap">{{ $sms->sent_at ? $sms->sent_at->format('M d, Y h:i A') : $sms->created_at->format('M d, Y h:i A') }}</td>
                                <td class="font-monospace fw-semibold">{{ $sms->recipient_phone }}</td>
                                <td>
                                    <span class="badge bg-light text-dark border text-uppercase" style="font-size: 0.7rem;">
                                        {{ str_replace('_', ' ', $sms->message_type) }}
                                    </span>
                                </td>
                                <td style="max-width: 320px;">
                                    <span class="text-truncate d-block" title="{{ $sms->message }}">{{ $sms->message }}</span>
                                </td>
                                <td><small class="text-muted">{{ $sms->gateway_provider }}</small></td>
                                <td class="text-center">
                                    <span class="badge {{ $sms->status === 'sent' ? 'bg-success-subtle text-success' : ($sms->status === 'simulated' ? 'bg-info-subtle text-info' : 'bg-danger-subtle text-danger') }} text-uppercase" style="font-size: 0.72rem;">
                                        {{ $sms->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <i class="bi bi-chat-square-dots fs-4 d-block mb-1 text-secondary"></i>
                                    No SMS notifications sent to this consumer yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@stop
