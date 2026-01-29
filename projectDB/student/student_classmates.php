<?php
require_once '../includes/config_session.inc.php';
require_once '../includes/dbh.inc.php';

// Only students
if (!isset($_SESSION['active_role']) || $_SESSION['active_role'] !== 'student') {
    die("Access denied");
}

$user_id   = $_SESSION['user_id'];
$course_id = $_GET['course_id'] ?? null;

if (!$course_id) die("Course ID missing");

// ✅ Verify enrollment
$sql = "
    SELECT c.course_title
    FROM course_members cm
    JOIN courses c ON c.course_id = cm.course_id
    WHERE cm.course_id = ? AND cm.user_id = ? AND cm.member_role = 'student'
";
$stmt = $pdo->prepare($sql);
$stmt->execute([$course_id, $user_id]);
$course = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$course) die("You are not enrolled in this course");

// Fetch classmates
$sql = "
    SELECT u.username, u.email, s.institute, s.degree
    FROM course_members cm
    JOIN users u ON u.user_id = cm.user_id
    LEFT JOIN student_profiles s ON s.user_id = u.user_id
    WHERE cm.course_id = ? AND cm.member_role = 'student'
    ORDER BY u.username ASC
";
$stmt = $pdo->prepare($sql);
$stmt->execute([$course_id]);
$classmates = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Classmates - <?= htmlspecialchars($course['course_title']) ?></title>
</head>
<body>

<div class="classmates-container">
    <h2>Classmates in <?= htmlspecialchars($course['course_title']) ?></h2>

    <a href="student_course.php?course_id=<?= $course_id ?>">← Back to Course</a>

    <?php if (empty($classmates)): ?>
        <p>No classmates found.</p>
    <?php else: ?>
        <table border="1" cellpadding="8" cellspacing="0">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Institute</th>
                    <th>Degree</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($classmates as $c): ?>
                    <tr>
                        <td><?= htmlspecialchars($c['username']) ?></td>
                        <td><?= htmlspecialchars($c['email']) ?></td>
                        <td><?= htmlspecialchars($c['institute'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($c['degree'] ?? '-') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

</body>
</html>
