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
    <h3>Login</h3>
    <form action="includes/login.inc.php" method="post">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" placeholder="Enter your username" required>
        <br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="pwd" placeholder="Enter your password" required>
        <br>
        <button type="submit">Login</button>
    </form>
    
    <a href="signup.php">Sign Up</a>
    
</body>
</html>