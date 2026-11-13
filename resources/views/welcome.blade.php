<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AquaTank | Water Billing System - Barangay Caruhayon</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Montserrat:wght@500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --primary-blue: #4a6cf7;
            --primary-blue-dark: #2563eb;
            --primary-dark: #111827;
            --light-bg: #f8faff;
            --text-gray: #6b7280;
            --card-border: rgba(229, 231, 235, 0.8);
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Inter', sans-serif;
            color: var(--text-gray);
            background-color: #ffffff;
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden;
        }

        h1, h2, h3, h4, h5, h6, .navbar-brand, .btn-primary-custom, .section-title, .calc-brand {
            font-family: 'Montserrat', sans-serif;
            color: var(--primary-dark);
            letter-spacing: -0.02em;
        }

        .text-primary-blue { color: var(--primary-blue) !important; }
        .text-primary-dark { color: var(--primary-dark) !important; }
        .bg-light-bg { background-color: var(--light-bg) !important; }

        /* Gradient Backgrounds */
        .bg-gradient-light {
            background: linear-gradient(180deg, #ffffff 0%, #f0f4ff 100%);
        }

        /* Frosted Glass Utilities */
        .glass-nav {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(229, 231, 235, 0.6);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(229, 231, 235, 0.9);
            border-radius: 16px;
            padding: 36px 30px;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.03);
            height: 100%;
        }

        .glass-card:hover {
            background: rgba(255, 255, 255, 0.95);
            transform: translateY(-5px);
            box-shadow: 0 16px 35px rgba(37, 99, 235, 0.08);
            border-color: rgba(74, 108, 247, 0.25);
        }

        /* Buttons */
        .btn-primary-custom {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-blue-dark) 100%);
            color: #ffffff !important;
            border-radius: 30px;
            padding: 12px 30px;
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
            text-decoration: none;
        }

        .btn-primary-custom:hover {
            background: linear-gradient(135deg, var(--primary-blue-dark) 0%, var(--primary-blue) 100%);
            color: #ffffff !important;
            transform: translateY(-2px);
            box-shadow: 0 8px 22px rgba(37, 99, 235, 0.35);
        }

        .btn-secondary-custom {
            background: #ffffff;
            color: var(--primary-dark) !important;
            border: 1.5px solid #e5e7eb;
            border-radius: 30px;
            padding: 12px 28px;
            font-weight: 600;
            font-size: 13.5px;
            transition: all 0.3s ease;
            letter-spacing: 0.3px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-secondary-custom:hover {
            border-color: var(--primary-blue);
            color: var(--primary-blue) !important;
            background: var(--light-bg);
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.05);
        }

        .btn-hero-action {
            min-width: 220px;
            padding: 15px 36px;
            font-size: 14.5px;
            letter-spacing: 0.4px;
        }

        .btn-admin-custom {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-gray);
            padding: 7px 16px;
            border: 1px solid #e5e7eb;
            border-radius: 20px;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            background: rgba(255, 255, 255, 0.9);
        }

        .btn-admin-custom:hover {
            border-color: var(--primary-blue);
            color: var(--primary-blue);
            background: #eff6ff;
        }

        /* Navbar Navigation & Underline Animation */
        .navbar {
            padding-top: 1.1rem;
            padding-bottom: 1.1rem;
            transition: all 0.3s ease;
        }

        .navbar-brand img {
            height: 38px;
            width: auto;
            object-fit: contain;
        }

        .nav-link-custom {
            color: var(--text-gray);
            font-weight: 500;
            font-size: 14px;
            padding: 8px 14px !important;
            margin: 0 4px;
            transition: color 0.3s ease;
            text-decoration: none;
        }

        .nav-link-custom:hover, .nav-link-custom.active {
            color: var(--primary-dark);
            font-weight: 600;
        }

        /* 60fps GPU-accelerated sliding indicator */
        .nav-indicator {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 24px;
            height: 2.5px;
            border-radius: 2px;
            background: linear-gradient(90deg, var(--primary-blue), var(--primary-blue-dark));
            transform-origin: center;
            will-change: transform;
            pointer-events: none;
            transition: none;
        }

        .nav-indicator.ready {
            transition: transform 0.35s cubic-bezier(0.2, 0.8, 0.2, 1);
        }

        /* Section Badges */
        .section-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--primary-blue-dark);
            background: #eef4ff;
            padding: 6px 16px;
            border-radius: 30px;
            margin-bottom: 16px;
            border: 1px solid #dbeafe;
        }

        /* Hero Section */
        .hero-section {
            min-height: 100vh;
            padding-top: 80px;
        }

        .hero-carousel-bg {
            background: radial-gradient(circle at 85% 20%, rgba(74, 108, 247, 0.12), transparent 45%),
                        radial-gradient(circle at 15% 85%, rgba(6, 182, 212, 0.1), transparent 45%),
                        #ffffff;
        }

        /* Accordion (Frosted Glass) */
        .accordion-item {
            background: rgba(255, 255, 255, 0.65) !important;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(229, 231, 235, 0.8) !important;
            border-radius: 14px !important;
            margin-bottom: 16px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
            transition: all 0.25s ease;
        }

        .accordion-item:hover {
            border-color: rgba(74, 108, 247, 0.3) !important;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.04);
        }

        .accordion-button {
            padding: 22px 24px;
            background: transparent !important;
            color: var(--primary-dark) !important;
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
            font-size: 16px;
            box-shadow: none !important;
        }

        .accordion-button:not(.collapsed) {
            color: var(--primary-blue) !important;
        }

        .accordion-button::after {
            filter: grayscale(1);
            transition: transform 0.25s ease;
        }

        .accordion-body {
            padding: 0 24px 24px 24px;
            color: var(--text-gray);
            font-weight: 300;
            line-height: 1.8;
            font-size: 15px;
        }

        /* Service Cards styling */
        .service-icon-box {
            width: 54px;
            height: 54px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #eff6ff;
            color: var(--primary-blue);
            margin-bottom: 22px;
            transition: all 0.3s ease;
        }

        .glass-card:hover .service-icon-box {
            background: var(--primary-blue);
            color: #ffffff;
            transform: scale(1.05);
        }

        /* Calculator Card */
        .calculator-card {
            background: #ffffff;
            border-radius: 24px;
            border: 1.5px solid #e0f2fe;
            box-shadow: 0 20px 45px rgba(15, 43, 91, 0.06);
            overflow: hidden;
        }

        .calc-header {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-blue-dark) 100%);
            color: #ffffff;
            padding: 32px 40px;
        }

        .calc-input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .calc-input {
            width: 100%;
            padding: 14px 18px;
            padding-right: 60px;
            border-radius: 12px;
            border: 1.5px solid #cbd5e1;
            font-size: 16px;
            font-family: 'Inter', sans-serif;
            font-weight: 600;
            color: var(--primary-dark);
            transition: all 0.2s ease;
        }

        .calc-input:focus {
            outline: none;
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 4px rgba(74, 108, 247, 0.15);
        }

        .calc-unit {
            position: absolute;
            right: 18px;
            color: #94a3b8;
            font-weight: 600;
            font-size: 14px;
            pointer-events: none;
        }

        .calc-error {
            color: #ef4444;
            font-size: 13px;
            font-weight: 600;
            margin-top: 8px;
            display: none;
        }

        .calc-result-box {
            background: #f8faff;
            border: 2px dashed #bfdbfe;
            border-radius: 20px;
            padding: 36px 28px;
            text-align: center;
        }

        .result-amount {
            font-family: 'Montserrat', sans-serif;
            font-size: 44px;
            font-weight: 800;
            color: var(--primary-blue);
            line-height: 1.1;
            margin-bottom: 12px;
        }

        .tariff-tag {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(8px);
            padding: 8px 18px;
            border-radius: 30px;
            font-weight: 700;
            font-size: 13.5px;
            border: 1px solid rgba(255, 255, 255, 0.4);
            color: #ffffff;
        }

        /* Footer */
        .footer-minimal {
            background-color: #ffffff;
            border-top: 1px solid #e5e7eb;
            padding: 80px 0 40px;
        }

        .footer-links a {
            color: var(--text-gray);
            font-size: 14px;
            text-decoration: none;
            margin: 0 12px;
            transition: color 0.2s ease;
        }

        .footer-links a:hover {
            color: var(--primary-blue);
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg glass-nav fixed-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ url('/') }}" style="letter-spacing: -0.5px;">
                <img src="{{ asset('photos/brgylogo.jpeg') }}" alt="Barangay Caruhayon Logo" class="rounded-circle shadow-sm" style="height: 38px; width: 38px; object-fit: cover;">
                <img src="{{ asset('photos/Aquatank logo.png') }}" alt="AquaTank Logo" style="height: 36px; width: auto; object-fit: contain;">
                <div class="d-flex flex-column ms-1">
                    <span class="fw-bold fs-5 text-primary-dark lh-1">AquaTank</span>
                    <span class="text-muted fw-semibold" style="font-size: 10px; letter-spacing: 0.8px; text-transform: uppercase;">Brgy. Caruhayon</span>
                </div>
            </a>

            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav me-lg-4 align-items-lg-center position-relative my-3 my-lg-0" id="navContainer">
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom active" href="#home">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom" href="#services">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom" href="#bill-calculator">Calculator</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom" href="#billing-info">Billing Info</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom" href="#about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom" href="#contact">Contact</a>
                    </li>
                    <div id="nav-indicator" class="nav-indicator d-none d-lg-block"></div>
                </ul>

                <div class="d-flex align-items-center gap-3">
                    <a href="{{ route('portal.my-water-bill') }}" class="btn-primary-custom">
                        <i class="bi bi-droplet-half"></i> My Water Bill
                    </a>

                    @auth
                        <a href="{{ route('dashboard') }}" class="btn-admin-custom">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn-admin-custom">
                            <i class="bi bi-lock-fill"></i> Staff Login
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>


    <section id="home" class="hero-section position-relative overflow-hidden d-flex align-items-center hero-carousel-bg">

        <div id="heroCarousel" class="carousel slide carousel-fade position-absolute top-0 start-0 w-100 h-100" data-bs-ride="carousel" style="z-index: 0;">
            <div class="carousel-inner w-100 h-100">
                <div class="carousel-item active w-100 h-100 position-relative">
                    <img src="{{ asset('photos/brgyhall.png') }}" class="d-block w-100 h-100" style="object-fit: cover;" alt="Water Infrastructure" onerror="this.style.display='none'">
                    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: radial-gradient(circle at 80% 20%, rgba(74, 108, 247, 0.15), transparent 60%);"></div>
                </div>
                <div class="carousel-item w-100 h-100 position-relative">
                    <img src="{{ asset('photos/tank.png') }}" class="d-block w-100 h-100" style="object-fit: cover;" alt="Records and Analytics" onerror="this.style.display='none'">
                    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: radial-gradient(circle at 20% 80%, rgba(6, 182, 212, 0.15), transparent 60%);"></div>
                </div>
                <div class="carousel-item w-100 h-100 position-relative">
                    <img src="{{ asset('photos/reading.png') }}" class="d-block w-100 h-100" style="object-fit: cover;" alt="Water Distribution" onerror="this.style.display='none'">
                    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: radial-gradient(circle at 50% 50%, rgba(37, 99, 235, 0.12), transparent 70%);"></div>
                </div>
            </div>
            <div class="position-absolute top-0 start-0 w-100 h-100 bg-white" style="opacity: 0.92; z-index: 1;"></div>
        </div>

        <div class="container position-relative py-5" style="z-index: 2;">
            <div class="row align-items-center g-5 mt-2">
                <div class="col-lg-8 col-xl-7">
                    <span class="section-badge">
                        <i class="bi bi-droplet-fill text-primary-blue"></i> Barangay Caruhayon Community Water Service
                    </span>

                    <h1 class="display-3 fw-bolder mb-4 text-primary-dark">
                        Smarter water billing for modern barangays.
                    </h1>

                    <p class="fs-5 text-muted mb-5 fw-light" style="line-height: 1.8; max-width: 92%;">
                        An elegant, web-based platform designed to simplify billing, consumer records, payment monitoring, and SMS notifications for Barangay Caruhayon.
                    </p>

                    <div class="d-flex flex-wrap gap-4">
                        <a href="{{ route('portal.my-water-bill') }}" class="btn-primary-custom btn-hero-action shadow-none">
                            Get Started <i class="bi bi-arrow-right"></i>
                        </a>

                        <a href="#bill-calculator" class="btn-secondary-custom btn-hero-action">
                            <i class="bi bi-calculator"></i> Calculate Bill
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <section id="services" class="bg-light-bg" style="padding: 110px 0;">
        <div class="container">
            <div class="text-center mb-5 pb-2">
                <span class="section-badge">Platform Capabilities</span>
                <h2 class="display-6 fw-bolder mb-3">Core Services</h2>
                <p class="text-muted fw-light mx-auto" style="max-width: 620px; font-size: 16px;">
                    AquaTank brings modern utility management tools to staff and transparent digital services to residents.
                </p>
            </div>

            <div class="row g-4">
                <div class="col-md-6 col-lg-4">
                    <div class="glass-card">
                        <div class="service-icon-box">
                            <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                        </div>
                        <h5 class="fw-bold mb-3">Consumer Records</h5>
                        <p class="fw-light mb-0" style="line-height: 1.8;">
                            Store and organize consumer accounts, meter serial numbers, and household profiles for effortless retrieval.
                        </p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="glass-card">
                        <div class="service-icon-box">
                            <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                <line x1="16" y1="17" x2="8" y2="17"></line>
                                <polyline points="10 9 9 9 8 9"></polyline>
                            </svg>
                        </div>
                        <h5 class="fw-bold mb-3">Water Billing</h5>
                        <p class="fw-light mb-0" style="line-height: 1.8;">
                            Calculate water bills automatically based on recorded meter readings and the official community tariff.
                        </p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="glass-card">
                        <div class="service-icon-box">
                            <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                                <line x1="1" y1="10" x2="23" y2="10"></line>
                            </svg>
                        </div>
                        <h5 class="fw-bold mb-3">Payment Tracking</h5>
                        <p class="fw-light mb-0" style="line-height: 1.8;">
                            Record cash payments over-the-counter and monitor outstanding consumer balances with complete transparency.
                        </p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="glass-card">
                        <div class="service-icon-box">
                            <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect>
                                <line x1="12" y1="18" x2="12.01" y2="18"></line>
                            </svg>
                        </div>
                        <h5 class="fw-bold mb-3">SMS Notifications</h5>
                        <p class="fw-light mb-0" style="line-height: 1.8;">
                            Send automated SMS text alerts regarding newly issued billing notices, due dates, and payment receipts.
                        </p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="glass-card">
                        <div class="service-icon-box">
                            <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="18" y1="20" x2="18" y2="10"></line>
                                <line x1="12" y1="20" x2="12" y2="4"></line>
                                <line x1="6" y1="20" x2="6" y2="14"></line>
                            </svg>
                        </div>
                        <h5 class="fw-bold mb-3">Reports & Analytics</h5>
                        <p class="fw-light mb-0" style="line-height: 1.8;">
                            Generate exportable financial summaries, monthly consumption reports, and collection auditing logs.
                        </p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="glass-card">
                        <div class="service-icon-box">
                            <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                        </div>
                        <h5 class="fw-bold mb-3">Secure Access</h5>
                        <p class="fw-light mb-0" style="line-height: 1.8;">
                            Role-based access controls ensure only authorized barangay staff can create bills or modify sensitive consumer records.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <section id="bill-calculator" style="padding: 110px 0; background-color: #ffffff;">
        <div class="container">
            <div class="calculator-card mx-auto" style="max-width: 980px;">
                <div class="calc-header d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div>
                        <h3 class="fw-bold mb-1 text-white"><i class="bi bi-calculator me-2"></i> Public Water Bill Calculator</h3>
                        <p class="mb-0 text-white-50" style="font-size: 14.5px;">Enter your previous and current meter readings to estimate your amount due.</p>
                    </div>
                    <div class="tariff-tag">
                        <i class="bi bi-tag-fill me-1"></i> Rate: ₱15.00 / m³
                    </div>
                </div>

                <div class="p-4 p-lg-5">
                    <div class="row g-5 align-items-center">
                        <div class="col-lg-6">
                            <div class="mb-4">
                                <label for="prevReading" class="form-label fw-semibold text-primary-dark">
                                    <i class="bi bi-clock-history text-primary-blue me-1"></i> Previous Meter Reading
                                </label>
                                <div class="calc-input-wrapper">
                                    <input type="number" step="0.01" min="0" id="prevReading" class="calc-input" placeholder="0.00" value="">
                                    <span class="calc-unit">m³</span>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="currReading" class="form-label fw-semibold text-primary-dark">
                                    <i class="bi bi-speedometer2 text-primary-blue me-1"></i> Current Meter Reading
                                </label>
                                <div class="calc-input-wrapper">
                                    <input type="number" step="0.01" min="0" id="currReading" class="calc-input" placeholder="0.00" value="">
                                    <span class="calc-unit">m³</span>
                                </div>
                                <div id="calcError" class="calc-error">
                                    <i class="bi bi-exclamation-circle-fill me-1"></i> Current reading cannot be less than previous reading.
                                </div>
                            </div>

                            <p class="text-muted small mb-0" style="line-height: 1.6;">
                                <i class="bi bi-info-circle me-1"></i> This is a public estimation tool and does not alter official barangay records.
                            </p>
                        </div>

                        <div class="col-lg-6">
                            <div class="calc-result-box">
                                <div class="text-uppercase fw-bold text-muted small letter-spacing-1 mb-2">Estimated Amount Due</div>
                                <div class="result-amount" id="estimatedBill">₱375.00</div>

                                <div class="d-flex justify-content-center gap-3 my-4">
                                    <span class="badge bg-white text-primary-dark border px-3 py-2 fw-semibold fs-6">
                                        Usage: <strong id="calcConsumption" class="text-primary-blue">25.00 m³</strong>
                                    </span>
                                    <span class="badge bg-white text-primary-dark border px-3 py-2 fw-semibold fs-6">
                                        Tariff: <strong>₱15.00 / m³</strong>
                                    </span>
                                </div>

                                <a href="{{ route('portal.my-water-bill') }}" class="btn-primary-custom w-100">
                                    View Official Statement &rarr;
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <section id="billing-info" class="bg-light-bg" style="padding: 100px 0;">
        <div class="container">
            <div class="text-center mb-5">
                <span class="section-badge">Consumer Guide</span>
                <h2 class="display-6 fw-bolder mb-2">Water Bill Information</h2>
                <p class="text-muted fw-light mx-auto" style="max-width: 600px;">
                    Guidelines and schedules for all registered water consumers of Barangay Caruhayon.
                </p>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="glass-card">
                        <div class="service-icon-box">
                            <i class="bi bi-calendar-check fs-4"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Reading Schedule</h5>
                        <p class="text-muted fw-light mb-0" style="line-height: 1.8;">
                            Official meter readers inspect physical consumer meters between the <strong>1st and 5th</strong> day of each month.
                        </p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="glass-card">
                        <div class="service-icon-box">
                            <i class="bi bi-cash-coin fs-4"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Tariff Rate</h5>
                        <p class="text-muted fw-light mb-0" style="line-height: 1.8;">
                            Barangay Caruhayon applies a flat community tariff rate of <strong>₱15.00 per cubic meter ($m^3$)</strong>.
                        </p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="glass-card">
                        <div class="service-icon-box">
                            <i class="bi bi-building fs-4"></i>
                        </div>
                        <h5 class="fw-bold mb-2">OTC Payment Policy</h5>
                        <p class="text-muted fw-light mb-0" style="line-height: 1.8;">
                            Payments are accepted strictly <strong>Over-the-Counter</strong> at the Barangay Treasury Office within 15 days of bill issuance.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <section id="about" style="padding: 120px 0; background: #ffffff;">
        <div class="container">
            <div class="row justify-content-between align-items-center g-5">

                <div class="col-lg-5">
                    <span class="section-badge">About AquaTank</span>
                    <h2 class="display-5 fw-bolder mb-4">Simplicity in <br>every drop.</h2>
                    <p class="text-muted fw-light mb-4" style="line-height: 1.9; font-size: 16.5px;">
                        AquaTank eliminates the manual hassle of managing community water utility services.
                        We provide an all-in-one platform for computing meter readings, tracking payments,
                        and empowering consumers with real-time bill inquiry tailored to Barangay Caruhayon, Juban, Sorsogon.
                    </p>
                    <a href="{{ route('portal.my-water-bill') }}" class="btn-primary-custom">
                        Access Portal <i class="bi bi-arrow-right"></i>
                    </a>
                </div>

                <div class="col-lg-7">
                    <div class="accordion" id="aboutAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingOne">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true">
                                    Which services does AquaTank provide?
                                </button>
                            </h2>
                            <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#aboutAccordion">
                                <div class="accordion-body">
                                    AquaTank provides automated water bill computation, official monthly billing statement generation, cash payment recording with receipt logging, and a 24/7 public consumer billing verification portal.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingTwo">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false">
                                    What do residents need to check their water bill?
                                </button>
                            </h2>
                            <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#aboutAccordion">
                                <div class="accordion-body">
                                    Residents only need their registered Consumer Account Number or Water Meter Number to instantly view their current bill, payment history, and official receipts without requiring an account login.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingThree">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false">
                                    Is consumer data secure?
                                </button>
                            </h2>
                            <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#aboutAccordion">
                                <div class="accordion-body">
                                    Yes. All consumer information and financial transaction logs are protected with strict role-based access control, ensuring only authorized barangay officials can update or manage records.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingFour">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false">
                                    Where are payments processed?
                                </button>
                            </h2>
                            <div id="collapseFour" class="accordion-collapse collapse" data-bs-parent="#aboutAccordion">
                                <div class="accordion-body">
                                    Water payments are settled Over-the-Counter at the Barangay Treasury Office in the Caruhayon Barangay Hall, where official printed receipts are handed directly to consumers upon payment.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>


    <section id="contact" class="bg-light-bg" style="padding: 100px 0;">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-6 fw-bolder mb-2">Contact Barangay Hall Office</h2>
                <p class="text-muted fw-light mx-auto" style="max-width: 600px;">
                    Have questions about your meter, monthly reading, or billing record? Our office is here to help.
                </p>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="glass-card text-center p-4">
                        <div class="service-icon-box mx-auto">
                            <i class="bi bi-geo-alt-fill fs-4"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Office Location</h5>
                        <p class="text-muted small mb-0" style="line-height: 1.8;">
                            Purok 3, Barangay Hall, Barangay Caruhayon<br>Juban, Sorsogon 4703
                        </p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="glass-card text-center p-4">
                        <div class="service-icon-box mx-auto">
                            <i class="bi bi-telephone-fill fs-4"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Customer Support</h5>
                        <p class="text-muted small mb-0" style="line-height: 1.8;">
                            0917-123-CARU (2278)<br>Mon - Fri, 8:00 AM - 5:00 PM
                        </p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="glass-card text-center p-4">
                        <div class="service-icon-box mx-auto">
                            <i class="bi bi-envelope-fill fs-4"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Official Inquiries</h5>
                        <p class="text-muted small mb-0" style="line-height: 1.8;">
                            caruhayon.water@aquatank.local<br>Official Community Water Portal
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <footer class="footer-minimal text-center">
        <div class="container">
            <a class="text-decoration-none fw-bold fs-4 text-primary-dark d-inline-flex align-items-center gap-2 mb-3" href="#" style="letter-spacing: -0.5px;">
                <img src="{{ asset('photos/Aquatank logo.png') }}" alt="AquaTank Logo" style="height: 32px; width: auto; object-fit: contain;">
                <span>AquaTank</span>
            </a>

            <p class="mb-1 text-muted fw-light" style="font-size: 15px;">
                Web-Based Water Billing System for Barangay Caruhayon
            </p>
            <p class="mb-4 text-muted fw-light" style="font-size: 14px;">
                Juban, Sorsogon
            </p>
            <div>
                <p class="mb-0 fw-light" style="font-size: 13px; color: #9ca3af;">
                    &copy; {{ date('Y') }} AquaTank. All rights reserved.
                </p>
            </div>
        </div>
    </footer>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const navLinks = document.querySelectorAll('.nav-link-custom');
            const indicator = document.getElementById('nav-indicator');
            const navContainer = document.getElementById('navContainer');
            let isClickScrolling = false;

            function updateIndicator(activeLink) {
                if (!activeLink || !indicator || !navContainer) return;
                const containerRect = navContainer.getBoundingClientRect();
                const linkRect = activeLink.getBoundingClientRect();
                const lineWidth = 24;
                const centerOffset = (linkRect.left - containerRect.left) + (linkRect.width / 2) - (lineWidth / 2);
                indicator.style.transform = `translateX(${centerOffset}px)`;
            }

            const initialActive = document.querySelector('.nav-link-custom.active');
            if (initialActive) {
                updateIndicator(initialActive);
                requestAnimationFrame(() => {
                    setTimeout(() => indicator && indicator.classList.add('ready'), 50);
                });
            }

            navLinks.forEach(link => {
                link.addEventListener('click', function() {
                    isClickScrolling = true;
                    navLinks.forEach(nav => nav.classList.remove('active'));
                    this.classList.add('active');
                    updateIndicator(this);
                    setTimeout(() => { isClickScrolling = false; }, 800);
                });
            });

            window.addEventListener('scroll', () => {
                if (isClickScrolling) return;
                let current = '';
                const sections = document.querySelectorAll('section');
                sections.forEach(section => {
                    const sectionTop = section.offsetTop;
                    if (window.scrollY >= sectionTop - 160) {
                        current = section.getAttribute('id');
                    }
                });

                if (current) {
                    navLinks.forEach(link => {
                        const href = link.getAttribute('href');
                        if (href && href.includes(current)) {
                            if (!link.classList.contains('active')) {
                                navLinks.forEach(nav => nav.classList.remove('active'));
                                link.classList.add('active');
                                updateIndicator(link);
                            }
                        }
                    });
                }
            });

            window.addEventListener('resize', () => {
                if (!indicator) return;
                indicator.classList.remove('ready');
                const active = document.querySelector('.nav-link-custom.active');
                if (active) updateIndicator(active);
                setTimeout(() => indicator.classList.add('ready'), 100);
            });
        });

        // Real-Time Water Bill Calculator Logic
        const prevInput = document.getElementById('prevReading');
        const currInput = document.getElementById('currReading');
        const errorDiv = document.getElementById('calcError');
        const consumptionSpan = document.getElementById('calcConsumption');
        const estimatedBillDiv = document.getElementById('estimatedBill');
        const ratePerCbm = 15.00;

        function calculateBill() {
            if (!prevInput || !currInput) return;
            const prev = parseFloat(prevInput.value) || 0;
            const curr = parseFloat(currInput.value) || 0;

            if (curr < prev) {
                if (errorDiv) errorDiv.style.display = 'block';
                if (consumptionSpan) consumptionSpan.textContent = '0.00 m³';
                if (estimatedBillDiv) {
                    estimatedBillDiv.textContent = '₱0.00';
                    estimatedBillDiv.style.color = '#ef4444';
                }
                return;
            }

            if (errorDiv) errorDiv.style.display = 'none';
            if (estimatedBillDiv) estimatedBillDiv.style.color = '#4a6cf7';

            const consumption = Math.max(0, curr - prev);
            const total = consumption * ratePerCbm;

            if (consumptionSpan) consumptionSpan.textContent = consumption.toFixed(2) + ' m³';
            if (estimatedBillDiv) {
                estimatedBillDiv.textContent = '₱' + total.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }
        }

        if (prevInput && currInput) {
            prevInput.addEventListener('input', calculateBill);
            currInput.addEventListener('input', calculateBill);
            calculateBill();
        }
    </script>
</body>
</html>