<?php
session_start();
require_once "../db/dbConnect.php";
if (!isset($_SESSION['user'])) {
    header('Location: ./login.php');
    exit();
}

$user = json_decode(file_get_contents('php://input'), true);

if (isset($user['id']) && isset($user['message']) && isset($user['timestamp'])) {
    $id = $user['id'];
    $message = $user['message'];
    $timestamp = $user['timestamp'];

    try {
        $sql = "INSERT INTO $messages_table (user_id, message, created_at) VALUES (:id, :message, :created_at)";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['id' => $id, 'message' => $message, 'created_at' => $timestamp]);
        echo json_encode(['status' => 'success', 'message' => 'Message sent', 'message_id' => $conn->lastInsertId()]);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => "It's not you, it's us. Please try again later."]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Error: Invalid data']);
}
