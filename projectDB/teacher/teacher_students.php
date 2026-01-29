<?php
require_once '../includes/config_session.inc.php';
require_once '../includes/dbh.inc.php';

if (!isset($_SESSION['active_role']) || $_SESSION['active_role'] !== 'teacher') {
    die("Access denied");
}

$course_id  = $_GET['course_id'] ?? null;
$teacher_id = $_SESSION['user_id'];

if (!$course_id) {
    die("Invalid course");
}

/* 🔐 Verify teacher owns this course */
$sql = "SELECT course_title FROM courses WHERE course_id = ? AND teacher_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$course_id, $teacher_id]);
$course = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$course) {
    die("Unauthorized access to course");
}

/* 👨‍🎓 Fetch enrolled students */
$sql = "
    SELECT 
        u.user_id,
        u.username,
        u.email,
        sp.institute,
        sp.degree
    FROM course_members cm
    JOIN users u ON cm.user_id = u.user_id
    LEFT JOIN student_profiles sp ON u.user_id = sp.user_id
    WHERE cm.course_id = ?
      AND cm.member_role = 'student'
    ORDER BY u.username ASC
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$course_id]);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Students - <?= htmlspecialchars($course['course_title']) ?></title>
    <link rel="stylesheet" href="../css/teacher_students.css">
</head>
<body>

<div class="students-container">
    <header class="students-header">
        <a href="teacher_course.php?course_id=<?= $course_id ?>" class="back-link">← Back to Course</a>
        <h1>Students Enrolled</h1>
        <p><?= htmlspecialchars($course['course_title']) ?></p>
    </header>

    <?php if (empty($students)): ?>
        <p class="empty">No students enrolled yet.</p>
    <?php else: ?>
        <table class="students-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Institute</th>
                    <th>Degree</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $index => $student): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($student['username']) ?></td>
                        <td><?= htmlspecialchars($student['email']) ?></td>
                        <td><?= htmlspecialchars($student['institute'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($student['degree'] ?? '—') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

</body>
</html>
