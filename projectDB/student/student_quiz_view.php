<?php
require_once '../includes/config_session.inc.php';
require_once '../includes/dbh.inc.php';

if ($_SESSION['active_role'] !== 'student') {
    die("Access denied");
}

$user_id = $_SESSION['user_id'];
$quiz_id = $_GET['quiz_id'] ?? null;

if (!$quiz_id) {
    die("Quiz ID missing");
}

/* Fetch quiz + course */
$sql = "
    SELECT q.*, c.course_id, c.course_title
    FROM quizzes q
    JOIN courses c ON q.course_id = c.course_id
    WHERE q.quiz_id = ?
";
$stmt = $pdo->prepare($sql);
$stmt->execute([$quiz_id]);
$quiz = $stmt->fetch();

if (!$quiz) {
    die("Quiz not found");
}

/* Check enrollment */
$sql = "
    SELECT *
    FROM course_members
    WHERE course_id = ? AND user_id = ? AND member_role = 'student'
";
$stmt = $pdo->prepare($sql);
$stmt->execute([$quiz['course_id'], $user_id]);
if (!$stmt->fetch()) {
    die("You are not enrolled in this course");
}

/* Time window enforcement */
$currentTime = date('Y-m-d H:i:s');

if ($currentTime < $quiz['start_time'] || $currentTime > $quiz['end_time']) {
    die("Quiz is not accessible at this time");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz View - <?= htmlspecialchars($quiz['title']) ?></title>
    <link rel="stylesheet" href="../css/student_quiz_view.css?v=<?php echo time(); ?>">
</head>
<body>

<div class="view-container">
    <header class="view-header">
        <a href="student_quiz.php?course_id=<?= $quiz['course_id'] ?>" class="back-link">← Back to Quizzes</a>
        <h2><?= htmlspecialchars($quiz['title']) ?></h2>
        <div class="course-badge"><?= htmlspecialchars($quiz['course_title']) ?></div>
    </header>

    <div class="view-card">
        <div class="info-section">
            <div class="time-box">
                <span class="label">Start Time</span>
                <span class="value"><?= date('F j, Y, g:i A', strtotime($quiz['start_time'])) ?></span>
            </div>
            <div class="time-box">
                <span class="label">End Time</span>
                <span class="value"><?= date('F j, Y, g:i A', strtotime($quiz['end_time'])) ?></span>
            </div>
        </div>

        <div class="action-block">
            <h3>1. Get Your Questions</h3>
            <?php if (!empty($quiz['pdf_path'])): ?>
                <a href="<?= htmlspecialchars($quiz['pdf_path']) ?>" target="_blank" class="download-btn">
                    <span>📄</span> Download Quiz PDF
                </a>
            <?php else: ?>
                <p class="empty-msg">No quiz file uploaded by the teacher.</p>
            <?php endif; ?>
        </div>

        <div class="action-block highlight">
            <h3>2. Submit Your Work</h3>
            <?php if (!empty($quiz['submission_link'])): ?>
                <p class="instruction">Complete the quiz and submit your answers via the link below:</p>
                <a href="<?= htmlspecialchars($quiz['submission_link']) ?>" target="_blank" class="submit-btn">
                    <span>📝</span> Open Google Form Submission
                </a>
            <?php else: ?>
                <p class="empty-msg">No submission link provided yet.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>