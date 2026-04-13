<?php
session_start();
require_once '../config/db.php';
if (!isset($_SESSION['student_id'])) redirect('login.php');

$student_id = (int)$_SESSION['student_id'];
$stmt = mysqli_prepare($conn, "SELECT r.*, t.test_name FROM results r JOIN tests t ON r.test_id = t.id WHERE r.student_id = ? ORDER BY r.attempted_at DESC");
mysqli_stmt_bind_param($stmt, 'i', $student_id);
mysqli_stmt_execute($stmt);
$results = mysqli_stmt_get_result($stmt);

$allResults = [];
$totalAttempts = 0;
$avgPercentage = 0;
$bestPercentage = 0;

while ($row = mysqli_fetch_assoc($results)) {
    $pct = $row['total_questions'] > 0 ? round(($row['score'] / $row['total_questions']) * 100, 2) : 0;
    $row['pct'] = $pct;
    $allResults[] = $row;
    $totalAttempts++;
    $avgPercentage += $pct;
    if ($pct > $bestPercentage) $bestPercentage = $pct;
}

$avgPercentage = $totalAttempts > 0 ? round($avgPercentage / $totalAttempts, 2) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Results</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            background:
                radial-gradient(circle at top left, rgba(13, 110, 253, 0.10), transparent 25%),
                radial-gradient(circle at bottom right, rgba(25, 135, 84, 0.10), transparent 30%),
                linear-gradient(135deg, #f8fbff, #eef5ff, #fdfefe);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #1f2937;
        }

        .page-header {
            animation: fadeDown 0.7s ease;
        }

        .main-card,
        .stats-card {
            border: 1px solid rgba(255,255,255,0.8);
            border-radius: 24px;
            background: rgba(255,255,255,0.82);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            box-shadow: 0 18px 45px rgba(80, 110, 160, 0.10);
        }

        .stats-card {
            padding: 22px;
            height: 100%;
            transition: all 0.3s ease;
            animation: fadeUp 0.8s ease;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 24px 50px rgba(80, 110, 160, 0.16);
        }

        .icon-box {
            width: 52px;
            height: 52px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            margin-bottom: 14px;
        }

        .icon-blue {
            background: linear-gradient(135deg, #dbeafe, #eff6ff);
            color: #0d6efd;
        }

        .icon-green {
            background: linear-gradient(135deg, #dcfce7, #f0fdf4);
            color: #198754;
        }

        .icon-purple {
            background: linear-gradient(135deg, #ede9fe, #f5f3ff);
            color: #6f42c1;
        }

        .stats-label {
            font-size: 0.92rem;
            color: #6b7280;
            margin-bottom: 4px;
        }

        .stats-value {
            font-size: 1.9rem;
            font-weight: 800;
            color: #111827;
            margin: 0;
        }

        .page-title {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 4px;
            color: #1e293b;
        }

        .page-subtitle {
            color: #64748b;
            margin-bottom: 0;
        }

        .btn-back {
            border-radius: 14px;
            padding: 10px 18px;
            font-weight: 600;
            transition: 0.25s ease;
        }

        .btn-back:hover {
            transform: translateY(-2px);
        }

        .success-alert {
            border: none;
            border-radius: 16px;
            background: linear-gradient(135deg, #e8fff2, #f3fff7);
            color: #157347;
            box-shadow: 0 10px 24px rgba(25, 135, 84, 0.08);
            animation: fadeUp 0.8s ease;
        }

        .table-wrap {
            overflow-x: auto;
        }

        .results-table {
            margin-bottom: 0;
            vertical-align: middle;
        }

        .results-table thead th {
            border: none;
            background: #f4f8fd;
            color: #475569;
            font-size: 0.92rem;
            font-weight: 700;
            padding: 16px 14px;
        }

        .results-table tbody tr {
            border: none;
            transition: all 0.25s ease;
        }

        .results-table tbody tr:hover {
            background: #f8fbff;
            transform: scale(1.003);
        }

        .results-table td {
            padding: 16px 14px;
            border-top: 1px solid #edf2f7;
        }

        .test-name {
            font-weight: 700;
            color: #1e293b;
        }

        .score-pill {
            display: inline-block;
            padding: 7px 14px;
            border-radius: 999px;
            font-weight: 700;
            font-size: 0.88rem;
            background: #edf6ff;
            color: #0d6efd;
        }

        .performance-badge {
            font-size: 0.82rem;
            font-weight: 700;
            border-radius: 999px;
            padding: 7px 12px;
            display: inline-block;
        }

        .excellent {
            background: #dcfce7;
            color: #166534;
        }

        .good {
            background: #e0f2fe;
            color: #075985;
        }

        .average {
            background: #fef3c7;
            color: #92400e;
        }

        .needs-work {
            background: #fee2e2;
            color: #b91c1c;
        }

        .progress {
            height: 10px;
            border-radius: 999px;
            background: #eaf1f8;
            overflow: hidden;
        }

        .progress-bar {
            border-radius: 999px;
            transition: width 1.2s ease;
        }

        .section-title {
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 0;
        }

        .empty-state {
            text-align: center;
            padding: 55px 20px;
        }

        .empty-icon {
            width: 82px;
            height: 82px;
            margin: 0 auto 18px;
            border-radius: 50%;
            background: linear-gradient(135deg, #eef4ff, #f8fbff);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: #7c93c3;
            box-shadow: inset 0 0 0 1px #e2e8f0;
        }

        .empty-title {
            font-size: 1.25rem;
            font-weight: 800;
            color: #1f2937;
        }

        .empty-text {
            color: #6b7280;
            max-width: 420px;
            margin: 10px auto 0;
        }

        .table-card-body {
            padding: 22px;
        }

        .muted-small {
            font-size: 0.88rem;
            color: #64748b;
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

        @keyframes fadeDown {
            from {
                opacity: 0;
                transform: translateY(-16px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 768px) {
            .page-title {
                font-size: 1.65rem;
            }

            .stats-value {
                font-size: 1.55rem;
            }
        }
    </style>
</head>
<body>
<div class="container py-4 py-md-5">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 page-header">
        <div>
            <h2 class="page-title">My Test Results</h2>
            <p class="page-subtitle">Track your performance, celebrate progress, and improve with every attempt.</p>
        </div>
        <a href="dashboard.php" class="btn btn-outline-primary btn-back">
            <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
        </a>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert success-alert mb-4">
            <i class="bi bi-check-circle-fill me-2"></i>
            Test submitted successfully.
        </div>
    <?php endif; ?>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="stats-card">
                <div class="icon-box icon-blue">
                    <i class="bi bi-journal-check"></i>
                </div>
                <div class="stats-label">Total Attempts</div>
                <h3 class="stats-value counter" data-target="<?php echo $totalAttempts; ?>">0</h3>
            </div>
        </div>

        <div class="col-md-4">
            <div class="stats-card">
                <div class="icon-box icon-green">
                    <i class="bi bi-bar-chart-line"></i>
                </div>
                <div class="stats-label">Average Percentage</div>
                <h3 class="stats-value counter-decimal" data-target="<?php echo $avgPercentage; ?>">0</h3>
                <div class="muted-small">Overall consistency across your attempts</div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="stats-card">
                <div class="icon-box icon-purple">
                    <i class="bi bi-trophy"></i>
                </div>
                <div class="stats-label">Best Score</div>
                <h3 class="stats-value counter-decimal" data-target="<?php echo $bestPercentage; ?>">0</h3>
                <div class="muted-small">Your top performance so far</div>
            </div>
        </div>
    </div>

    <div class="main-card">
        <div class="table-card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="section-title">Performance History</h5>
            </div>

            <?php if (count($allResults) === 0): ?>
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="bi bi-clipboard-data"></i>
                    </div>
                    <div class="empty-title">No attempts yet</div>
                    <p class="empty-text">
                        Once you take a mock test, your scores and performance details will appear here.
                    </p>
                </div>
            <?php else: ?>
                <div class="table-wrap">
                    <table class="table results-table align-middle">
                        <thead>
                            <tr>
                                <th>Test</th>
                                <th>Score</th>
                                <th>Total</th>
                                <th style="min-width: 180px;">Percentage</th>
                                <th>Performance</th>
                                <th>Attempted At</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($allResults as $row): ?>
                                <?php
                                    $pct = $row['pct'];

                                    if ($pct >= 85) {
                                        $badgeClass = 'excellent';
                                        $badgeText = 'Excellent';
                                        $barClass = 'bg-success';
                                    } elseif ($pct >= 70) {
                                        $badgeClass = 'good';
                                        $badgeText = 'Good';
                                        $barClass = 'bg-primary';
                                    } elseif ($pct >= 50) {
                                        $badgeClass = 'average';
                                        $badgeText = 'Average';
                                        $barClass = 'bg-warning';
                                    } else {
                                        $badgeClass = 'needs-work';
                                        $badgeText = 'Needs Work';
                                        $barClass = 'bg-danger';
                                    }
                                ?>
                                <tr>
                                    <td>
                                        <div class="test-name"><?php echo e($row['test_name']); ?></div>
                                    </td>
                                    <td>
                                        <span class="score-pill"><?php echo (int)$row['score']; ?></span>
                                    </td>
                                    <td><?php echo (int)$row['total_questions']; ?></td>
                                    <td>
                                        <div class="fw-bold mb-1"><?php echo $pct; ?>%</div>
                                        <div class="progress">
                                            <div class="progress-bar <?php echo $barClass; ?> progress-fill" data-width="<?php echo $pct; ?>" style="width:0%;"></div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="performance-badge <?php echo $badgeClass; ?>">
                                            <?php echo $badgeText; ?>
                                        </span>
                                    </td>
                                    <td><?php echo e($row['attempted_at']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    function animateCounter(element, isDecimal = false, suffix = '') {
        const target = parseFloat(element.getAttribute('data-target')) || 0;
        const duration = 1200;
        const start = 0;
        const startTime = performance.now();

        function update(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            const value = start + (target - start) * progress;

            if (isDecimal) {
                element.textContent = value.toFixed(2) + suffix;
            } else {
                element.textContent = Math.floor(value) + suffix;
            }

            if (progress < 1) {
                requestAnimationFrame(update);
            } else {
                element.textContent = (isDecimal ? target.toFixed(2) : Math.floor(target)) + suffix;
            }
        }

        requestAnimationFrame(update);
    }

    document.querySelectorAll('.counter').forEach(el => animateCounter(el));
    document.querySelectorAll('.counter-decimal').forEach(el => animateCounter(el, true, '%'));

    window.addEventListener('load', () => {
        document.querySelectorAll('.progress-fill').forEach(bar => {
            const width = bar.getAttribute('data-width');
            setTimeout(() => {
                bar.style.width = width + '%';
            }, 250);
        });
    });
</script>
</body>
</html>
