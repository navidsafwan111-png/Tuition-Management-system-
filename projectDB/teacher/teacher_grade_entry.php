<?php
require_once '../includes/config_session.inc.php';
require_once '../includes/dbh.inc.php';

if ($_SESSION['active_role'] !== 'teacher') {
    die("Access denied");
}

$course_id = $_GET['course_id'] ?? null;
$teacher_id = $_SESSION['user_id'];

/* Validate course ownership */
$stmt = $pdo->prepare(
    "SELECT * FROM courses WHERE course_id = ? AND teacher_id = ?"
);
$stmt->execute([$course_id, $teacher_id]);
$course = $stmt->fetch();

if (!$course) {
    die("Unauthorized course access");
}

/* Fetch enrolled students */
/* Fetch enrolled students */
$sql = "
    SELECT u.user_id, u.username
    FROM course_members cm
    JOIN users u ON cm.user_id = u.user_id
    WHERE cm.course_id = ?
      AND cm.member_role = 'student'
";
$students = $pdo->prepare($sql);
$students->execute([$course_id]);
$students = $students->fetchAll();

?>

<link rel="stylesheet" href="../css/teacher_grade_entry.css">

<div class="grade-entry-wrapper">
    <div class="grade-entry-card">
        <header class="entry-header">
            <a href="teacher_course.php?course_id=<?= $course_id ?>" class="back-link">← Back to Course</a>
            <h2>Grade Entry</h2>
            <div class="course-label"><?= htmlspecialchars($course['course_title']) ?></div>
        </header>

        <form method="post" action="teacher_grade_save.php">
            <input type="hidden" name="course_id" value="<?= $course_id ?>">

            <div class="table-responsive">
                <table class="grade-table">
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Assignment Marks</th>
                            <th>Quiz Marks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $student): ?>
                            <tr>
                                <td class="student-name">
                                    <strong><?= htmlspecialchars($student['username']) ?></strong>
                                </td>
                                <td>
                                    <div class="input-container">
                                        <input type="number" step="0.01"
                                               name="assignment_marks[<?= $student['user_id'] ?>]"
                                               placeholder="0.00"
                                               required>
                                    </div>
                                </td>
                                <td>
                                    <div class="input-container">
                                        <input type="number" step="0.01"
                                               name="quiz_marks[<?= $student['user_id'] ?>]"
                                               placeholder="0.00"
                                               required>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-save">Save All Grades</button>
            </div>
        </form>
    </div>
</div>