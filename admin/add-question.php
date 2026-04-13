<?php
session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit();
}

require_once "../config/db.php";

$message      = "";
$message_type = "";

$selected_test_id = isset($_GET["test_id"]) ? intval($_GET["test_id"]) : 0;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $test_id       = intval($_POST["test_id"]);
    $question_text = trim($_POST["question_text"]);
    $option_a      = trim($_POST["option_a"]);
    $option_b      = trim($_POST["option_b"]);
    $option_c      = trim($_POST["option_c"]);
    $option_d      = trim($_POST["option_d"]);
    $correct       = trim($_POST["correct_option"]);

    if (
        empty($question_text) || empty($option_a) ||
        empty($option_b) || empty($option_c) ||
        empty($option_d) || empty($correct) || $test_id == 0
    ) {
        $message      = "All fields are required.";
        $message_type = "danger";
    } else {
        $sql = "INSERT INTO questions
                    (test_id, question_text, option_a, option_b, option_c, option_d, correct_option)
                VALUES
                    ('$test_id', '$question_text', '$option_a', '$option_b', '$option_c', '$option_d', '$correct')";

        if (mysqli_query($conn, $sql)) {
            $message          = "Question added successfully!";
            $message_type     = "success";
            $selected_test_id = $test_id;
        } else {
            $message      = "Something went wrong. Please try again.";
            $message_type = "danger";
        }
    }
}

$all_tests = mysqli_query($conn, "SELECT * FROM tests ORDER BY created_at DESC");

$all_questions = mysqli_query($conn,
    "SELECT questions.*, tests.test_name
     FROM questions
     JOIN tests ON questions.test_id = tests.id
     ORDER BY questions.test_id, questions.id DESC"
);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin - Add Questions</title>

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
    transition:.2s;
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

.form-control,.form-select{
    border-radius:14px;
    padding:12px 14px;
    border:1px solid #dbe5ef;
    background:#fbfdff;
}

.form-control:focus,.form-select:focus{
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

.question-card{
    background:rgba(255,255,255,0.82);
    border:1px solid rgba(255,255,255,0.75);
    border-radius:20px;
    padding:22px;
    margin-bottom:18px;
    box-shadow:0 12px 26px rgba(80,110,160,0.07);
    transition:.28s ease;
}

.question-card:hover{
    transform:translateY(-4px);
    box-shadow:0 20px 36px rgba(80,110,160,0.12);
}

.test-badge{
    background:#ffe8e8;
    color:#dc3545;
    padding:6px 14px;
    border-radius:999px;
    font-size:.8rem;
    font-weight:700;
    display:inline-block;
    margin-bottom:12px;
}

.q-number{
    font-size:.82rem;
    color:#dc3545;
    font-weight:700;
    margin-bottom:6px;
}

.q-text{
    font-size:1rem;
    font-weight:700;
    color:#1e293b;
    margin-bottom:14px;
}

.option{
    padding:10px 14px;
    border-radius:12px;
    margin-bottom:8px;
    background:#f8fafc;
    font-size:.92rem;
    color:#475569;
}

.option.correct{
    background:#e8fff2;
    color:#198754;
    font-weight:700;
    border:1px solid #b7efce;
}

.alert{
    border:none;
    border-radius:14px;
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
            Add New Question
        </div>

        <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo $message_type; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <div class="mb-3">
                <label class="form-label">Select Test</label>
                <select class="form-select" name="test_id" required>
                    <option value="">-- Choose a Test --</option>
                    <?php mysqli_data_seek($all_tests, 0); while ($test = mysqli_fetch_assoc($all_tests)): ?>
                        <option value="<?php echo $test['id']; ?>"
                            <?php echo ($selected_test_id == $test['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($test['test_name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Question</label>
                <textarea class="form-control" name="question_text" rows="2"
                    placeholder="Type your question here" required></textarea>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Option A</label>
                    <input type="text" class="form-control" name="option_a" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Option B</label>
                    <input type="text" class="form-control" name="option_b" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Option C</label>
                    <input type="text" class="form-control" name="option_c" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Option D</label>
                    <input type="text" class="form-control" name="option_d" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Correct Answer</label>
                <select class="form-select" name="correct_option" required>
                    <option value="">-- Select Correct Option --</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                    <option value="D">D</option>
                </select>
            </div>

            <button type="submit" class="btn btn-red">
                <i class="bi bi-plus-lg me-1"></i> Add Question
            </button>

            <a href="manage-tests.php" class="btn btn-outline-secondary ms-2 rounded-4">
                <i class="bi bi-arrow-left"></i> Back to Tests
            </a>

        </form>
    </div>

    <div class="section-title">
        <i class="bi bi-list-ul text-danger"></i>
        All Questions
    </div>

    <?php if (mysqli_num_rows($all_questions) == 0): ?>
        <div class="alert alert-info rounded-4">No questions added yet.</div>
    <?php else: ?>
        <?php $counter = 1; while ($row = mysqli_fetch_assoc($all_questions)): ?>
            <div class="question-card">

                <div class="test-badge">
                    <i class="bi bi-clipboard2"></i>
                    <?php echo htmlspecialchars($row["test_name"]); ?>
                </div>

                <div class="q-number">Question <?php echo $counter; ?></div>

                <div class="q-text">
                    <?php echo htmlspecialchars($row["question_text"]); ?>
                </div>

                <?php
                foreach (["A","B","C","D"] as $opt):
                    $key = "option_" . strtolower($opt);
                    $is_correct = ($row["correct_option"] == $opt);
                ?>
                    <div class="option <?php echo $is_correct ? 'correct' : ''; ?>">
                        <?php echo $is_correct ? "✓" : "○"; ?>
                        <strong><?php echo $opt; ?>.</strong>
                        <?php echo htmlspecialchars($row[$key]); ?>
                        <?php echo $is_correct ? " ← Correct" : ""; ?>
                    </div>
                <?php endforeach; ?>

            </div>
        <?php $counter++; endwhile; ?>
    <?php endif; ?>

</div>

</body>
</html>