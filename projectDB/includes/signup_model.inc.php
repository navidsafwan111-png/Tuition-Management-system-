<?php

declare(strict_types=1);

// Database functions for signup

function get_username(object $pdo, string $username) {
    $sql = "SELECT * FROM users WHERE username = :username;";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':username', $username);
    $stmt->execute();



    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return $result;
    }

function get_useremail(object $pdo, string $email) {
    $sql = "SELECT * FROM users WHERE email = :email;";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();



        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result;
    }
function set_user(object $pdo, string $username, string $pwd, string $email) {
    $hashedPwd = password_hash($pwd, PASSWORD_BCRYPT);

    $sql = "INSERT INTO users (username,email,pwd) VALUES (:username, :email, :pwd);";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':pwd', $hashedPwd);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
}
