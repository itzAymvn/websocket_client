<?php

session_start();
require_once '../db/dbConnect.php';
if (isset($_POST['password'])) {
    // check if the password is correct
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->execute(['id' => $_SESSION['user']['id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!password_verify($_POST['password'], $user['password'])) {
        header('Location: ../profile.php?error=invalid_password');
        exit();
    }
    // delete the messages of the user if he has any
    $stmt = $conn->prepare("DELETE FROM messages WHERE user_id = :id");
    $stmt->execute(['id' => $_SESSION['user']['id']]);

    // delete the user
    $stmt = $conn->prepare("DELETE FROM users WHERE id = :id");
    $stmt->execute(['id' => $_SESSION['user']['id']]);
    // delete the image of the user if he has one
    if ($user['image']) {
        unlink('../public/images/' . $user['image']);
    }
    // delete the session
    session_destroy();
    header('Location: ../index.php?success=account_deleted');
    exit();
} else {
    header('Location: ../profile.php');
    exit();
}
