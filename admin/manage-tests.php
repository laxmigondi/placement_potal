<?php
session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit();
}

require_once "../config/db.php";

$message      = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $test_name = trim($_POST["test_name"]);
    $duration  = trim($_POST["duration"]);

    if (empty($test_name) || empty($duration)) {
        $message      = "All fields are required.";
        $message_type = "danger";
    }

    elseif (!is_numeric($duration) || $duration <= 0) {
        $message      = "Duration must be a valid number greater than 0.";
        $message_type = "danger";
    }

    else {
        $sql = "INSERT INTO tests (test_name, duration_minutes)
                VALUES ('$test_name', '$duration')";

        if (mysqli_query($conn, $sql)) {
            $message      = "Test created successfully!";
            $message_type = "success";
        } else {
            $message      = "Something went wrong. Please try again.";
            $message_type = "danger";
        }
    }
}

$all_tests = mysqli_query($conn,
    "SELECT tests.*, COUNT(questions.id) as question_count
     FROM tests
     LEFT JOIN questions ON tests.id = questions.test_id
     GROUP BY tests.id
     ORDER BY tests.created_at DESC"
);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin - Manage Tests</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<style>
body{
    background:
        radial-gradient(circle at top left, rgba(220,53,69,0.08), transparent 25%),
        radial-gradient(circle at bottom right, rgba(255,99,132,0.08), transparent 30%),
        linear-gradient(135deg,#fff8f8,#fffdfd,#fefefe);
    font-family:'Segoe UI',sans-serif;
}

.navbar{
    background:rgba(255,255,255,0.82);
    backdrop-filter:blur(12px);
    padding:14px 28px;
    border-bottom:1px solid rgba(255,255,255,0.75);
    box-shadow:0 8px 28px rgba(0,0,0,0.05);
}

.navbar-brand{
    color:#dc3545 !important;
    font-weight:800;
    font-size:1.2rem;
}

.nav-right a{
    color:#475569;
    text-decoration:none;
    font-weight:600;
    margin-left:18px;
}

.nav-right a:hover{
    color:#dc3545;
}

.glass-card{
    background:rgba(255,255,255,0.84);
    backdrop-filter:blur(12px);
    border:1px solid rgba(255,255,255,0.75);
    border-radius:24px;
    padding:28px;
    box-shadow:0 16px 38px rgba(80,110,160,0.08);
    margin-bottom:30px;
}

.section-title{
    font-size:1.25rem;
    font-weight:800;
    color:#1e293b;
    margin-bottom:22px;
    display:flex;
    align-items:center;
    gap:10px;
}

.form-label{
    font-weight:700;
    color:#334155;
}

.form-control{
    border-radius:14px;
    padding:12px 14px;
    border:1px solid #dbe5ef;
    background:#fbfdff;
}

.form-control:focus{
    border-color:#f199a3;
    box-shadow:0 0 0 .18rem rgba(220,53,69,0.12);
}

.btn-red{
    border:none;
    border-radius:14px;
    padding:12px 22px;
    font-weight:700;
    background:linear-gradient(135deg,#dc3545,#ff6b81);
    color:white;
    box-shadow:0 12px 24px rgba(220,53,69,0.18);
}

.btn-red:hover{
    color:white;
    transform:translateY(-2px);
}

.test-card{
    background:rgba(255,255,255,0.82);
    border:1px solid rgba(255,255,255,0.75);
    border-radius:20px;
    padding:22px;
    margin-bottom:18px;
    box-shadow:0 12px 26px rgba(80,110,160,0.07);
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:20px;
    transition:.28s ease;
}

.test-card:hover{
    transform:translateY(-4px);
    box-shadow:0 20px 36px rgba(80,110,160,0.12);
}

.test-card h6{
    font-weight:800;
    color:#1e293b;
    margin-bottom:6px;
}

.test-card p{
    margin-bottom:0;
    color:#64748b;
    font-size:.9rem;
}

.badge-duration{
    background:#fff3cd;
    color:#856404;
    padding:6px 14px;
    border-radius:999px;
    font-size:.8rem;
    font-weight:700;
    margin-right:8px;
}

.badge-questions{
    background:#e8f4fd;
    color:#0d6efd;
    padding:6px 14px;
    border-radius:999px;
    font-size:.8rem;
    font-weight:700;
}

.alert{
    border:none;
    border-radius:14px;
}

@media(max-width:768px){
    .test-card{
        flex-direction:column;
        align-items:flex-start;
    }
}
</style>
</head>
<body>

<nav class="navbar">
    <span class="navbar-brand">
        <i class="bi bi-shield-fill"></i> Admin Panel
    </span>

    <div class="ms-auto nav-right">
        <a href="add-material.php"><i class="bi bi-book"></i> Materials</a>
        <a href="manage-tests.php"><i class="bi bi-clipboard2"></i> Tests</a>
        <a href="add-question.php"><i class="bi bi-question-circle"></i> Questions</a>
        <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
    </div>
</nav>

<div class="container py-4">

    <div class="glass-card">
        <div class="section-title">
            <i class="bi bi-plus-circle-fill text-danger"></i>
            Create New Test
        </div>

        <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo $message_type; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <div class="mb-3">
                <label class="form-label">Test Name</label>
                <input type="text" class="form-control" name="test_name"
                       placeholder="e.g. Aptitude Mock Test 1" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Duration (in minutes)</label>
                <input type="number" class="form-control" name="duration"
                       min="1" max="180" placeholder="30" required>
                <div class="form-text mt-2">
                    Enter the duration students get to complete this test.
                </div>
            </div>

            <button type="submit" class="btn btn-red">
                <i class="bi bi-plus-lg me-1"></i> Create Test
            </button>

        </form>
    </div>

    <div class="section-title">
        <i class="bi bi-list-check text-danger"></i>
        All Tests
    </div>

    <?php if (mysqli_num_rows($all_tests) == 0): ?>
        <div class="alert alert-info rounded-4">
            No tests created yet. Create your first test above.
        </div>
    <?php else: ?>
        <?php while ($row = mysqli_fetch_assoc($all_tests)): ?>
            <div class="test-card">

                <div>
                    <h6>
                        <i class="bi bi-clipboard2-check text-danger"></i>
                        <?php echo htmlspecialchars($row["test_name"]); ?>
                    </h6>

                    <p>
                        Created on <?php echo date("d M Y", strtotime($row["created_at"])); ?>
                    </p>
                </div>

                <div class="text-end">
                    <span class="badge-duration">
                        <i class="bi bi-clock"></i>
                        <?php echo $row["duration_minutes"]; ?> mins
                    </span>

                    <span class="badge-questions">
                        <i class="bi bi-question-circle"></i>
                        <?php echo $row["question_count"]; ?> Questions
                    </span>

                    <div class="mt-2">
                        <a href="add-question.php?test_id=<?php echo $row['id']; ?>"
                           class="btn btn-sm btn-outline-danger rounded-4">
                            <i class="bi bi-plus"></i> Add Questions
                        </a>
                    </div>
                </div>

            </div>
        <?php endwhile; ?>
    <?php endif; ?>

</div>

</body>
</html>