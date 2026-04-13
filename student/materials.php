<?php
session_start();

if (!isset($_SESSION["student_id"])) {
    header("Location: login.php");
    exit();
}

require_once "../config/db.php";

$student_name = $_SESSION["student_name"];
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
body{
    background:
        radial-gradient(circle at top left, rgba(13,110,253,0.08), transparent 25%),
        radial-gradient(circle at bottom right, rgba(25,135,84,0.08), transparent 30%),
        linear-gradient(135deg,#f8fbff,#eef4ff,#fcfdff);
    font-family:'Segoe UI',sans-serif;
    min-height:100vh;
}

.navbar{
    background:rgba(255,255,255,0.8);
    backdrop-filter:blur(12px);
    border-bottom:1px solid rgba(255,255,255,0.7);
    padding:14px 26px;
    box-shadow:0 8px 24px rgba(0,0,0,0.05);
}

.navbar-brand{
    font-weight:800;
    font-size:20px;
    color:#0d6efd !important;
}

.nav-right{
    font-size:14px;
    font-weight:600;
    color:#475569;
}

.nav-right a{
    text-decoration:none;
    color:#dc3545;
    font-weight:700;
}

.back-btn .btn{
    border-radius:14px;
    font-weight:600;
    padding:10px 18px;
}

.page-banner{
    background:linear-gradient(135deg,#0d6efd,#4facfe);
    color:white;
    padding:30px;
    border-radius:24px;
    margin-bottom:30px;
    box-shadow:0 20px 40px rgba(13,110,253,0.18);
    animation:fadeDown .7s ease;
}

.page-banner h4{
    font-weight:800;
    font-size:1.7rem;
    margin-bottom:6px;
}

.page-banner p{
    opacity:.92;
    margin:0;
}

.section-title{
    font-size:1.3rem;
    font-weight:800;
    margin-bottom:20px;
    color:#1e293b;
}

.material-card{
    background:rgba(255,255,255,0.82);
    backdrop-filter:blur(12px);
    border:1px solid rgba(255,255,255,0.75);
    border-radius:22px;
    padding:22px;
    margin-bottom:18px;
    box-shadow:0 14px 32px rgba(80,110,160,0.08);
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:20px;
    transition:.3s ease;
    animation:fadeUp .6s ease;
}

.material-card:hover{
    transform:translateY(-5px);
    box-shadow:0 24px 40px rgba(80,110,160,0.15);
}

.material-left{
    display:flex;
    align-items:flex-start;
    gap:16px;
}

.file-icon{
    width:54px;
    height:54px;
    border-radius:18px;
    background:linear-gradient(135deg,#e8f1ff,#f4f8ff);
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:1.4rem;
    color:#0d6efd;
    flex-shrink:0;
}

.material-card h6{
    font-weight:800;
    margin-bottom:6px;
    color:#1e293b;
    font-size:1.05rem;
}

.material-card p{
    margin-bottom:6px;
    color:#64748b;
    font-size:.94rem;
}

.date-text{
    font-size:.83rem;
    color:#94a3b8;
}

.open-btn{
    border-radius:14px;
    padding:10px 18px;
    font-weight:700;
    box-shadow:0 10px 20px rgba(13,110,253,0.15);
}

.empty-state{
    background:rgba(255,255,255,0.85);
    border-radius:24px;
    padding:50px 20px;
    text-align:center;
    box-shadow:0 14px 32px rgba(80,110,160,0.08);
}

.empty-state i{
    font-size:2.5rem;
    color:#94a3b8;
    margin-bottom:12px;
}

@keyframes fadeUp{
    from{opacity:0;transform:translateY(20px);}
    to{opacity:1;transform:translateY(0);}
}

@keyframes fadeDown{
    from{opacity:0;transform:translateY(-20px);}
    to{opacity:1;transform:translateY(0);}
}

@media(max-width:768px){
    .material-card{
        flex-direction:column;
        align-items:flex-start;
    }

    .page-banner h4{
        font-size:1.4rem;
    }
}
</style>
</head>
<body>

<nav class="navbar">
    <span class="navbar-brand">
        <i class="bi bi-mortarboard-fill"></i> Placement Portal
    </span>

    <div class="ms-auto nav-right">
        <i class="bi bi-person-circle"></i>
        <?php echo htmlspecialchars($student_name); ?>
        &nbsp;|&nbsp;
        <a href="logout.php">
            <i class="bi bi-box-arrow-right"></i> Logout
        </a>
    </div>
</nav>

<div class="container py-4">

    <div class="back-btn mb-3">
        <a href="dashboard.php" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <div class="page-banner">
        <h4><i class="bi bi-book-fill me-2"></i>Study Materials</h4>
        <p>Browse curated resources, notes, PDFs, and preparation material uploaded by admin.</p>
    </div>

    <div class="section-title">Available Materials</div>

    <?php if (mysqli_num_rows($materials) == 0): ?>
        <div class="empty-state">
            <i class="bi bi-folder-x"></i>
            <h4 class="fw-bold">No Materials Yet</h4>
            <p class="text-muted mb-0">Study materials will appear here once uploaded by admin.</p>
        </div>
    <?php else: ?>
        <?php while ($row = mysqli_fetch_assoc($materials)): ?>
            <div class="material-card">
                <div class="material-left">
                    <div class="file-icon">
                        <i class="bi bi-file-earmark-text"></i>
                    </div>

                    <div>
                        <h6><?php echo htmlspecialchars($row["title"]); ?></h6>
                        <p><?php echo htmlspecialchars($row["description"]); ?></p>
                        <span class="date-text">
                            <i class="bi bi-calendar3"></i>
                            Added on <?php echo date("d M Y", strtotime($row["uploaded_at"])); ?>
                        </span>
                    </div>
                </div>

                <a href="<?php echo $row['file_link']; ?>" target="_blank" class="btn btn-primary open-btn">
                    <i class="bi bi-box-arrow-up-right me-1"></i> Open
                </a>
            </div>
        <?php endwhile; ?>
    <?php endif; ?>

</div>

</body>
</html>