<?php

require_once '../db/dbConnect.php';
session_start();
if (isset($_POST['current_password']) && isset($_POST['new_password']) && isset($_POST['confirm_password'])) {
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($newPassword !== $confirmPassword) {
        header('Location: ../profile.php?error=passwords_do_not_match');
        exit();
    }

    $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->execute(['id' => $_SESSION['user']['id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        header('Location: ../profile.php?error=invalid_user');
        exit();
    }

    if (!password_verify($currentPassword, $user['password'])) {
        header('Location: ../profile.php?error=invalid_password');
        exit();
    }

    $newPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE users SET `password` = :password WHERE id = :id");
    $stmt->execute(['password' => $newPassword, 'id' => $_SESSION['user']['id']]);
    $_SESSION['user']['password'] = $newPassword;
    header('Location: ../profile.php?success=password_changed');
    exit();
} else {
    header('Location: ../profile.php');
    exit();
}
