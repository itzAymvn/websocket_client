<?php
$host = "sql108.epizy.com";
$user = "epiz_33873057";
$pass = "MVJD2BauZu";
$db = "epiz_33873057_websocket_chat";
$users_table = "users";
$messages_table = "messages";

try {
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
