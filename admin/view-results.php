<?php
session_start();
require_once '../config/db.php';
if (!isset($_SESSION['admin_id'])) redirect('login.php');

$sql = "SELECT r.*, s.name AS student_name, s.email, t.test_name
        FROM results r
        JOIN students s ON r.student_id = s.id
        JOIN tests t ON r.test_id = t.id
        ORDER BY r.attempted_at DESC";
$results = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Results</title>
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

        .page-header {
            animation: fadeDown 0.7s ease;
        }

        .page-title {
            font-size: 2rem;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 5px;
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

        .main-card {
            border: 1px solid rgba(255,255,255,0.8);
            border-radius: 24px;
            background: rgba(255,255,255,0.84);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            box-shadow: 0 18px 45px rgba(80, 110, 160, 0.10);
            animation: fadeUp 0.8s ease;
        }

        .table-card-body {
            padding: 22px;
        }

        .section-title {
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 0;
        }

        .results-table {
            margin-bottom: 0;
            vertical-align: middle;
        }

        .results-table thead th {
            border: none;
            background: #fff2f4;
            color: #7a3340;
            font-size: 0.92rem;
            font-weight: 700;
            padding: 16px 14px;
        }

        .results-table tbody tr {
            transition: all 0.25s ease;
        }

        .results-table tbody tr:hover {
            background: #fff8f9;
        }

        .results-table td {
            padding: 16px 14px;
            border-top: 1px solid #f0e9eb;
        }

        .student-name {
            font-weight: 700;
            color: #1e293b;
        }

        .email-text {
            color: #64748b;
            font-size: 0.92rem;
        }

        .score-pill {
            display: inline-block;
            padding: 7px 14px;
            border-radius: 999px;
            font-weight: 700;
            font-size: 0.88rem;
            background: #ffe8e8;
            color: #dc3545;
        }

        .percent-badge {
            display: inline-block;
            padding: 7px 12px;
            border-radius: 999px;
            font-size: 0.82rem;
            font-weight: 700;
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
            height: 9px;
            border-radius: 999px;
            background: #f4e9ec;
            overflow: hidden;
            margin-top: 7px;
        }

        .progress-bar {
            border-radius: 999px;
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
            background: linear-gradient(135deg, #fff1f3, #fff8f9);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: #dc3545;
            box-shadow: inset 0 0 0 1px #f4d6dc;
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
        }
    </style>
</head>
<body>
<div class="container py-4 py-md-5">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 page-header">
        <div>
            <h2 class="page-title">All Student Results</h2>
            <p class="page-subtitle">Review student performance across all mock tests from one place.</p>
        </div>
        <a href="dashboard.php" class="btn btn-outline-danger btn-back">
            <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
        </a>
    </div>

    <div class="main-card">
        <div class="table-card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="section-title">Performance Records</h5>
            </div>

            <div class="table-responsive">
                <table class="table results-table align-middle">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Email</th>
                            <th>Test</th>
                            <th>Score</th>
                            <th>Total</th>
                            <th style="min-width: 180px;">Percentage</th>
                            <th>Attempted At</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (mysqli_num_rows($results) === 0): ?>
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <div class="empty-icon">
                                        <i class="bi bi-bar-chart-line"></i>
                                    </div>
                                    <div class="empty-title">No results available yet</div>
                                    <p class="empty-text">
                                        Student test submissions will appear here once results are generated.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    <?php else: while ($row = mysqli_fetch_assoc($results)): $pct = $row['total_questions'] > 0 ? round(($row['score'] / $row['total_questions']) * 100, 2) : 0; ?>
                        <?php
                            if ($pct >= 85) {
                                $badgeClass = 'excellent';
                                $barClass = 'bg-success';
                            } elseif ($pct >= 70) {
                                $badgeClass = 'good';
                                $barClass = 'bg-primary';
                            } elseif ($pct >= 50) {
                                $badgeClass = 'average';
                                $barClass = 'bg-warning';
                            } else {
                                $badgeClass = 'needs-work';
                                $barClass = 'bg-danger';
                            }
                        ?>
                        <tr>
                            <td class="student-name"><?php echo e($row['student_name']); ?></td>
                            <td class="email-text"><?php echo e($row['email']); ?></td>
                            <td><?php echo e($row['test_name']); ?></td>
                            <td><span class="score-pill"><?php echo (int)$row['score']; ?></span></td>
                            <td><?php echo (int)$row['total_questions']; ?></td>
                            <td>
                                <span class="percent-badge <?php echo $badgeClass; ?>"><?php echo $pct; ?>%</span>
                                <div class="progress">
                                    <div class="progress-bar <?php echo $barClass; ?>" style="width: <?php echo $pct; ?>%;"></div>
                                </div>
                            </td>
                            <td><?php echo e($row['attempted_at']); ?></td>
                        </tr>
                    <?php endwhile; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
</body>
</html>