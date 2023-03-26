<?php
require_once "../db/dbConnect.php";
if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Remove whitespaces
    $username = preg_replace('/\s+/', '', $username);
    $password = preg_replace('/\s+/', '', $password);

    // Check if username already exists
    $query = $conn->prepare("SELECT * FROM $users_table WHERE `name` = :name");
    $query->bindParam(':name', $username);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        header('Location: ../register.php?error=user_exists');
    } else {
        // Insert new user
        $query = $conn->prepare("INSERT INTO $users_table (`name`, password) VALUES (:name, :password)");
        $query->bindParam(':name', $username);
        $query->bindParam(':password', password_hash($password, PASSWORD_DEFAULT));
        $query->execute();
        header('Location: ../login.php');
    }
}
