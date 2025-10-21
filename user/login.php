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
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans">
<div class="form-container bg-white p-8 rounded-2xl shadow-lg max-w-lg mx-auto mt-16 border-2 border-gradient-to-r from-green-400 to-blue-500">
    <h2 class="text-3xl text-center text-green-500 mb-6 font-bold">Login to Your Account</h2>
    <?php echo $message; ?>
    <form method="POST" class="space-y-4">
        <input type="email" name="email" placeholder="Email" class="w-full p-3 border border-green-300 rounded-lg bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-400" required>
        <input type="password" name="password" placeholder="Password" class="w-full p-3 border border-green-300 rounded-lg bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-400" required>

        <button type="submit" class="w-full p-3 bg-green-400 text-white rounded-lg font-bold hover:bg-green-500 transition-colors">Login</button>
    </form>
    <a class="block text-center mt-4 text-green-500 hover:underline" href="create_account.php">Don't have an account? Sign Up</a>
</div>
</body>
</html>