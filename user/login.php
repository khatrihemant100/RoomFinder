<?php
require '../db.php';
session_start();
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT id, name, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows == 1) {
        $stmt->bind_result($id, $name, $hashed_password, $role);
        $stmt->fetch();
        if (password_verify($password, $hashed_password)) {
            $_SESSION["user_id"] = $id;
            $_SESSION["name"] = $name;
            $_SESSION["role"] = $role;
            header("Location: ../index.php");
            exit();
        } else {
            $message = "<div class='error'>Invalid password.</div>";
        }
    } else {
        $message = "<div class='error'>No account found with that email.</div>";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
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
            background: white
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
        }
        .form-container {
            background: #fff;
            padding: 2.5rem 2.5rem 2rem 2.5rem;
            border-radius: 18px;
            box-shadow: 0 8px 32px 0 rgba(77, 235, 10, 0.18);
            max-width: 450px;
            max-height: 800px; 
            margin: 60px auto;
            border: 2px solid green;
        }
        .form-container h2 {
            text-align: center;
            color:rgb(9, 3, 4);
            margin-bottom: 10px;
        }
        .form-container input {
            width: 100%;
            padding: 12px;
            margin: 12px 0 18px 0;
            border: 1.5px solid green;
            border-radius: 8px;
            background: #f9f9f9;
            font-size: 1rem;
        }
        .form-container button {
            width: 100%;
            padding: 12px;
            background:rgb(192, 96, 96);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            cursor: pointer;
            transition: background 0.3s;
            font-weight: bold;
        }
        .form-container button:hover {
            background:rgb(63, 250, 11);
        }
        .form-container .link {
            display: block;
            text-align: center;
            margin-top: 14px;
            color:rgb(21, 5, 243);
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
        <h2>Login</h2>
        <?php echo $message; ?>
        <form method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <a class="link" href="createaccount.php">Don't have an account? Create Account</a>
    </div>
</body>
</html>