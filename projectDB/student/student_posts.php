<?php
require_once '../includes/config_session.inc.php';
require_once '../includes/dbh.inc.php';

// 🔐 Allow only students
if (!isset($_SESSION['active_role']) || $_SESSION['active_role'] !== 'student') {
    die("Access denied");
}

$user_id   = $_SESSION['user_id'];
$course_id = $_GET['course_id'] ?? null;

if (!$course_id) {
    die("Course ID missing");
}

/*
|--------------------------------------------------------------------------
| 1. Verify student is enrolled in this course
|--------------------------------------------------------------------------
*/
$sql = "
    SELECT c.course_title
    FROM course_members cm
    JOIN courses c ON c.course_id = cm.course_id
    WHERE cm.course_id = ?
      AND cm.user_id = ?
      AND cm.member_role = 'student'
";
$stmt = $pdo->prepare($sql);
$stmt->execute([$course_id, $user_id]);
$course = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$course) {
    die("You are not enrolled in this course");
}

/*
|--------------------------------------------------------------------------
| 2. Fetch course posts (created by teacher)
|--------------------------------------------------------------------------
*/
$sql = "
    SELECT 
        cp.post_id,
        cp.content,
        cp.post_type,
        cp.created_at,
        u.username AS teacher_name
    FROM course_posts cp
    JOIN users u ON u.user_id = cp.user_id
    WHERE cp.course_id = ?
    ORDER BY cp.created_at DESC
";
$stmt = $pdo->prepare($sql);
$stmt->execute([$course_id]);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Course Posts</title>
    <link rel="stylesheet" href="../css/student_post.css">
</head>
<body>

<h2>
    <?php echo htmlspecialchars($course['course_title']); ?> — Posts
</h2>

<a href="student_course.php?course_id=<?php echo $course_id; ?>">
    ← Back to Course
</a>

<hr>

<?php if (empty($posts)): ?>
    <p>No posts have been published yet.</p>
<?php else: ?>
    <?php foreach ($posts as $post): ?>
        <article class="course-post">

            <p>
                <strong>Posted by:</strong>
                <?php echo htmlspecialchars($post['teacher_name']); ?>
            </p>

            <?php if (!empty($post['post_type'])): ?>
                <p>
                    <strong>Type:</strong>
                    <?php echo htmlspecialchars($post['post_type']); ?>
                </p>
            <?php endif; ?>

            <div class="post-content">
                <?php echo nl2br(htmlspecialchars($post['content'])); ?>
            </div>

            <small>
                Posted on:
                <?php echo htmlspecialchars($post['created_at']); ?>
            </small>

        </article>

        <hr>
    <?php endforeach; ?>
<?php endif; ?>

</body>
</html>
