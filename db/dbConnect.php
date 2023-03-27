<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "websocket_chat";
$users_table = "users";
$messages_table = "messages";

try {
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
