<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Retrieve form data
    $username = $_POST["username"];
    $pwd = $_POST["pwd"];

    try {
        //code...
        require_once 'dbh.inc.php';
        require_once 'config_session.inc.php'; 
        // Retrieve the hashed password from the database for the given username
        $query = "SELECT user_id, pwd FROM users WHERE username = :username";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(":username", $username);
        
        $stmt->execute();

        // Fetch the user data
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if the user exists
        if (!$user) {
            die("User not found.");
        }

        // Verify the provided password against the stored hash
        if (!password_verify($pwd, $user["pwd"])) {
            die("Incorrect password.");
        }

        $pdo = null; //close connection
        $stmt = null; //close statement

        // ✅ STORE USER IN SESSION
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];        
        header("Location: ../home.php");

        

    } catch (PDOException $e) {
        die("ERROR: Could not able to execute. " . $e->getMessage());
    }


} else {
    // If not a POST request, redirect to the main page
    header("Location: ../index.php");
    die();
}
