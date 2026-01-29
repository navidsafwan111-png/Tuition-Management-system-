<?php


// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Include session configuration
    require_once 'config_session.inc.php';

    // Ensure user is logged in by checking for user_id in session
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../index.php");
        exit();
    }

    // Check if role is provided in POST data
    if (!isset($_POST['role'])) {
        die("Role not selected");
    }

    $role = $_POST['role'];

    // Validate that the role is either 'student' or 'teacher'
    if ($role !== 'student' && $role !== 'teacher') {
        die("Invalid role");
    }

    // Store the selected role in the session for future use
    $_SESSION['active_role'] = $role;

    // Redirect based on the selected role
    if ($role === 'student') {
        header("Location: ../student_dashboard.php");
    } else {
        header("Location: ../teacher_dashboard.php");
    }
    exit();

} else {
    // If not a POST request, redirect to home page
    header("Location: ../home.php");
    exit();
}
