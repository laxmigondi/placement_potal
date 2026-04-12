<?php
session_start();
require_once "../config/db.php";

$message = "";
$message_type = "";

// ---------- PHP VALIDATION (runs after form submit) ----------
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name     = trim($_POST["name"]);
    $email    = trim($_POST["email"]);
    $phone    = trim($_POST["phone"]);
    $password = trim($_POST["password"]);
    $confirm  = trim($_POST["confirm_password"]);

    // 1. Check empty fields
    if (empty($name) || empty($email) || empty($phone) || empty($password) || empty($confirm)) {
        $message = "All fields are required.";
        $message_type = "danger";
    }

    // 2. Validate email format using PHP filter
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address (e.g. name@gmail.com).";
        $message_type = "danger";
    }

    // 3. Validate phone — must be exactly 10 digits
    elseif (!preg_match('/^[0-9]{10}$/', $phone)) {
        $message = "Phone number must be exactly 10 digits (numbers only).";
        $message_type = "danger";
    }

    // 4. Password rules:
    // - At least 8 characters
    // - At least 1 uppercase letter
    // - At least 1 number
    // - At least 1 special character
    elseif (strlen($password) < 8) {
        $message = "Password must be at least 8 characters long.";
        $message_type = "danger";
    }
    elseif (!preg_match('/[A-Z]/', $password)) {
        $message = "Password must contain at least one uppercase letter.";
        $message_type = "danger";
    }
    elseif (!preg_match('/[0-9]/', $password)) {
        $message = "Password must contain at least one number.";
        $message_type = "danger";
    }
    elseif (!preg_match('/[\W]/', $password)) {
        $message = "Password must contain at least one special character (e.g. @, #, !).";
        $message_type = "danger";
    }

    // 5. Check password match
    elseif ($password !== $confirm) {
        $message = "Passwords do not match.";
        $message_type = "danger";
    }

    // 6. Check duplicate email
    else {
        $check_email = mysqli_query($conn, "SELECT id FROM students WHERE email = '$email'");

        if (mysqli_num_rows($check_email) > 0) {
            $message = "This email is already registered. Please login.";
            $message_type = "danger";
        }

        // 7. All good — save to database
        else {
            $hashed_password = md5($password);
            $sql = "INSERT INTO students (name, email, phone, password)
                    VALUES ('$name', '$email', '$phone', '$hashed_password')";

            if (mysqli_query($conn, $sql)) {
                $message = "Registration successful! You can now login.";
                $message_type = "success";
            } else {
                $message = "Something went wrong. Please try again.";
                $message_type = "danger";
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

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f0f2f5;
        }

        .register-card {
            max-width: 500px;
            margin: 50px auto;
            background: #ffffff;
            padding: 35px 40px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .register-card h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #333;
            font-weight: 700;
        }

        .btn-register {
            width: 100%;
            padding: 10px;
            font-size: 16px;
        }

        .login-link {
            text-align: center;
            margin-top: 15px;
        }

        /* Password rules hint box */
        .password-rules {
            font-size: 13px;
            color: #666;
            background: #f8f9fa;
            border-left: 4px solid #0d6efd;
            padding: 8px 12px;
            border-radius: 4px;
            margin-top: 6px;
        }

        /* Each rule shown as green tick or red cross */
        .rule {
            display: flex;
            align-items: center;
            gap: 6px;
            margin: 3px 0;
            font-size: 13px;
        }

        .rule.valid {
            color: green;
        }

        .rule.invalid {
            color: red;
        }
    </style>
</head>
<body>

<div class="register-card">

    <h2>📝 Student Registration</h2>

    <!-- Success or Error Message -->
    <?php if (!empty($message)): ?>
        <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <form method="POST" action="" id="registerForm" novalidate>

        <!-- Full Name -->
        <div class="mb-3">
            <label for="name" class="form-label">Full Name</label>
            <input
                type="text"
                class="form-control"
                id="name"
                name="name"
                placeholder="Enter your full name"
                required
            >
            <div class="invalid-feedback">Please enter your full name.</div>
        </div>

        <!-- Email -->
        <div class="mb-3">
            <label for="email" class="form-label">Email Address</label>
            <input
                type="email"
                class="form-control"
                id="email"
                name="email"
                placeholder="e.g. name@gmail.com"
                required
            >
            <!-- Live feedback shown below email field -->
            <div id="emailFeedback" class="form-text" style="color:red; display:none;">
                Please enter a valid email (e.g. name@gmail.com).
            </div>
        </div>

        <!-- Phone -->
        <div class="mb-3">
            <label for="phone" class="form-label">Phone Number</label>
            <input
                type="text"
                class="form-control"
                id="phone"
                name="phone"
                placeholder="Enter 10-digit phone number"
                maxlength="10"
                required
            >
            <!-- Live feedback shown below phone field -->
            <div id="phoneFeedback" class="form-text" style="color:red; display:none;">
                Phone number must be exactly 10 digits.
            </div>
        </div>

        <!-- Password -->
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input
                type="password"
                class="form-control"
                id="password"
                name="password"
                placeholder="Create a strong password"
                required
            >

            <!-- Live password rules checklist -->
            <div class="password-rules mt-2" id="passwordRules">
                <div class="rule invalid" id="rule-length">✗ At least 8 characters</div>
                <div class="rule invalid" id="rule-upper">✗ At least 1 uppercase letter (A-Z)</div>
                <div class="rule invalid" id="rule-number">✗ At least 1 number (0-9)</div>
                <div class="rule invalid" id="rule-special">✗ At least 1 special character (@, #, ! etc.)</div>
            </div>
        </div>

        <!-- Confirm Password -->
        <div class="mb-3">
            <label for="confirm_password" class="form-label">Confirm Password</label>
            <input
                type="password"
                class="form-control"
                id="confirm_password"
                name="confirm_password"
                placeholder="Re-enter your password"
                required
            >
            <!-- Live feedback for password match -->
            <div id="matchFeedback" class="form-text" style="display:none;"></div>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary btn-register" id="submitBtn">
            Register
        </button>

    </form>

    <div class="login-link">
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>

</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// ============================================================
// JAVASCRIPT LIVE VALIDATION
// Runs instantly as the user types — before form is submitted
// ============================================================

// --- EMAIL VALIDATION ---
document.getElementById("email").addEventListener("input", function () {
    const email = this.value;
    // Simple pattern: must have @ and a dot after it
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const feedback = document.getElementById("emailFeedback");

    if (email.length > 0 && !emailPattern.test(email)) {
        feedback.style.display = "block"; // Show error
    } else {
        feedback.style.display = "none";  // Hide error
    }
});

// --- PHONE VALIDATION ---
document.getElementById("phone").addEventListener("input", function () {
    // Allow only numbers to be typed
    this.value = this.value.replace(/[^0-9]/g, "");

    const feedback = document.getElementById("phoneFeedback");

    if (this.value.length > 0 && this.value.length !== 10) {
        feedback.style.display = "block"; // Show error if not 10 digits
    } else {
        feedback.style.display = "none";  // Hide when exactly 10 digits
    }
});

// --- PASSWORD RULES LIVE CHECKER ---
document.getElementById("password").addEventListener("input", function () {
    const password = this.value;

    // Check each rule and update the tick/cross
    checkRule("rule-length",  password.length >= 8);
    checkRule("rule-upper",   /[A-Z]/.test(password));
    checkRule("rule-number",  /[0-9]/.test(password));
    checkRule("rule-special", /[\W]/.test(password));

    // Also check password match if confirm field has value
    checkPasswordMatch();
});

// Helper function to show ✓ or ✗ for each rule
function checkRule(ruleId, isValid) {
    const el = document.getElementById(ruleId);
    const text = el.textContent.substring(2); // Remove old ✓/✗ symbol

    if (isValid) {
        el.className = "rule valid";
        el.textContent = "✓ " + text;
    } else {
        el.className = "rule invalid";
        el.textContent = "✗ " + text;
    }
}

// --- CONFIRM PASSWORD MATCH ---
document.getElementById("confirm_password").addEventListener("input", checkPasswordMatch);

function checkPasswordMatch() {
    const password = document.getElementById("password").value;
    const confirm  = document.getElementById("confirm_password").value;
    const feedback = document.getElementById("matchFeedback");

    if (confirm.length === 0) {
        feedback.style.display = "none";
        return;
    }

    if (password === confirm) {
        feedback.style.display = "block";
        feedback.style.color   = "green";
        feedback.textContent   = "✓ Passwords match!";
    } else {
        feedback.style.display = "block";
        feedback.style.color   = "red";
        feedback.textContent   = "✗ Passwords do not match.";
    }
}

// --- BLOCK FORM SUBMIT IF JS ERRORS EXIST ---
document.getElementById("registerForm").addEventListener("submit", function (e) {
    const email   = document.getElementById("email").value;
    const phone   = document.getElementById("phone").value;
    const password = document.getElementById("password").value;
    const confirm  = document.getElementById("confirm_password").value;

    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    // Stop submission if any rule is broken
    if (!emailPattern.test(email)) {
        e.preventDefault();
        document.getElementById("emailFeedback").style.display = "block";
        return;
    }

    if (phone.length !== 10) {
        e.preventDefault();
        document.getElementById("phoneFeedback").style.display = "block";
        return;
    }

    if (
        password.length < 8 ||
        !/[A-Z]/.test(password) ||
        !/[0-9]/.test(password) ||
        !/[\W]/.test(password)
    ) {
        e.preventDefault();
        alert("Please make sure your password meets all the rules shown.");
        return;
    }

    if (password !== confirm) {
        e.preventDefault();
        alert("Passwords do not match.");
        return;
    }
});
</script>

</body>
</html>