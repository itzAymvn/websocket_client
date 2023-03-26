<?php
session_start();
require_once "../db/dbConnect.php";

if (!isset($_SESSION['user'])) {
    header('Location: ./login.php');
    exit();
}

try {
    $query = "SELECT messages.id, messages.user_id, users.name AS user_name, messages.message, messages.created_at FROM $users_table JOIN users ON messages.user_id = users.id ORDER BY messages.created_at ASC";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($messages);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
}
