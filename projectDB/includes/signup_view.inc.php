<?php

declare(strict_types=1);

function check_signup_errors()
{

    if (isset ($_SESSION["errors_signup"])){
        $errors = $_SESSION["errors_signup"];
        echo "<br>";

        foreach ($errors as $error){
        
            echo "<p style='color:red;'>$error</p>";
        
        }

        unset ($_SESSION["errors_signup"]);
    } else if (isset($_GET['signup']) && $_GET['signup'] === 'success') {
        echo "<p style='color:green;'>Signup successful! You can now log in.</p>";
        header("Refresh:2; url=login.php");
    }


}