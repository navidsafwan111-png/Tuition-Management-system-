<?php
require_once '../includes/config_session.inc.php';
require_once '../includes/dbh.inc.php';

if ($_SESSION['active_role'] !== 'teacher') {
    die("Access denied");
}

$course_id = $_GET['course_id'] ?? null;
$teacher_id = $_SESSION['user_id'];

// Validate course ownership
$sql = "SELECT * FROM courses WHERE course_id = ? AND teacher_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$course_id, $teacher_id]);
$course = $stmt->fetch();

if (!$course) {
    die("Course not found or unauthorized");
}
?>

<link rel="stylesheet" href="../css/teacher_course.css">
<div class="course-management-container">
    <header class="course-header">
        <div class="header-top">
            <a href="../teacher_dashboard.php" class="back-link">← Back to Dashboard</a>
        </div>
        <h1><?= htmlspecialchars($course['course_title']) ?></h1>
        <p class="course-code">Instructor Control Panel • ID: <?= htmlspecialchars($course_id) ?></p>
    </header>

    <nav class="management-grid">
        <a href="teacher_posts.php?course_id=<?= $course_id ?>" class="nav-card">
            <span class="icon">📢</span>
            <div class="nav-text">
                <h3>Announcements</h3>
                <p>Post updates and news</p>
            </div>
        </a>

        <a href="teacher_calendar.php?course_id=<?= $course_id ?>" class="nav-card">
            <span class="icon">📅</span>
            <div class="nav-text">
                <h3>Schedules</h3>
                <p>Deadlines and events</p>
            </div>
        </a>

        <a href="teacher_assignments.php?course_id=<?= $course_id ?>" class="nav-card">
            <span class="icon">📁</span>
            <div class="nav-text">
                <h3>Assignments</h3>
                <p>Manage tasks and submissions</p>
            </div>
        </a>
 
        <a href="teacher_quiz.php?course_id=<?= $course_id ?>" class="nav-card">
            <span class="icon">💡</span>
            <div class="nav-text">
                <h3>Quiz Center</h3>
                <p>Create and review quizzes</p>
            </div>
        </a>        

        <a href="teacher_grade_entry.php?course_id=<?= $course_id ?>" class="nav-card">
            <span class="icon">📊</span>
            <div class="nav-text">
                <h3>Gradesheet</h3>
                <p>Manage student performance</p>
            </div>
        </a>           

        <a href="teacher_students.php?course_id=<?= $course_id ?>" class="nav-card">
            <span class="icon">👥</span>
            <div class="nav-text">
                <h3>Student Roster</h3>
                <p>View enrollment and activity</p>
            </div>
        </a>
    </nav>
</div>