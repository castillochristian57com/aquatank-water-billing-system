@extends('adminlte::page')

@section('title', 'Consumer Management')

@section('content_header')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
        <div>
            <h1 class="m-0 text-dark fw-bold" style="font-size: 1.75rem;">
                <i class="bi bi-people-fill text-primary me-2"></i>Consumer Management
            </h1>
            <p class="text-muted small mb-0">Manage registered consumers, service connections, and meter records</p>
        </div>
        <div class="mt-2 mt-md-0">
            <a href="{{ route('consumers.create') }}" class="btn btn-primary btn-sm px-3 shadow-sm" style="background: linear-gradient(135deg, #0284c7 0%, #155eef 100%); border: none;">
                <i class="bi bi-person-plus-fill me-1"></i> Register New Consumer
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

    <div class="card border-0 shadow-sm rounded-4 mb-3">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('consumers.index') }}" class="row g-2 align-items-center">
                <div class="col-12 col-md-5 position-relative">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" id="consumerPageSearch" class="form-control" placeholder="Search by name, account #, meter #, address, purok..." value="{{ request('search') }}" autocomplete="off">
                    </div>
                    <div id="consumerPageDropdown" class="dropdown-menu w-100 shadow-lg border-0 rounded-3 mt-1 p-2" style="display: none; position: absolute; z-index: 1050; max-height: 380px; overflow-y: auto;">
                        <div id="consumerPageResults"></div>
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <select name="purok" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">All Puroks</option>
                        @foreach($purokList ?? [] as $p)
                            <option value="{{ $p }}" {{ request('purok') === $p ? 'selected' : '' }}>{{ $p }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">All Statuses</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active Only</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive Only</option>
                        <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived (Deleted)</option>
                    </select>
                </div>
                <div class="col-12 col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary flex-grow-1">Filter</button>
                    @if(request('search') || request('status') || request('purok'))
                        <a href="{{ route('consumers.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
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
                        <th>Account #</th>
                        <th>Consumer Name</th>
                        <th>Meter Serial</th>
                        <th>Service Address / Purok</th>
                        <th>Contact #</th>
                        <th>Connected</th>
                        <th class="text-center">Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($consumers as $consumer)
                        <tr class="{{ $consumer->trashed() ? 'opacity-75 bg-light' : '' }}">
                            <td class="font-monospace fw-bold text-primary">
                                @if(!$consumer->trashed())
                                    <a href="{{ route('consumers.show', $consumer) }}" class="text-decoration-none">
                                        {{ $consumer->account_number }}
                                    </a>
                                @else
                                    <span class="text-secondary text-decoration-line-through">{{ $consumer->account_number }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $consumer->full_name }}</div>
                                <small class="text-muted">{{ $consumer->bills_count }} bill(s) recorded</small>
                            </td>
                            <td><span class="badge bg-light text-dark border font-monospace">{{ $consumer->meter_number }}</span></td>
                            <td>
                                <div>{{ $consumer->address }}</div>
                                @if($consumer->purok)
                                    <span class="badge bg-light text-secondary border" style="font-size: 0.72rem;">{{ $consumer->purok }}</span>
                                @endif
                            </td>
                            <td>{{ $consumer->contact_number }}</td>
                            <td>{{ $consumer->connection_date ? $consumer->connection_date->format('M d, Y') : 'N/A' }}</td>
                            <td class="text-center">
                                @if($consumer->trashed())
                                    <span class="badge bg-dark-subtle text-dark border text-uppercase" style="font-size: 0.75rem;">
                                        ARCHIVED
                                    </span>
                                @else
                                    <span class="badge {{ $consumer->status === 'active' ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }} text-uppercase" style="font-size: 0.75rem;">
                                        {{ $consumer->status }}
                                    </span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    @if(!$consumer->trashed())
                                        <a href="{{ route('meter-readings.create', ['consumer_id' => $consumer->id]) }}" class="btn btn-light" title="Record Meter Reading">
                                            <i class="bi bi-speedometer2 text-info"></i>
                                        </a>
                                        <a href="{{ route('consumers.show', $consumer) }}" class="btn btn-light" title="View Details">
                                            <i class="bi bi-eye text-primary"></i>
                                        </a>
                                        <a href="{{ route('consumers.edit', $consumer) }}" class="btn btn-light" title="Edit Consumer">
                                            <i class="bi bi-pencil text-secondary"></i>
                                        </a>
                                        <form method="POST" action="{{ route('consumers.toggle-status', $consumer) }}" class="d-inline" onsubmit="return confirm('Toggle active/inactive status for {{ $consumer->full_name }}?')">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-light" title="Toggle Connection Status">
                                                <i class="bi bi-arrow-repeat text-warning"></i>
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('consumers.destroy', $consumer) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete/archive consumer {{ $consumer->full_name }} ({{ $consumer->account_number }})?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-light" title="Delete or Archive Consumer">
                                                <i class="bi bi-trash text-danger"></i>
                                            </button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('consumers.restore', $consumer->id) }}" class="d-inline" onsubmit="return confirm('Restore archived consumer {{ $consumer->full_name }}?')">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success" title="Restore Consumer">
                                                <i class="bi bi-arrow-counterclockwise me-1"></i> Restore
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <i class="bi bi-person-x fs-3 d-block mb-2 text-secondary"></i>
                                No consumers found matching your filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($consumers->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $consumers->links() }}
            </div>
        @endif
    </div>

</div>
@stop

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof window.setupConsumerAutocomplete === 'function') {
            window.setupConsumerAutocomplete('consumerPageSearch', 'consumerPageDropdown', 'consumerPageResults');
        }
    });
</script>
@endpush
