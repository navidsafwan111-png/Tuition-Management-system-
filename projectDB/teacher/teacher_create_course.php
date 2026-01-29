<?php


// Include session configuration and database connection
require_once '../includes/config_session.inc.php';
require_once '../includes/dbh.inc.php';

// Check if user is logged in and has teacher role; deny access otherwise
if (!isset($_SESSION['active_role']) || $_SESSION['active_role'] !== 'teacher') {
    die("Access denied");
}

// Handle POST request for course creation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize input data
    $title = trim($_POST['title']);
    $code = trim($_POST['code']);
    $teacher_id = $_SESSION['user_id'];

    // Validate that both fields are filled
    if ($title && $code) {
        // Insert new course into database
        $sql = "INSERT INTO courses (course_title, course_code, teacher_id) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$title, $code, $teacher_id]);
        $course_id = $pdo->lastInsertId();

        // Add teacher as a member of the course
        $sql2 = "INSERT INTO course_members (course_id, user_id, member_role) VALUES (?, ?, 'teacher')";
        $stmt2 = $pdo->prepare($sql2);
        $stmt2->execute([$course_id, $teacher_id]);

        // Redirect to teacher dashboard after successful creation
        header("Location: ../teacher_dashboard.php");
        exit;
    } else {
        // Set error message if fields are empty
        $error = "Please fill in all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Course</title>
    <link rel="stylesheet" href="../css/create_course.css">
</head>
<body>

<div class="page-wrapper">
    <div class="form-container">
        <a href="../teacher_dashboard.php" class="back-link">← Back to Dashboard</a>
        
        <h2>Create New Course</h2>
        <p class="subtitle">Enter the details to set up your new classroom.</p>

        <?php if (isset($error)): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="input-group">
                <label for="title">Course Title</label>
                <input type="text" name="title" id="title" placeholder="e.g. Advanced Web Development" required>
            </div>

            <div class="input-group">
                <label for="code">Course Enroll Key</label>
                <input type="text" name="code" id="code" placeholder="Give Course Enroll Key" required>
            </div>

            <button type="submit" class="btn-submit">Create Course</button>
        </form>
    </div>
</div>

</body>
</html>