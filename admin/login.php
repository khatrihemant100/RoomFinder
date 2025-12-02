<?php
session_start();
require '../db.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (!empty($email) && !empty($password)) {
        $stmt = $conn->prepare("SELECT id, name, email, password, is_admin FROM users WHERE email = ? AND is_admin = 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['admin_name'] = $user['name'];
                $_SESSION['admin_email'] = $user['email'];
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Invalid email or password.";
            }
        } else {
            $error = "Invalid email or password.";
        }
        $stmt->close();
    } else {
        $error = "Please fill in all fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - RoomFinder</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
    <script src="https://cdn.tailwindcss.com/3.4.16"></script>
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body>
    <div class="w-full max-w-md px-4">
        <div class="bg-white rounded-2xl shadow-2xl p-8">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-['Pacifico'] text-gray-800 mb-2">RoomFinder</h1>
                <h2 class="text-2xl font-bold text-gray-700">Admin Panel</h2>
                <p class="text-gray-500 mt-2">Sign in to manage your website</p>
            </div>
            
            <?php if ($error): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                    <i class="ri-error-warning-line mr-2"></i><?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="" class="space-y-6">
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">
                        <i class="ri-mail-line mr-2 text-blue-500"></i>Email Address
                    </label>
                    <input type="email" name="email" required 
                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:outline-none transition-colors"
                           placeholder="admin@example.com">
                </div>
                
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">
                        <i class="ri-lock-line mr-2 text-blue-500"></i>Password
                    </label>
                    <input type="password" name="password" required 
                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:outline-none transition-colors"
                           placeholder="Enter your password">
                </div>
                
                <button type="submit" 
                        class="w-full bg-gradient-to-r from-blue-500 to-blue-600 text-white py-3 rounded-lg font-semibold text-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                    <i class="ri-login-box-line mr-2"></i>Sign In
                </button>
            </form>
            
            <div class="mt-6 text-center space-y-2">
                <a href="create_admin.php" class="block text-blue-500 hover:text-blue-600 transition-colors text-sm font-medium">
                    <i class="ri-user-add-line mr-1"></i>Create Admin Account
                </a>
                <a href="../index.php" class="block text-gray-500 hover:text-blue-500 transition-colors text-sm">
                    <i class="ri-arrow-left-line mr-1"></i>Back to Website
                </a>
            </div>
        </div>
    </div>
</body>
</html>

