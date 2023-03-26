<?php
session_start();
require_once "../db/dbConnect.php";
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Remove whitespaces

    $username = preg_replace('/\s+/', '', $username);
    $password = preg_replace('/\s+/', '', $password);

    // Check if username doesn't exist
    $query = $conn->prepare("SELECT * FROM $users_table WHERE `name` = :name");
    $query->bindParam(':name', $username);
    $query->execute();
    $user = $query->fetch(PDO::FETCH_ASSOC);
    if (!$user) {
        header('Location: ../login.php?error=user_not_exists');
    } else {
        // Check if password is correct
        if (password_verify($password, $user['password'])) {
            // Start session
            $_SESSION['user'] = $user;
            header('Location: ../index.php');
        } else {
            header('Location: ../login.php?error=wrong_password');
        }
    }
}
