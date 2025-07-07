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
            header("Location: ../index.php"); // यहाँ createaccount.php लाई index.php मा बदल्नुहोस्
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
    <style>
        body {
            background: linear-gradient(120deg, #f093fb 0%, #f5576c 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
        }
        .form-container {
            background: #fff;
            padding: 2.5rem 2.5rem 2rem 2.5rem;
            border-radius: 18px;
            box-shadow: 0 8px 32px 0 rgba(245, 87, 108, 0.18);
            max-width: 400px;
            margin: 60px auto;
            border: 2px solid #f5576c;
        }
        .form-container h2 {
            text-align: center;
            color: #f5576c;
            margin-bottom: 10px;
        }
        .form-container input {
            width: 100%;
            padding: 12px;
            margin: 12px 0 18px 0;
            border: 1.5px solid #f5576c;
            border-radius: 8px;
            background: #f9f9f9;
            font-size: 1rem;
        }
        .form-container button {
            width: 100%;
            padding: 12px;
            background: #f5576c;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            cursor: pointer;
            transition: background 0.3s;
            font-weight: bold;
        }
        .form-container button:hover {
            background: #f093fb;
        }
        .form-container .link {
            display: block;
            text-align: center;
            margin-top: 14px;
            color: #f5576c;
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