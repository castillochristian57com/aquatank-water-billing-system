@extends('adminlte::page')

@section('title', 'Record OTC Payment')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="m-0 text-dark fw-bold" style="font-size: 1.75rem;">
                <i class="bi bi-cash text-success me-2"></i>Record Physical Payment
            </h1>
            <p class="text-muted small mb-0">Record over-the-counter cash collection and generate an official receipt</p>
        </div>
        <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to Payments List
        </a>
    </div>
@stop

@section('content')
<div class="container-fluid px-0">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-bottom p-3">
                    <h6 class="m-0 fw-bold text-dark">Over-the-Counter Cash Payment Entry</h6>
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

                    <form method="POST" action="{{ route('payments.store') }}">
                        @csrf

                        <div class="row g-3 mb-4">
                            <div class="col-12">
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1.5 text-uppercase fw-bold" style="font-size: 0.72rem;">
                                    1. Select Pending Water Bill
                                </span>
                            </div>

                            <div class="col-12">
                                <label for="bill_id" class="form-label small fw-bold text-secondary">
                                    Unpaid or Overdue Bill <span class="text-danger">*</span>
                                </label>
                                <select name="bill_id" id="bill_id" class="form-select @error('bill_id') is-invalid @enderror" required>
                                    <option value="">-- Choose Unpaid Bill Statement --</option>
                                    @foreach($unpaidBills as $b)
                                        <option value="{{ $b->id }}"
                                                data-due="{{ $b->balance }}"
                                                data-period="{{ $b->billing_period }}"
                                                data-consumer="{{ $b->consumer->full_name }}"
                                                data-account="{{ $b->consumer->account_number }}"
                                                {{ (old('bill_id', $selectedBill?->id) == $b->id) ? 'selected' : '' }}>
                                            {{ $b->bill_number }} &bull; {{ $b->consumer->full_name }} ({{ $b->consumer->account_number }}) &bull; Due: ₱{{ number_format($b->balance, 2) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('bill_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-12">
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1.5 text-uppercase fw-bold" style="font-size: 0.72rem;">
                                    2. Collection Details
                                </span>
                            </div>

                            <div class="col-md-6">
                                <label for="reference_number" class="form-label small fw-bold text-secondary">
                                    Official Receipt (OR) / Ref Number <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="reference_number" id="reference_number"
                                       class="form-control font-monospace @error('reference_number') is-invalid @enderror"
                                       value="{{ old('reference_number', $suggestedReferenceNo) }}" required>
                                @error('reference_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="amount_paid" class="form-label small fw-bold text-secondary">
                                    Amount Paid (₱) <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light fw-bold">₱</span>
                                    <input type="number" step="0.01" min="0.01" name="amount_paid" id="amount_paid"
                                           class="form-control fw-bold text-success @error('amount_paid') is-invalid @enderror"
                                           value="{{ old('amount_paid', $selectedBill ? $selectedBill->balance : '') }}" placeholder="0.00" required>
                                </div>
                                <div class="form-text small" id="balanceHint">
                                    @if($selectedBill)
                                        Remaining balance: <strong>₱{{ number_format($selectedBill->balance, 2) }}</strong>
                                    @endif
                                </div>
                                @error('amount_paid')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="payment_date" class="form-label small fw-bold text-secondary">
                                    Payment Date <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="payment_date" id="payment_date"
                                       class="form-control @error('payment_date') is-invalid @enderror"
                                       value="{{ old('payment_date', date('Y-m-d')) }}" required>
                                @error('payment_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="payment_method" class="form-label small fw-bold text-secondary">
                                    Payment Method <span class="text-danger">*</span>
                                </label>
                                <select name="payment_method" id="payment_method" class="form-select @error('payment_method') is-invalid @enderror" required>
                                    <option value="Cash" {{ old('payment_method') === 'Cash' ? 'selected' : '' }}>Cash (Over-the-Counter)</option>
                                    <option value="Other" {{ old('payment_method') === 'Other' ? 'selected' : '' }}>Pay via online! (This service is currently not available)</option>
                                </select>
                                @error('payment_method')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="notes" class="form-label small fw-bold text-secondary">
                                    Transaction Notes / Remarks
                                </label>
                                <input type="text" name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror"
                                       value="{{ old('notes', 'Over-the-counter payment at Barangay Hall') }}">
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 border-top pt-3">
                            <a href="{{ route('payments.index') }}" class="btn btn-light btn-sm px-3">Cancel</a>
                            <button type="submit" class="btn btn-success btn-sm px-4 shadow-sm">
                                <i class="bi bi-check-circle me-1"></i> Record Payment & Issue OR
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
    const billSelect = document.getElementById('bill_id');
    const amountInput = document.getElementById('amount_paid');
    const balanceHint = document.getElementById('balanceHint');

    billSelect.addEventListener('change', function () {
        const opt = this.options[this.selectedIndex];
        if (opt && opt.dataset.due !== undefined) {
            const due = parseFloat(opt.dataset.due) || 0;
            amountInput.value = due.toFixed(2);
            balanceHint.innerHTML = 'Remaining balance: <strong>₱' + due.toFixed(2) + '</strong>';
        }
    });
</script>
@endpush
@stop
