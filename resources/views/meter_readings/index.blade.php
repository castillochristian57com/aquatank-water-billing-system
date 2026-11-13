@extends('adminlte::page')

@section('title', 'Water Meter Reading History')

@push('css')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Montserrat:wght@500;600;700;800&display=swap" rel="stylesheet">
<style>
:root {
    --font-heading: 'Montserrat', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    --font-body: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    --aquatank-blue: #4a6cf7;
    --aquatank-blue-dark: #2563eb;
    --aquatank-border-blue: #bfdbfe;
    --gradient-primary: linear-gradient(135deg, #4a6cf7 0%, #2563eb 100%);
}

body, .app-wrapper, .content-wrapper, table, .table, .btn, .badge {
    font-family: var(--font-body) !important;
}

h1, h2, h3, h4, h5, h6, .card-title {
    font-family: var(--font-heading) !important;
}

.table thead th {
    background: #eef6ff !important;
    color: #1e3a8a !important;
    font-size: 0.78rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    border-bottom: 2px solid var(--aquatank-border-blue) !important;
    padding: 0.85rem 1rem !important;
}

.table tbody td {
    padding: 0.85rem 1rem !important;
    border-bottom: 1px solid #e2e8f0 !important;
    vertical-align: middle;
}
</style>
@endpush

@section('content_header')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2 mb-2">
        <div>
            <h1 class="m-0 text-dark fw-bold" style="font-size: 1.75rem;">
                <i class="bi bi-clock-history text-primary me-2"></i>Meter Reading History
            </h1>
            <p class="text-muted small mb-0">Audit log of all water meter readings recorded across Barangay Caruhayon</p>
        </div>
        <div>
            <a href="{{ route('meter-readings.create') }}" class="btn btn-primary btn-sm px-3 py-2 shadow-sm rounded-3 d-flex align-items-center gap-1" style="background: var(--gradient-primary); border: none;">
                <i class="bi bi-speedometer2"></i> Record New Reading
            </a>
        </div>
    </div>
@stop

@section('content')
<div class="container-fluid px-0">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 mb-3" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Filters Card --}}
    <div class="card border-0 shadow-sm rounded-4 mb-3">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('meter-readings.index') }}" class="row g-2 align-items-center">
                <div class="col-12 col-md-6">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                        <input type="text"
                               name="search"
                               class="form-control"
                               placeholder="Search consumer, account #, meter #, or period..."
                               value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">All Statuses</option>
                        <option value="recorded" {{ request('status') === 'recorded' ? 'selected' : '' }}>Recorded (Unbilled)</option>
                        <option value="billed" {{ request('status') === 'billed' ? 'selected' : '' }}>Billed</option>
                    </select>
                </div>

                <div class="col-6 col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary flex-grow-1">Filter</button>
                    @if(request('search') || request('status'))
                        <a href="{{ route('meter-readings.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Readings Table --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                <thead class="table-light">
                    <tr class="small text-muted text-uppercase">
                        <th>Reading Date</th>
                        <th>Consumer</th>
                        <th>Billing Period</th>
                        <th>Prev &rarr; Curr Reading</th>
                        <th>Consumption</th>
                        <th>Est. Amount</th>
                        <th>Reader / Staff</th>
                        <th class="text-center">Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($readings as $reading)
                        <tr>
                            <td>
                                <div class="fw-bold text-dark">{{ $reading->reading_date->format('M d, Y') }}</div>
                                <small class="text-muted font-monospace">#R-{{ str_pad($reading->id, 5, '0', STR_PAD_LEFT) }}</small>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $reading->consumer->full_name }}</div>
                                <div class="text-muted small">
                                    {{ $reading->consumer->account_number }} &bull; Meter: {{ $reading->consumer->meter_number }}
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $reading->billing_period }}</span>
                            </td>
                            <td>
                                <span class="font-monospace text-muted">{{ number_format($reading->previous_reading, 2) }}</span>
                                <span class="text-primary fw-bold mx-1">&rarr;</span>
                                <span class="font-monospace fw-bold text-dark">{{ number_format($reading->current_reading, 2) }} m³</span>
                            </td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 fw-bold fs-6">
                                    {{ number_format($reading->consumption, 2) }} m³
                                </span>
                            </td>
                            <td class="fw-bold text-success">
                                ₱{{ number_format($reading->consumption * $tariffRate, 2) }}
                            </td>
                            <td>
                                <div class="small fw-semibold text-dark">{{ $reading->reader?->name ?? 'System Reader' }}</div>
                                <div class="text-muted" style="font-size: 0.75rem;">{{ $reading->created_at->diffForHumans() }}</div>
                            </td>
                            <td class="text-center">
                                @if($reading->status === 'billed')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1">
                                        <i class="bi bi-check-circle me-1"></i> Billed
                                    </span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2.5 py-1">
                                        <i class="bi bi-hourglass-split me-1"></i> Recorded (Unbilled)
                                    </span>
                                @endif
                            </td>
                            <td class="text-end">
                                @if($reading->bill)
                                    <a href="{{ route('bills.statement', $reading->bill) }}" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill px-2.5 py-1" title="View Official Statement">
                                        <i class="bi bi-file-earmark-text me-1"></i> Statement
                                    </a>
                                @else
                                    <a href="{{ route('bills.create', ['consumer_id' => $reading->consumer_id]) }}" class="btn btn-primary btn-sm rounded-pill px-2.5 py-1" title="Generate bill from this reading">
                                        <i class="bi bi-receipt me-1"></i> Generate Bill
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="bi bi-speedometer2 display-5 d-block text-secondary mb-2 opacity-50"></i>
                                <h6>No meter readings recorded yet.</h6>
                                <p class="small mb-3">Start by recording the first water meter reading using your smartphone or desktop.</p>
                                <a href="{{ route('meter-readings.create') }}" class="btn btn-primary btn-sm px-3 rounded-pill">
                                    <i class="bi bi-plus-circle me-1"></i> Record First Reading
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($readings->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $readings->links() }}
            </div>
        @endif
    </div>

</div>
@stop
