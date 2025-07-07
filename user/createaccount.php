<?php
session_start();
require '../db.php';

$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $role = "user"; // Default role

    if ($name && $email && $password && $role) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $hashed_password, $role);
        if ($stmt->execute()) {
            $message = "<div class='success'>User account created successfully. <a href='login.php'>Login here</a>.</div>";
        } else {
            $message = "<div class='error'>Email already exists or error occurred.</div>";
        }
        $stmt->close();
    } else {
        $message = "<div class='error'>All fields are required.</div>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Create User Account</title>
    <!-- Navbar/Header CSS & Tailwind -->
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
                    colors: { primary: '#4A90E2', secondary: '#43c6ac' },
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
            background: white
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
        }
        .form-container {
            background: #fff;
            padding: 2.5rem 2.5rem 2rem 2.5rem;
            border-radius: 18px;
            box-shadow: 0 8px 32px 0 rgba(67, 198, 172, 0.25);
            max-width: 420px;
            margin: 60px auto;
            border: 2px solid #43c6ac;
        }
        .form-container h2 {
            text-align: center;
            color: #43c6ac;
            margin-bottom: 10px;
        }
        .form-container input, .form-container select {
            width: 100%;
            padding: 12px;
            margin: 12px 0 18px 0;
            border: 1.5px solid #43c6ac;
            border-radius: 8px;
            background: #f9f9f9;
            font-size: 1rem;
        }
        .form-container button {
            width: 100%;
            padding: 12px;
            background: #43c6ac;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            cursor: pointer;
            transition: background 0.3s;
            font-weight: bold;
        }
        .form-container button:hover {
            background: #34a69a;
        }
        .form-container .link {
            display: block;
            text-align: center;
            margin-top: 14px;
            color: #43c6ac;
            text-decoration: none;
        }
        .form-container .error {
            color: #d8000c;
            background: #ffd2d2;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 12px;
            text-align: center;
        }
        .form-container .success {
            color: #4f8a10;
            background: #dff2bf;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 12px;
            text-align: center;
        }
    </style>
</head>
<body>
    <!-- Header/Navbar Start -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="container mx-auto px-4 py-3 flex items-center justify-between">
            <a href="../index.php" class="text-2xl font-['Pacifico'] text-primary">RoomFinder</a>
            <div class="hidden md:flex items-center space-x-6">
                <a href="../index.php" class="text-gray-700 hover:text-primary transition-colors">Home</a>
                <a href="../find-rooms.php" class="text-gray-700 hover:text-primary transition-colors">Find Rooms</a>
                <a href="../list-property.php" class="text-gray-700 hover:text-primary transition-colors">List Property</a>
                <a href="../about.html" class="text-gray-700 hover:text-primary transition-colors">About Us</a>
                <a href="../contact.php" class="text-gray-700 hover:text-primary transition-colors">Contact</a>
            </div>
            <div class="flex items-center space-x-4">
                <?php if(isset($_SESSION["user_id"])): ?>
                    <span class="px-4 py-2 text-primary font-semibold rounded-button bg-primary/10">
                        <?php echo htmlspecialchars($_SESSION["name"]); ?>
                    </span>
                    <a href="logout.php" class="px-4 py-2 bg-secondary text-white rounded-button hover:bg-secondary/90 transition-colors whitespace-nowrap">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="hidden md:block px-4 py-2 text-primary border border-primary rounded-button hover:bg-primary hover:text-white transition-colors whitespace-nowrap">Sign In</a>
                    <a href="createaccount.php" class="px-4 py-2 bg-primary text-white rounded-button hover:bg-primary/90 transition-colors whitespace-nowrap">Sign Up</a>
                <?php endif; ?>
                <button class="md:hidden w-10 h-10 flex items-center justify-center text-gray-700">
                    <i class="ri-menu-line text-xl"></i>
                </button>
            </div>
        </div>
    </header>
    <!-- Header/Navbar End -->

    <div class="form-container">
        <h2>Create New User Account</h2>
        <?php echo $message; ?>
        <form method="POST">
            <input type="text" name="name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Create Account</button>
        </form>
        <a class="link" href="login.php">Already have an account? Login</a>
    </div>
</body>
</html>