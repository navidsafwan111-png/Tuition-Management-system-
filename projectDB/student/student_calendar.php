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

// Fetch events for this course
$sql = "SELECT * FROM course_events WHERE course_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$course_id]);
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Convert to JS array
$jsEvents = json_encode(array_map(function($e){
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
        // Pass events to JS
        let events = <?= $jsEvents ?>;
        let isStudentView = true; // Read-only mode
    </script>
</head>
<body class="calendar-page">

<div class="calendar-container">
    <h2 class="calendar-title">Calendar for <?= htmlspecialchars($course['course_title']) ?></h2>

    <a href="student_course.php?course_id=<?= $course_id ?>">← Back to Course</a>

    <div id="monthYear" class="month-year-display"></div>
    <div id="calendar" class="calendar-grid"></div>
</div>

<!-- Modal is present but hidden, students cannot open it -->
<div id="eventModal" class="modal" style="display:none;">
    <div class="modal-content">
        <form id="eventForm" class="event-form" method="POST" action="student_calendar_action.php">
            <input type="hidden" name="course_id" value="<?= $course_id ?>">
            <input type="hidden" name="event_id" id="eventId">
            <!-- All fields disabled for students -->
            <div class="form-group">
                <label for="title">Title:</label>
                <input type="text" name="title" id="title" disabled>
            </div>
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea name="description" id="description" disabled></textarea>
            </div>
            <div class="form-group">
                <label for="startDate">Start Date:</label>
                <input type="date" name="start_date" id="startDate" disabled>
            </div>
            <div class="form-group">
                <label for="endDate">End Date:</label>
                <input type="date" name="end_date" id="endDate" disabled>
            </div>
            <div class="form-group">
                <label for="startTime">Start Time:</label>
                <input type="time" name="start_time" id="startTime" disabled>
            </div>
            <div class="form-group">
                <label for="endTime">End Time:</label>
                <input type="time" name="end_time" id="endTime" disabled>
            </div>
            <div class="form-actions" style="display:none;">
                <button type="submit">Save</button>
            </div>
        </form>
    </div>
</div>

<script src="../js/teacher_calendar.js"></script>
<script>
    // Disable all overlay buttons for students
    document.addEventListener("DOMContentLoaded", function(){
        const overlays = document.querySelectorAll(".overlay-btn");
        overlays.forEach(btn => btn.style.display = "none");
    });
</script>

</body>
</html>
