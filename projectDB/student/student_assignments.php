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

// Fetch assignments for this course
$sql = "
    SELECT a.assignment_id, a.title, a.description, a.deadline, a.created_at, u.username AS teacher_name
    FROM assignments a
    JOIN users u ON a.created_by = u.user_id
    WHERE a.course_id = ?
    ORDER BY a.deadline ASC
";
$stmt = $pdo->prepare($sql);
$stmt->execute([$course_id]);
$assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Assignments - <?= htmlspecialchars($course['course_title']) ?></title>
</head>
<body>

<div class="assignments-container">
    <h2>Assignments for <?= htmlspecialchars($course['course_title']) ?></h2>

    <a href="student_course.php?course_id=<?= $course_id ?>">← Back to Course</a>

    <?php if (empty($assignments)): ?>
        <p>No assignments have been posted yet.</p>
    <?php else: ?>
        <table border="1" cellpadding="8" cellspacing="0">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Deadline</th>
                    <th>Created By</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($assignments as $a): ?>
                    <tr>
                        <td><?= htmlspecialchars($a['title']) ?></td>
                        <td><?= nl2br(htmlspecialchars($a['description'])) ?></td>
                        <td><?= htmlspecialchars(date("Y-m-d H:i", strtotime($a['deadline']))) ?></td>
                        <td><?= htmlspecialchars($a['teacher_name']) ?></td>
                        <td><?= htmlspecialchars(date("Y-m-d H:i", strtotime($a['created_at']))) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

</body>
</html>
