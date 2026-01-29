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
    header("Location: teacher_course.php?course_id=" . urlencode($course_id));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Quiz - <?= htmlspecialchars($course['course_title']) ?></title>
    <link rel="stylesheet" href="../css/teacher_quiz_create.css">
</head>
<body>

<div class="form-wrapper">
    <div class="form-card">
        <div class="form-header">
            <a href="teacher_quiz.php?course_id=<?= $course_id ?>" class="back-link">
                ← Back to Quizzes
            </a>
            <h2>Create New Quiz</h2>
            <div class="course-badge">Course: <?= htmlspecialchars($course['course_title']) ?></div>
        </div>

        <form action="teacher_quiz_action.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="course_id" value="<?= $course_id ?>">

            <div class="form-group">
                <label for="title">Quiz Title</label>
                <input type="text" name="title" id="title" placeholder="Enter quiz title" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Start Time</label>
                    <input type="datetime-local" name="start_time" required>
                </div>
                <div class="form-group">
                    <label>End Time</label>
                    <input type="datetime-local" name="end_time" required>
                </div>
            </div>

            <div class="form-group">
                <label>Upload Question PDF</label>
                <div class="file-area">
                    <input type="file" name="quiz_pdf" accept="application/pdf" required>
                </div>
            </div>
            <div class="form-group">
                <label for="submission_link">Submission Link (Google Form):</label><br>
                <input type="url" name="submission_link" id="submission_link"
                       placeholder="https://forms.google.com/..."
                       required><br><br>
            </div>

            <button type="submit" class="btn-submit">Generate Quiz</button>
        </form>
    </div>
</div>

</body>
</html>