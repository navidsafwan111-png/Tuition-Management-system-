<?php

$host="localhost";
$dbname="managedb";
$dbusername="root";
$dbpassword="";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $dbusername, $dbpassword);
    // set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch (PDOException $e) {
    die ("Connection failed: " . $e->getMessage());
    //throw $th;
}
