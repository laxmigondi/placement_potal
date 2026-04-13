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

    $title       = trim($_POST["title"]);
    $description = trim($_POST["description"]);
    $file_link   = trim($_POST["file_link"]);

    if (empty($title) || empty($description) || empty($file_link)) {
        $message      = "All fields are required.";
        $message_type = "danger";
    } else {
        $sql = "INSERT INTO materials (title, description, file_link)
                VALUES ('$title', '$description', '$file_link')";

        if (mysqli_query($conn, $sql)) {
            $message      = "Material added successfully!";
            $message_type = "success";
        } else {
            $message      = "Something went wrong. Please try again.";
            $message_type = "danger";
        }
    }
}

$all_materials = mysqli_query($conn, "SELECT * FROM materials ORDER BY uploaded_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin - Add Material</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<style>
body{
    background:
        radial-gradient(circle at top left, rgba(220,53,69,0.08), transparent 25%),
        radial-gradient(circle at bottom right, rgba(255,99,132,0.08), transparent 30%),
        linear-gradient(135deg,#fff8f8,#fffdfd,#fefefe);
    font-family:'Segoe UI',sans-serif;
    min-height:100vh;
}

.navbar{
    background:rgba(255,255,255,0.82);
    backdrop-filter:blur(12px);
    border-bottom:1px solid rgba(255,255,255,0.75);
    padding:14px 28px;
    box-shadow:0 8px 28px rgba(0,0,0,0.05);
}

.navbar-brand{
    color:#dc3545 !important;
    font-weight:800;
    font-size:1.2rem;
}

.nav-right{
    color:#475569;
    font-weight:600;
}

.nav-right a{
    color:#dc3545;
    text-decoration:none;
    font-weight:700;
}

.glass-card{
    background:rgba(255,255,255,0.84);
    backdrop-filter:blur(12px);
    border:1px solid rgba(255,255,255,0.75);
    border-radius:24px;
    padding:28px;
    box-shadow:0 16px 38px rgba(80,110,160,0.08);
    margin-bottom:30px;
    animation:fadeUp .7s ease;
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
    background:#fff;
}

.btn-add{
    border:none;
    border-radius:14px;
    padding:12px 22px;
    font-weight:700;
    background:linear-gradient(135deg,#dc3545,#ff6b81);
    color:white;
    box-shadow:0 12px 24px rgba(220,53,69,0.18);
    transition:.28s ease;
}

.btn-add:hover{
    transform:translateY(-2px);
    color:white;
}

.alert{
    border:none;
    border-radius:14px;
}

.material-item{
    background:rgba(255,255,255,0.82);
    border:1px solid rgba(255,255,255,0.75);
    border-radius:20px;
    padding:20px 22px;
    margin-bottom:16px;
    box-shadow:0 12px 26px rgba(80,110,160,0.07);
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:20px;
    transition:.28s ease;
}

.material-item:hover{
    transform:translateY(-4px);
    box-shadow:0 20px 36px rgba(80,110,160,0.12);
}

.material-item h6{
    font-weight:800;
    margin-bottom:5px;
    color:#1e293b;
}

.material-item p{
    margin-bottom:5px;
    font-size:.93rem;
    color:#64748b;
}

.badge-date{
    font-size:.82rem;
    color:#94a3b8;
}

.view-btn{
    border-radius:12px;
    font-weight:700;
}

@keyframes fadeUp{
    from{
        opacity:0;
        transform:translateY(20px);
    }
    to{
        opacity:1;
        transform:translateY(0);
    }
}

@media(max-width:768px){
    .material-item{
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
        <i class="bi bi-person-circle"></i>
        <?php echo htmlspecialchars($_SESSION["admin_name"]); ?>
        &nbsp;|&nbsp;
        <a href="logout.php">
            <i class="bi bi-box-arrow-right"></i> Logout
        </a>
    </div>
</nav>

<div class="container py-4">

    <div class="glass-card">
        <div class="section-title">
            <i class="bi bi-plus-circle-fill text-danger"></i>
            Add New Study Material
        </div>

        <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo $message_type; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <div class="mb-3">
                <label class="form-label">Material Title</label>
                <input type="text" class="form-control" name="title"
                       placeholder="e.g. Aptitude Preparation Notes" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea class="form-control" name="description" rows="3"
                          placeholder="Brief description of this material" required></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Material Link (URL)</label>
                <input type="url" class="form-control" name="file_link"
                       placeholder="https://drive.google.com/your-file-link" required>
                <div class="form-text mt-2">
                    Paste a Google Drive link, PDF link, or any website link.
                </div>
            </div>

            <button type="submit" class="btn btn-add">
                <i class="bi bi-plus-lg me-1"></i> Add Material
            </button>

        </form>
    </div>

    <div class="section-title">
        <i class="bi bi-list-ul text-danger"></i>
        All Study Materials
    </div>

    <?php if (mysqli_num_rows($all_materials) == 0): ?>
        <div class="alert alert-info rounded-4">No materials added yet.</div>
    <?php else: ?>
        <?php while ($row = mysqli_fetch_assoc($all_materials)): ?>
            <div class="material-item">
                <div>
                    <h6><i class="bi bi-file-earmark-text text-danger"></i> <?php echo htmlspecialchars($row["title"]); ?></h6>
                    <p><?php echo htmlspecialchars($row["description"]); ?></p>
                </div>

                <div class="text-end">
                    <a href="<?php echo $row['file_link']; ?>" target="_blank"
                       class="btn btn-sm btn-outline-danger view-btn mb-1">
                        <i class="bi bi-box-arrow-up-right"></i> View
                    </a>
                    <br>
                    <span class="badge-date">
                        <?php echo date("d M Y", strtotime($row["uploaded_at"])); ?>
                    </span>
                </div>
            </div>
        <?php endwhile; ?>
    <?php endif; ?>

</div>

</body>
</html>