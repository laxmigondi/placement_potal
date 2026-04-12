<?php
// Start session to access stored login info
session_start();

// Session protection:
// If student is NOT logged in, send them back to login page
if (!isset($_SESSION["student_id"])) {
    header("Location: login.php");
    exit();
}

// Get student name from session to display on dashboard
$student_name = $_SESSION["student_name"];

// Include database connection
require_once "../config/db.php";

// Get total number of tests available
$tests_result = mysqli_query($conn, "SELECT COUNT(*) as total FROM tests");
$tests_row    = mysqli_fetch_assoc($tests_result);
$total_tests  = $tests_row["total"];

// Get total number of study materials available
$materials_result = mysqli_query($conn, "SELECT COUNT(*) as total FROM materials");
$materials_row    = mysqli_fetch_assoc($materials_result);
$total_materials  = $materials_row["total"];

// Get total number of tests this student has attempted
$student_id      = $_SESSION["student_id"];
$results_result  = mysqli_query($conn, "SELECT COUNT(*) as total FROM results WHERE student_id = '$student_id'");
$results_row     = mysqli_fetch_assoc($results_result);
$tests_attempted = $results_row["total"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --primary: #4f46e5;
            --primary-dark: #3730a3;
            --secondary: #06b6d4;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --text-dark: #1e293b;
            --text-muted: #64748b;
            --card-bg: rgba(255, 255, 255, 0.82);
            --border-light: rgba(255, 255, 255, 0.25);
            --shadow-soft: 0 10px 30px rgba(15, 23, 42, 0.08);
            --shadow-hover: 0 18px 40px rgba(79, 70, 229, 0.18);
        }

        * {
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text-dark);
            background:
                radial-gradient(circle at top left, rgba(79, 70, 229, 0.20), transparent 30%),
                radial-gradient(circle at top right, rgba(6, 182, 212, 0.18), transparent 28%),
                linear-gradient(135deg, #eef2ff 0%, #f8fafc 45%, #ecfeff 100%);
            background-attachment: fixed;
        }

        /* Navbar */
        .navbar {
            background: rgba(15, 23, 42, 0.78);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border-bottom: 1px solid rgba(255,255,255,0.08);
            padding: 14px 28px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.12);
        }

        .navbar-brand {
            color: #fff !important;
            font-size: 1.35rem;
            font-weight: 700;
            letter-spacing: 0.3px;
        }

        .navbar-brand i {
            color: #a5b4fc;
            margin-right: 6px;
        }

        .nav-username {
            color: rgba(255,255,255,0.92);
            font-size: 0.95rem;
            font-weight: 500;
            background: rgba(255,255,255,0.08);
            padding: 8px 14px;
            border-radius: 999px;
        }

        .nav-username i {
            margin-right: 5px;
            color: #c4b5fd;
        }

        .btn.btn-light.btn-sm {
            border-radius: 999px;
            padding: 8px 16px;
            font-weight: 600;
            border: none;
            box-shadow: 0 8px 20px rgba(255,255,255,0.15);
            transition: all 0.25s ease;
        }

        .btn.btn-light.btn-sm:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px rgba(255,255,255,0.2);
        }

        /* Main container */
        .container {
            max-width: 1200px;
        }

        /* Welcome banner */
        .welcome-banner {
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, #4f46e5, #7c3aed, #06b6d4);
            background-size: 200% 200%;
            animation: gradientMove 8s ease infinite;
            color: white;
            padding: 42px 34px;
            border-radius: 24px;
            margin-bottom: 34px;
            box-shadow: 0 20px 45px rgba(79, 70, 229, 0.25);
        }

        .welcome-banner::before,
        .welcome-banner::after {
            content: "";
            position: absolute;
            border-radius: 50%;
            background: rgba(255,255,255,0.10);
        }

        .welcome-banner::before {
            width: 180px;
            height: 180px;
            top: -60px;
            right: -40px;
        }

        .welcome-banner::after {
            width: 120px;
            height: 120px;
            bottom: -30px;
            left: -20px;
        }

        .welcome-banner h3 {
            position: relative;
            z-index: 1;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .welcome-banner p {
            position: relative;
            z-index: 1;
            font-size: 1rem;
            margin-bottom: 0;
            opacity: 0.95;
            max-width: 700px;
            line-height: 1.6;
        }

        /* Section title */
        .section-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 24px;
            padding-left: 14px;
            border-left: 5px solid var(--primary);
            letter-spacing: 0.2px;
        }

        /* Stats cards */
        .stat-card {
            background: var(--card-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--border-light);
            border-radius: 22px;
            padding: 28px 22px;
            box-shadow: var(--shadow-soft);
            text-align: center;
            margin-bottom: 24px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            height: 100%;
        }

        .stat-card::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(79,70,229,0.06), rgba(6,182,212,0.04));
            opacity: 0;
            transition: 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-hover);
        }

        .stat-card:hover::before {
            opacity: 1;
        }

        .stat-card .stat-icon {
            position: relative;
            z-index: 1;
            width: 72px;
            height: 72px;
            margin: 0 auto 16px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            background: linear-gradient(135deg, rgba(79,70,229,0.12), rgba(6,182,212,0.10));
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.5);
        }

        .stat-card .stat-number {
            position: relative;
            z-index: 1;
            font-size: 2.2rem;
            font-weight: 800;
            color: var(--primary);
            line-height: 1;
            margin-bottom: 8px;
        }

        .stat-card .stat-label {
            position: relative;
            z-index: 1;
            font-size: 0.95rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        /* Feature cards */
        .feature-card {
            position: relative;
            overflow: hidden;
            background: var(--card-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--border-light);
            border-radius: 24px;
            padding: 32px 22px;
            text-align: center;
            text-decoration: none;
            color: var(--text-dark);
            display: block;
            box-shadow: var(--shadow-soft);
            transition: all 0.35s ease;
            margin-bottom: 24px;
            height: 100%;
        }

        .feature-card::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(79,70,229,0.08), rgba(6,182,212,0.05));
            opacity: 0;
            transition: opacity 0.35s ease;
        }

        .feature-card:hover {
            transform: translateY(-10px) scale(1.01);
            box-shadow: 0 20px 45px rgba(15, 23, 42, 0.14);
            color: var(--text-dark);
            border-color: rgba(79, 70, 229, 0.18);
        }

        .feature-card:hover::before {
            opacity: 1;
        }

        .feature-card .card-icon {
            position: relative;
            z-index: 1;
            width: 82px;
            height: 82px;
            margin: 0 auto 18px;
            border-radius: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            background: linear-gradient(135deg, rgba(255,255,255,0.9), rgba(241,245,249,0.8));
            box-shadow: 0 10px 22px rgba(15, 23, 42, 0.08);
            transition: all 0.3s ease;
        }

        .feature-card:hover .card-icon {
            transform: scale(1.08) rotate(-3deg);
        }

        .feature-card h5 {
            position: relative;
            z-index: 1;
            font-weight: 700;
            font-size: 1.15rem;
            margin-bottom: 10px;
        }

        .feature-card p {
            position: relative;
            z-index: 1;
            font-size: 0.92rem;
            color: var(--text-muted);
            line-height: 1.6;
            margin-bottom: 0;
        }

        /* Icon colors */
        .icon-blue {
            color: var(--primary);
        }

        .icon-green {
            color: var(--success);
        }

        .icon-orange {
            color: var(--warning);
        }

        .icon-red {
            color: var(--danger);
        }

        /* Footer */
        .footer {
            text-align: center;
            padding: 26px 15px 20px;
            color: #64748b;
            font-size: 0.9rem;
            margin-top: 30px;
        }

        /* Animation */
        @keyframes gradientMove {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Responsive */
        @media (max-width: 991px) {
            .navbar {
                padding: 14px 16px;
            }

            .welcome-banner {
                padding: 34px 24px;
            }

            .welcome-banner h3 {
                font-size: 1.7rem;
            }
        }

        @media (max-width: 767px) {
            .navbar {
                flex-direction: column;
                align-items: flex-start !important;
                gap: 12px;
            }

            .navbar .ms-auto {
                margin-left: 0 !important;
                width: 100%;
                justify-content: space-between;
                flex-wrap: wrap;
            }

            .welcome-banner {
                padding: 28px 20px;
                border-radius: 20px;
            }

            .welcome-banner h3 {
                font-size: 1.5rem;
            }

            .stat-card,
            .feature-card {
                border-radius: 20px;
            }
        }
    </style>
</head>
<body>

<!-- ===== NAVBAR ===== -->
<nav class="navbar navbar-expand-lg">
    <span class="navbar-brand">
        <i class="bi bi-mortarboard-fill"></i> Placement Portal
    </span>
    <div class="ms-auto d-flex align-items-center gap-3">
        <span class="nav-username">
            <i class="bi bi-person-circle"></i>
            Welcome, <?php echo htmlspecialchars($student_name); ?>!
        </span>
        <a href="logout.php" class="btn btn-light btn-sm">
            <i class="bi bi-box-arrow-right"></i> Logout
        </a>
    </div>
</nav>

<!-- ===== MAIN CONTENT ===== -->
<div class="container py-4 py-md-5">

    <!-- Welcome Banner -->
    <div class="welcome-banner">
        <h3>👋 Hello, <?php echo htmlspecialchars($student_name); ?>!</h3>
        <p>Welcome to your Placement Preparation Portal. Start learning, practice mock tests, and track your progress.</p>
    </div>

    <!-- Stats Row -->
    <div class="row g-4 mb-4">

        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon text-primary">
                    <i class="bi bi-journal-richtext"></i>
                </div>
                <div class="stat-number"><?php echo $total_materials; ?></div>
                <div class="stat-label">Study Materials Available</div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon text-success">
                    <i class="bi bi-clipboard2-check"></i>
                </div>
                <div class="stat-number"><?php echo $total_tests; ?></div>
                <div class="stat-label">Mock Tests Available</div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon text-warning">
                    <i class="bi bi-trophy"></i>
                </div>
                <div class="stat-number"><?php echo $tests_attempted; ?></div>
                <div class="stat-label">Tests Attempted by You</div>
            </div>
        </div>

    </div>

    <!-- Feature Cards -->
    <div class="section-title">What would you like to do?</div>

    <div class="row g-4">

        <!-- Study Materials -->
        <div class="col-lg-3 col-md-6 col-sm-6">
            <a href="materials.php" class="feature-card">
                <div class="card-icon icon-blue">
                    <i class="bi bi-book-fill"></i>
                </div>
                <h5>Study Materials</h5>
                <p>Access placement preparation notes and resources</p>
            </a>
        </div>

        <!-- Mock Test -->
        <div class="col-lg-3 col-md-6 col-sm-6">
            <a href="mock-test.php" class="feature-card">
                <div class="card-icon icon-green">
                    <i class="bi bi-pencil-square"></i>
                </div>
                <h5>Mock Test</h5>
                <p>Practice with timed online tests and improve your score</p>
            </a>
        </div>

        <!-- My Results -->
        <div class="col-lg-3 col-md-6 col-sm-6">
            <a href="results.php" class="feature-card">
                <div class="card-icon icon-orange">
                    <i class="bi bi-bar-chart-fill"></i>
                </div>
                <h5>My Results</h5>
                <p>View your test scores and performance history</p>
            </a>
        </div>

        <!-- Logout -->
        <div class="col-lg-3 col-md-6 col-sm-6">
            <a href="logout.php" class="feature-card">
                <div class="card-icon icon-red">
                    <i class="bi bi-box-arrow-right"></i>
                </div>
                <h5>Logout</h5>
                <p>Safely logout from your account</p>
            </a>
        </div>

    </div>

</div>

<!-- Footer -->
<div class="footer">
    &copy; 2025 Placement Portal | Built with PHP & Bootstrap
</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>