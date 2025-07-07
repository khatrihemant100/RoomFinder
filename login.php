<?php
session_start();
include 'db.php';
$email = $_POST['email'];
$password = $_POST['password'];

$stmt = $conn->prepare("SELECT id, password FROM users WHERE email=?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($id, $hash);
if ($stmt->num_rows > 0) {
    $stmt->fetch();
    if (password_verify($password, $hash)) {
        $_SESSION['user_id'] = $id;
        echo "success";
    } else {
        echo "invalid";
    }
} else {
    echo "invalid";
}
?>