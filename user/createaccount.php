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
    <style>
        body {
            background: linear-gradient(135deg, #f8ffae 0%, #43c6ac 100%);
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