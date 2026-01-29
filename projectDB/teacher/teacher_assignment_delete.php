<?php
require_once '../includes/config_session.inc.php';
require_once '../includes/dbh.inc.php';

if ($_SESSION['active_role'] !== 'teacher') {
    die("Access denied");
}

$assignment_id = $_GET['assignment_id'] ?? null;
$course_id = $_GET['course_id'] ?? null;
$teacher_id = $_SESSION['user_id'];

$sql = "DELETE FROM assignments 
        WHERE assignment_id = ? AND created_by = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$assignment_id, $teacher_id]);

header("Location: teacher_assignments.php?course_id=" . $course_id);
exit;
