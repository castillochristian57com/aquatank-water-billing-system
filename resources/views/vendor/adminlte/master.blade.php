@php
    // master layout configuration
    $layoutFixed = config('adminlte.layout_fixed_sidebar');
    $fixedHeader = config('adminlte.layout_fixed_navbar');
    $fixedFooter = config('adminlte.layout_fixed_footer');
    $rtl = config('adminlte.layout_rtl', false);
    $sidebarBreakpoint = config('adminlte.sidebar_breakpoint', 'lg');
    $sidebarMini = config('adminlte.sidebar_mini');
    $sidebarCollapse = config('adminlte.sidebar_collapse');

    $bodyClasses = collect([
        $layoutFixed ? 'layout-fixed' : null,
        $fixedHeader ? 'fixed-header' : null,
        $fixedFooter ? 'fixed-footer' : null,
        'sidebar-expand-'.$sidebarBreakpoint,
        $sidebarMini ? 'sidebar-mini' : null,
        $sidebarCollapse ? 'sidebar-collapse' : null,
        'bg-light text-dark',
        config('adminlte.classes_body'),
    ])->filter()->implode(' ');

    $titlePrefix = config('adminlte.title_prefix', '');
    $titlePostfix = config('adminlte.title_postfix', '');
    $title = trim($titlePrefix.' '.($title ?? config('adminlte.title', 'AquaTank 2.0')).' '.$titlePostfix);
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      dir="{{ $rtl ? 'rtl' : 'ltr' }}"
      data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script>
        (function() {
            document.documentElement.setAttribute('data-bs-theme', 'light');
            document.documentElement.classList.remove('dark-mode');
            try {
                localStorage.setItem('theme', 'light');
                localStorage.setItem('color-mode', 'light');
                localStorage.setItem('adminlte:color-mode', 'light');
            } catch (e) {}
            if (typeof MutationObserver !== 'undefined') {
                new MutationObserver(function() {
                    if (document.documentElement.getAttribute('data-bs-theme') === 'dark') {
                        document.documentElement.setAttribute('data-bs-theme', 'light');
                    }
                    if (document.documentElement.classList.contains('dark-mode')) {
                        document.documentElement.classList.remove('dark-mode');
                    }
                }).observe(document.documentElement, { attributes: true, attributeFilter: ['data-bs-theme', 'class'] });
            }
        })();
    </script>

    <title>{{ $title }}</title>

    @hasSection('adminlte_css')
        @yield('adminlte_css')
    @endif

    @vite(['resources/css/adminlte.css', 'resources/js/adminlte.js'])

    @if ($rtl)
        <link rel="stylesheet" href="{{ asset('vendor/adminlte/css/adminlte.rtl.min.css') }}">
    @endif

    @include('adminlte::partials.theme-colors')

    <link rel="stylesheet" href="{{ asset('css/aquatank-light.css') }}?v={{ file_exists(public_path('css/aquatank-light.css')) ? filemtime(public_path('css/aquatank-light.css')) : time() }}">

    <style>
        /* guaranteed light theme override */
        html, body, .app-wrapper, .app-main, .content-wrapper {
            background-color: #f4f8fe !important;
            background: linear-gradient(180deg, #f0f7ff 0%, #e8f2fe 35%, #ffffff 100%) !important;
            background-attachment: fixed !important;
            color: #0f172a !important;
        }
        h1, h2, h3, h4, h5, h6, .text-dark, .app-content-header h1 {
            color: #0f172a !important;
            font-weight: 700 !important;
        }
        .text-muted {
            color: #334155 !important;
        }
    </style>

    @stack('css')
    @yield('css')
    @pluginStyles
</head>
<body class="{{ $bodyClasses }}">
    @include('adminlte::partials.preloader')

    @include('adminlte::partials.impersonation-banner')

    <div class="app-wrapper">
        @include('adminlte::partials.navbar')
        @include('adminlte::partials.sidebar')

        <main class="app-main">
            @hasSection('content_header')
                <div class="app-content-header {{ config('adminlte.classes_content_header') }}">
                    <div class="container-fluid">
                        @yield('content_header')
                    </div>
                </div>
            @endif

            <div class="app-content {{ config('adminlte.classes_content') }}">
                <div class="container-fluid">
                    @yield('content')
                </div>
            </div>
        </main>

        @include('adminlte::partials.footer')
        @include('adminlte::partials.control-sidebar')
    </div>

    @pluginScripts
    @stack('js')
    @yield('js')
</body>
</html>
