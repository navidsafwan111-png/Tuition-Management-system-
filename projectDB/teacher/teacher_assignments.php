<?php
require_once '../includes/config_session.inc.php';
require_once '../includes/dbh.inc.php';

if ($_SESSION['active_role'] !== 'teacher') {
    die("Access denied");
}

$course_id  = $_GET['course_id'] ?? null;
$teacher_id = $_SESSION['user_id'];

if (!$course_id) {
    die("Invalid course");
}

/* Verify course ownership */
$stmt = $pdo->prepare(
    "SELECT * FROM courses WHERE course_id = ? AND teacher_id = ?"
);
$stmt->execute([$course_id, $teacher_id]);
$course = $stmt->fetch();

if (!$course) {
    die("Unauthorized access");
}

/* Handle assignment creation */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title']);
    $description = trim($_POST['description']);
    $deadline    = $_POST['deadline'];

    if ($title && $deadline) {
        $stmt = $pdo->prepare(
            "INSERT INTO assignments (course_id, created_by, title, description, deadline)
             VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->execute([$course_id, $teacher_id, $title, $description, $deadline]);
    }
}

/* Fetch assignments */
$stmt = $pdo->prepare(
    "SELECT * FROM assignments
     WHERE course_id = ?
     ORDER BY created_at DESC"
);
$stmt->execute([$course_id]);
$assignments = $stmt->fetchAll();
?>

<link rel="stylesheet" href="../css/teacher_assignments.css">

<div class="assignment-container">

    <header>
        <a href="teacher_courses.php?course_id=<?= $course_id ?>">← Back</a>
        <h2><?= htmlspecialchars($course['course_title']) ?> — Assignments</h2>
    </header>

    <!-- Create Assignment -->
    <section class="create-assignment">
        <h3>Create New Assignment</h3>

        <form method="POST">
            <input type="text" name="title" placeholder="Assignment Title" required>

            <textarea name="description" placeholder="Assignment Description"></textarea>

            <input type="datetime-local" name="deadline" required>

            <button type="submit">Create Assignment</button>
        </form>
    </section>

    <!-- Assignment List -->
    <section class="assignment-list">
        <h3>Existing Assignments</h3>

        <?php if (empty($assignments)): ?>
            <p>No assignments yet.</p>
        <?php else: ?>
            <?php foreach ($assignments as $a): ?>
                <!-- <div class="assignment-card">
                    <h4><?= htmlspecialchars($a['title']) ?></h4>
                    <p><?= nl2br(htmlspecialchars($a['description'])) ?></p>
                    <small>
                        Deadline: <?= date('d M Y, h:i A', strtotime($a['deadline'])) ?>
                    </small>
                </div> -->
                <div class="assignment-card">
                    <h4><?= htmlspecialchars($a['title']) ?></h4>
                    <p><?= nl2br(htmlspecialchars($a['description'])) ?></p>
                    <small>Deadline: <?= date("d M Y, h:i A", strtotime($a['deadline'])) ?></small>

                    <div class="assignment-actions">
                        <a class="edit-btn"
                        href="teacher_assignment_edit.php?assignment_id=<?= $a['assignment_id'] ?>&course_id=<?= $course_id ?>">
                        ✏ Edit
                       </a>

                       <a class="delete-btn"
                         href="teacher_assignment_delete.php?assignment_id=<?= $a['assignment_id'] ?>&course_id=<?= $course_id ?>"
                        onclick="return confirm('Are you sure you want to delete this assignment?');">
                        🗑 Delete
                       </a>
                    </div>
                </div>

            <?php endforeach; ?>
        <?php endif; ?>
    </section>

</div>
