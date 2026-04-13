<?php
session_start();
if (isset($_SESSION['student_id'])) {
    redirect('dashboard.php');
}
require_once '../config/db.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = clean_input($_POST['email'] ?? '');
    $password = clean_input($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        $message = 'Please enter both email and password.';
    } else {
        $stmt = mysqli_prepare($conn, 'SELECT id, name, password FROM students WHERE email = ? LIMIT 1');
        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $student = mysqli_fetch_assoc($result);

        if ($student && password_matches($password, $student['password'])) {
            $_SESSION['student_id'] = $student['id'];
            $_SESSION['student_name'] = $student['name'];
            redirect('dashboard.php');
        }
        $message = 'Invalid email or password. Please try again.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background:
                radial-gradient(circle at top left, rgba(102, 126, 234, 0.18), transparent 28%),
                radial-gradient(circle at bottom right, rgba(118, 75, 162, 0.15), transparent 30%),
                linear-gradient(135deg, #f8fbff, #eef4ff, #f9fcff);
            overflow-x: hidden;
            position: relative;
        }

        .bg-shape {
            position: absolute;
            border-radius: 50%;
            filter: blur(8px);
            animation: floatShape 8s ease-in-out infinite;
            z-index: 0;
        }

        .shape-1 {
            width: 180px;
            height: 180px;
            background: rgba(13, 110, 253, 0.10);
            top: 8%;
            left: 6%;
        }

        .shape-2 {
            width: 240px;
            height: 240px;
            background: rgba(25, 135, 84, 0.08);
            bottom: 8%;
            right: 7%;
            animation-delay: 1s;
        }

        .shape-3 {
            width: 110px;
            height: 110px;
            background: rgba(255, 193, 7, 0.12);
            top: 20%;
            right: 16%;
            animation-delay: 2s;
        }

        @keyframes floatShape {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-14px);
            }
        }

        .page-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 15px;
            position: relative;
            z-index: 1;
        }

        .login-card {
            width: 100%;
            max-width: 500px;
            border: 1px solid rgba(255,255,255,0.7);
            border-radius: 28px;
            background: rgba(255,255,255,0.75);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            box-shadow: 0 20px 60px rgba(76, 110, 168, 0.16);
            overflow: hidden;
            animation: fadeUp 0.8s ease;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(28px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .top-strip {
            height: 8px;
            background: linear-gradient(90deg, #4facfe, #00c6ff, #43e97b);
        }

        .card-body-custom {
            padding: 38px 34px 30px;
        }

        .login-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #edf6ff;
            color: #0d6efd;
            padding: 8px 16px;
            border-radius: 999px;
            font-size: 0.88rem;
            font-weight: 600;
            margin-bottom: 18px;
            box-shadow: 0 6px 18px rgba(13, 110, 253, 0.08);
            animation: pulseSoft 2.5s infinite;
        }

        @keyframes pulseSoft {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.03);
            }
        }

        .login-title {
            font-size: 2rem;
            font-weight: 800;
            color: #1f2d3d;
            margin-bottom: 8px;
            text-align: center;
        }

        .login-subtitle {
            text-align: center;
            color: #6c7a89;
            font-size: 0.98rem;
            margin-bottom: 30px;
        }

        .form-label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
        }

        .input-group-custom {
            position: relative;
        }

        .input-icon {
            position: absolute;
            top: 50%;
            left: 15px;
            transform: translateY(-50%);
            color: #7a8ca5;
            font-size: 1rem;
            z-index: 2;
        }

        .form-control {
            height: 52px;
            border-radius: 14px;
            border: 1px solid #dce6f2;
            padding-left: 44px;
            background: #fbfdff;
            transition: all 0.25s ease;
            font-size: 0.96rem;
        }

        .form-control:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.22rem rgba(13, 110, 253, 0.12);
            background: #ffffff;
            transform: translateY(-1px);
        }

        .alert {
            border-radius: 14px;
            border: none;
            font-size: 0.95rem;
        }

        .btn-login {
            height: 52px;
            border: none;
            border-radius: 14px;
            background: linear-gradient(135deg, #0d6efd, #4facfe);
            font-weight: 700;
            font-size: 1rem;
            letter-spacing: 0.3px;
            color: #fff;
            box-shadow: 0 12px 24px rgba(13, 110, 253, 0.22);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 16px 28px rgba(13, 110, 253, 0.28);
        }

        .btn-login:active {
            transform: scale(0.98);
        }

        .btn-login::after {
            content: "";
            position: absolute;
            top: 0;
            left: -75%;
            width: 50%;
            height: 100%;
            background: rgba(255,255,255,0.25);
            transform: skewX(-20deg);
            transition: 0.6s;
        }

        .btn-login:hover::after {
            left: 130%;
        }

        .bottom-text {
            text-align: center;
            margin-top: 22px;
            color: #6b7280;
            font-size: 0.95rem;
        }

        .bottom-text a {
            color: #0d6efd;
            text-decoration: none;
            font-weight: 700;
            transition: 0.2s ease;
        }

        .bottom-text a:hover {
            color: #084298;
            text-decoration: underline;
        }

        .mini-features {
            display: flex;
            justify-content: center;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 20px;
        }

        .mini-chip {
            background: #ffffff;
            border: 1px solid #e8eef8;
            border-radius: 999px;
            padding: 7px 14px;
            font-size: 0.82rem;
            color: #5f6f82;
            box-shadow: 0 6px 14px rgba(0,0,0,0.04);
        }

        @media (max-width: 576px) {
            .card-body-custom {
                padding: 28px 20px 24px;
            }

            .login-title {
                font-size: 1.7rem;
            }

            .login-subtitle {
                font-size: 0.92rem;
            }
        }
    </style>
</head>
<body>

    <div class="bg-shape shape-1"></div>
    <div class="bg-shape shape-2"></div>
    <div class="bg-shape shape-3"></div>

    <div class="page-wrapper">
        <div class="login-card">
            <div class="top-strip"></div>
            <div class="card-body-custom">

                <div class="text-center">
                    <div class="login-badge">
                        <i class="bi bi-stars"></i> Welcome Back, Future Achiever
                    </div>
                </div>

                <h2 class="login-title">Student Login</h2>
                <p class="login-subtitle">Access your dashboard, track your progress, and get ready to level up.</p>

                <?php if ($message !== ''): ?>
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-circle me-2"></i><?php echo e($message); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" id="loginForm">
                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <div class="input-group-custom">
                            <i class="bi bi-envelope input-icon"></i>
                            <input type="email" class="form-control" name="email" placeholder="Enter your email" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <div class="input-group-custom">
                            <i class="bi bi-lock input-icon"></i>
                            <input type="password" class="form-control" name="password" placeholder="Enter your password" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-login w-100" id="loginBtn">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Login
                    </button>
                </form>

                <div class="mini-features">
                    <div class="mini-chip"><i class="bi bi-graph-up-arrow me-1"></i> Progress</div>
                    <div class="mini-chip"><i class="bi bi-award me-1"></i> Performance</div>
                    <div class="mini-chip"><i class="bi bi-lightning-charge me-1"></i> Practice</div>
                </div>

                <p class="bottom-text">
                    Don't have an account?
                    <a href="register.php">Register here</a>
                </p>
            </div>
        </div>
    </div>

    <audio id="clickSound" preload="auto">
        <source src="https://cdn.pixabay.com/download/audio/2022/03/15/audio_c8c8a73467.mp3?filename=click-124467.mp3" type="audio/mpeg">
    </audio>

    <script>
        const loginBtn = document.getElementById('loginBtn');
        const clickSound = document.getElementById('clickSound');

        loginBtn.addEventListener('click', function () {
            if (clickSound) {
                clickSound.volume = 0.2;
                clickSound.play().catch(() => {});
            }
        });
    </script>
</body>
</html>