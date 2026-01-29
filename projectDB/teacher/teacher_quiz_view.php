<?php
require_once '../includes/config_session.inc.php';
require_once '../includes/dbh.inc.php';

if ($_SESSION['active_role'] !== 'teacher') {
    die("Access denied");
}

$quiz_id    = $_GET['quiz_id'] ?? null;
$teacher_id = $_SESSION['user_id'];

if (!$quiz_id) {
    die("Quiz ID missing");
}

/* Fetch quiz + validate teacher ownership */
$sql = "
    SELECT q.*, c.course_title
    FROM quizzes q
    JOIN courses c ON q.course_id = c.course_id
    WHERE q.quiz_id = ? AND q.created_by = ?
";
$stmt = $pdo->prepare($sql);
$stmt->execute([$quiz_id, $teacher_id]);
$quiz = $stmt->fetch();

if (!$quiz) {
    die("Quiz not found or unauthorized");
}

$currentTime = date('Y-m-d H:i:s');

if ($currentTime < $quiz['start_time']) {
    $status = "Upcoming";
} elseif ($currentTime > $quiz['end_time']) {
    $status = "Ended";
} else {
    $status = "Running";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Quiz View</title>
    <link rel="stylesheet" href="../css/teacher_quiz_view.css">
</head>
<body>

<h2>Quiz Details</h2>

<p>
    <a href="teacher_quiz.php?course_id=<?= $quiz['course_id'] ?>">
        ← Back to Quiz List
    </a>
</p>

<hr>

<p><strong>Course:</strong> <?= htmlspecialchars($quiz['course_title']) ?></p>
<p><strong>Quiz Title:</strong> <?= htmlspecialchars($quiz['title']) ?></p>
<p><strong>Start Time:</strong> <?= htmlspecialchars($quiz['start_time']) ?></p>
<p><strong>End Time:</strong> <?= htmlspecialchars($quiz['end_time']) ?></p>
<p><strong>Status:</strong> <?= $status ?></p>

<hr>

<h3>Quiz Document</h3>

<?php if (!empty($quiz['pdf_path'])): ?>
    <p>
        <a href="<?= htmlspecialchars($quiz['pdf_path']) ?>" target="_blank">
            📄 Download Quiz PDF
        </a>
    </p>
<?php else: ?>
    <p>No PDF uploaded.</p>
<?php endif; ?>

<hr>

<!-- Future extensions -->
<!--
<h3>Student Submissions</h3>
<p>Coming soon...</p>
-->

</body>
</html>
