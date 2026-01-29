<?php
require_once '../includes/config_session.inc.php';
require_once '../includes/dbh.inc.php';

if ($_SESSION['active_role'] !== 'student') {
    die("Access denied");
}

$course_id = $_GET['course_id'] ?? null;
$user_id = $_SESSION['user_id'];

/* Verify enrollment */
$sql = "SELECT c.course_title
        FROM courses c
        JOIN course_members cm ON c.course_id = cm.course_id
        WHERE cm.course_id = ? AND cm.user_id = ? AND cm.member_role = 'student'";
$stmt = $pdo->prepare($sql);
$stmt->execute([$course_id, $user_id]);
$course = $stmt->fetch();

if (!$course) {
    die("Not enrolled in this course");
}
?>

<link rel="stylesheet" href="../css/student_course.css">

<div class="course-management-container">
    <header class="course-header">
        <a href="../student_dashboard.php">← My Courses</a>
        <h1><?= htmlspecialchars($course['course_title']) ?></h1>
        <p>Student View</p>
    </header>

    <nav class="management-grid">
        <a href="student_posts.php?course_id=<?= $course_id ?>" class="nav-card">📢 Posts</a>
        <a href="student_calendar.php?course_id=<?= $course_id ?>" class="nav-card">📅 Calendar</a>
        <a href="student_assignments.php?course_id=<?= $course_id ?>" class="nav-card">📝 Assignments</a>
        <a href="student_classmates.php?course_id=<?= $course_id ?>" class="nav-card">👥 Classmates</a>
        <a href="student_quiz.php?course_id=<?= $course_id ?>" class="nav-card">💡 quiz</a>
        <a href="student_grade_view.php?course_id=<?= $course_id ?>" class="nav-card">📊 Gradesheet</a>
    </nav>
</div>
