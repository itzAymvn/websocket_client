<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: ./login.php');
    exit();
}

require_once "../db/dbConnect.php";
// Prepare the query so it changes the "updated_at" column to the current time
$query = "UPDATE users SET `updated_at` = UNIX_TIMESTAMP()";
$setQueries = [
    'name' => false,
    'image' => false
];
$currentData = $conn->prepare("SELECT * FROM users WHERE id = :id");
$currentData->execute(['id' => $_SESSION['user']['id']]);
$currentData = $currentData->fetch(PDO::FETCH_ASSOC);

if (isset($_POST['name']) && $_POST['name'] !== $currentData['name']) {
    $query .= ", `name` = :name";
    $setQueries['name'] = true;
}

if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
    $image = $_FILES['image'];
    $imageType = $image['type'];
    $imageSize = $image['size'];
    $imageTmp = $image['tmp_name'];
    $imageAllowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
    $imageAllowedSize = 2000000; // 2MB
    if (!in_array($imageType, $imageAllowedTypes)) {
        header('Location: ../profile.php?error=invalid_image_type');
        exit();
    }
    if ($imageSize > $imageAllowedSize) {
        header('Location: ../profile.php?error=invalid_image_size');
        exit();
    }

    $imageOldName = pathinfo($image['name'], PATHINFO_FILENAME);
    $imageExtension = pathinfo($image['name'], PATHINFO_EXTENSION);
    $imageNewName = time() . '_' . $imageOldName . '.' . $imageExtension;
    $imageDestination = '../public/images/' . $imageNewName;

    if (move_uploaded_file($imageTmp, $imageDestination)) {
        $query .= ", `image` = :image";
        $setQueries['image'] = true;

        // Delete the old image
        if ($currentData['image']) {
            unlink('../public/images/' . $currentData['image']);
        }
    } else {
        header('Location: ../profile.php?error=invalid_image');
        exit();
    }
}

if ($setQueries['name'] || $setQueries['image']) {
    $query .= " WHERE id = :id";
    $stmt = $conn->prepare($query);
    if ($setQueries['name']) {
        $stmt->bindParam(':name', $_POST['name']);
    }
    if ($setQueries['image']) {
        $stmt->bindParam(':image', $imageNewName);
    }
    $stmt->bindParam(':id', $_SESSION['user']['id']);
    if ($stmt->execute()) {
        if ($setQueries['name']) {
            $_SESSION['user']['name'] = $_POST['name'];
        }
        if ($setQueries['image']) {
            $_SESSION['user']['image'] = $imageNewName;
        }
        header('Location: ../profile.php?success=profile_updated');
        exit();
    }
} else {
    header('Location: ../profile.php?error=no_changes');
    exit();
}
