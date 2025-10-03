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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - RoomFinder</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
    <script src="https://cdn.tailwindcss.com/3.4.16"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { primary: '#4A90E2', secondary: '#FF6B6B' },
                    borderRadius: {
                        'none': '0px', 'sm': '4px', DEFAULT: '8px', 'md': '12px', 'lg': '16px',
                        'xl': '20px', '2xl': '24px', '3xl': '32px', 'full': '9999px', 'button': '8px'
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background: #fff;
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .contact-container {
            background: #fff;
            padding: 2.5rem 2.5rem 2rem 2.5rem;
            border-radius: 18px;
            box-shadow: 0 8px 32px 0 rgba(74, 144, 226, 0.10);
            max-width: 480px;
            margin: 60px auto;
            border: 1.5px solid #e5e7eb;
        }
        .contact-container h2 {
            text-align: center;
            color: #4A90E2;
            margin-bottom: 10px;
            font-family: 'Pacifico', cursive;
            font-size: 2rem;
        }
        .contact-container input, .contact-container textarea {
            width: 100%;
            padding: 12px;
            margin: 12px 0 18px 0;
            border: 1.5px solid #4A90E2;
            border-radius: 8px;
            background: #f9f9f9;
            font-size: 1rem;
            transition: border 0.2s;
        }
        .contact-container input:focus, .contact-container textarea:focus {
            border-color: #FF6B6B;
            outline: none;
        }
        .contact-container button {
            width: 100%;
            padding: 12px;
            background: #4A90E2;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            cursor: pointer;
            transition: background 0.3s;
            font-weight: bold;
            margin-top: 10px;
        }
        .contact-container button:hover {
            background: #357ABD;
        }
        .contact-container .success {
            color: #4f8a10;
            background: #dff2bf;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 12px;
            text-align: center;
        }
        .contact-container .error {
            color: #d8000c;
            background: #ffd2d2;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 12px;
            text-align: center;
        }
        .contact-info {
            margin-top: 24px;
            text-align: left,;
            color: #555;
        }
        .contact-info p {
            margin: 6px 0;
        }
    </style>
</head>
<body>
    <!-- Header/Navbar Start -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="container mx-auto px-4 py-3 flex items-center justify-between">
            <a href="index.php" class="text-2xl font-['Pacifico'] text-primary">RoomFinder</a>
            <div class="hidden md:flex items-center space-x-6">
                <a href="index.php" class="text-gray-700 hover:text-primary transition-colors">Home</a>
                <a href="find-rooms.php" class="text-gray-700 hover:text-primary transition-colors">Find Rooms</a>
                <a href="list-property.php" class="text-gray-700 hover:text-primary transition-colors">List Property</a>
                <a href="about.html" class="text-gray-700 hover:text-primary transition-colors">About Us</a>
                <a href="contact.php" class="text-gray-700 hover:text-primary transition-colors">Contact</a>
            </div>
            <div class="flex items-center space-x-4">
                <?php if(isset($_SESSION["user_id"])): ?>
                    <span class="px-4 py-2 text-primary font-semibold rounded-button bg-primary/10">
                        <?php echo htmlspecialchars($_SESSION["name"]); ?>
                    </span>
                    <a href="user/logout.php" class="px-4 py-2 bg-secondary text-white rounded-button hover:bg-secondary/90 transition-colors whitespace-nowrap">Logout</a>
                <?php else: ?>
                    <a href="user/login.php" class="hidden md:block px-4 py-2 text-primary border border-primary rounded-button hover:bg-primary hover:text-white transition-colors whitespace-nowrap">Sign In</a>
                    <a href="user/createaccount.php" class="px-4 py-2 bg-primary text-white rounded-button hover:bg-primary/90 transition-colors whitespace-nowrap">Sign Up</a>
                <?php endif; ?>
                <button class="md:hidden w-10 h-10 flex items-center justify-center text-gray-700">
                    <i class="ri-menu-line text-xl"></i>
                </button>
            </div>
        </div>
    </header>
    <!-- Header/Navbar End -->

    <div class="contact-container">
        <h2>Contact Us</h2>
        <?php if(isset($msg)): ?>
            <div class="<?php echo ($msg == 'Your message has been sent!') ? 'success' : 'error'; ?>">
                <?php echo htmlspecialchars($msg); ?>
            </div>
        <?php endif; ?>
        <form method="post" action="">
            <input type="text" name="name" placeholder="Your Name" required>
            <input type="email" name="email" placeholder="Your Email" required>
            <textarea name="message" rows="5" placeholder="Your Message" required></textarea>
            <button type="submit">Send Message</button>
        </form>
        <div class="contact-info">
            <p><strong>Email:</strong> support@roomfinder.com</p>
            <p><strong>Phone:</strong> +977-1234567890</p>
            <p><strong>Address:</strong> Yokohama, Japan</p>
        </div>
    </div>
</body>
</html>