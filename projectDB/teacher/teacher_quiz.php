<?php
require_once '../includes/config_session.inc.php';
require_once '../includes/dbh.inc.php';

if ($_SESSION['active_role'] !== 'teacher') {
    die("Access denied");
}

$course_id  = $_GET['course_id'] ?? null;
$teacher_id = $_SESSION['user_id'];

if (!$course_id) {
    die("Course ID missing");
}

/* Validate teacher owns this course */
$sql = "SELECT * FROM courses WHERE course_id = ? AND teacher_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$course_id, $teacher_id]);
$course = $stmt->fetch();

if (!$course) {
    die("Unauthorized course access");
}

/* Fetch quizzes for this course */
$sql = "SELECT * FROM quizzes 
        WHERE course_id = ? AND created_by = ?
        ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$course_id, $teacher_id]);
$quizzes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$currentTime = date('Y-m-d H:i:s');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Quizzes - <?= htmlspecialchars($course['course_title']) ?></title>
    <link rel="stylesheet" href="../css/teacher_quiz.css">
</head>
<body>

<h2>Quizzes for: <?= htmlspecialchars($course['course_title']) ?></h2>

<p>
    <a href="teacher_course.php?course_id=<?= $course_id ?>">← Back to Course</a>
</p>

<hr>

<!-- Create Quiz Button -->
<p>
    <a href="teacher_quiz_create.php?course_id=<?= $course_id ?>">
        ➕ Create New Quiz
    </a>
</p>

<hr>

<?php if (empty($quizzes)): ?>
    <p>No quizzes created yet.</p>
<?php else: ?>
    <table border="1" cellpadding="8" cellspacing="0">
        <thead>
            <tr>
                <th>Quiz Title</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($quizzes as $quiz): ?>
            <?php
                if ($currentTime < $quiz['start_time']) {
                    $status = "Upcoming";
                } elseif ($currentTime > $quiz['end_time']) {
                    $status = "Ended";
                } else {
                    $status = "Running";
                }
            ?>
            <tr>
                <td><?= htmlspecialchars($quiz['title']) ?></td>
                <td><?= htmlspecialchars($quiz['start_time']) ?></td>
                <td><?= htmlspecialchars($quiz['end_time']) ?></td>
                <td><?= $status ?></td>
                <td>
                    <a href="teacher_quiz_view.php?quiz_id=<?= $quiz['quiz_id'] ?>">
                        View
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

</body>
</html>
