<?php
/**
 * teacher_dashboard.php
 *
 * This page serves as the dashboard for teachers after login and role selection.
 * It displays a welcome message and lists all courses created by the logged-in teacher.
 * Access is restricted to users with the 'teacher' role.
 *
 * Features:
 * - Fetches courses from the database based on teacher_id.
 * - Provides a link to create new courses.
 * - Shows "No courses found." if no courses exist.
 */

// Include session configuration and database connection
require_once 'includes/config_session.inc.php';
require_once 'includes/dbh.inc.php';

// Check if user is logged in and has teacher role; deny access otherwise
if (!isset($_SESSION['active_role']) || $_SESSION['active_role'] !== 'teacher') {
    die("Access denied");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Teacher Dashboard</title>
    <link rel="stylesheet" href="css/teacher_dashboard.css">
</head>
<body>

<div class="dashboard-container">
    <h2>Teacher Dashboard</h2>
    <p class="welcome-message">Welcome, <?php ($_SESSION['username']); ?></p>
    <?php
    // Get the teacher's user ID from session
    $teacher_id = $_SESSION['user_id'];

    // Prepare and execute query to fetch all courses for this teacher
    $sql = "SELECT * FROM courses WHERE teacher_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$teacher_id]);
    $courses = $stmt->fetchAll();
    ?>

    <h3>Your Courses</h3>
    <a href="teacher/teacher_create_course.php" class="create-course-link">➕ Create New Course</a>
    <?php if (empty($courses)): ?>
        <p class="no-courses">No courses found.</p>
    <?php else: ?>
    <ul class="courses-list">
    <?php foreach ($courses as $course): ?>
        <li class="course-item">
            <a href="teacher/teacher_course.php?course_id=<?= $course['course_id'] ?>" class="course-link">
                <?= htmlspecialchars($course['course_title']) ?>
            </a>
        </li>
    <?php endforeach; ?>
    </ul>
    <?php endif; ?>
</div>

</body>
</html>
