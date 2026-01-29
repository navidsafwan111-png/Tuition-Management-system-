<?php
require_once '../includes/config_session.inc.php';
require_once '../includes/dbh.inc.php';

if ($_SESSION['active_role'] !== 'student') {
    die("Access denied");
}

$user_id = $_SESSION['user_id'];
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_title = trim($_POST['course_title']);
    $course_code  = trim($_POST['course_code']);

    /* Find course */
    $stmt = $pdo->prepare(
        "SELECT course_id FROM courses 
         WHERE course_title = ? AND course_code = ?"
    );
    $stmt->execute([$course_title, $course_code]);
    $course = $stmt->fetch();

    if (!$course) {
        $error = "Course not found";
    } else {
        $course_id = $course['course_id'];

        /* Check already enrolled */
        $check = $pdo->prepare(
            "SELECT 1 FROM course_members 
             WHERE course_id = ? AND user_id = ?"
        );
        $check->execute([$course_id, $user_id]);

        if ($check->fetch()) {
            $error = "You are already enrolled in this course";
        } else {
            /* Enroll */
            $insert = $pdo->prepare(
                "INSERT INTO course_members (course_id, user_id, member_role)
                 VALUES (?, ?, 'student')"
            );
            $insert->execute([$course_id, $user_id]);

            header("Location: ../student_dashboard.php?success=1");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Enroll Course</title>
    <link rel="stylesheet" href="../css/enroll_course.css">
</head>
<body>

<h2>Enroll in a Course</h2>

<?php if ($error): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="post">
    <label>Course Title</label>
    <input type="text" name="course_title" required>

    <label>Course Code</label>
    <input type="text" name="course_code" required>

    <button type="submit">Enroll</button>
</form>

<a href="../student_dashboard.php">← Back</a>

</body>
</html>
