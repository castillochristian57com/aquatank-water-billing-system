<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'AquaTank') }} | Staff Login</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Montserrat:wght@500;600;700;800&display=swap" rel="stylesheet">

        <!-- Bootstrap Icons -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            :root {
                --primary-blue: #4a6cf7;
                --primary-blue-dark: #2563eb;
                --primary-dark: #111827;
                --light-bg: #f8faff;
                --text-gray: #6b7280;
            }

            body {
                font-family: 'Inter', sans-serif !important;
                color: var(--primary-dark);
                background-color: #ffffff;
                background-image: 
                    radial-gradient(circle at 85% 20%, rgba(74, 108, 247, 0.12), transparent 45%),
                    radial-gradient(circle at 15% 85%, rgba(6, 182, 212, 0.1), transparent 45%),
                    #ffffff;
                min-height: 100vh;
            }

            h1, h2, h3, h4, h5, h6, .brand-title {
                font-family: 'Montserrat', sans-serif !important;
                letter-spacing: -0.02em;
            }

            .glass-card {
                background: rgba(255, 255, 255, 0.85);
                backdrop-filter: blur(16px);
                -webkit-backdrop-filter: blur(16px);
                border: 1px solid rgba(229, 231, 235, 0.8);
                border-radius: 20px;
                box-shadow: 0 16px 40px rgba(37, 99, 235, 0.08);
            }

            .btn-primary-custom {
                background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-blue-dark) 100%);
                color: #ffffff !important;
                border-radius: 30px;
                padding: 12px 30px;
                font-family: 'Montserrat', sans-serif;
                font-weight: 600;
                font-size: 13.5px;
                border: none;
                transition: all 0.3s ease;
                letter-spacing: 0.5px;
                text-transform: uppercase;
                box-shadow: 0 4px 15px rgba(37, 99, 235, 0.25);
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                width: 100%;
                cursor: pointer;
                text-decoration: none;
            }

            .btn-primary-custom:hover {
                background: linear-gradient(135deg, var(--primary-blue-dark) 0%, var(--primary-blue) 100%);
                transform: translateY(-2px);
                box-shadow: 0 8px 22px rgba(37, 99, 235, 0.35);
            }

            .auth-input {
                width: 100%;
                padding: 12px 16px;
                border-radius: 12px;
                border: 1.5px solid #cbd5e1;
                font-size: 14.5px;
                font-family: 'Inter', sans-serif;
                color: #111827;
                background-color: #ffffff;
                transition: all 0.2s ease;
            }

            .auth-input:focus {
                outline: none;
                border-color: var(--primary-blue);
                box-shadow: 0 0 0 4px rgba(74, 108, 247, 0.15);
            }
        </style>
    </head>
    <body class="antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center py-8 px-4">
            <div class="text-center mb-4">
                <a href="/" class="d-inline-flex flex-column align-items-center text-decoration-none">
                    <div class="flex items-center justify-center gap-3 mb-2">
                        <img src="{{ asset('photos/brgylogo.jpeg') }}" alt="Barangay Caruhayon Logo" class="rounded-circle shadow-xs" style="width: 48px; height: 48px; object-fit: contain;">
                        <img src="{{ asset('photos/Aquatank logo.png') }}" alt="AquaTank Logo" style="width: 48px; height: 48px; object-fit: contain;">
                    </div>
                    <h2 class="brand-title text-2xl font-extrabold text-gray-900 mt-1 mb-0">AquaTank</h2>
                    <p class="text-xs font-semibold text-gray-500 tracking-wider uppercase mt-1">Water Billing System &bull; Staff Portal</p>
                </a>
            </div>

            <div class="w-full sm:max-w-md px-8 py-7 glass-card">
                {{ $slot }}
            </div>

            <div class="mt-6 text-center">
                <a href="/" class="text-sm font-medium text-gray-500 hover:text-blue-600 transition-colors inline-flex items-center gap-1.5 text-decoration-none">
                    <i class="bi bi-arrow-left"></i> Return to Homepage
                </a>
            </div>
        </div>
    </body>
</html>
