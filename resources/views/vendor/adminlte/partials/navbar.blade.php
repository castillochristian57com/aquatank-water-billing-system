@php
    // navbar left menu items
    $navLeft = app('adminlte')->menu('navbar-left');
@endphp
<nav class="app-header {{ config('adminlte.classes_topnav', 'navbar-expand bg-white border-bottom shadow-xs') }} navbar">
    <div class="{{ config('adminlte.classes_topnav_container', 'container-fluid') }}">
        {{-- left navigation --}}
        <ul class="navbar-nav align-items-center">
            <li class="nav-item">
                <a class="nav-link px-2" data-lte-toggle="sidebar" href="#" role="button" aria-label="{{ __('Toggle sidebar') }}">
                    <i class="bi bi-list fs-5 text-dark" aria-hidden="true"></i>
                </a>
            </li>

            <li class="nav-item d-none d-md-block">
                <a href="{{ url('/dashboard') }}" class="nav-link px-3 text-dark fw-semibold">
                    <i class="bi bi-speedometer2 text-primary me-1" aria-hidden="true"></i> {{ __('Dashboard') }}
                </a>
            </li>

            <li class="nav-item d-none d-md-block">
                <a href="{{ url('/') }}" class="nav-link px-2 text-muted" target="_blank">
                    <i class="bi bi-box-arrow-up-right me-1" aria-hidden="true"></i> {{ __('Public Site') }}
                </a>
            </li>

            @foreach ($navLeft as $item)
                <li class="nav-item d-none d-md-block">
                    <a href="{{ $item['href'] ?? '#' }}" class="nav-link text-dark">{{ $item['text'] ?? '' }}</a>
                </li>
            @endforeach
        </ul>

        <div class="position-relative mx-lg-4 mx-2 d-none d-sm-block flex-grow-1" style="max-width: 460px;">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-light border-end-0 rounded-start-pill ps-3 text-primary">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text"
                       id="topbarConsumerSearch"
                       class="form-control bg-light border-start-0 border-end-0 py-1"
                       placeholder="Search consumer, account #, meter #..."
                       autocomplete="off">
                <span class="input-group-text bg-light border-start-0 rounded-end-pill pe-3" id="topbarSearchSpinner" style="display: none;">
                    <span class="spinner-border spinner-border-sm text-primary" role="status" style="width: 0.85rem; height: 0.85rem;"></span>
                </span>
            </div>

            <div id="topbarSearchDropdown" class="dropdown-menu w-100 shadow-lg border-0 rounded-3 mt-1 p-2" style="display: none; position: absolute; top: 100%; left: 0; z-index: 1060; max-height: 420px; overflow-y: auto;">
                <div id="topbarSearchResults"></div>
            </div>
        </div>

        {{-- right navigation with clean user menu --}}
        <ul class="navbar-nav ms-auto align-items-center">
            {{-- fullscreen toggle --}}
            <li class="nav-item">
                <a class="nav-link px-2" href="#" data-lte-toggle="fullscreen" aria-label="{{ __('Toggle fullscreen') }}" title="Toggle Fullscreen">
                    <i data-lte-icon="maximize" class="bi bi-arrows-fullscreen text-dark" aria-hidden="true"></i>
                    <i data-lte-icon="minimize" class="bi bi-fullscreen-exit text-dark d-none" aria-hidden="true"></i>
                </a>
            </li>

            {{-- user menu without broken avatar --}}
            @if (config('adminlte.usermenu_enabled', true))
                @include('adminlte::partials.usermenu')
            @endif
        </ul>
    </div>
</nav>

<script>
    // Universal consumer search autocomplete handler
    document.addEventListener('DOMContentLoaded', function () {
        function setupConsumerAutocomplete(inputId, dropdownId, resultsId, spinnerId = null) {
            const input = document.getElementById(inputId);
            const dropdown = document.getElementById(dropdownId);
            const results = document.getElementById(resultsId);
            const spinner = spinnerId ? document.getElementById(spinnerId) : null;

            if (!input || !dropdown || !results) return;

            let debounceTimeout = null;

            input.addEventListener('input', function () {
                const query = this.value.trim();
                clearTimeout(debounceTimeout);

                if (query.length === 0) {
                    dropdown.style.display = 'none';
                    results.innerHTML = '';
                    if (spinner) spinner.style.display = 'none';
                    return;
                }

                if (spinner) spinner.style.display = 'inline-block';

                debounceTimeout = setTimeout(() => {
                    fetch(`{{ route('consumers.autocomplete') }}?q=${encodeURIComponent(query)}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (spinner) spinner.style.display = 'none';

                        if (!Array.isArray(data) || data.length === 0) {
                            results.innerHTML = `
                                <div class="p-3 text-center text-muted small">
                                    <i class="bi bi-person-x fs-4 d-block mb-1 text-secondary"></i>
                                    No consumers found matching "<strong>${escapeHtml(query)}</strong>"
                                </div>
                            `;
                            dropdown.style.display = 'block';
                            return;
                        }

                        let html = `<div class="px-2 py-1 text-muted small fw-bold text-uppercase border-bottom mb-1" style="font-size: 0.7rem;">Matching Consumers (${data.length})</div>`;
                        data.forEach(c => {
                            const statusBadge = c.status === 'active'
                                ? '<span class="badge bg-success-subtle text-success text-uppercase" style="font-size: 0.65rem;">Active</span>'
                                : '<span class="badge bg-secondary-subtle text-secondary text-uppercase" style="font-size: 0.65rem;">Inactive</span>';

                            html += `
                                <div class="dropdown-item p-2 rounded-2 d-flex justify-content-between align-items-center mb-1" style="cursor: pointer; transition: background 0.15s ease;">
                                    <a href="${c.view_url}" class="text-decoration-none text-dark flex-grow-1 me-2">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="fw-bold" style="font-size: 0.9rem;">${escapeHtml(c.name)}</div>
                                            ${statusBadge}
                                        </div>
                                        <div class="d-flex flex-wrap gap-2 text-muted small mt-0.5" style="font-size: 0.78rem;">
                                            <span><i class="bi bi-credit-card-2-front text-primary me-1"></i><strong class="font-monospace">${escapeHtml(c.account_number)}</strong></span>
                                            <span>&bull;</span>
                                            <span><i class="bi bi-speedometer2 text-info me-1"></i>Meter: <strong class="font-monospace">${escapeHtml(c.meter_number)}</strong></span>
                                            ${c.contact_number ? `<span>&bull;</span><span><i class="bi bi-telephone text-success me-1"></i>${escapeHtml(c.contact_number)}</span>` : ''}
                                        </div>
                                    </a>
                                    <div class="d-flex gap-1">
                                        <a href="${c.view_url}" class="btn btn-xs btn-outline-primary py-1 px-2" title="View Profile" style="font-size: 0.75rem;">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                        <a href="${c.bill_url}" class="btn btn-xs btn-primary py-1 px-2" title="Encode Bill" style="font-size: 0.75rem; background: linear-gradient(135deg, #0284c7 0%, #155eef 100%); border: none;">
                                            <i class="bi bi-receipt"></i> Bill
                                        </a>
                                    </div>
                                </div>
                            `;
                        });

                        results.innerHTML = html;
                        dropdown.style.display = 'block';
                    })
                    .catch(err => {
                        if (spinner) spinner.style.display = 'none';
                        console.error('Autocomplete fetch error:', err);
                    });
                }, 250);
            });

            // Close when clicking outside
            document.addEventListener('click', function (e) {
                if (!input.contains(e.target) && !dropdown.contains(e.target)) {
                    dropdown.style.display = 'none';
                }
            });

            // Close on escape key
            input.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    dropdown.style.display = 'none';
                }
            });
        }

        function escapeHtml(str) {
            if (!str) return '';
            return String(str).replace(/[&<>"']/g, function (m) {
                return ({
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;'
                })[m];
            });
        }

        // Initialize topbar search
        setupConsumerAutocomplete('topbarConsumerSearch', 'topbarSearchDropdown', 'topbarSearchResults', 'topbarSearchSpinner');

        // Expose globally so other pages can easily attach it
        window.setupConsumerAutocomplete = setupConsumerAutocomplete;
    });
</script>
