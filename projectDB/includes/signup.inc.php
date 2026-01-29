<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $_POST["username"];
    $pwd = $_POST["pwd"];
    $email = $_POST["email"];



    try {
        require_once 'dbh.inc.php';
        require_once 'signup_model.inc.php';

        require_once 'signup_contr.inc.php';

        //ERROR HANDLING FOR DUPLICATE USERNAMES OR EMAILS
        $errors = [];
        if (is_input_empty($username,$pwd,$email) == true){
            $errors["empty_input"] = "All fields are required.";
        }
        if (is_email_invalid($email) == true){
            $errors["invalid_email"] = "Invalid email format.";
        }
        if (is_username_taken($pdo, $username) == true){
            $errors["username_taken"] = "Username already taken.";
        }
        if (is_email_registered($pdo, $email) == true){
            $errors["email_registered"] = "Email already registered.";
        }
        
        require_once 'config_session.inc.php';

        if ($errors){

            $_SESSION['errors_signup'] = $errors;
            header("location: ../signup.php");
            die();
            
        }
        create_user($pdo, $username, $pwd, $email);
        header("location: ../signup.php?signup=success");
        $pdo = null; //close connection
        $stmt = null; //close statement 
       
        die();

    } catch (PDOException $th){
        die("ERROR: Could not able to execute. " . $th->getMessage());
    }

}
else{
    header("location: ../signup.php"); //redirect to homepage if not accessed via form
    die();
}
