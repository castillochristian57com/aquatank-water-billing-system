@extends('adminlte::page')

@section('title', 'Edit Consumer')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="m-0 text-dark fw-bold" style="font-size: 1.75rem;">
                <i class="bi bi-pencil-square text-primary me-2"></i>Edit Consumer
            </h1>
            <p class="text-muted small mb-0">Update information for <strong>{{ $consumer->full_name }}</strong> ({{ $consumer->account_number }})</p>
        </div>
        <a href="{{ route('consumers.show', $consumer) }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to Consumer Profile
        </a>
    </div>
@stop

@section('content')
<div class="container-fluid px-0">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-9">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-bottom p-3">
                    <h6 class="m-0 fw-bold text-dark">Update Consumer Record</h6>
                </div>
                <div class="card-body p-4">

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                            <strong class="d-block mb-1"><i class="bi bi-exclamation-triangle-fill me-1"></i> Please correct the errors below:</strong>
                            <ul class="mb-0 ps-3 small">
                                @foreach($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('consumers.update', $consumer) }}">
                        @csrf
                        @method('PUT')

                        <div class="row g-3 mb-4">
                            <div class="col-12">
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1.5 text-uppercase fw-bold" style="font-size: 0.72rem;">
                                    1. Identification & Meter
                                </span>
                            </div>

                            <div class="col-md-6">
                                <label for="account_number" class="form-label small fw-bold text-secondary">
                                    Account Number <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="account_number" id="account_number"
                                       class="form-control font-monospace @error('account_number') is-invalid @enderror"
                                       value="{{ old('account_number', $consumer->account_number) }}" required>
                                @error('account_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="meter_number" class="form-label small fw-bold text-secondary">
                                    Meter Serial Number <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="meter_number" id="meter_number"
                                       class="form-control font-monospace @error('meter_number') is-invalid @enderror"
                                       value="{{ old('meter_number', $consumer->meter_number) }}" required>
                                @error('meter_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-12">
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1.5 text-uppercase fw-bold" style="font-size: 0.72rem;">
                                    2. Consumer Information
                                </span>
                            </div>

                            <div class="col-md-4">
                                <label for="first_name" class="form-label small fw-bold text-secondary">
                                    First Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="first_name" id="first_name"
                                       class="form-control @error('first_name') is-invalid @enderror"
                                       value="{{ old('first_name', $consumer->first_name) }}" required>
                                @error('first_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="middle_name" class="form-label small fw-bold text-secondary">
                                    Middle Name
                                </label>
                                <input type="text" name="middle_name" id="middle_name"
                                       class="form-control @error('middle_name') is-invalid @enderror"
                                       value="{{ old('middle_name', $consumer->middle_name) }}">
                                @error('middle_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="last_name" class="form-label small fw-bold text-secondary">
                                    Last Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="last_name" id="last_name"
                                       class="form-control @error('last_name') is-invalid @enderror"
                                       value="{{ old('last_name', $consumer->last_name) }}" required>
                                @error('last_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-8">
                                <label for="address" class="form-label small fw-bold text-secondary">
                                    Complete Service Address <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="address" id="address"
                                       class="form-control @error('address') is-invalid @enderror"
                                       value="{{ old('address', $consumer->address) }}" required>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="purok" class="form-label small fw-bold text-secondary">Purok</label>
                                <select name="purok" id="purok" class="form-select @error('purok') is-invalid @enderror">
                                    <option value="">-- Select Purok --</option>
                                    @foreach($purokList ?? ['Purok 1', 'Purok 2', 'Purok 3', 'Purok 4', 'Purok 5', 'Purok 6', 'Purok 7'] as $purokOpt)
                                        <option value="{{ $purokOpt }}" {{ old('purok', $consumer->purok) == $purokOpt ? 'selected' : '' }}>{{ $purokOpt }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="contact_number" class="form-label small fw-bold text-secondary">
                                    Mobile Contact Number <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="contact_number" id="contact_number"
                                       class="form-control @error('contact_number') is-invalid @enderror"
                                       value="{{ old('contact_number', $consumer->contact_number) }}" required>
                                @error('contact_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label for="connection_date" class="form-label small fw-bold text-secondary">
                                    Connection Date <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="connection_date" id="connection_date"
                                       class="form-control @error('connection_date') is-invalid @enderror"
                                       value="{{ old('connection_date', $consumer->connection_date ? $consumer->connection_date->format('Y-m-d') : '') }}" required>
                                @error('connection_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label for="status" class="form-label small fw-bold text-secondary">
                                    Account Status <span class="text-danger">*</span>
                                </label>
                                <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="active" {{ old('status', $consumer->status) === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status', $consumer->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 border-top pt-3">
                            <a href="{{ route('consumers.show', $consumer) }}" class="btn btn-light btn-sm px-3">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-sm px-4 shadow-sm" style="background: linear-gradient(135deg, #0284c7 0%, #155eef 100%); border: none;">
                                <i class="bi bi-check-circle me-1"></i> Update Consumer Details
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@stop
