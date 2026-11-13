@php
    // active user details
    $user = auth()->user();
    $name = $user->name ?? ($user->email ?? 'Administrator');
    $initial = strtoupper(substr($name, 0, 1));
@endphp
<li class="nav-item dropdown user-menu">
    <a href="#" class="nav-link dropdown-toggle d-flex align-items-center gap-2 text-dark px-2" data-bs-toggle="dropdown" aria-expanded="false">
        <div class="rounded-circle d-flex align-items-center justify-content-center text-white shadow-sm"
             style="width: 32px; height: 32px; background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); font-weight: 700; font-size: 0.85rem;">
            {{ $initial }}
        </div>
        <span class="d-none d-md-inline fw-semibold text-dark">{{ $name }}</span>
    </a>
    <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end shadow border-0 rounded-4 mt-2 overflow-hidden">
        {{-- user header --}}
        <li class="p-3 text-center text-white" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);">
            <div class="rounded-circle d-inline-flex align-items-center justify-content-center bg-white text-primary fw-bold shadow-sm mb-2"
                 style="width: 54px; height: 54px; font-size: 1.35rem;">
                {{ $initial }}
            </div>
            <p class="mb-0 fw-bold text-white fs-6">{{ $name }}</p>
            <small class="text-white opacity-75">{{ $user?->email ?? '' }}</small>
        </li>

        {{-- user actions --}}
        <li class="p-2 d-flex justify-content-between align-items-center bg-light">
            <a href="{{ route('profile.edit') }}" class="btn btn-sm btn-outline-primary rounded-3 px-3">
                <i class="bi bi-person-gear me-1"></i> Profile
            </a>
            <a href="#" class="btn btn-sm btn-outline-danger rounded-3 px-3"
               onclick="event.preventDefault(); document.getElementById('adminlte-logout-form').submit();">
                <i class="bi bi-box-arrow-right me-1"></i> Sign Out
            </a>
            <form id="adminlte-logout-form" action="{{ url('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </li>
    </ul>
</li>
