<?php
require_once 'includes/signup_view.inc.php';
require_once 'includes/config_session.inc.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/main.css">
    <link rel="preconnect" href="css/reset.css">
    <title>Document</title>
</head>
<body>
    <h3>Sign Up</h3>
    <form action="includes/signup.inc.php" method="post">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" placeholder="Enter your username" >
        <br>
        <label for="email">Email:</label>
        <input type="text" id="email" name="email" placeholder="Enter your email" >
        <br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="pwd" placeholder="Enter your password">
        <br>
        <button type="submit">Sign Up</button>
    </form>

    <a href="index.php">Back to Home Page</a>
    
    <?php

    check_signup_errors();

    ?>

</body>
</html>