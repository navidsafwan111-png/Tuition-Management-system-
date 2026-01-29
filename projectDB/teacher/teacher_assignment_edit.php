<?php
require_once '../includes/config_session.inc.php';
require_once '../includes/dbh.inc.php';

if ($_SESSION['active_role'] !== 'teacher') {
    die("Access denied");
}

$assignment_id = $_GET['assignment_id'] ?? null;
$teacher_id = $_SESSION['user_id'];

$sql = "SELECT * FROM assignments 
        WHERE assignment_id = ? AND created_by = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$assignment_id, $teacher_id]);
$assignment = $stmt->fetch();

if (!$assignment) {
    die("Assignment not found or unauthorized");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $deadline = $_POST['deadline'];

    $update = "UPDATE assignments 
               SET title = ?, description = ?, deadline = ?
               WHERE assignment_id = ? AND created_by = ?";
    $stmt = $pdo->prepare($update);
    $stmt->execute([$title, $description, $deadline, $assignment_id, $teacher_id]);

    header("Location: teacher_assignments.php?course_id=" . $assignment['course_id']);
    exit;
}
?>

<link rel="stylesheet" href="../css/teacher_assignments.css">

<div class="assignment-container">
    <header>
        <h2>Edit Assignment</h2>
        <a href="teacher_assignments.php?course_id=<?= $assignment['course_id'] ?>">← Back</a>
    </header>

    <form method="post" class="create-assignment">
        <input type="text" name="title"
               value="<?= htmlspecialchars($assignment['title']) ?>" required>

        <textarea name="description"><?= htmlspecialchars($assignment['description']) ?></textarea>

        <input type="datetime-local" name="deadline"
               value="<?= date('Y-m-d\TH:i', strtotime($assignment['deadline'])) ?>" required>

        <button type="submit">Update Assignment</button>
    </form>
</div>
