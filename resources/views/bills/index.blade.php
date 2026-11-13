@extends('adminlte::page')

@section('title', 'Water Bills & Meter Readings')

@section('content_header')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
        <div>
            <h1 class="m-0 text-dark fw-bold" style="font-size: 1.75rem;">
                <i class="bi bi-receipt-cutoff text-primary me-2"></i>Water Bills & Meter Readings
            </h1>
            <p class="text-muted small mb-0">Record monthly readings, view generated statements, and track balances</p>
        </div>
        <div class="mt-2 mt-md-0">
            <a href="{{ route('bills.create') }}" class="btn btn-primary btn-sm px-3 shadow-sm" style="background: linear-gradient(135deg, #0284c7 0%, #155eef 100%); border: none;">
                <i class="bi bi-speedometer2 me-1"></i> Encode New Reading
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

    <div class="card border-0 shadow-sm rounded-4 mb-3">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('bills.index') }}" class="row g-2 align-items-center">
                <div class="col-12 col-md-6 position-relative">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" id="billPageSearch" class="form-control" placeholder="Search by bill #, consumer name, account #, or period..." value="{{ request('search') }}" autocomplete="off">
                    </div>
                    <div id="billPageDropdown" class="dropdown-menu w-100 shadow-lg border-0 rounded-3 mt-1 p-2" style="display: none; position: absolute; z-index: 1050; max-height: 380px; overflow-y: auto;">
                        <div id="billPageResults"></div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">All Statuses</option>
                        <option value="unpaid" {{ request('status') === 'unpaid' ? 'selected' : '' }}>Unpaid Only</option>
                        <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid Only</option>
                        <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Overdue Only</option>
                    </select>
                </div>
                <div class="col-6 col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary flex-grow-1">Filter</button>
                    @if(request('search') || request('status'))
                        <a href="{{ route('bills.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                <thead class="table-light">
                    <tr class="small text-muted text-uppercase">
                        <th>Bill #</th>
                        <th>Consumer</th>
                        <th>Billing Period</th>
                        <th>Prev &rarr; Curr Reading</th>
                        <th>Consumption</th>
                        <th>Rate</th>
                        <th>Amount Due</th>
                        <th>Due Date</th>
                        <th class="text-center">Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bills as $bill)
                        <tr>
                            <td class="font-monospace fw-bold text-primary">
                                <a href="{{ route('bills.statement', $bill) }}" target="_blank" class="text-decoration-none">
                                    {{ $bill->bill_number }}
                                </a>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $bill->consumer->full_name }}</div>
                                <small class="text-muted">{{ $bill->consumer->account_number }}</small>
                            </td>
                            <td>{{ $bill->billing_period }}</td>
                            <td>{{ number_format($bill->previous_reading, 1) }} &rarr; {{ number_format($bill->current_reading, 1) }} m³</td>
                            <td class="fw-bold text-primary">{{ number_format($bill->consumption, 1) }} m³</td>
                            <td>₱{{ number_format($bill->rate_per_cubic_meter, 2) }}</td>
                            <td class="fw-bold">₱{{ number_format($bill->amount_due, 2) }}</td>
                            <td>
                                <span class="{{ $bill->status === 'unpaid' && $bill->due_date < now() ? 'text-danger fw-bold' : '' }}">
                                    {{ $bill->due_date->format('M d, Y') }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $bill->status === 'paid' ? 'bg-success-subtle text-success' : ($bill->status === 'overdue' ? 'bg-danger-subtle text-danger' : 'bg-warning-subtle text-warning') }} text-uppercase" style="font-size: 0.75rem;">
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
                                            <button type="submit" class="btn btn-light text-primary" title="Dispatch SMS Reminder" onclick="return confirm('Send SMS billing reminder to {{ $bill->consumer->full_name }}?')">
                                                <i class="bi bi-chat-dots"></i>
                                            </button>
                                        </form>
                                        @if($bill->status === 'overdue' || $bill->due_date < now())
                                            <form action="{{ route('sms.send-overdue-alert', $bill) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-light text-danger" title="Dispatch SMS Overdue Alert" onclick="return confirm('Send urgent SMS overdue alert to {{ $bill->consumer->full_name }}?')">
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
                            <td colspan="10" class="text-center py-4 text-muted">
                                <i class="bi bi-receipt fs-3 d-block mb-2 text-secondary"></i>
                                No billing records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($bills->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $bills->links() }}
            </div>
        @endif
    </div>

</div>
@stop

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof window.setupConsumerAutocomplete === 'function') {
            window.setupConsumerAutocomplete('billPageSearch', 'billPageDropdown', 'billPageResults');
        }
    });
</script>
@endpush
