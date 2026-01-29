<?php
require_once '../includes/config_session.inc.php';
require_once '../includes/dbh.inc.php';

if ($_SESSION['active_role'] !== 'teacher') {
    die("Access denied");
}

$course_id  = $_POST['course_id'] ?? null;
$title      = trim($_POST['title'] ?? '');
$start_time = $_POST['start_time'] ?? '';
$end_time   = $_POST['end_time'] ?? '';
$teacher_id = $_SESSION['user_id'];
$submission_link = trim($_POST['submission_link'] ?? '');

if (!$course_id || !$title || !$start_time || !$end_time) {
    die("All fields are required");
}

if ($start_time >= $end_time) {
    die("Start time must be before end time");
}

/* Validate teacher owns course */
$sql = "SELECT * FROM courses WHERE course_id = ? AND teacher_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$course_id, $teacher_id]);
$course = $stmt->fetch();

if (!$course) {
    die("Unauthorized course access");
}

/* -------- PDF UPLOAD (OPTIONAL) -------- */
$pdf_path = null;

if (isset($_FILES['quiz_pdf']) && $_FILES['quiz_pdf']['error'] === 0) {

    if ($_FILES['quiz_pdf']['type'] !== 'application/pdf') {
        die("Only PDF files are allowed");
    }

    $uploadDir = "../uploads/quizzes/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileName = time() . "_" . basename($_FILES['quiz_pdf']['name']);
    $filePath = $uploadDir . $fileName;

    if (!move_uploaded_file($_FILES['quiz_pdf']['tmp_name'], $filePath)) {
        die("Failed to save PDF");
    }

    $pdf_path = $filePath; // ✅ CORRECT
}

/* -------- INSERT QUIZ -------- */
$sql = "INSERT INTO quizzes 
        (course_id, created_by, title, start_time, end_time, pdf_path, submission_link)
        VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    $course_id,
    $teacher_id,
    $title,
    $start_time,
    $end_time,
    $pdf_path,
    $submission_link
]);

header("Location: teacher_quiz.php?course_id=$course_id");
exit;
