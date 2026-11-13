@extends('adminlte::page')

@section('title', 'Encode Meter Reading & Bill')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="m-0 text-dark fw-bold" style="font-size: 1.75rem;">
                <i class="bi bi-speedometer2 text-primary me-2"></i>Encode Meter Reading
            </h1>
            <p class="text-muted small mb-0">Record monthly consumer readings and automatically compute water charges</p>
        </div>
        <a href="{{ route('bills.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to Bills List
        </a>
    </div>
@stop

@section('content')
<div class="container-fluid px-0">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-9">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-bottom p-3">
                    <h6 class="m-0 fw-bold text-dark">Meter Reading & Automatic Billing Form</h6>
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

                    <form method="POST" action="{{ route('bills.store') }}" id="billingForm">
                        @csrf

                        <div class="row g-3 mb-4">
                            <div class="col-12">
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1.5 text-uppercase fw-bold" style="font-size: 0.72rem;">
                                    Select Consumer
                                </span>
                            </div>

                            <div class="col-12">
                                <label for="consumer_id" class="form-label small fw-bold text-secondary">
                                    Active Consumer Account <span class="text-danger">*</span>
                                </label>
                                <select name="consumer_id" id="consumer_id" class="form-select @error('consumer_id') is-invalid @enderror" required>
                                    <option value="">Choose Registered Consumer </option>
                                    @foreach($consumers as $c)
                                        <option value="{{ $c->id }}"
                                                data-prev="{{ $c->latest_reading }}"
                                                data-account="{{ $c->account_number }}"
                                                data-meter="{{ $c->meter_number }}"
                                                {{ (old('consumer_id', $selectedConsumer?->id) == $c->id) ? 'selected' : '' }}>
                                            {{ $c->full_name }} — {{ $c->account_number }} (Meter: {{ $c->meter_number }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('consumer_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-12">
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1.5 text-uppercase fw-bold" style="font-size: 0.72rem;">
                                    Meter Readings & Dates
                                </span>
                            </div>

                            <div class="col-md-6">
                                <label for="previous_reading" class="form-label small fw-bold text-secondary">
                                    Previous Reading (m³) <span class="text-danger">*</span>
                                </label>
                                <input type="number" step="0.01" min="0" name="previous_reading" id="previous_reading"
                                       class="form-control @error('previous_reading') is-invalid @enderror"
                                       value="{{ old('previous_reading', $previousReading) }}" required>
                                <div class="form-text small">Auto-filled from latest statement (or 0 if initial reading).</div>
                                @error('previous_reading')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="current_reading" class="form-label small fw-bold text-secondary">
                                    Current Reading (m³) <span class="text-danger">*</span>
                                </label>
                                <input type="number" step="0.01" min="0" name="current_reading" id="current_reading"
                                       class="form-control @error('current_reading') is-invalid @enderror"
                                       value="{{ old('current_reading', $suggestedCurrentReading) }}" placeholder="e.g. 150.00" required>
                                <div id="readingError" class="text-danger small fw-bold mt-1" style="display: none;">
                                    <i class="bi bi-exclamation-circle-fill me-1"></i> Current reading cannot be lower than previous reading.
                                </div>
                                @error('current_reading')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="billing_period" class="form-label small fw-bold text-secondary">
                                    Billing Period <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="billing_period" id="billing_period"
                                       class="form-control @error('billing_period') is-invalid @enderror"
                                       value="{{ old('billing_period', $currentPeriod) }}" required>
                                @error('billing_period')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="billing_date" class="form-label small fw-bold text-secondary">
                                    Reading / Billing Date <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="billing_date" id="billing_date"
                                       class="form-control @error('billing_date') is-invalid @enderror"
                                       value="{{ old('billing_date', $defaultBillingDate) }}" required>
                                @error('billing_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="due_date" class="form-label small fw-bold text-secondary">
                                    Payment Due Date <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="due_date" id="due_date"
                                       class="form-control @error('due_date') is-invalid @enderror"
                                       value="{{ old('due_date', $defaultDueDate) }}" required>
                                @error('due_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-12">
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1.5 text-uppercase fw-bold" style="font-size: 0.72rem;">
                                    Calculation Preview
                                </span>
                            </div>

                            <div class="col-md-6">
                                <label for="rate_per_cubic_meter" class="form-label small fw-bold text-secondary">
                                    Tariff Rate (₱ per m³) <span class="text-danger">*</span>
                                </label>
                                <input type="number" step="0.01" min="0.01" name="rate_per_cubic_meter" id="rate_per_cubic_meter"
                                       class="form-control @error('rate_per_cubic_meter') is-invalid @enderror"
                                       value="{{ old('rate_per_cubic_meter', $defaultRate) }}" required>
                                <div class="form-text small">Configurable tariff retrieved from system settings.</div>
                                @error('rate_per_cubic_meter')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <div class="p-3 rounded-3 bg-light border text-center h-100 d-flex flex-column justify-content-center">
                                    <div class="text-muted small fw-bold text-uppercase mb-1">Calculated Amount Due</div>
                                    <h3 class="fw-bold text-primary mb-1" id="previewAmount">₱0.00</h3>
                                    <small class="text-secondary">
                                        Consumption: <strong id="previewConsumption">0.00 m³</strong>
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 border-top pt-3">
                            <a href="{{ route('bills.index') }}" class="btn btn-light btn-sm px-3">Cancel</a>
                            <button type="submit" id="submitBtn" class="btn btn-primary btn-sm px-4 shadow-sm" style="background: linear-gradient(135deg, #0284c7 0%, #155eef 100%); border: none;">
                                <i class="bi bi-receipt me-1"></i> Generate Water Bill
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
    const consumerSelect = document.getElementById('consumer_id');
    const prevInput = document.getElementById('previous_reading');
    const currInput = document.getElementById('current_reading');
    const rateInput = document.getElementById('rate_per_cubic_meter');
    const previewAmount = document.getElementById('previewAmount');
    const previewConsumption = document.getElementById('previewConsumption');
    const readingError = document.getElementById('readingError');
    const submitBtn = document.getElementById('submitBtn');

    function calculateBilling() {
        const prev = parseFloat(prevInput.value) || 0;
        const curr = parseFloat(currInput.value) || 0;
        const rate = parseFloat(rateInput.value) || 15.00;

        if (curr < prev) {
            readingError.style.display = 'block';
            submitBtn.disabled = true;
            previewConsumption.textContent = '0.00 m³';
            previewAmount.textContent = '₱0.00';
            previewAmount.className = 'fw-bold text-danger mb-1';
            return;
        }

        readingError.style.display = 'none';
        submitBtn.disabled = false;
        previewAmount.className = 'fw-bold text-primary mb-1';

        const consumption = Math.max(0, curr - prev);
        const amount = consumption * rate;

        previewConsumption.textContent = consumption.toFixed(2) + ' m³';
        previewAmount.textContent = '₱' + amount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    consumerSelect.addEventListener('change', function () {
        const consumerId = this.value;
        if (!consumerId) return;

        fetch(`{{ url('api/consumers') }}/${consumerId}/reading`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.unbilled_reading) {
                prevInput.value = parseFloat(data.unbilled_reading.previous_reading).toFixed(2);
                currInput.value = parseFloat(data.unbilled_reading.current_reading).toFixed(2);
                if (data.unbilled_reading.billing_period) {
                    document.getElementById('billing_period').value = data.unbilled_reading.billing_period;
                }
            } else if (data.previous_reading !== undefined) {
                prevInput.value = parseFloat(data.previous_reading).toFixed(2);
            }
            calculateBilling();
        })
        .catch(err => {
            const selected = consumerSelect.options[consumerSelect.selectedIndex];
            if (selected && selected.dataset.prev !== undefined) {
                prevInput.value = parseFloat(selected.dataset.prev).toFixed(2);
                calculateBilling();
            }
        });
    });

    prevInput.addEventListener('input', calculateBilling);
    currInput.addEventListener('input', calculateBilling);
    rateInput.addEventListener('input', calculateBilling);

    calculateBilling();
</script>
@endpush
@stop
