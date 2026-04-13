<?php
session_start();
require_once '../config/db.php';
if (!isset($_SESSION['student_id'])) redirect('login.php');

$student_id = (int)$_SESSION['student_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $test_id = (int)($_POST['test_id'] ?? 0);
    $qstmt = mysqli_prepare($conn, 'SELECT id, correct_option FROM questions WHERE test_id = ?');
    mysqli_stmt_bind_param($qstmt, 'i', $test_id);
    mysqli_stmt_execute($qstmt);
    $qres = mysqli_stmt_get_result($qstmt);

    $score = 0;
    $total = 0;
    while ($q = mysqli_fetch_assoc($qres)) {
        $total++;
        $answer = $_POST['answers'][$q['id']] ?? '';
        if ($answer === $q['correct_option']) $score++;
    }

    $insert = mysqli_prepare($conn, 'INSERT INTO results (student_id, test_id, score, total_questions) VALUES (?, ?, ?, ?)');
    mysqli_stmt_bind_param($insert, 'iiii', $student_id, $test_id, $score, $total);
    mysqli_stmt_execute($insert);
    redirect('results.php?success=1');
}

$selected_test_id = (int)($_GET['test_id'] ?? 0);
$test = null;
$questions = null;
if ($selected_test_id > 0) {
    $tstmt = mysqli_prepare($conn, 'SELECT * FROM tests WHERE id = ? LIMIT 1');
    mysqli_stmt_bind_param($tstmt, 'i', $selected_test_id);
    mysqli_stmt_execute($tstmt);
    $test = mysqli_fetch_assoc(mysqli_stmt_get_result($tstmt));

    if ($test) {
        $qstmt = mysqli_prepare($conn, 'SELECT * FROM questions WHERE test_id = ? ORDER BY id ASC');
        mysqli_stmt_bind_param($qstmt, 'i', $selected_test_id);
        mysqli_stmt_execute($qstmt);
        $questions = mysqli_stmt_get_result($qstmt);
    }
}

$tests = mysqli_query($conn, 'SELECT t.*, COUNT(q.id) AS question_count FROM tests t LEFT JOIN questions q ON t.id = q.test_id GROUP BY t.id ORDER BY t.created_at DESC');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mock Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            background:
                radial-gradient(circle at top left, rgba(13, 110, 253, 0.10), transparent 25%),
                radial-gradient(circle at bottom right, rgba(25, 135, 84, 0.10), transparent 28%),
                linear-gradient(135deg, #f8fbff, #eef4ff, #fcfdff);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #1f2937;
        }

        .page-header {
            animation: fadeDown 0.7s ease;
        }

        .page-title {
            font-size: 2rem;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 5px;
        }

        .page-subtitle {
            color: #64748b;
            margin-bottom: 0;
        }

        .btn-back {
            border-radius: 14px;
            padding: 10px 18px;
            font-weight: 600;
            transition: all 0.25s ease;
        }

        .btn-back:hover {
            transform: translateY(-2px);
        }

        .glass-box {
            border: 1px solid rgba(255,255,255,0.8);
            border-radius: 24px;
            background: rgba(255,255,255,0.82);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            box-shadow: 0 18px 45px rgba(80, 110, 160, 0.11);
        }

        .timer-box {
            border: none;
            border-radius: 20px;
            background: linear-gradient(135deg, #eef6ff, #f8fbff);
            box-shadow: 0 10px 24px rgba(13, 110, 253, 0.08);
            padding: 20px;
            animation: fadeUp 0.7s ease;
        }

        .timer-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
        }

        .timer-label {
            font-size: 0.95rem;
            font-weight: 700;
            color: #0d6efd;
            letter-spacing: 0.2px;
        }

        .timer-value {
            font-size: 1.4rem;
            font-weight: 800;
            color: #1e293b;
        }

        .timer-progress {
            height: 12px;
            background: #e6eef8;
            border-radius: 999px;
            overflow: hidden;
            margin-top: 14px;
        }

        .timer-progress-bar {
            height: 100%;
            width: 100%;
            border-radius: 999px;
            background: linear-gradient(90deg, #0d6efd, #4facfe, #43e97b);
            transition: width 1s linear;
        }

        .test-card {
            padding: 28px;
            animation: fadeUp 0.8s ease;
        }

        .test-heading {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 14px;
            flex-wrap: wrap;
            margin-bottom: 24px;
        }

        .test-title {
            font-size: 1.7rem;
            font-weight: 800;
            margin-bottom: 6px;
            color: #1e293b;
        }

        .test-meta {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .meta-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            border-radius: 999px;
            background: #f3f8ff;
            color: #46607f;
            font-weight: 600;
            font-size: 0.88rem;
            border: 1px solid #e4edf7;
        }

        .question-card {
            border: 1px solid #e7eef7;
            border-radius: 20px;
            background: linear-gradient(180deg, #ffffff, #fbfdff);
            padding: 22px;
            margin-bottom: 22px;
            box-shadow: 0 10px 24px rgba(31, 41, 55, 0.04);
            transition: all 0.3s ease;
        }

        .question-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 18px 34px rgba(31, 41, 55, 0.08);
        }

        .question-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: linear-gradient(135deg, #e8f1ff, #f3f8ff);
            color: #0d6efd;
            font-weight: 800;
            margin-right: 10px;
            font-size: 0.95rem;
        }

        .question-text {
            font-size: 1.05rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 18px;
            line-height: 1.55;
            display: flex;
            align-items: flex-start;
        }

        .option-item {
            margin-bottom: 12px;
        }

        .option-input {
            display: none;
        }

        .option-label {
            display: flex;
            align-items: center;
            gap: 12px;
            width: 100%;
            padding: 14px 16px;
            border-radius: 16px;
            border: 1px solid #dfe8f2;
            background: #ffffff;
            cursor: pointer;
            font-weight: 600;
            color: #334155;
            transition: all 0.25s ease;
            position: relative;
            overflow: hidden;
        }

        .option-label:hover {
            background: #f7fbff;
            border-color: #bfd6f8;
            transform: translateX(3px);
        }

        .option-letter {
            width: 34px;
            height: 34px;
            min-width: 34px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #eff5fb;
            color: #0d6efd;
            font-weight: 800;
        }

        .option-input:checked + .option-label {
            background: linear-gradient(135deg, #eaf3ff, #f4f9ff);
            border-color: #86b7fe;
            box-shadow: 0 10px 22px rgba(13, 110, 253, 0.10);
            transform: translateX(4px);
        }

        .option-input:checked + .option-label .option-letter {
            background: linear-gradient(135deg, #0d6efd, #4facfe);
            color: #fff;
        }

        .submit-btn {
            border: none;
            border-radius: 16px;
            padding: 14px 26px;
            font-weight: 700;
            background: linear-gradient(135deg, #198754, #43e97b);
            color: #fff;
            box-shadow: 0 14px 28px rgba(25, 135, 84, 0.22);
            transition: all 0.3s ease;
            font-size: 1rem;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 18px 34px rgba(25, 135, 84, 0.28);
        }

        .submit-btn:active {
            transform: scale(0.98);
        }

        .tests-grid-card {
            padding: 24px;
            height: 100%;
            border: 1px solid rgba(255,255,255,0.75);
            border-radius: 24px;
            background: rgba(255,255,255,0.84);
            box-shadow: 0 16px 36px rgba(80, 110, 160, 0.10);
            transition: all 0.32s ease;
            animation: fadeUp 0.75s ease;
        }

        .tests-grid-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 24px 44px rgba(80, 110, 160, 0.16);
        }

        .test-icon-box {
            width: 58px;
            height: 58px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            margin-bottom: 16px;
            background: linear-gradient(135deg, #e8f1ff, #f4f8ff);
            color: #0d6efd;
        }

        .test-card-title {
            font-size: 1.2rem;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 10px;
        }

        .test-info {
            color: #64748b;
            font-size: 0.95rem;
            margin-bottom: 7px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .start-btn {
            margin-top: 16px;
            border-radius: 14px;
            font-weight: 700;
            padding: 11px 18px;
            transition: all 0.28s ease;
        }

        .start-btn:hover:not(.disabled) {
            transform: translateY(-2px);
        }

        .empty-tests {
            padding: 50px 18px;
            text-align: center;
        }

        .empty-tests i {
            font-size: 2.3rem;
            color: #94a3b8;
            margin-bottom: 12px;
        }

        .danger-time {
            color: #dc3545 !important;
            animation: pulseDanger 1s infinite;
        }

        .warning-time {
            color: #fd7e14 !important;
        }

        @keyframes pulseDanger {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.04); }
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(24px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeDown {
            from {
                opacity: 0;
                transform: translateY(-18px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 768px) {
            .page-title {
                font-size: 1.65rem;
            }

            .test-title {
                font-size: 1.35rem;
            }

            .question-card,
            .test-card {
                padding: 18px;
            }

            .timer-value {
                font-size: 1.15rem;
            }
        }
    </style>
</head>
<body>
<div class="container py-4 py-md-5">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 page-header">
        <div>
            <h2 class="page-title">Mock Tests</h2>
            <p class="page-subtitle">Practice smarter, improve faster, and make every attempt count.</p>
        </div>
        <a href="dashboard.php" class="btn btn-outline-primary btn-back">
            <i class="bi bi-arrow-left me-2"></i>Back
        </a>
    </div>

<?php if ($test && $questions && mysqli_num_rows($questions) > 0): ?>

    <?php
        $durationSeconds = ((int)$test['duration_minutes']) * 60;
        $questionCount = mysqli_num_rows($questions);
        mysqli_data_seek($questions, 0);
    ?>

    <div class="timer-box mb-4">
        <div class="timer-header">
            <div>
                <div class="timer-label">
                    <i class="bi bi-stopwatch me-2"></i>Time Remaining
                </div>
                <div class="timer-value" id="timer" data-seconds="<?php echo $durationSeconds; ?>">00:00</div>
            </div>
            <div class="test-meta">
                <span class="meta-pill"><i class="bi bi-clock-history"></i><?php echo (int)$test['duration_minutes']; ?> Minutes</span>
                <span class="meta-pill"><i class="bi bi-patch-question"></i><?php echo $questionCount; ?> Questions</span>
            </div>
        </div>
        <div class="timer-progress">
            <div class="timer-progress-bar" id="timerProgressBar"></div>
        </div>
    </div>

    <form method="POST" id="testForm">
        <input type="hidden" name="test_id" value="<?php echo (int)$test['id']; ?>">

        <div class="glass-box test-card">
            <div class="test-heading">
                <div>
                    <h3 class="test-title"><?php echo e($test['test_name']); ?></h3>
                    <p class="text-muted mb-0">Choose the best answer for each question and submit before time runs out.</p>
                </div>
                <div class="meta-pill">
                    <i class="bi bi-lightning-charge-fill"></i> Stay focused
                </div>
            </div>

            <?php $i = 1; while ($q = mysqli_fetch_assoc($questions)): ?>
                <div class="question-card">
                    <div class="question-text">
                        <span class="question-number"><?php echo $i; ?></span>
                        <span><?php echo e($q['question_text']); ?></span>
                    </div>

                    <?php foreach (['A'=>'option_a','B'=>'option_b','C'=>'option_c','D'=>'option_d'] as $opt => $field): ?>
                        <?php $inputId = 'q' . (int)$q['id'] . $opt; ?>
                        <div class="option-item">
                            <input
                                class="option-input"
                                type="radio"
                                name="answers[<?php echo (int)$q['id']; ?>]"
                                value="<?php echo $opt; ?>"
                                id="<?php echo $inputId; ?>"
                            >
                            <label class="option-label" for="<?php echo $inputId; ?>">
                                <span class="option-letter"><?php echo $opt; ?></span>
                                <span><?php echo e($q[$field]); ?></span>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php $i++; endwhile; ?>

            <button class="submit-btn" type="submit" id="submitBtn">
                <i class="bi bi-check2-circle me-2"></i>Submit Test
            </button>
        </div>
    </form>

<?php else: ?>

    <div class="row g-4">
        <?php
        $hasTests = false;
        while ($row = mysqli_fetch_assoc($tests)):
            $hasTests = true;
        ?>
            <div class="col-md-6 col-lg-4">
                <div class="tests-grid-card">
                    <div class="test-icon-box">
                        <i class="bi bi-journal-code"></i>
                    </div>

                    <h5 class="test-card-title"><?php echo e($row['test_name']); ?></h5>

                    <div class="test-info">
                        <i class="bi bi-clock"></i>
                        <span><?php echo (int)$row['duration_minutes']; ?> minutes</span>
                    </div>

                    <div class="test-info">
                        <i class="bi bi-list-check"></i>
                        <span><?php echo (int)$row['question_count']; ?> questions</span>
                    </div>

                    <a class="btn btn-primary start-btn <?php echo ((int)$row['question_count'] === 0) ? 'disabled' : ''; ?>"
                       href="mock-test.php?test_id=<?php echo (int)$row['id']; ?>">
                        <i class="bi bi-play-fill me-1"></i>
                        <?php echo ((int)$row['question_count'] === 0) ? 'No Questions Yet' : 'Start Test'; ?>
                    </a>
                </div>
            </div>
        <?php endwhile; ?>

        <?php if (!$hasTests): ?>
            <div class="col-12">
                <div class="glass-box empty-tests">
                    <i class="bi bi-clipboard-x"></i>
                    <h4 class="fw-bold">No tests available right now</h4>
                    <p class="text-muted mb-0">New mock tests will appear here once they are added by the admin.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>

<?php endif; ?>

</div>

<audio id="selectSound" preload="auto">
    <source src="https://cdn.pixabay.com/download/audio/2022/03/10/audio_c8630c24c2.mp3?filename=click-124467.mp3" type="audio/mpeg">
</audio>

<audio id="submitSound" preload="auto">
    <source src="https://cdn.pixabay.com/download/audio/2022/03/15/audio_c8c8a73467.mp3?filename=click-124467.mp3" type="audio/mpeg">
</audio>

<script>
    const timerEl = document.getElementById('timer');
    const timerProgressBar = document.getElementById('timerProgressBar');
    const testForm = document.getElementById('testForm');
    const selectSound = document.getElementById('selectSound');
    const submitSound = document.getElementById('submitSound');
    const submitBtn = document.getElementById('submitBtn');

    document.querySelectorAll('.option-input').forEach(input => {
        input.addEventListener('change', () => {
            if (selectSound) {
                selectSound.volume = 0.18;
                selectSound.play().catch(() => {});
            }
        });
    });

    if (submitBtn) {
        submitBtn.addEventListener('click', () => {
            if (submitSound) {
                submitSound.volume = 0.22;
                submitSound.play().catch(() => {});
            }
        });
    }

    if (timerEl) {
        let totalSeconds = parseInt(timerEl.getAttribute('data-seconds')) || 0;
        let remainingSeconds = totalSeconds;

        function formatTime(seconds) {
            const mins = Math.floor(seconds / 60);
            const secs = seconds % 60;
            return String(mins).padStart(2, '0') + ':' + String(secs).padStart(2, '0');
        }

        function updateTimerUI() {
            timerEl.textContent = formatTime(remainingSeconds);

            const progressPercent = totalSeconds > 0 ? (remainingSeconds / totalSeconds) * 100 : 0;
            if (timerProgressBar) {
                timerProgressBar.style.width = progressPercent + '%';
            }

            timerEl.classList.remove('warning-time', 'danger-time');

            if (remainingSeconds <= 60) {
                timerEl.classList.add('danger-time');
            } else if (remainingSeconds <= 180) {
                timerEl.classList.add('warning-time');
            }
        }

        updateTimerUI();

        const countdown = setInterval(() => {
            remainingSeconds--;
            updateTimerUI();

            if (remainingSeconds <= 0) {
                clearInterval(countdown);
                if (testForm) {
                    testForm.submit();
                }
            }
        }, 1000);
    }
</script>
</body>
</html>