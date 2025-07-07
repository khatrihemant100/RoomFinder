<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = new mysqli("localhost", "root", "", "roomfinder");
    if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];

    $stmt = $conn->prepare("INSERT INTO contacts (name, email, message) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $message);

    if ($stmt->execute()) {
        $msg = "Your message has been sent!";
    } else {
        $msg = "There was an error. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- नेपाली नोट: Contact पेजको लागि हेडर -->
    <meta charset="UTF-8">
    <title>Contact | RoomFinder</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <!-- नेपाली नोट: सम्पर्क section सुरु -->
    <section class="contact-section">
        <!-- नेपाली नोट: Contact पेजको मुख्य भाग -->
        <h1>Contact</h1>
        <p>हामीलाई सम्पर्क गर्न तलको विवरण प्रयोग गर्नुहोस्:</p>

        <!-- नेपाली नोट: सम्पर्क विवरणहरू -->
        <ul>
            <li><strong>फोन:</strong> +977-9800000000</li>
            <li><strong>इमेल:</strong> info@roomfinder.com</li>
            <li><strong>ठेगाना:</strong> काठमाडौं, नेपाल</li>
        </ul>
        <?php if (isset($msg)) echo "<p style='color:green;'>$msg</p>"; ?>
        <!-- नेपाली नोट: सिधै सम्पर्क गर्न फारम -->
        <form method="POST">
            <label for="name">नाम:</label>
            <input type="text" id="name" name="name" required>

            <label for="email">इमेल:</label>
            <input type="email" id="email" name="email" required>

            <label for="message">सन्देश:</label>
            <textarea id="message" name="message" rows="4" required></textarea>

            <button type="submit" class="px-4 py-2 bg-primary text-white rounded-button">पठाउनुहोस्</button>
        </form>

        <a href="index.html">Home</a>
        <a href="contact.php" class="text-gray-700 hover:text-primary transition-colors">Contact</a>
    </section>
</body>
</html>