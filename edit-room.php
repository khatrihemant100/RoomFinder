<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: user/login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "roomfinder");
if ($conn->connect_error) die("DB error");

$id = intval($_GET['id'] ?? 0);
$user_id = $_SESSION["user_id"];
$msg = "";

// Fetch room and check ownership
$stmt = $conn->prepare("SELECT * FROM properties WHERE id=? AND user_id=?");
$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$room = $result->fetch_assoc();
if (!$room) {
    die("Room not found or you do not have permission.");
}

// Handle update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['room-title'];
    $location = $_POST['room-location'];
    $price = $_POST['room-price'];
    $type = $_POST['room-type'];
    $status = $_POST['room-status'];
    $desc = $_POST['room-description'];
    $imgPath = $room['image_url'];

    // Handle new image upload
    if (isset($_FILES["room-image"]) && $_FILES["room-image"]["error"] == 0) {
        // Delete old image if exists
        if ($imgPath && file_exists($imgPath)) unlink($imgPath);

        $ext = pathinfo($_FILES["room-image"]["name"], PATHINFO_EXTENSION);
        $imgPath = "uploads/room_" . time() . "_" . rand(1000,9999) . "." . $ext;
        move_uploaded_file($_FILES["room-image"]["tmp_name"], $imgPath);
    }

    $stmt = $conn->prepare("UPDATE properties SET title=?, location=?, price=?, type=?, status=?, description=?, image_url=? WHERE id=? AND user_id=?");
    $stmt->bind_param("ssdssssii", $title, $location, $price, $type, $status, $desc, $imgPath, $id, $user_id);

    if ($stmt->execute()) {
        header("Location: find-rooms.php?msg=updated");
        exit();
    } else {
        $msg = "Update failed!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Room</title>
    <style>
        body { font-family: Arial; background: #f8fafc; }
        .edit-form { max-width: 480px; margin: 40px auto; background: #fff; border-radius: 12px; box-shadow: 0 4px 16px #0001; padding: 32px; }
        .edit-form h2 { color: #4A90E2; text-align: center; }
        .edit-form label { display: block; margin-top: 16px; }
        .edit-form input, .edit-form textarea, .edit-form select { width: 100%; padding: 8px; margin-top: 4px; border-radius: 6px; border: 1px solid #ddd; }
        .edit-form button { margin-top: 20px; background: #4A90E2; color: #fff; border: none; padding: 10px 24px; border-radius: 8px; font-size: 1rem; cursor:pointer; }
        .edit-form img { max-width: 100%; margin-top: 10px; border-radius: 8px; }
        .msg { margin-top: 10px; color: green; text-align: center; }
    </style>
</head>
<body>
    <form class="edit-form" method="post" enctype="multipart/form-data">
        <h2>Edit Room</h2>
        <?php if($msg): ?><div class="msg"><?=htmlspecialchars($msg)?></div><?php endif; ?>
        <label>Room Title
            <input type="text" name="room-title" value="<?=htmlspecialchars($room['title'])?>" required>
        </label>
        <label>Location
            <input type="text" name="room-location" value="<?=htmlspecialchars($room['location'])?>" required>
        </label>
        <label>Price
            <input type="number" name="room-price" value="<?=htmlspecialchars($room['price'])?>" required>
        </label>
        <label>Room Type
            <input type="text" name="room-type" value="<?=htmlspecialchars($room['type'])?>" required>
        </label>
        <label>Room Status
            <select name="room-status" required>
                <option value="available" <?= $room['status']=="available"?'selected':'' ?>>Available</option>
                <option value="not_available" <?= $room['status']=="not_available"?'selected':'' ?>>Not Available</option>
                <option value="maintenance" <?= $room['status']=="maintenance"?'selected':'' ?>>Under Maintenance</option>
                <option value="reserved" <?= $room['status']=="reserved"?'selected':'' ?>>Reserved</option>
            </select>
        </label>
        <label>Description
            <textarea name="room-description" rows="4" required><?=htmlspecialchars($room['description'])?></textarea>
        </label>
        <label>Current Image:<br>
            <img src="<?=htmlspecialchars($room['image_url'])?>" alt="Room Image">
        </label>
        <label>Change Image
            <input type="file" name="room-image" accept="image/*">
        </label>
        <button type="submit">Update Room</button>
        <a href="find-rooms.php" style="display:block;text-align:center;margin-top:18px;">&larr; Back to Find Rooms</a>
    </form>
</body>
</html>
