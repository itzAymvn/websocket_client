<?php
session_start();
require_once "../db/dbConnect.php";

if (!isset($_SESSION['user'])) {
    header('Location: ./login.php');
    exit();
}

try {
    $query = "SELECT $messages_table.id, $messages_table.user_id, $users_table.name AS user_name, $messages_table.message, $messages_table.created_at FROM $messages_table JOIN users ON $messages_table.user_id = $users_table.id ORDER BY $messages_table.created_at ASC";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($messages);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
}
