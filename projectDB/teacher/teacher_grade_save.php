<?php
require_once '../includes/config_session.inc.php';
require_once '../includes/dbh.inc.php';

if ($_SESSION['active_role'] !== 'teacher') {
    die("Access denied");
}

$course_id = $_POST['course_id'];
$assignment_marks = $_POST['assignment_marks'];
$quiz_marks = $_POST['quiz_marks'];

foreach ($assignment_marks as $student_id => $a_marks) {

    $q_marks = $quiz_marks[$student_id];

    $stmt = $pdo->prepare("
        INSERT INTO grade_sheet (course_id, student_id, assignment_marks, quiz_marks)
        VALUES (?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
            assignment_marks = VALUES(assignment_marks),
            quiz_marks = VALUES(quiz_marks)
    ");

    $stmt->execute([
        $course_id,
        $student_id,
        $a_marks,
        $q_marks
    ]);
}

header("Location: teacher_grade_entry.php?course_id=$course_id");
exit;
?>