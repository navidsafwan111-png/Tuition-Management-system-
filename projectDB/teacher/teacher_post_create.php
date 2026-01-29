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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = trim($_POST['content']);
    $type = $_POST['post_type'] ?? 'announcement';

    if ($content) {
        $sql = "INSERT INTO course_posts (course_id, user_id, content, post_type) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$course_id, $teacher_id, $content, $type]);

        header("Location: teacher_posts.php?course_id=" . $course_id);
        exit;
    } else {
        $error = "Please write some content for the post.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Post</title>
    <link rel="stylesheet" href="../css/create_post_style.css">
</head>
<body>
    <div class="form-wrapper">
        <div class="create-post-container">
            <h2 class="create-post-title">Create Post</h2>
            <p class="course-subtitle">Course: <?= htmlspecialchars($course['course_title']) ?></p>

            <?php if (isset($error)): ?>
                <div class="error-box"><?= $error ?></div>
            <?php endif; ?>

            <form method="post" class="create-post-form">
                <div class="form-group">
                    <label for="content">Post Content</label>
                    <textarea name="content" id="content" placeholder="Share something with your students..." required class="post-textarea"></textarea>
                </div>

                <div class="form-group">
                    <label for="post_type">Post Category</label>
                    <select name="post_type" id="post_type" class="post-select">
                        <option value="announcement">📢 Announcement</option>
                        <option value="material">📚 Material</option>
                        <option value="assignment">📝 Assignment</option>
                    </select>
                </div>

                <button type="submit" class="submit-btn">Publish Post</button>
            </form>

            <a href="teacher_posts.php?course_id=<?= $course_id ?>" class="cancel-link">Cancel and Go Back</a>
        </div>
    </div>
</body>
</html>
