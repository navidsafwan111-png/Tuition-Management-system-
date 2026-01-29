<?php
/**
 * teacher_posts.php
 *
 * This page displays all posts for a specific course that the teacher owns.
 * It validates course ownership, fetches posts from the database, and renders them.
 * Access is restricted to teachers who own the course.
 */

// Include session configuration and database connection
require_once '../includes/config_session.inc.php';
require_once '../includes/dbh.inc.php';

// Check if user is logged in and has teacher role
if (!isset($_SESSION['active_role']) || $_SESSION['active_role'] !== 'teacher') {
    die("Access denied");
}

// Get course_id from GET parameters and validate
$course_id = $_GET['course_id'] ?? null;
if (!$course_id || !is_numeric($course_id)) {
    die("Invalid course ID");
}

$teacher_id = $_SESSION['user_id'];

// Validate course ownership
$sql = "SELECT * FROM courses WHERE course_id = ? AND teacher_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$course_id, $teacher_id]);
$course = $stmt->fetch();

if (!$course) {
    die("Course not found or unauthorized");
}

// Fetch posts for the course
$sql = "SELECT * FROM course_posts WHERE course_id = ? ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$course_id]);
$posts = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Posts - <?= htmlspecialchars($course['course_title']) ?></title>
    <link rel="stylesheet" href="../css/post_show.css">
<body>

    <header class="page-header">
        <h2>Posts for <?= htmlspecialchars($course['course_title']) ?></h2>
        <a href="teacher_post_create.php?course_id=<?= $course_id ?>" class="btn-create">➕ Create New Post</a>
    </header>

    <?php if (empty($posts)): ?>
        <p class="empty-msg">No posts found for this course.</p>
    <?php else: ?>
        <ul class="posts-list">
        <?php foreach ($posts as $post): ?>
            <li class="post-card">
                <div class="post-meta">
                    <span class="post-type tag-<?= strtolower($post['post_type']) ?>">
                        <?= htmlspecialchars($post['post_type']) ?>
                    </span>
                    <span class="post-time"><?= date('M j, Y', strtotime($post['created_at'])) ?></span>
                </div>
                
                <div class="post-body">
                    <?= nl2br(htmlspecialchars($post['content'])) ?>
                </div>
            </li>
        <?php endforeach; ?>
        </ul>
    <?php endif; ?>

</body>
</html>