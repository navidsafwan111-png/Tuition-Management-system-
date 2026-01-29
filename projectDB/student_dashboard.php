<?php
require_once 'includes/config_session.inc.php';
require_once 'includes/dbh.inc.php';

if (!isset($_SESSION['active_role']) || $_SESSION['active_role'] !== 'student') {
    die("Access denied");
}

$user_id = $_SESSION['user_id'];

/* Fetch enrolled courses */
$sql = "SELECT c.course_id, c.course_title, c.course_code
        FROM courses c
        JOIN course_members cm ON c.course_id = cm.course_id
        WHERE cm.user_id = ? AND cm.member_role = 'student'";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$courses = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="css/student_dashboard.css?v=<?php echo time(); ?>">
</head>
<body>

<div class="dashboard-container">
    <header class="main-header">
        <div class="welcome-block">
            <h1>Student Dashboard</h1>
            <p>Welcome back, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong></p>
        </div>
        
        <a href="student/enroll_course.php" class="enroll-btn">
            <span>+</span> Enroll in a Course
        </a>
    </header>

    <?php if (isset($_GET['success'])): ?>
        <div class="status-msg success">✅ Enrolled successfully</div>
    <?php elseif (isset($_GET['error'])): ?>
        <div class="status-msg error">❗ <?= htmlspecialchars($_GET['error']) ?></div>
    <?php endif; ?>

    <div class="course-grid">
        <?php if ($courses): ?>
            <?php foreach ($courses as $course): ?>
                <a class="course-card" href="student/student_course.php?course_id=<?= $course['course_id'] ?>">
                    <div class="card-content">
                        <span class="category-tag">Course</span>
                        <h3><?= htmlspecialchars($course['course_title']) ?></h3>
                        <p class="course-code"><?= htmlspecialchars($course['course_code']) ?></p>
                    </div>
                    <div class="card-footer">
                        Open Classroom →
                    </div>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <p>You are not enrolled in any courses yet.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>