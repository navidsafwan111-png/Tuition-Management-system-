<?php
require_once '../includes/config_session.inc.php';
require_once '../includes/dbh.inc.php';
$viewMode = $_GET['view'] ?? 'teacher';
$isStudentView = ($viewMode === 'student');


if ($_SESSION['active_role'] !== 'teacher') {
    die("Access denied");
}

$course_id = $_POST['course_id'] ?? null;
$teacher_id = $_SESSION['user_id'];

// Validate teacher ownership
$sql = "SELECT * FROM courses WHERE course_id = ? AND teacher_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$course_id, $teacher_id]);
$course = $stmt->fetch();
if (!$course) die("Course not found or unauthorized");

$event_id = $_POST['event_id'] ?? null;
$title = $_POST['title'] ?? '';
$description = $_POST['description'] ?? '';
$start_date = $_POST['start_date'] ?? '';
$end_date = $_POST['end_date'] ?? '';
$start_time = $_POST['start_time'] ?? '';
$end_time = $_POST['end_time'] ?? '';

if (empty($title) || empty($start_date) || empty($end_date) || empty($start_time) || empty($end_time)) {
    die("All fields are required");
}

if ($event_id) {
    // Update event
    $sql = "UPDATE course_events SET title = ?, description = ?, start_date = ?, end_date = ?, start_time = ?, end_time = ? WHERE event_id = ? AND course_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$title, $description, $start_date, $end_date, $start_time, $end_time, $event_id, $course_id]);
} else {
    // Insert new event
    $sql = "INSERT INTO course_events (course_id, title, description, start_date, end_date, start_time, end_time) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$course_id, $title, $description, $start_date, $end_date, $start_time, $end_time]);
}

// Redirect back to calendar
header("Location: teacher_calendar.php?course_id=$course_id");
exit();
?>