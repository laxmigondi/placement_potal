<?php require_once __DIR__ . '/config/db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Placement Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body.portal-home {
            min-height: 100vh;
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background:
                linear-gradient(rgba(245, 248, 255, 0.70), rgba(245, 248, 255, 0.82)),
                url('https://images.unsplash.com/photo-1523050854058-8df90110c9f1?auto=format&fit=crop&w=1600&q=80') center/cover no-repeat fixed;
            position: relative;
            overflow-x: hidden;
        }

        .top-navbar {
            background: rgba(255, 255, 255, 0.72);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border-bottom: 1px solid rgba(255,255,255,0.7);
            box-shadow: 0 8px 30px rgba(0,0,0,0.06);
            padding: 14px 0;
        }

        .navbar-brand-custom {
            font-size: 1.3rem;
            font-weight: 800;
            color: #0d3b66;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .navbar-brand-custom i {
            color: #0d6efd;
            font-size: 1.4rem;
        }

        .nav-actions .btn {
            border-radius: 14px;
            font-weight: 600;
            padding: 9px 16px;
        }

        .hero-wrapper {
            min-height: calc(100vh - 78px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 0;
        }

        .hero-card {
            max-width: 920px;
            margin: auto;
            padding: 48px 40px;
            border-radius: 32px;
            background: rgba(255,255,255,0.68);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255,255,255,0.75);
            box-shadow: 0 24px 60px rgba(56, 88, 138, 0.16);
            animation: fadeUp 0.8s ease;
        }

        .hero-badge {
            background: #edf4ff;
            color: #0d6efd;
            border-radius: 999px;
            padding: 10px 18px;
            font-size: 0.92rem;
            font-weight: 700;
            display: inline-block;
            box-shadow: 0 8px 20px rgba(13,110,253,0.08);
        }

        .hero-title {
            font-size: 3.2rem;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.15;
            margin-top: 18px;
            margin-bottom: 16px;
        }

        .hero-subtitle {
            font-size: 1.12rem;
            color: #5f6f82;
            max-width: 720px;
            margin: 0 auto 30px;
        }

        .hero-buttons .btn {
            border-radius: 16px;
            padding: 13px 24px;
            font-size: 1rem;
            font-weight: 700;
            transition: all 0.28s ease;
            box-shadow: 0 10px 22px rgba(0,0,0,0.08);
        }

        .hero-buttons .btn:hover {
            transform: translateY(-3px);
        }

        .btn-student {
            background: linear-gradient(135deg, #0d6efd, #4facfe);
            color: white;
            border: none;
        }

        .btn-student:hover {
            color: white;
        }

        .btn-login {
            background: linear-gradient(135deg, #198754, #43e97b);
            color: white;
            border: none;
        }

        .btn-login:hover {
            color: white;
        }

        .btn-admin {
            background: linear-gradient(135deg, #212529, #495057);
            color: white;
            border: none;
        }

        .btn-admin:hover {
            color: white;
        }

        .feature-row {
            margin-top: 34px;
        }

        .feature-card {
            background: rgba(255,255,255,0.82);
            border: 1px solid rgba(255,255,255,0.75);
            border-radius: 22px;
            padding: 22px 18px;
            box-shadow: 0 14px 28px rgba(77, 106, 153, 0.08);
            transition: all 0.28s ease;
            height: 100%;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 22px 38px rgba(77, 106, 153, 0.14);
        }

        .feature-icon {
            width: 56px;
            height: 56px;
            border-radius: 18px;
            background: linear-gradient(135deg, #eaf2ff, #f5f9ff);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 14px;
            font-size: 1.4rem;
            color: #0d6efd;
        }

        .feature-title {
            font-size: 1.06rem;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 8px;
        }

        .feature-text {
            font-size: 0.93rem;
            color: #64748b;
            margin-bottom: 0;
        }

        .hero-footer-text {
            margin-top: 26px;
            color: #6b7280;
            font-size: 0.95rem;
        }

        .floating-shape {
            position: absolute;
            border-radius: 50%;
            filter: blur(10px);
            z-index: 0;
            animation: floatY 8s ease-in-out infinite;
        }

        .shape-1 {
            width: 180px;
            height: 180px;
            background: rgba(13, 110, 253, 0.10);
            top: 12%;
            left: 4%;
        }

        .shape-2 {
            width: 220px;
            height: 220px;
            background: rgba(25, 135, 84, 0.08);
            bottom: 10%;
            right: 5%;
            animation-delay: 1.5s;
        }

        .shape-3 {
            width: 120px;
            height: 120px;
            background: rgba(255, 193, 7, 0.10);
            top: 20%;
            right: 20%;
            animation-delay: 2.5s;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(24px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes floatY {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-16px); }
        }

        @media (max-width: 768px) {
            .hero-card {
                padding: 32px 20px;
                border-radius: 24px;
            }

            .hero-title {
                font-size: 2.2rem;
            }

            .hero-subtitle {
                font-size: 1rem;
            }

            .nav-actions {
                margin-top: 12px;
            }
        }
    </style>
</head>
<body class="portal-home">

    <div class="floating-shape shape-1"></div>
    <div class="floating-shape shape-2"></div>
    <div class="floating-shape shape-3"></div>

    <nav class="top-navbar">
        <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
            <a href="index.php" class="navbar-brand-custom">
                <i class="bi bi-mortarboard-fill"></i>
                Placement Portal
            </a>

            <div class="nav-actions d-flex flex-wrap gap-2">
                <a href="student/register.php" class="btn btn-outline-primary">
                    <i class="bi bi-person-plus-fill me-1"></i> Register
                </a>
                <a href="student/login.php" class="btn btn-outline-success">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Student Login
                </a>
                <a href="admin/login.php" class="btn btn-outline-dark">
                    <i class="bi bi-shield-lock-fill me-1"></i> Admin
                </a>
            </div>
        </div>
    </nav>

    <div class="container hero-wrapper">
        <div class="hero-card text-center position-relative" style="z-index: 2;">
            <span class="hero-badge">
                <i class="bi bi-stars me-1"></i> Student Placement Preparation Portal
            </span>

            <h1 class="hero-title">Prepare smarter for placements</h1>

            <p class="hero-subtitle">
                One premium space for study materials, mock tests, performance tracking, and admin management —
                designed to help students stay focused, confident, and placement-ready.
            </p>

            <div class="hero-buttons d-flex flex-wrap justify-content-center gap-3">
                <a href="student/register.php" class="btn btn-student btn-lg">
                    <i class="bi bi-person-plus-fill me-2"></i>Student Register
                </a>
                <a href="student/login.php" class="btn btn-login btn-lg">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Student Login
                </a>
                <a href="admin/login.php" class="btn btn-admin btn-lg">
                    <i class="bi bi-shield-lock-fill me-2"></i>Admin Login
                </a>
            </div>

            <div class="row g-4 feature-row text-center">
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-book-half"></i>
                        </div>
                        <div class="feature-title">Study Materials</div>
                        <p class="feature-text">Access important notes, preparation content, and resources in one place.</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-patch-check"></i>
                        </div>
                        <div class="feature-title">Mock Tests</div>
                        <p class="feature-text">Practice with timed tests and sharpen your confidence for placement rounds.</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                        <div class="feature-title">Result Tracking</div>
                        <p class="feature-text">Monitor your performance, review progress, and improve with each attempt.</p>
                    </div>
                </div>
            </div>

            <p class="hero-footer-text">
                Clean. Modern. Student-friendly. Built to make preparation feel organized and motivating.
            </p>
        </div>
    </div>
</body>
</html>
