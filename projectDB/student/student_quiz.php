<?php
require_once '../includes/config_session.inc.php';
require_once '../includes/dbh.inc.php';

if ($_SESSION['active_role'] !== 'student') {
    die("Access denied");
}

$user_id   = $_SESSION['user_id'];
$course_id = $_GET['course_id'] ?? null;

if (!$course_id) {
    die("Course ID missing");
}

/* Check student enrollment */
$sql = "
    SELECT *
    FROM course_members
    WHERE course_id = ? AND user_id = ? AND member_role = 'student'
";
$stmt = $pdo->prepare($sql);
$stmt->execute([$course_id, $user_id]);
if (!$stmt->fetch()) {
    die("You are not enrolled in this course");
}

/* Fetch quizzes */
$sql = "
    SELECT *
    FROM quizzes
    WHERE course_id = ?
    ORDER BY start_time ASC
";
$stmt = $pdo->prepare($sql);
$stmt->execute([$course_id]);
$quizzes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$currentTime = date('Y-m-d H:i:s');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Quizzes</title>
    <link rel="stylesheet" href="../css/<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Quizzes</title>
    <link rel="stylesheet" href="../css/student_quiz.css?v=<?php echo time(); ?>">
</head>
<body>

<div class="quiz-container">
    <header class="quiz-header">
        <h2>Course Quizzes</h2>
        <a href="../student_course.php?course_id=<?= $course_id ?>" class="back-link">← Back to Course</a>
    </header>

    <div class="quiz-list">
        <?php if (!$quizzes): ?>
            <div class="empty-state">
                <p>No quizzes available for this course yet.</p>
            </div>
        <?php endif; ?>

        <?php foreach ($quizzes as $quiz): ?>
            <?php
                if ($currentTime < $quiz['start_time']) {
                    $status = "Upcoming";
                    $statusClass = "upcoming";
                    $canAccess = false;
                } elseif ($currentTime > $quiz['end_time']) {
                    $status = "Ended";
                    $statusClass = "ended";
                    $canAccess = false;
                } else {
                    $status = "Running";
                    $statusClass = "running";
                    $canAccess = true;
                }
            ?>

            <div class="quiz-card <?= $statusClass ?>">
                <div class="quiz-details">
                    <span class="status-badge"><?= $status ?></span>
                    <h3><?= htmlspecialchars($quiz['title']) ?></h3>
                    
                    <div class="quiz-timing">
                        <div class="time-row">
                            <strong>Starts:</strong> <?= date('M j, Y - g:i A', strtotime($quiz['start_time'])) ?>
                        </div>
                        <div class="time-row">
                            <strong>Ends:</strong> <?= date('M j, Y - g:i A', strtotime($quiz['end_time'])) ?>
                        </div>
                    </div>
                </div>

                <div class="quiz-action">
                    <?php if ($canAccess): ?>
                        <a href="student_quiz_view.php?quiz_id=<?= $quiz['quiz_id'] ?>" class="btn-primary">
                            Attempt Quiz
                        </a>
                    <?php else: ?>
                        <span class="btn-locked">Access Locked</span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

</body>
</html>?v=<?php echo time(); ?>">
</head>
<body>

