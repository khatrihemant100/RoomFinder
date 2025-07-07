<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo "not_logged_in";
    exit;
}

$title = $_POST['title'];
$location = $_POST['location'];
$price = $_POST['price'];
$type = $_POST['type'];
$description = $_POST['description'];
$user_id = $_SESSION['user_id'];

// Handle image upload
$image = "";
if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
    $target = "uploads/" . basename($_FILES['image']['name']);
    if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
        $image = $target;
    }
}

$stmt = $conn->prepare("INSERT INTO rooms (user_id, title, location, price, type, description, image) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("issdsss", $user_id, $title, $location, $price, $type, $description, $image);
if ($stmt->execute()) {
    echo "success";
} else {
    echo "error";
}
?>