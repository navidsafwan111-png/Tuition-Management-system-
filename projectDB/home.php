<?php
require_once 'includes/config_session.inc.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Select Role</title>
    <link rel="stylesheet" href="css/home.css">
</head>
<body>

<h2>Continue as</h2>

<form action="includes/set_role.inc.php" method="post">
    <button type="submit" name="role" value="student">Student</button>
    <button type="submit" name="role" value="teacher">Teacher</button>
</form>

</body>
</html>
<!-- here we select the role of the user after login ,
  then it is handled by set_role.inc.php -->