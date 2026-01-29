<?php
require_once '../includes/config_session.inc.php';
require_once '../includes/dbh.inc.php'; 



if ($_SESSION['active_role'] !== 'teacher') {
    die("Access denied");
}

$course_id = $_GET['course_id'] ?? null;
$teacher_id = $_SESSION['user_id'];

// Validate teacher ownership of course
$sql = "SELECT * FROM courses WHERE course_id = ? AND teacher_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$course_id, $teacher_id]);
$course = $stmt->fetch();
if (!$course) die("Course not found or unauthorized");

// Fetch events
$sql = "SELECT * FROM course_events WHERE course_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$course_id]);
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Convert events to JS array
$jsEvents = json_encode(array_map(function($e) {
    return [
        'id' => $e['event_id'],
        'title' => $e['title'],
        'description' => $e['description'],
        'start_date' => $e['start_date'],
        'end_date' => $e['end_date'],
        'start_time' => $e['start_time'],
        'end_time' => $e['end_time']
    ];
}, $events));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Calendar - <?= htmlspecialchars($course['course_title']) ?></title>
    
    <link rel="stylesheet" href="../css/calendar.css">
    <script>
        let events = <?= $jsEvents ?>;
    </script>
</head>
<body class="calendar-page">

<div class="calendar-container">
    <h2 class="calendar-title">Calendar for <?= htmlspecialchars($course['course_title']) ?></h2>

    <div id="monthYear" class="month-year-display"></div>
    <div id="calendar" class="calendar-grid"></div>
</div>

<div id="eventModal" class="modal" style="display:none;">
    <div class="modal-content">
        <form id="eventForm" class="event-form" method="POST" action="teacher_calendar_action.php">
            <input type="hidden" name="course_id" value="<?= $course_id ?>">
            <input type="hidden" name="event_id" id="eventId">
            <div class="form-group">
                <label for="title">Title:</label>
                <input type="text" name="title" id="title" required>
            </div>
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea name="description" id="description"></textarea>
            </div>
            <div class="form-group">
                <label for="startDate">Start Date:</label>
                <input type="date" name="start_date" id="startDate" required>
            </div>
            <div class="form-group">
                <label for="endDate">End Date:</label>
                <input type="date" name="end_date" id="endDate" required>
            </div>
            <div class="form-group">
                <label for="startTime">Start Time:</label>
                <input type="time" name="start_time" id="startTime" required>
            </div>
            <div class="form-group">
                <label for="endTime">End Time:</label>
                <input type="time" name="end_time" id="endTime" required>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn-save">Save</button>
                <button type="button" class="btn-cancel" onclick="closeModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script src="../js/teacher_calendar.js"></script>
</body>
</html>
