<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>AquaTank | Water Billing System</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f4faff;
            color: #0f172a;
        }

        a {
            text-decoration: none;
        }

        /* NAVBAR */
        .navbar {
            width: 100%;
            padding: 20px 7%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: white;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
            position: relative;
            z-index: 10;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo-icon {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: linear-gradient(135deg, #2563eb, #06b6d4);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 22px;
        }

        .logo-text {
            font-size: 22px;
            font-weight: 700;
            color: #155eef;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .nav-links a {
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
        }

        .login-btn {
            color: #155eef;
        }

        .login-btn:hover {
            background: #eff6ff;
        }

        .register-btn {
            color: white;
            background: #2563eb;
        }

        .register-btn:hover {
            background: #1d4ed8;
        }

        /* HERO */
        .hero {
            min-height: 650px;
            padding: 80px 7%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 50px;
            overflow: hidden;
            position: relative;
            background:
                radial-gradient(circle at 80% 20%, rgba(6, 182, 212, 0.16), transparent 30%),
                radial-gradient(circle at 20% 80%, rgba(37, 99, 235, 0.12), transparent 30%),
                #f4faff;
        }

        .hero-content {
            max-width: 620px;
            position: relative;
            z-index: 2;
        }

        .badge {
            display: inline-block;
            padding: 8px 15px;
            border-radius: 30px;
            background: #e0f2fe;
            color: #0369a1;
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 22px;
        }

        .hero h1 {
            font-size: clamp(42px, 6vw, 70px);
            line-height: 1.05;
            margin-bottom: 22px;
            color: #0f2b5b;
        }

        .hero h1 span {
            color: #2563eb;
        }

        .hero p {
            font-size: 18px;
            line-height: 1.8;
            color: #64748b;
            margin-bottom: 32px;
            max-width: 570px;
        }

        .hero-buttons {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .primary-btn,
        .secondary-btn {
            padding: 14px 25px;
            border-radius: 9px;
            font-size: 15px;
            font-weight: 700;
            transition: 0.2s;
        }

        .primary-btn {
            background: #2563eb;
            color: white;
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.25);
        }

        .primary-btn:hover {
            background: #1d4ed8;
            transform: translateY(-2px);
        }

        .secondary-btn {
            border: 1px solid #bfdbfe;
            color: #2563eb;
            background: white;
        }

        .secondary-btn:hover {
            background: #eff6ff;
        }

        /* WATER ILLUSTRATION */
        .hero-visual {
            width: 430px;
            height: 430px;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .circle {
            width: 360px;
            height: 360px;
            border-radius: 50%;
            background: linear-gradient(145deg, #dff6ff, #bfdbfe);
            position: absolute;
        }

        .tank {
            width: 180px;
            height: 210px;
            background: white;
            border-radius: 25px 25px 35px 35px;
            border: 7px solid #2563eb;
            position: relative;
            z-index: 2;
            box-shadow: 0 20px 40px rgba(30, 64, 175, 0.15);
        }

        .tank-top {
            position: absolute;
            width: 115px;
            height: 22px;
            border-radius: 15px;
            background: #2563eb;
            top: -20px;
            left: 25px;
        }

        .water {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 58%;
            background: linear-gradient(180deg, #38bdf8, #2563eb);
            border-radius: 0 0 27px 27px;
        }

        .drop {
            position: absolute;
            z-index: 3;
            width: 80px;
            height: 105px;
            background: linear-gradient(180deg, #06b6d4, #2563eb);
            border-radius: 70% 30% 70% 30%;
            transform: rotate(45deg);
            right: 45px;
            top: 55px;
            box-shadow: 0 15px 30px rgba(6, 182, 212, 0.25);
        }

        /* FEATURES */
        .features {
            padding: 90px 7%;
            background: white;
        }

        .section-title {
            text-align: center;
            max-width: 650px;
            margin: 0 auto 50px;
        }

        .section-title h2 {
            font-size: 36px;
            color: #0f2b5b;
            margin-bottom: 12px;
        }

        .section-title p {
            color: #64748b;
            line-height: 1.7;
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 25px;
            max-width: 1100px;
            margin: auto;
        }

        .feature-card {
            padding: 30px;
            background: #f8fcff;
            border: 1px solid #e0f2fe;
            border-radius: 16px;
            transition: 0.2s;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(15, 23, 42, 0.08);
        }

        .feature-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            background: #dbeafe;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 18px;
        }

        .feature-card h3 {
            margin-bottom: 10px;
            color: #0f2b5b;
        }

        .feature-card p {
            color: #64748b;
            line-height: 1.6;
            font-size: 14px;
        }

        /* FOOTER */
        footer {
            background: #0f2b5b;
            color: white;
            padding: 35px 7%;
            text-align: center;
        }

        footer h3 {
            margin-bottom: 8px;
        }

        footer p {
            color: #bfdbfe;
            font-size: 14px;
        }

        /* RESPONSIVE */
        @media (max-width: 900px) {
            .hero {
                flex-direction: column;
                text-align: center;
                padding-top: 60px;
            }

            .hero p {
                margin-left: auto;
                margin-right: auto;
            }

            .hero-buttons {
                justify-content: center;
            }

            .hero-visual {
                width: 350px;
                height: 350px;
            }

            .circle {
                width: 300px;
                height: 300px;
            }

            .feature-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 600px) {
            .navbar {
                padding: 15px 5%;
            }

            .nav-links {
                gap: 3px;
            }

            .nav-links a {
                padding: 8px 10px;
                font-size: 13px;
            }

            .hero {
                padding: 55px 5%;
            }

            .hero-visual {
                width: 290px;
                height: 290px;
            }

            .circle {
                width: 250px;
                height: 250px;
            }

            .tank {
                width: 140px;
                height: 170px;
            }

            .tank-top {
                width: 90px;
                left: 18px;
            }

            .drop {
                width: 60px;
                height: 80px;
                right: 15px;
            }

            .feature-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

    <!-- NAVIGATION -->
    <nav class="navbar">

        <div class="logo">
            <div class="logo-icon">💧</div>
            <div class="logo-text">AquaTank</div>
        </div>

        <div class="nav-links">
            <a href="{{ route('login') }}" class="login-btn">
                Login
            </a>

            <a href="{{ route('register') }}" class="register-btn">
                Register
            </a>
        </div>

    </nav>


    <!-- HERO SECTION -->
    <section class="hero">

        <div class="hero-content">

            <div class="badge">
                💧 Barangay Caruhayon Water Service
            </div>

            <h1>
                Smarter Water Billing with
                <span>AquaTank</span>
            </h1>

            <p>
                A web-based water billing system designed to make
                billing, consumer records, payment monitoring, and
                notifications more organized, efficient, and accessible
                for Barangay Caruhayon.
            </p>

            <div class="hero-buttons">

                <a href="{{ route('login') }}" class="primary-btn">
                    Get Started →
                </a>

                <a href="#features" class="secondary-btn">
                    Explore Features
                </a>

            </div>

        </div>


        <!-- WATER ILLUSTRATION -->
        <div class="hero-visual">

            <div class="circle"></div>

            <div class="tank">
                <div class="tank-top"></div>
                <div class="water"></div>
            </div>

            <div class="drop"></div>

        </div>

    </section>


    <!-- FEATURES -->
    <section class="features" id="features">

        <div class="section-title">

            <h2>Everything in One System</h2>

            <p>
                AquaTank brings essential water billing functions
                together in one organized web-based platform.
            </p>

        </div>


        <div class="feature-grid">

            <div class="feature-card">

                <div class="feature-icon">👥</div>

                <h3>Consumer Records</h3>

                <p>
                    Store and organize consumer information for
                    faster and easier record retrieval.
                </p>

            </div>


            <div class="feature-card">

                <div class="feature-icon">🧾</div>

                <h3>Water Billing</h3>

                <p>
                    Calculate water bills based on the recorded
                    meter reading and applicable billing rates.
                </p>

            </div>


            <div class="feature-card">

                <div class="feature-icon">💰</div>

                <h3>Payment Tracking</h3>

                <p>
                    Record payments and monitor outstanding
                    balances of consumers.
                </p>

            </div>


            <div class="feature-card">

                <div class="feature-icon">📱</div>

                <h3>SMS Notifications</h3>

                <p>
                    Notify consumers about their billing information
                    and payment reminders through SMS.
                </p>

            </div>


            <div class="feature-card">

                <div class="feature-icon">📊</div>

                <h3>Reports & Analytics</h3>

                <p>
                    Generate useful summaries and reports for
                    monitoring billing and collection activities.
                </p>

            </div>


            <div class="feature-card">

                <div class="feature-icon">🔐</div>

                <h3>Secure Access</h3>

                <p>
                    Provide authorized users with secure access
                    based on their assigned system roles.
                </p>

            </div>

        </div>

    </section>


    <!-- FOOTER -->
    <footer>

        <h3>AquaTank</h3>

        <p>
            Web-Based Water Billing System for Barangay Caruhayon
        </p>

        <p>
            Juban, Sorsogon
        </p>

        <br>

        <p>
            © {{ date('Y') }} AquaTank. All rights reserved.
        </p>

    </footer>

</body>
</html>