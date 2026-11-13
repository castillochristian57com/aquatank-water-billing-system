@extends('adminlte::page')

@section('title', 'SMS Notifications Hub')

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
    --aquatank-dark: #0f172a;
}
body, .table, .form-control, .btn, .badge {
    font-family: var(--font-body) !important;
}
h1, h2, h3, h4, h5, h6, .card-title, .stat-number {
    font-family: var(--font-heading) !important;
    color: #0f172a !important;
}
.stat-card {
    background: #ffffff !important;
    border: 1.5px solid #dbeafe !important;
    border-radius: 1rem !important;
    padding: 1.25rem 1.5rem !important;
    box-shadow: 0 4px 16px rgba(37, 99, 235, 0.05) !important;
    transition: transform 0.2s ease;
}
.stat-card:hover {
    transform: translateY(-3px);
}
.badge-simulated {
    background-color: #fef3c7;
    color: #92400e;
    border: 1px solid #fde68a;
}
.badge-sent {
    background-color: #dcfce7;
    color: #166534;
    border: 1px solid #bbf7d0;
}
.badge-failed {
    background-color: #fee2e2;
    color: #991b1b;
    border: 1px solid #fecaca;
}
</style>
@endpush

@section('content_header')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">
            <i class="bi bi-chat-dots-fill text-primary me-2"></i>SMS Notifications Center
        </h1>
        <p class="text-muted small mb-0">Automated billing alerts, overdue notices, and Philippine SMS gateway integration</p>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#testSmsModal">
            <i class="bi bi-send me-1"></i> Send Custom SMS
        </button>
        <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#gatewaySettingsModal">
            <i class="bi bi-gear-fill me-1"></i> Gateway Settings
        </button>
    </div>
</div>
@stop

@section('content')
<div class="container-fluid px-0">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 rounded-3 mb-3" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 rounded-3 mb-3" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show shadow-sm border-0 rounded-3 mb-3" role="alert">
            <i class="bi bi-info-circle-fill me-2"></i> {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Total Dispatched</span>
                    <div class="p-2 rounded-3 bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-broadcast fs-5"></i>
                    </div>
                </div>
                <h3 class="stat-number fw-bold text-dark mb-0">{{ number_format($totalLogs) }}</h3>
                <small class="text-muted">{{ number_format($totalSent) }} Live &bull; {{ number_format($totalSimulated) }} Simulated</small>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Gateway Status</span>
                    <div class="p-2 rounded-3 {{ $simulationMode ? 'bg-warning bg-opacity-10 text-warning' : 'bg-success bg-opacity-10 text-success' }}">
                        <i class="bi bi-reception-4 fs-5"></i>
                    </div>
                </div>
                <h4 class="stat-number fw-bold text-dark mb-0 text-capitalize">{{ $currentProvider }}</h4>
                <small class="badge {{ $simulationMode ? 'badge-simulated' : 'badge-sent' }} mt-1">
                    {{ $simulationMode ? 'Sandbox / Simulation Active' : 'Live Gateway Connected' }}
                </small>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Due Soon (5 Days)</span>
                    <div class="p-2 rounded-3 bg-info bg-opacity-10 text-info">
                        <i class="bi bi-calendar-event fs-5"></i>
                    </div>
                </div>
                <h3 class="stat-number fw-bold text-dark mb-0">{{ $pendingDueCount }}</h3>
                <form action="{{ route('sms.batch-reminders') }}" method="POST" class="mt-1">
                    @csrf
                    <button type="submit" class="btn btn-link p-0 text-decoration-none small text-primary fw-semibold" onclick="return confirm('Send automated billing reminders to all unpaid bills due in next 5 days?')">
                        <i class="bi bi-send-fill me-1"></i> Send Batch Reminders
                    </button>
                </form>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Overdue Accounts</span>
                    <div class="p-2 rounded-3 bg-danger bg-opacity-10 text-danger">
                        <i class="bi bi-exclamation-octagon fs-5"></i>
                    </div>
                </div>
                <h3 class="stat-number fw-bold text-danger mb-0">{{ $overdueCount }}</h3>
                <form action="{{ route('sms.batch-overdue') }}" method="POST" class="mt-1">
                    @csrf
                    <button type="submit" class="btn btn-link p-0 text-decoration-none small text-danger fw-semibold" onclick="return confirm('Send urgent overdue alerts to all delinquent consumers?')">
                        <i class="bi bi-megaphone-fill me-1"></i> Dispatch Overdue Alerts
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white py-3 border-light d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h5 class="card-title fw-bold text-dark mb-0">
                <i class="bi bi-clock-history text-secondary me-2"></i>SMS Dispatch History
            </h5>
            
            <form method="GET" action="{{ route('sms.index') }}" class="d-flex flex-wrap gap-2 align-items-center">
                <select name="message_type" class="form-select form-select-sm" style="width: 140px;" onchange="this.form.submit()">
                    <option value="">All Types</option>
                    <option value="reminder" {{ request('message_type') == 'reminder' ? 'selected' : '' }}>Reminder</option>
                    <option value="overdue_alert" {{ request('message_type') == 'overdue_alert' ? 'selected' : '' }}>Overdue Alert</option>
                    <option value="payment_ack" {{ request('message_type') == 'payment_ack' ? 'selected' : '' }}>Payment Ack</option>
                    <option value="custom" {{ request('message_type') == 'custom' ? 'selected' : '' }}>Custom</option>
                </select>

                <select name="status" class="form-select form-select-sm" style="width: 130px;" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                    <option value="simulated" {{ request('status') == 'simulated' ? 'selected' : '' }}>Simulated</option>
                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                </select>

                <div class="input-group input-group-sm" style="width: 220px;">
                    <input type="text" name="search" class="form-control" placeholder="Search phone or text..." value="{{ request('search') }}">
                    <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
                </div>

                @if(request()->anyFilled(['message_type', 'status', 'search']))
                    <a href="{{ route('sms.index') }}" class="btn btn-sm btn-outline-danger" title="Clear Filters">
                        <i class="bi bi-x-circle"></i>
                    </a>
                @endif
            </form>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4">Recipient</th>
                            <th>Type</th>
                            <th>Message Content</th>
                            <th>Gateway</th>
                            <th>Status</th>
                            <th>Timestamp</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-semibold text-dark">{{ $log->recipient_phone }}</div>
                                @if($log->consumer)
                                    <small class="text-muted">{{ $log->consumer->full_name }} ({{ $log->consumer->account_number }})</small>
                                @endif
                            </td>
                            <td>
                                @if($log->message_type === 'reminder')
                                    <span class="badge bg-primary bg-opacity-10 text-primary">Bill Reminder</span>
                                @elseif($log->message_type === 'overdue_alert')
                                    <span class="badge bg-danger bg-opacity-10 text-danger">Overdue Alert</span>
                                @elseif($log->message_type === 'payment_ack')
                                    <span class="badge bg-success bg-opacity-10 text-success">Payment Ack</span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary">Custom</span>
                                @endif
                            </td>
                            <td style="max-width: 380px;">
                                <div class="text-truncate text-secondary small" title="{{ $log->message }}">
                                    {{ $log->message }}
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $log->gateway_provider }}</span>
                            </td>
                            <td>
                                @if($log->status === 'sent')
                                    <span class="badge badge-sent px-2 py-1"><i class="bi bi-check-circle me-1"></i>Sent</span>
                                @elseif($log->status === 'simulated')
                                    <span class="badge badge-simulated px-2 py-1"><i class="bi bi-shield-check me-1"></i>Simulated</span>
                                @else
                                    <span class="badge badge-failed px-2 py-1" title="{{ $log->error_message }}"><i class="bi bi-x-circle me-1"></i>Failed</span>
                                @endif
                            </td>
                            <td class="text-muted small">
                                {{ $log->sent_at ? $log->sent_at->format('M d, Y h:i A') : $log->created_at->format('M d, Y h:i A') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-chat-square-quote display-6 d-block mb-2 text-muted opacity-50"></i>
                                No SMS notifications logged yet. Dispatch automated reminders above!
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($logs->hasPages())
        <div class="card-footer bg-white border-light py-3">
            <div class="d-flex justify-content-end">
                {{ $logs->links() }}
            </div>
        </div>
        @endif
    </div>
</div>

<div class="modal fade" id="testSmsModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form action="{{ route('sms.send-test') }}" method="POST">
                @csrf
                <div class="modal-header border-light">
                    <h5 class="modal-title fw-bold"><i class="bi bi-send text-primary me-2"></i>Send Custom SMS</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Recipient Mobile Number</label>
                        <input type="text" name="phone" class="form-control rounded-3" placeholder="09XXXXXXXXX or +639XXXXXXXXX" required>
                        <div class="form-text small">Standard Philippine mobile format (Smart, Globe, DITO).</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Message Content</label>
                        <textarea name="message" class="form-control rounded-3" rows="4" maxlength="160" placeholder="Type your SMS message..." required id="customSmsMsg"></textarea>
                        <div class="d-flex justify-content-between form-text small">
                            <span>Standard SMS: 1 credit per 160 characters.</span>
                            <span id="charCount">0/160</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-light">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Dispatch SMS</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="gatewaySettingsModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form action="{{ route('sms.update-settings') }}" method="POST">
                @csrf
                <div class="modal-header border-light">
                    <h5 class="modal-title fw-bold"><i class="bi bi-sliders text-secondary me-2"></i>SMS Gateway Configuration</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Third-Party Gateway Provider</label>
                        <select name="sms_gateway_provider" class="form-select rounded-3">
                            <option value="semaphore" {{ $currentProvider === 'semaphore' ? 'selected' : '' }}>Semaphore SMS (Philippines)</option>
                            <option value="philsms" {{ $currentProvider === 'philsms' ? 'selected' : '' }}>PhilSMS API (v3)</option>
                            <option value="generic" {{ $currentProvider === 'generic' ? 'selected' : '' }}>Generic HTTP Webhook</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Sender Name / ID</label>
                        <input type="text" name="sms_sender_name" class="form-control rounded-3" value="{{ $senderName }}" required maxlength="15">
                        <div class="form-text small">Approved alphanumeric Sender ID (e.g. AQUATANK or CARUHAYON).</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">API Key / Token</label>
                        <input type="password" name="sms_api_key" class="form-control rounded-3" value="{{ $apiKey }}" placeholder="Enter Gateway API Key">
                        <div class="form-text small">Leave blank to use Sandbox / Simulation Mode.</div>
                    </div>

                    <div class="form-check form-switch p-3 bg-light rounded-3">
                        <input class="form-check-input ms-0 me-2" type="checkbox" name="sms_simulation_mode" id="simModeCheck" value="1" {{ $simulationMode ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold text-dark" for="simModeCheck">
                            Sandbox / Simulation Mode (Recommended for Testing)
                        </label>
                        <p class="small text-muted mb-0 mt-1">When active, SMS messages are simulated, logged in database, and validated without spending real API balance.</p>
                    </div>
                </div>
                <div class="modal-footer border-light">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Save Configuration</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('js')
<script>
document.getElementById('customSmsMsg')?.addEventListener('input', function() {
    document.getElementById('charCount').textContent = this.value.length + '/160';
});
</script>
@endpush
@stop
