<?php
session_start();
require_once '../config/db.php';

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = clean_input($_POST['name'] ?? '');
    $email = clean_input($_POST['email'] ?? '');
    $phone = clean_input($_POST['phone'] ?? '');
    $password = clean_input($_POST['password'] ?? '');
    $confirm = clean_input($_POST['confirm_password'] ?? '');

    if ($name === '' || $email === '' || $phone === '' || $password === '' || $confirm === '') {
        $message = 'All fields are required.';
        $message_type = 'danger';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Please enter a valid email address.';
        $message_type = 'danger';
    } elseif (!preg_match('/^[0-9]{10}$/', $phone)) {
        $message = 'Phone number must be exactly 10 digits.';
        $message_type = 'danger';
    } elseif (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password) || !preg_match('/[^A-Za-z0-9]/', $password)) {
        $message = 'Password must be at least 8 characters and include an uppercase letter, number, and special character.';
        $message_type = 'danger';
    } elseif ($password !== $confirm) {
        $message = 'Passwords do not match.';
        $message_type = 'danger';
    } else {
        $stmt = mysqli_prepare($conn, 'SELECT id FROM students WHERE email = ?');
        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        $exists = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($exists) > 0) {
            $message = 'This email is already registered. Please login.';
            $message_type = 'danger';
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $insert = mysqli_prepare($conn, 'INSERT INTO students (name, email, phone, password) VALUES (?, ?, ?, ?)');
            mysqli_stmt_bind_param($insert, 'ssss', $name, $email, $phone, $hashed_password);

            if (mysqli_stmt_execute($insert)) {
                $message = 'Registration successful! You can now login.';
                $message_type = 'success';
            } else {
                $message = 'Something went wrong. Please try again.';
                $message_type = 'danger';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background:
                radial-gradient(circle at top left, rgba(13, 110, 253, 0.16), transparent 25%),
                radial-gradient(circle at bottom right, rgba(25, 135, 84, 0.10), transparent 30%),
                linear-gradient(135deg, #f8fbff, #eef4ff, #fcfdff);
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
            bottom: 6%;
            right: 7%;
            animation-delay: 1s;
        }

        .shape-3 {
            width: 110px;
            height: 110px;
            background: rgba(255, 193, 7, 0.12);
            top: 18%;
            right: 15%;
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

        .register-card {
            width: 100%;
            max-width: 560px;
            border: 1px solid rgba(255,255,255,0.75);
            border-radius: 30px;
            background: rgba(255,255,255,0.80);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            box-shadow: 0 22px 60px rgba(76, 110, 168, 0.16);
            overflow: hidden;
            animation: fadeUp 0.8s ease;
        }

        .top-strip {
            height: 8px;
            background: linear-gradient(90deg, #4facfe, #00c6ff, #43e97b);
        }

        .card-body-custom {
            padding: 36px 34px 30px;
        }

        .register-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #edf6ff;
            color: #0d6efd;
            padding: 8px 16px;
            border-radius: 999px;
            font-size: 0.88rem;
            font-weight: 700;
            margin-bottom: 18px;
            box-shadow: 0 6px 18px rgba(13, 110, 253, 0.08);
        }

        .register-title {
            font-size: 2rem;
            font-weight: 800;
            color: #1f2d3d;
            margin-bottom: 8px;
            text-align: center;
        }

        .register-subtitle {
            text-align: center;
            color: #6c7a89;
            font-size: 0.97rem;
            margin-bottom: 28px;
        }

        .form-label {
            font-weight: 700;
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

        textarea.form-control {
            min-height: 110px;
            padding-top: 14px;
        }

        .form-control:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.22rem rgba(13, 110, 253, 0.12);
            background: #ffffff;
            transform: translateY(-1px);
        }

        .form-text {
            color: #7b8797;
            font-size: 0.84rem;
        }

        .alert {
            border-radius: 14px;
            border: none;
            font-size: 0.95rem;
        }

        .btn-register {
            height: 52px;
            border: none;
            border-radius: 14px;
            background: linear-gradient(135deg, #0d6efd, #4facfe);
            font-weight: 700;
            font-size: 1rem;
            letter-spacing: 0.2px;
            color: #fff;
            box-shadow: 0 12px 24px rgba(13, 110, 253, 0.22);
            transition: all 0.3s ease;
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 16px 28px rgba(13, 110, 253, 0.28);
            color: #fff;
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
        }

        .bottom-text a:hover {
            text-decoration: underline;
        }

        .mini-features {
            display: flex;
            justify-content: center;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 18px;
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

        @media (max-width: 576px) {
            .card-body-custom {
                padding: 28px 20px 24px;
            }

            .register-title {
                font-size: 1.7rem;
            }
        }
    </style>
</head>
<body>

    <div class="bg-shape shape-1"></div>
    <div class="bg-shape shape-2"></div>
    <div class="bg-shape shape-3"></div>

    <div class="page-wrapper">
        <div class="register-card">
            <div class="top-strip"></div>
            <div class="card-body-custom">

                <div class="text-center">
                    <div class="register-badge">
                        <i class="bi bi-stars"></i> Start Your Preparation Journey
                    </div>
                </div>

                <h2 class="register-title">Student Registration</h2>
                <p class="register-subtitle">Create your account and unlock mock tests, materials, and progress tracking.</p>

                <?php if ($message !== ''): ?>
                    <div class="alert alert-<?php echo e($message_type); ?>">
                        <?php echo e($message); ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <div class="input-group-custom">
                            <i class="bi bi-person input-icon"></i>
                            <input type="text" class="form-control" name="name" placeholder="Enter your full name" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <div class="input-group-custom">
                            <i class="bi bi-envelope input-icon"></i>
                            <input type="email" class="form-control" name="email" placeholder="Enter your email" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <div class="input-group-custom">
                            <i class="bi bi-telephone input-icon"></i>
                            <input type="text" class="form-control" name="phone" maxlength="10" placeholder="Enter 10-digit phone number" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <div class="input-group-custom">
                            <i class="bi bi-lock input-icon"></i>
                            <input type="password" class="form-control" name="password" placeholder="Create a strong password" required>
                        </div>
                        <div class="form-text mt-2">At least 8 characters, 1 uppercase, 1 number, and 1 special character.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Confirm Password</label>
                        <div class="input-group-custom">
                            <i class="bi bi-shield-lock input-icon"></i>
                            <input type="password" class="form-control" name="confirm_password" placeholder="Re-enter your password" required>
                        </div>
                    </div>

                    <button class="btn btn-register w-100" type="submit">
                        <i class="bi bi-person-check-fill me-2"></i>Register
                    </button>

                    <div class="mini-features">
                        <div class="mini-chip"><i class="bi bi-journal-check me-1"></i> Mock Tests</div>
                        <div class="mini-chip"><i class="bi bi-book-half me-1"></i> Materials</div>
                        <div class="mini-chip"><i class="bi bi-graph-up-arrow me-1"></i> Progress</div>
                    </div>

                    <p class="bottom-text">Already have an account? <a href="login.php">Login here</a></p>
                </form>
            </div>
        </div>
    </div>

</body>
</html>