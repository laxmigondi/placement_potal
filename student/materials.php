<?php
session_start();

// Only logged in students can view this
if (!isset($_SESSION["student_id"])) {
    header("Location: login.php");
    exit();
}

require_once "../config/db.php";

$student_name = $_SESSION["student_name"];

// Fetch all materials from database
$materials = mysqli_query($conn, "SELECT * FROM materials ORDER BY uploaded_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Study Materials</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background-color: #f0f2f5;
            font-family: 'Segoe UI', sans-serif;
        }

        .navbar {
            background-color: #0d6efd;
            padding: 12px 25px;
        }

        .navbar-brand {
            color: white !important;
            font-weight: 700;
            font-size: 20px;
        }

        .nav-right {
            color: white;
            font-size: 14px;
        }

        /* Page header banner */
        .page-banner {
            background: linear-gradient(135deg, #0d6efd, #0a58ca);
            color: white;
            padding: 25px 30px;
            border-radius: 12px;
            margin-bottom: 30px;
        }

        .page-banner h4 {
            font-weight: 700;
            margin-bottom: 4px;
        }

        .page-banner p {
            opacity: 0.9;
            margin-bottom: 0;
            font-size: 14px;
        }

        /* Each material card */
        .material-card {
            background: white;
            border-radius: 12px;
            padding: 22px 25px;
            margin-bottom: 18px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.07);
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: transform 0.2s;
        }

        .material-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 18px rgba(0,0,0,0.12);
        }

        .material-card h6 {
            font-weight: 700;
            color: #333;
            margin-bottom: 5px;
            font-size: 16px;
        }

        .material-card p {
            font-size: 13px;
            color: #777;
            margin-bottom: 4px;
        }

        .material-card .date-text {
            font-size: 12px;
            color: #aaa;
        }

        .section-title {
            font-size: 20px;
            font-weight: 700;
            color: #333;
            margin-bottom: 20px;
            border-left: 4px solid #0d6efd;
            padding-left: 12px;
        }

        .back-btn {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar">
    <span class="navbar-brand">
        <i class="bi bi-mortarboard-fill"></i> Placement Portal
    </span>
    <div class="ms-auto nav-right">
        <i class="bi bi-person-circle"></i>
        <?php echo htmlspecialchars($student_name); ?>
        &nbsp;|&nbsp;
        <a href="logout.php" style="color:white;">
            <i class="bi bi-box-arrow-right"></i> Logout
        </a>
    </div>
</nav>

<div class="container mt-4">

    <!-- Back Button -->
    <div class="back-btn">
        <a href="dashboard.php" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <!-- Page Banner -->
    <div class="page-banner">
        <h4><i class="bi bi-book-fill"></i> Study Materials</h4>
        <p>Browse and access all placement preparation materials shared by your admin.</p>
    </div>

    <!-- Materials List -->
    <div class="section-title">Available Materials</div>

    <?php if (mysqli_num_rows($materials) == 0): ?>
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i>
            No study materials have been added yet. Please check back later.
        </div>
    <?php else: ?>
        <?php while ($row = mysqli_fetch_assoc($materials)): ?>
            <div class="material-card">
                <div>
                    <h6>
                        <i class="bi bi-file-earmark-text text-primary"></i>
                        <?php echo htmlspecialchars($row["title"]); ?>
                    </h6>
                    <p><?php echo htmlspecialchars($row["description"]); ?></p>
                    <span class="date-text">
                        <i class="bi bi-calendar3"></i>
                        Added on <?php echo date("d M Y", strtotime($row["uploaded_at"])); ?>
                    </span>
                </div>
                <div>
                    <a href="<?php echo $row['file_link']; ?>" target="_blank" class="btn btn-primary btn-sm">
                        <i class="bi bi-box-arrow-up-right"></i> Open
                    </a>
                </div>
            </div>
        <?php endwhile; ?>
    <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>