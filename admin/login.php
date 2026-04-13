<?php
session_start();
require_once '../config/db.php';

if (isset($_SESSION['admin_id'])) {
    redirect('dashboard.php');
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = clean_input($_POST['username'] ?? '');
    $password = clean_input($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $message = 'Please enter both username and password.';
    } else {
        $stmt = mysqli_prepare($conn, 'SELECT id, username, password FROM admins WHERE username = ? LIMIT 1');
        mysqli_stmt_bind_param($stmt, 's', $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $admin = mysqli_fetch_assoc($result);

        if ($admin && password_matches($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['username'];
            redirect('dashboard.php');
        }

        $message = 'Invalid username or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<style>
body{
    min-height:100vh;
    margin:0;
    font-family:'Segoe UI',sans-serif;
    background:
        radial-gradient(circle at top left, rgba(220,53,69,0.12), transparent 25%),
        radial-gradient(circle at bottom right, rgba(255,99,132,0.10), transparent 30%),
        linear-gradient(135deg,#fff8f8,#fffdfd,#fefefe);
    overflow:hidden;
    position:relative;
}

.bg-shape{
    position:absolute;
    border-radius:50%;
    filter:blur(8px);
    animation:floatShape 8s ease-in-out infinite;
}

.shape-1{
    width:180px;
    height:180px;
    background:rgba(220,53,69,0.10);
    top:8%;
    left:8%;
}

.shape-2{
    width:240px;
    height:240px;
    background:rgba(255,99,132,0.08);
    bottom:10%;
    right:8%;
    animation-delay:1s;
}

@keyframes floatShape{
    0%,100%{transform:translateY(0);}
    50%{transform:translateY(-14px);}
}

.login-wrapper{
    min-height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:20px;
    position:relative;
    z-index:2;
}

.login-card{
    width:100%;
    max-width:470px;
    border-radius:28px;
    background:rgba(255,255,255,0.82);
    backdrop-filter:blur(14px);
    border:1px solid rgba(255,255,255,0.75);
    box-shadow:0 24px 60px rgba(80,110,160,0.14);
    overflow:hidden;
    animation:fadeUp .7s ease;
}

.top-strip{
    height:8px;
    background:linear-gradient(90deg,#dc3545,#ff6b81);
}

.card-content{
    padding:34px;
}

.login-badge{
    display:inline-flex;
    align-items:center;
    gap:8px;
    background:#ffe8e8;
    color:#dc3545;
    padding:8px 16px;
    border-radius:999px;
    font-size:.85rem;
    font-weight:700;
    margin-bottom:18px;
}

.login-title{
    font-size:2rem;
    font-weight:800;
    color:#1e293b;
    margin-bottom:8px;
    text-align:center;
}

.login-subtitle{
    text-align:center;
    color:#64748b;
    margin-bottom:28px;
    font-size:.95rem;
}

.form-label{
    font-weight:700;
    color:#334155;
}

.input-group-custom{
    position:relative;
}

.input-icon{
    position:absolute;
    top:50%;
    left:14px;
    transform:translateY(-50%);
    color:#94a3b8;
    z-index:2;
}

.form-control{
    height:52px;
    border-radius:14px;
    border:1px solid #dbe5ef;
    padding-left:42px;
    background:#fbfdff;
}

.form-control:focus{
    border-color:#f199a3;
    box-shadow:0 0 0 .18rem rgba(220,53,69,0.12);
}

.btn-login{
    height:52px;
    border:none;
    border-radius:14px;
    background:linear-gradient(135deg,#dc3545,#ff6b81);
    font-weight:700;
    color:white;
    box-shadow:0 14px 26px rgba(220,53,69,0.18);
    transition:.28s ease;
}

.btn-login:hover{
    transform:translateY(-2px);
    color:white;
}

.alert{
    border:none;
    border-radius:14px;
}

@keyframes fadeUp{
    from{
        opacity:0;
        transform:translateY(24px);
    }
    to{
        opacity:1;
        transform:translateY(0);
    }
}
</style>
</head>
<body>

<div class="bg-shape shape-1"></div>
<div class="bg-shape shape-2"></div>

<div class="login-wrapper">
    <div class="login-card">
        <div class="top-strip"></div>

        <div class="card-content">

            <div class="text-center">
                <div class="login-badge">
                    <i class="bi bi-shield-lock-fill"></i>
                    Secure Admin Access
                </div>
            </div>

            <h2 class="login-title">Admin Login</h2>
            <p class="login-subtitle">
                Sign in to manage tests, materials, questions, and student performance.
            </p>

            <?php if ($message !== ''): ?>
                <div class="alert alert-danger">
                    <?php echo e($message); ?>
                </div>
            <?php endif; ?>

            <form method="POST">

                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <div class="input-group-custom">
                        <i class="bi bi-person input-icon"></i>
                        <input type="text" class="form-control" name="username" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <div class="input-group-custom">
                        <i class="bi bi-lock input-icon"></i>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-login w-100">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Login as Admin
                </button>

            </form>

        </div>
    </div>
</div>

</body>
</html>
