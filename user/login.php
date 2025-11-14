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
            // Set session variables
            $_SESSION["user_id"] = $id;
            // Ensure name is set - use trimmed name or fallback
            $userName = trim($name);
            $_SESSION["name"] = !empty($userName) ? $userName : "User";
            $_SESSION["role"] = $role;
            
            // Regenerate session ID for security
            session_regenerate_id(true);
            
            // Debug: Uncomment to check session values
            // error_log("Login - User ID: " . $id . ", Name: " . $_SESSION["name"]);
            
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
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .error {
            background-color: #fee;
            color: #c33;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 16px;
            border: 1px solid #fcc;
        }
        .success {
            background-color: #efe;
            color: #3c3;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 16px;
            border: 1px solid #cfc;
        }
    </style>
</head>
<body class="bg-white font-sans min-h-screen flex items-center justify-center">
<div class="form-container bg-white p-8 rounded-2xl shadow-lg max-w-lg w-full mx-4 border border-gray-200">
    <h2 class="text-3xl text-center mb-6 font-bold" style="font-family:'Pacifico',cursive;color:#4A90E2;">Login to Your Account</h2>
    <?php echo $message; ?>
    <form method="POST" class="space-y-4">
        <input type="email" name="email" placeholder="Email" class="w-full p-3 border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 outline-none" required>
        <input type="password" name="password" placeholder="Password" class="w-full p-3 border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 outline-none" required>

        <button type="submit" class="w-full p-3 bg-blue-500 text-white rounded-lg font-bold hover:bg-blue-600 transition-colors">Login</button>
    </form>
    <a class="block text-center mt-4 text-blue-500 hover:text-blue-600 hover:underline" href="createaccount.php">Don't have an account? Sign Up</a>
</div>
</body>
</html>