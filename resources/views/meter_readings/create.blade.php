@extends('adminlte::page')

@section('title', 'Record Meter Reading')

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
    --aquatank-blue-light: #eff6ff;
    --aquatank-border-blue: #bfdbfe;
    --gradient-primary: linear-gradient(135deg, #4a6cf7 0%, #2563eb 100%);
    --gradient-success: linear-gradient(135deg, #10b981 0%, #059669 100%);
    --glass-bg: #ffffff;
    --glass-border: #bfdbfe;
    --glass-shadow: 0 4px 16px rgba(37, 99, 235, 0.06);
}

body, .app-wrapper, .content-wrapper, .form-control, .form-select, .btn {
    font-family: var(--font-body) !important;
}

h1, h2, h3, h4, h5, h6, .brand-text, .section-title {
    font-family: var(--font-heading) !important;
    letter-spacing: -0.02em;
}

.meter-card {
    background: #ffffff;
    border: 1.5px solid var(--glass-border);
    border-radius: 1.25rem;
    box-shadow: var(--glass-shadow);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

/* Smartphone touch-friendly adjustments */
.touch-btn {
    min-height: 52px;
    font-size: 1.05rem;
    font-weight: 600;
    border-radius: 0.85rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.touch-input {
    min-height: 52px;
    font-size: 1.15rem;
    font-weight: 600;
    border-radius: 0.85rem;
}

.reading-big-input {
    font-size: 2rem !important;
    font-weight: 700 !important;
    letter-spacing: 0.02em;
    min-height: 64px !important;
    text-align: right;
    color: #1e3a8a;
    background-color: #f8fbff;
    border: 2px solid #93c5fd;
    border-radius: 1rem;
}

.reading-big-input:focus {
    background-color: #ffffff;
    border-color: #2563eb;
    box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.15);
}

.calc-display-card {
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
    border: 1.5px solid #bfdbfe;
    border-radius: 1rem;
}

.consumer-badge {
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    color: #1e40af;
    font-weight: 600;
    padding: 0.35rem 0.65rem;
    border-radius: 0.5rem;
    font-size: 0.8rem;
}

.live-search-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    z-index: 1050;
    background: #ffffff;
    border: 1.5px solid #93c5fd;
    border-radius: 0.85rem;
    box-shadow: 0 10px 25px rgba(37, 99, 235, 0.15);
    max-height: 320px;
    overflow-y: auto;
}

.search-item {
    cursor: pointer;
    padding: 0.75rem 1rem;
    border-bottom: 1px solid #f1f5f9;
    transition: background-color 0.15s ease;
}
.search-item:last-child {
    border-bottom: none;
}
.search-item:hover, .search-item.active {
    background-color: #eff6ff;
}

.step-pill {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: var(--gradient-primary);
    color: white;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.85rem;
    font-weight: bold;
    margin-right: 0.5rem;
}
</style>
@endpush

@section('content_header')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2 mb-2">
        <div>
            <h1 class="m-0 text-dark fw-bold" style="font-size: 1.75rem;">
                <i class="bi bi-speedometer2 text-primary me-2"></i>Record Meter Reading
            </h1>
            <p class="text-muted small mb-0">Mobile-friendly water meter reading entry for field inspection in Barangay Caruhayon</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('meter-readings.index') }}" class="btn btn-outline-secondary btn-sm px-3 d-flex align-items-center gap-1 rounded-3">
                <i class="bi bi-clock-history"></i> Reading Logs
            </a>
            <a href="{{ route('bills.index') }}" class="btn btn-outline-primary btn-sm px-3 d-flex align-items-center gap-1 rounded-3">
                <i class="bi bi-receipt"></i> Bills
            </a>
        </div>
    </div>
@stop

@section('content')
<div class="container-fluid px-0">

    {{-- Session Success Banner --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4 p-3" role="alert" style="background: rgba(209, 250, 229, 0.95); border-left: 5px solid #10b981 !important;">
            <div class="d-flex align-items-start">
                <i class="bi bi-check-circle-fill text-success fs-4 me-3 mt-1"></i>
                <div class="flex-grow-1">
                    <h6 class="fw-bold text-success mb-1">Meter Reading Saved Successfully!</h6>
                    <p class="mb-2 text-dark small">{{ session('success') }}</p>
                    <div class="d-flex flex-wrap gap-2 mt-2">
                        <button type="button" class="btn btn-success btn-sm px-3 rounded-pill fw-semibold" onclick="resetFormForNext()">
                            <i class="bi bi-arrow-repeat me-1"></i> Record Next Consumer
                        </button>
                        <a href="{{ route('meter-readings.index') }}" class="btn btn-outline-success btn-sm px-3 rounded-pill fw-semibold">
                            <i class="bi bi-list-check me-1"></i> View Reading Logs
                        </a>
                        <a href="{{ route('bills.create') }}" class="btn btn-outline-primary btn-sm px-3 rounded-pill fw-semibold">
                            <i class="bi bi-receipt me-1"></i> Proceed to Bill Generation
                        </a>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif

    {{-- Error Banner --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4" role="alert" style="background: #fef2f2; border-left: 5px solid #ef4444 !important;">
            <div class="d-flex align-items-start">
                <i class="bi bi-exclamation-triangle-fill text-danger fs-4 me-3 mt-1"></i>
                <div>
                    <h6 class="fw-bold text-danger mb-1">Please Correct the Errors Below:</h6>
                    <ul class="mb-0 ps-3 small text-danger">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-12 col-lg-8 col-xl-7">
            <div class="meter-card p-3 p-md-4 mb-4">
                <form method="POST" action="{{ route('meter-readings.store') }}" id="meterReadingForm" novalidate>
                    @csrf

                    {{-- Hidden consumer ID input --}}
                    <input type="hidden" name="consumer_id" id="consumer_id" value="{{ old('consumer_id', $selectedConsumer?->id) }}" required>

                    {{-- SECTION 1: SEARCH & SELECT CONSUMER --}}
                    <div class="mb-4">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <label class="form-label mb-0 fw-bold text-dark d-flex align-items-center">
                                <span class="step-pill">1</span> Search & Select Consumer
                            </label>
                            /////////////
                        </div>

                        {{-- Live Search Input with Instant Dropdown --}}
                        <div class="position-relative mb-2">
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-primary">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="text"
                                       id="consumerSearchInput"
                                       class="form-control touch-input border-start-0"
                                       placeholder="Type name, account #, meter #, or address..."
                                       autocomplete="off"
                                       aria-label="Search consumer">
                                <button type="button" class="btn btn-outline-secondary" id="clearSearchBtn" title="Clear search" style="display: none;">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                            {{-- Live search results dropdown --}}
                            <div id="searchResultsDropdown" class="live-search-dropdown" style="display: none;">
                                <div id="searchResultsList"></div>
                            </div>
                        </div>

                        {{-- Dropdown fallback select --}}
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <div class="flex-grow-1">
                                <select id="consumerSelect" class="form-select form-select-sm text-secondary rounded-3">
                                    <option value="">-- Or choose from active consumer accounts ({{ $consumers->count() }}) --</option>
                                    @foreach($consumers as $c)
                                        <option value="{{ $c->id }}"
                                                data-name="{{ $c->full_name }}"
                                                data-account="{{ $c->account_number }}"
                                                data-meter="{{ $c->meter_number }}"
                                                data-address="{{ $c->address }}"
                                                data-has-prev="{{ $c->hasPreviousReading() ? '1' : '0' }}"
                                                data-prev="{{ $c->latest_reading }}"
                                                {{ (old('consumer_id', $selectedConsumer?->id) == $c->id) ? 'selected' : '' }}>
                                            {{ $c->full_name }} — {{ $c->account_number }} (Meter: {{ $c->meter_number }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Consumer Details Card (Populated upon selection) --}}
                        <div id="consumerInfoCard" class="card border-0 rounded-4 p-3 mt-3 shadow-xs" style="background: #f8fbff; border: 1.5px dashed #93c5fd !important; display: none;">
                            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start gap-2">
                                <div>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0.5 rounded-pill mb-1">
                                        <i class="bi bi-check-circle me-1"></i> Consumer Selected
                                    </span>
                                    <h5 class="fw-bold text-dark mb-1" id="infoConsumerName">—</h5>
                                    <div class="d-flex flex-wrap gap-2 text-muted small">
                                        <span><i class="bi bi-person-badge text-primary me-1"></i>Acct: <strong id="infoAccountNumber" class="text-dark">—</strong></span>
                                        <span>&bull;</span>
                                        <span><i class="bi bi-speedometer text-primary me-1"></i>Meter: <strong id="infoMeterNumber" class="text-dark">—</strong></span>
                                    </div>
                                    <div class="text-muted small mt-1">
                                        <i class="bi bi-geo-alt text-primary me-1"></i><span id="infoAddress">—</span>
                                    </div>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-2.5 py-1" onclick="clearConsumerSelection()">
                                        <i class="bi bi-arrow-repeat me-1"></i> Change
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Prompt to select when none selected --}}
                        <div id="noConsumerSelectedAlert" class="alert alert-light border border-primary-subtle rounded-3 py-2.5 px-3 mt-2 text-muted small d-flex align-items-center">
                            <i class="bi bi-info-circle text-primary me-2 fs-5"></i>
                            <span>Search or select a consumer above to automatically retrieve their previous meter reading.</span>
                        </div>
                    </div>

                    <hr class="my-4" style="border-top: 1.5px solid #e2e8f0;">

                    {{-- SECTION 2: METER READINGS & DATES --}}
                    <div class="mb-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <label class="form-label mb-0 fw-bold text-dark d-flex align-items-center">
                                <span class="step-pill">2</span> Meter Readings & Calculation
                            </label>
                            <span class="text-muted small">Tariff: ₱{{ number_format($tariffRate, 2) }} / m³</span>
                        </div>

                        {{-- Previous Reading Display Card --}}
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-bold text-secondary mb-1">
                                    Previous Meter Reading (m³)
                                </label>
                                <div class="card border-0 rounded-3 p-3 text-center" style="background: #f1f5f9; border: 1.5px solid #cbd5e1 !important;">
                                    <div class="text-muted small fw-semibold mb-1">Database Historical Value</div>
                                    <div class="h3 fw-bold mb-0 text-dark" id="displayPreviousReading">
                                        <span class="text-muted font-monospace">--</span>
                                    </div>
                                    <div class="small mt-1" id="previousReadingNote">
                                        <span class="text-muted">Select a consumer first</span>
                                    </div>
                                </div>
                                <input type="hidden" name="previous_reading_hidden" id="previous_reading_hidden" value="0.00">
                            </div>

                            {{-- Current Reading Input --}}
                            <div class="col-12 col-md-6">
                                <label for="current_reading" class="form-label small fw-bold text-secondary mb-1">
                                    Current Meter Reading (m³) <span class="text-danger">*</span>
                                </label>
                                <div class="position-relative">
                                    <input type="number"
                                           step="0.01"
                                           min="0"
                                           inputmode="decimal"
                                           name="current_reading"
                                           id="current_reading"
                                           class="form-control reading-big-input @error('current_reading') is-invalid @enderror"
                                           placeholder="0.00"
                                           value="{{ old('current_reading') }}"
                                           required>
                                    <span class="position-absolute end-0 top-50 translate-middle-y me-3 text-muted fw-bold small" style="pointer-events: none;">
                                        m³
                                    </span>
                                </div>
                                <div class="form-text small text-muted">
                                    Enter reading visible on physical water meter dial.
                                </div>
                                <div id="liveReadingError" class="text-danger small fw-bold mt-1" style="display: none;">
                                    <i class="bi bi-exclamation-circle-fill me-1"></i> Current reading cannot be lower than previous reading.
                                </div>
                                @error('current_reading')
                                    <div class="text-danger small fw-bold mt-1">
                                        <i class="bi bi-exclamation-circle-fill me-1"></i> {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        {{-- Dynamic Real-Time Consumption Display Card --}}
                        <div class="calc-display-card p-3 mb-3">
                            <div class="row g-2 align-items-center text-center text-sm-start">
                                <div class="col-12 col-sm-6">
                                    <div class="text-uppercase small fw-bold text-primary" style="letter-spacing: 0.05em;">
                                        Computed Water Consumption
                                    </div>
                                    <div class="d-flex align-items-baseline justify-content-center justify-content-sm-start mt-1">
                                        <span class="h2 fw-bold text-primary mb-0 me-1" id="displayConsumption">0.00</span>
                                        <span class="fw-bold text-primary">m³</span>
                                    </div>
                                    <div class="small text-muted" id="consumptionFormula">
                                        Current reading − Previous reading
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 border-start-sm border-primary-subtle pt-2 pt-sm-0">
                                    <div class="text-uppercase small fw-bold text-secondary" style="letter-spacing: 0.05em;">
                                        Estimated Water Charge
                                    </div>
                                    <div class="h3 fw-bold text-success mb-0 mt-1" id="displayEstimatedAmount">
                                        ₱0.00
                                    </div>
                                    <div class="small text-muted">
                                        Rate: ₱{{ number_format($tariffRate, 2) }}/m³
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Reading Date and Billing Period --}}
                        <div class="row g-3">
                            <div class="col-12 col-sm-6">
                                <label for="reading_date" class="form-label small fw-bold text-secondary mb-1">
                                    Reading Date <span class="text-danger">*</span>
                                </label>
                                <input type="date"
                                       name="reading_date"
                                       id="reading_date"
                                       class="form-control touch-input @error('reading_date') is-invalid @enderror"
                                       value="{{ old('reading_date', $todayDate) }}"
                                       required>
                                @error('reading_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-sm-6">
                                <label for="billing_period" class="form-label small fw-bold text-secondary mb-1">
                                    Billing Period <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       name="billing_period"
                                       id="billing_period"
                                       class="form-control touch-input @error('billing_period') is-invalid @enderror"
                                       value="{{ old('billing_period', $currentPeriod) }}"
                                       placeholder="e.g. October 2026"
                                       required>
                                @error('billing_period')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="notes" class="form-label small fw-bold text-secondary mb-1">
                                    Field Notes / Remarks <span class="text-muted fw-normal">(Optional)</span>
                                </label>
                                <input type="text"
                                       name="notes"
                                       id="notes"
                                       class="form-control rounded-3"
                                       placeholder="e.g. Meter dial glass cleared, normal household usage"
                                       value="{{ old('notes') }}"
                                       maxlength="500">
                            </div>
                        </div>
                    </div>

                    {{-- SUBMIT BUTTON --}}
                    <div class="d-grid gap-2 pt-2">
                        <button type="submit"
                                id="saveReadingBtn"
                                class="btn btn-primary touch-btn shadow-sm"
                                style="background: var(--gradient-primary); border: none;">
                            <i class="bi bi-cloud-arrow-up-fill me-1 fs-5"></i>
                            <span id="btnText">Save Reading</span>
                            <span id="btnSpinner" class="spinner-border spinner-border-sm ms-2" role="status" aria-hidden="true" style="display: none;"></span>
                        </button>

                        <button type="button"
                                class="btn btn-outline-secondary touch-btn"
                                onclick="resetFormForNext()">
                            <i class="bi bi-arrow-repeat me-1"></i> Reset / Next Consumer
                        </button>
                    </div>
                </form>
            </div>

            {{-- Recent Readings Recorded Today --}}
            @if($recentReadings->count() > 0)
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-bottom p-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 fw-bold text-dark d-flex align-items-center">
                            <i class="bi bi-clock-history text-primary me-2"></i> Recent Readings Saved Today ({{ $recentReadings->count() }})
                        </h6>
                        <a href="{{ route('meter-readings.index') }}" class="small text-primary fw-bold text-decoration-none">
                            View All Logs &rarr;
                        </a>
                    </div>
                    <div class="list-group list-group-flush rounded-bottom-4">
                        @foreach($recentReadings as $rr)
                            <div class="list-group-item p-3 d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-bold text-dark">{{ $rr->consumer->full_name }}</div>
                                    <div class="text-muted small">
                                        {{ $rr->consumer->account_number }} &bull; Meter: {{ $rr->consumer->meter_number }}
                                    </div>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-6 px-2.5 py-1">
                                        {{ number_format($rr->current_reading, 2) }} m³
                                    </span>
                                    <div class="small text-muted mt-1">
                                        +{{ number_format($rr->consumption, 2) }} m³ consumed
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>
@stop

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tariffRate = {{ $tariffRate }};
    const searchInput = document.getElementById('consumerSearchInput');
    const searchDropdown = document.getElementById('searchResultsDropdown');
    const searchResultsList = document.getElementById('searchResultsList');
    const clearSearchBtn = document.getElementById('clearSearchBtn');

    const consumerSelect = document.getElementById('consumerSelect');
    const consumerIdInput = document.getElementById('consumer_id');

    const consumerInfoCard = document.getElementById('consumerInfoCard');
    const noConsumerSelectedAlert = document.getElementById('noConsumerSelectedAlert');
    const infoConsumerName = document.getElementById('infoConsumerName');
    const infoAccountNumber = document.getElementById('infoAccountNumber');
    const infoMeterNumber = document.getElementById('infoMeterNumber');
    const infoAddress = document.getElementById('infoAddress');

    const displayPreviousReading = document.getElementById('displayPreviousReading');
    const previousReadingNote = document.getElementById('previousReadingNote');
    const previousReadingHidden = document.getElementById('previous_reading_hidden');

    const currentReadingInput = document.getElementById('current_reading');
    const displayConsumption = document.getElementById('displayConsumption');
    const displayEstimatedAmount = document.getElementById('displayEstimatedAmount');
    const consumptionFormula = document.getElementById('consumptionFormula');
    const liveReadingError = document.getElementById('liveReadingError');

    const form = document.getElementById('meterReadingForm');
    const saveReadingBtn = document.getElementById('saveReadingBtn');
    const btnText = document.getElementById('btnText');
    const btnSpinner = document.getElementById('btnSpinner');

    let currentHasPrevious = false;
    let currentPreviousVal = 0.00;
    let searchDebounceTimeout = null;

    // Handle consumer selection update
    window.selectConsumer = function (consumer) {
        consumerIdInput.value = consumer.id;
        consumerSelect.value = consumer.id;

        infoConsumerName.textContent = consumer.name;
        infoAccountNumber.textContent = consumer.account_number;
        infoMeterNumber.textContent = consumer.meter_number || 'N/A';
        infoAddress.textContent = consumer.address || 'Barangay Caruhayon';

        consumerInfoCard.style.display = 'block';
        noConsumerSelectedAlert.style.display = 'none';

        currentHasPrevious = consumer.has_previous_reading;
        currentPreviousVal = parseFloat(consumer.previous_reading) || 0.00;
        previousReadingHidden.value = currentPreviousVal.toFixed(2);

        if (currentHasPrevious) {
            displayPreviousReading.innerHTML = `${currentPreviousVal.toFixed(2)} <span class="fs-6 text-muted">m³</span>`;
            previousReadingNote.innerHTML = `<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">Historical Record</span>`;
        } else {
            displayPreviousReading.innerHTML = `<span class="text-warning-emphasis fw-bold">Initial Reading</span>`;
            previousReadingNote.innerHTML = `<span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">No previous reading — Initial Reading</span>`;
        }

        // Hide live search dropdown
        searchDropdown.style.display = 'none';
        searchInput.value = `${consumer.name} (${consumer.account_number})`;
        clearSearchBtn.style.display = 'inline-block';

        // Recompute consumption
        calculateConsumption();

        // Focus current reading input for staff convenience on mobile
        setTimeout(() => {
            currentReadingInput.focus();
        }, 150);
    };

    // Clear consumer selection
    window.clearConsumerSelection = function () {
        consumerIdInput.value = '';
        consumerSelect.value = '';
        searchInput.value = '';
        clearSearchBtn.style.display = 'none';

        consumerInfoCard.style.display = 'none';
        noConsumerSelectedAlert.style.display = 'flex';

        displayPreviousReading.innerHTML = `<span class="text-muted font-monospace">--</span>`;
        previousReadingNote.innerHTML = `<span class="text-muted">Select a consumer first</span>`;
        previousReadingHidden.value = '0.00';
        currentPreviousVal = 0.00;
        currentHasPrevious = false;

        calculateConsumption();
    };

    // Reset full form for next consumer
    window.resetFormForNext = function () {
        clearConsumerSelection();
        currentReadingInput.value = '';
        document.getElementById('notes').value = '';
        liveReadingError.style.display = 'none';
        currentReadingInput.classList.remove('is-invalid');
        searchInput.focus();
    };

    // Live AJAX Search input listener with debouncing
    searchInput.addEventListener('input', function () {
        const query = searchInput.value.trim();

        if (query.length > 0) {
            clearSearchBtn.style.display = 'inline-block';
        } else {
            clearSearchBtn.style.display = 'none';
            searchDropdown.style.display = 'none';
            return;
        }

        clearTimeout(searchDebounceTimeout);
        searchDebounceTimeout = setTimeout(() => {
            fetchSearchResults(query);
        }, 220);
    });

    clearSearchBtn.addEventListener('click', function () {
        clearConsumerSelection();
    });

    // Close search dropdown on click outside
    document.addEventListener('click', function (e) {
        if (!searchInput.contains(e.target) && !searchDropdown.contains(e.target)) {
            searchDropdown.style.display = 'none';
        }
    });

    function fetchSearchResults(query) {
        fetch(`{{ route('meter-readings.search') }}?q=${encodeURIComponent(query)}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            searchResultsList.innerHTML = '';
            if (data.length === 0) {
                searchResultsList.innerHTML = `
                    <div class="p-3 text-center text-muted small">
                        <i class="bi bi-search me-1"></i> No matching consumers found for "${query}"
                    </div>
                `;
            } else {
                data.forEach(item => {
                    const div = document.createElement('div');
                    div.className = 'search-item';
                    const prevBadge = item.has_previous_reading
                        ? `<span class="badge bg-light text-primary border">${parseFloat(item.previous_reading).toFixed(2)} m³</span>`
                        : `<span class="badge bg-warning-subtle text-warning-emphasis border">Initial Reading</span>`;

                    div.innerHTML = `
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-bold text-dark">${item.name}</div>
                                <div class="text-muted small">${item.account_number} &bull; Meter: ${item.meter_number || 'N/A'}</div>
                                <div class="text-muted small" style="font-size: 0.75rem;"><i class="bi bi-geo-alt me-1"></i>${item.address}</div>
                            </div>
                            <div class="text-end">
                                ${prevBadge}
                            </div>
                        </div>
                    `;
                    div.addEventListener('click', () => {
                        selectConsumer(item);
                    });
                    searchResultsList.appendChild(div);
                });
            }
            searchDropdown.style.display = 'block';
        })
        .catch(err => {
            console.error('Search fetch error:', err);
        });
    }

    // Dropdown change listener
    consumerSelect.addEventListener('change', function () {
        const selectedId = this.value;
        if (!selectedId) {
            clearConsumerSelection();
            return;
        }

        // Fetch fresh consumer reading data from server
        fetch(`{{ url('meter-readings/consumer') }}/${selectedId}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(consumer => {
            selectConsumer(consumer);
        })
        .catch(err => {
            console.error('Fetch consumer error:', err);
            // Fallback to select data attributes
            const opt = consumerSelect.selectedOptions[0];
            selectConsumer({
                id: opt.value,
                name: opt.getAttribute('data-name'),
                account_number: opt.getAttribute('data-account'),
                meter_number: opt.getAttribute('data-meter'),
                address: opt.getAttribute('data-address'),
                has_previous_reading: opt.getAttribute('data-has-prev') === '1',
                previous_reading: parseFloat(opt.getAttribute('data-prev')) || 0.00
            });
        });
    });

    // Real-time consumption computation
    function calculateConsumption() {
        const rawCurr = currentReadingInput.value.trim();
        const currVal = parseFloat(rawCurr);

        if (isNaN(currVal) || rawCurr === '') {
            displayConsumption.textContent = '0.00';
            displayEstimatedAmount.textContent = '₱0.00';
            consumptionFormula.textContent = 'Current reading − Previous reading';
            liveReadingError.style.display = 'none';
            currentReadingInput.classList.remove('is-invalid');
            saveReadingBtn.disabled = false;
            return;
        }

        if (currentHasPrevious) {
            if (currVal < currentPreviousVal) {
                // Invalid: Current lower than previous
                displayConsumption.textContent = '0.00';
                displayEstimatedAmount.textContent = '₱0.00';
                consumptionFormula.innerHTML = `<span class="text-danger">Invalid reading (${currVal.toFixed(2)} < ${currentPreviousVal.toFixed(2)})</span>`;
                liveReadingError.style.display = 'block';
                currentReadingInput.classList.add('is-invalid');
                saveReadingBtn.disabled = true;
                return;
            }

            // Valid reading >= previous
            const consumption = Math.round((currVal - currentPreviousVal) * 100) / 100;
            const estimatedAmount = Math.round(consumption * tariffRate * 100) / 100;

            displayConsumption.textContent = consumption.toFixed(2);
            displayEstimatedAmount.textContent = `₱${estimatedAmount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
            consumptionFormula.textContent = `${currVal.toFixed(2)} − ${currentPreviousVal.toFixed(2)} = ${consumption.toFixed(2)} m³`;
            liveReadingError.style.display = 'none';
            currentReadingInput.classList.remove('is-invalid');
            saveReadingBtn.disabled = false;
        } else {
            // Initial reading
            const consumption = currVal;
            const estimatedAmount = Math.round(consumption * tariffRate * 100) / 100;

            displayConsumption.textContent = consumption.toFixed(2);
            displayEstimatedAmount.textContent = `₱${estimatedAmount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
            consumptionFormula.textContent = `Initial reading: ${consumption.toFixed(2)} m³`;
            liveReadingError.style.display = 'none';
            currentReadingInput.classList.remove('is-invalid');
            saveReadingBtn.disabled = false;
        }
    }

    currentReadingInput.addEventListener('input', calculateConsumption);
    currentReadingInput.addEventListener('change', calculateConsumption);

    // Form submission validation & UI loading state
    form.addEventListener('submit', function (e) {
        if (!consumerIdInput.value) {
            e.preventDefault();
            alert('Please search for and select a consumer account first.');
            searchInput.focus();
            return false;
        }

        const currVal = parseFloat(currentReadingInput.value);
        if (isNaN(currVal) || currVal < 0) {
            e.preventDefault();
            alert('Please enter a valid nonnegative current meter reading.');
            currentReadingInput.focus();
            return false;
        }

        if (currentHasPrevious && currVal < currentPreviousVal) {
            e.preventDefault();
            alert(`The current meter reading (${currVal.toFixed(2)} m³) cannot be lower than the previous reading (${currentPreviousVal.toFixed(2)} m³).`);
            currentReadingInput.focus();
            return false;
        }

        // Show spinner and prevent duplicate submission
        saveReadingBtn.disabled = true;
        btnText.textContent = 'Saving Reading...';
        btnSpinner.style.display = 'inline-block';
        return true;
    });

    // Auto-select if selected consumer was provided via controller or old input
    @if($selectedConsumer)
        selectConsumer({
            id: {{ $selectedConsumer->id }},
            name: @json($selectedConsumer->full_name),
            account_number: @json($selectedConsumer->account_number),
            meter_number: @json($selectedConsumer->meter_number),
            address: @json($selectedConsumer->address),
            has_previous_reading: {{ $selectedConsumer->hasPreviousReading() ? 'true' : 'false' }},
            previous_reading: {{ (float) $selectedConsumer->latest_reading }}
        });
        @if(old('current_reading'))
            currentReadingInput.value = "{{ old('current_reading') }}";
            calculateConsumption();
        @endif
    @endif
});
</script>
@endpush
