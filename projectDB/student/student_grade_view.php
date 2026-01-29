<?php
require_once '../includes/config_session.inc.php';
require_once '../includes/dbh.inc.php';

if ($_SESSION['active_role'] !== 'student') {
    die("Access denied");
}

$student_id = $_SESSION['user_id'];
$course_id = $_GET['course_id'] ?? null;

/* Verify enrollment */
$check = $pdo->prepare(
    "SELECT * FROM course_members WHERE course_id = ? AND user_id = ?"
);
$check->execute([$course_id, $student_id]);

if (!$check->fetch()) {
    die("Not enrolled in this course");
}

/* Fetch grade */
$stmt = $pdo->prepare("
    SELECT 
        c.course_title,
        g.assignment_marks,
        g.quiz_marks,
        (g.assignment_marks + g.quiz_marks) AS total_marks
    FROM grade_sheet g
    JOIN courses c ON g.course_id = c.course_id
    WHERE g.course_id = ? AND g.student_id = ?
");

$stmt->execute([$course_id, $student_id]);
$grade = $stmt->fetch();
?>

<link rel="stylesheet" href="../css/student_grades.css">

<div class="grade-wrapper">
    <div class="grade-card">
        <header class="grade-header">
            <h2>Grade Sheet</h2>
            <?php if ($grade): ?>
                <div class="course-badge"><?= htmlspecialchars($grade['course_title']) ?></div>
            <?php endif; ?>
        </header>

        <?php if ($grade): ?>
            <div class="stats-container">
                <div class="stat-item">
                    <span class="stat-label">Assignment</span>
                    <span class="stat-value"><?= $grade['assignment_marks'] ?></span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Quizzes</span>
                    <span class="stat-value"><?= $grade['quiz_marks'] ?></span>
                </div>
                <div class="stat-item total">
                    <span class="stat-label">Total Marks</span>
                    <span class="stat-value"><?= $grade['total_marks'] ?></span>
                </div>
            </div>

            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Marks</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Assignments</td>
                        <td><?= $grade['assignment_marks'] ?></td>
                    </tr>
                    <tr>
                        <td>Quizzes</td>
                        <td><?= $grade['quiz_marks'] ?></td>
                    </tr>
                </tbody>
            </table>
        <?php else: ?>
            <div class="no-data">
                <p>No grades published yet for this course.</p>
            </div>
        <?php endif; ?>

        <div class="footer-nav">
            <a href="student_course.php?course_id=<?= $course_id ?>" class="btn-back">
                ← Back to Course
            </a>
        </div>
    </div>
</div>