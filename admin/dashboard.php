<?php
session_start();
require_once '../config/db.php';
if (!isset($_SESSION['admin_id'])) redirect('login.php');

$counts = [];
foreach (['students','materials','tests','results'] as $table) {
    $res = mysqli_query($conn, "SELECT COUNT(*) AS total FROM {$table}");
    $counts[$table] = (int)(mysqli_fetch_assoc($res)['total'] ?? 0);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background:
                radial-gradient(circle at top left, rgba(220,53,69,0.08), transparent 25%),
                radial-gradient(circle at bottom right, rgba(255,99,132,0.08), transparent 30%),
                linear-gradient(135deg, #fff8f8, #fffdfd, #fefefe);
            font-family: 'Segoe UI', sans-serif;
            min-height: 100vh;
        }

        .topbar {
            background: rgba(255,255,255,0.82);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255,255,255,0.75);
            box-shadow: 0 8px 28px rgba(0,0,0,0.05);
            padding: 14px 0;
        }

        .brand-title {
            color: #dc3545;
            font-weight: 800;
            font-size: 1.3rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .welcome-text {
            color: #475569;
            font-weight: 600;
        }

        .welcome-text a {
            color: #dc3545;
            text-decoration: none;
            font-weight: 700;
        }

        .hero-strip {
            background: linear-gradient(135deg, #dc3545, #ff6b81);
            color: #fff;
            border-radius: 28px;
            padding: 30px;
            box-shadow: 0 20px 40px rgba(220,53,69,0.18);
            margin-bottom: 26px;
            animation: fadeDown .7s ease;
        }

        .hero-strip h2 {
            font-weight: 800;
            margin-bottom: 8px;
        }

        .hero-strip p {
            margin-bottom: 0;
            opacity: 0.92;
        }

        .stat-card {
            background: rgba(255,255,255,0.84);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,0.75);
            border-radius: 24px;
            padding: 24px;
            box-shadow: 0 16px 38px rgba(80,110,160,0.08);
            transition: 0.3s ease;
            height: 100%;
            animation: fadeUp .7s ease;
        }

        .stat-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 24px 42px rgba(80,110,160,0.14);
        }

        .stat-label {
            color: #64748b;
            font-size: 0.95rem;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 800;
            color: #1e293b;
            line-height: 1;
        }

        .icon-box {
            width: 58px;
            height: 58px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            background: linear-gradient(135deg, #ffe5e8, #fff3f5);
            color: #dc3545;
            flex-shrink: 0;
        }

        .section-title {
            font-size: 1.2rem;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 18px;
        }

        .action-btn {
            width: 100%;
            border-radius: 20px;
            padding: 22px 16px;
            background: rgba(255,255,255,0.84);
            border: 1px solid rgba(255,255,255,0.75);
            box-shadow: 0 14px 30px rgba(80,110,160,0.08);
            text-decoration: none;
            color: #1e293b;
            display: block;
            transition: 0.28s ease;
            height: 100%;
        }

        .action-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 22px 38px rgba(80,110,160,0.13);
            color: #dc3545;
        }

        .action-icon {
            width: 54px;
            height: 54px;
            margin: 0 auto 14px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #ffe5e8, #fff3f5);
            color: #dc3545;
            font-size: 1.3rem;
        }

        .action-title {
            font-weight: 800;
            font-size: 1rem;
            margin-bottom: 6px;
        }

        .action-text {
            font-size: 0.9rem;
            color: #64748b;
            margin-bottom: 0;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(22px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeDown {
            from {
                opacity: 0;
                transform: translateY(-18px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 768px) {
            .hero-strip {
                padding: 24px 20px;
                border-radius: 22px;
            }

            .hero-strip h2 {
                font-size: 1.5rem;
            }

            .stat-number {
                font-size: 1.7rem;
            }
        }
    </style>
</head>
<body>

    <nav class="topbar">
        <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
            <div class="brand-title">
                <i class="bi bi-shield-fill"></i>
                Admin Dashboard
            </div>
            <div class="welcome-text">
                Welcome, <?php echo e($_SESSION['admin_name']); ?> |
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container py-4">

        <div class="hero-strip">
            <h2><i class="bi bi-speedometer2 me-2"></i>Admin Control Center</h2>
            <p>Manage study materials, mock tests, questions, and student performance from one clean dashboard.</p>
        </div>

        <div class="row g-4 mb-4">
            <?php foreach ([['Students','students','bi-people-fill'],['Materials','materials','bi-book-fill'],['Tests','tests','bi-clipboard2-check-fill'],['Results','results','bi-bar-chart-fill']] as $card): ?>
                <div class="col-md-6 col-lg-3">
                    <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="stat-label"><?php echo $card[0]; ?></div>
                                <div class="stat-number"><?php echo $counts[$card[1]]; ?></div>
                            </div>
                            <div class="icon-box">
                                <i class="bi <?php echo $card[2]; ?>"></i>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="section-title">Quick Actions</div>

        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <a class="action-btn text-center" href="add-material.php">
                    <div class="action-icon">
                        <i class="bi bi-book"></i>
                    </div>
                    <div class="action-title">Manage Materials</div>
                    <p class="action-text">Upload and organize study resources for students.</p>
                </a>
            </div>

            <div class="col-md-6 col-lg-3">
                <a class="action-btn text-center" href="manage-tests.php">
                    <div class="action-icon">
                        <i class="bi bi-clipboard2-check"></i>
                    </div>
                    <div class="action-title">Manage Tests</div>
                    <p class="action-text">Create and organize mock tests for preparation.</p>
                </a>
            </div>

            <div class="col-md-6 col-lg-3">
                <a class="action-btn text-center" href="add-question.php">
                    <div class="action-icon">
                        <i class="bi bi-question-circle"></i>
                    </div>
                    <div class="action-title">Add Questions</div>
                    <p class="action-text">Add MCQs and map them to the right tests.</p>
                </a>
            </div>

            <div class="col-md-6 col-lg-3">
                <a class="action-btn text-center" href="view-results.php">
                    <div class="action-icon">
                        <i class="bi bi-bar-chart"></i>
                    </div>
                    <div class="action-title">View Results</div>
                    <p class="action-text">Track student performance and result history.</p>
                </a>
            </div>
        </div>

    </div>
</body>
</html>