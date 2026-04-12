<?php
// Start session — needed to store login info
session_start();

// If student is already logged in, send them to dashboard directly
if (isset($_SESSION["student_id"])) {
    header("Location: dashboard.php");
    exit();
}

// Include database connection
require_once "../config/db.php";

// Variable to hold error message
$message = "";

// This block runs only when the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Step 1: Get what the student typed
    $email    = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    // Step 2: Check if fields are empty
    if (empty($email) || empty($password)) {
        $message = "Please enter both email and password.";
    }

    else {
        // Step 3: Encrypt the password the same way we did during registration
        $hashed_password = md5($password);

        // Step 4: Search for a student with this email and password
        $sql    = "SELECT * FROM students WHERE email = '$email' AND password = '$hashed_password'";
        $result = mysqli_query($conn, $sql);

        // Step 5: Check if we found a matching student
        if (mysqli_num_rows($result) == 1) {

            // Get the student's data from the result
            $student = mysqli_fetch_assoc($result);

            // Step 6: Save student info in session
            $_SESSION["student_id"]   = $student["id"];
            $_SESSION["student_name"] = $student["name"];

            // Step 7: Redirect to dashboard
            header("Location: dashboard.php");
            exit();

        } else {
            // No matching student found
            $message = "Invalid email or password. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* Light grey background */
        body {
            background-color: #f0f2f5;
        }

        /* White card in center */
        .login-card {
            max-width: 450px;
            margin: 80px auto;
            background: #ffffff;
            padding: 35px 40px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        /* Title */
        .login-card h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #333;
            font-weight: 700;
        }

        /* Full width button */
        .btn-login {
            width: 100%;
            padding: 10px;
            font-size: 16px;
        }

        /* Register link at bottom */
        .register-link {
            text-align: center;
            margin-top: 15px;
        }
    </style>
</head>
<body>

<div class="login-card">

    <h2>🔐 Student Login</h2>

    <!-- Show error message if login fails -->
    <?php if (!empty($message)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Login Form -->
    <form method="POST" action="">

        <!-- Email -->
        <div class="mb-3">
            <label for="email" class="form-label">Email Address</label>
            <input
                type="email"
                class="form-control"
                id="email"
                name="email"
                placeholder="Enter your email"
                required
            >
        </div>

        <!-- Password -->
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input
                type="password"
                class="form-control"
                id="password"
                name="password"
                placeholder="Enter your password"
                required
            >
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-success btn-login">
            Login
        </button>

    </form>

    <!-- Link to Register page -->
    <div class="register-link">
        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>

</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>