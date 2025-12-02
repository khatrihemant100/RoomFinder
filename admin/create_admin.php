<?php
// Admin User Creation Script
// Run this file once to create an admin user, then delete it for security

require '../db.php';

$message = '';
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (empty($name) || empty($email) || empty($password)) {
        $message = "All fields are required.";
    } elseif ($password !== $confirm_password) {
        $message = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $message = "Password must be at least 6 characters long.";
    } else {
        // Check if email already exists
        $checkStmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $checkStmt->bind_param("s", $email);
        $checkStmt->execute();
        $result = $checkStmt->get_result();
        
        if ($result->num_rows > 0) {
            // User exists, update to admin
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $updateStmt = $conn->prepare("UPDATE users SET name = ?, password = ?, is_admin = 1 WHERE email = ?");
            $updateStmt->bind_param("sss", $name, $hashed_password, $email);
            
            if ($updateStmt->execute()) {
                $message = "Admin user updated successfully! You can now login.";
                $success = true;
            } else {
                $message = "Error updating user: " . $updateStmt->error;
            }
            $updateStmt->close();
        } else {
            // Create new admin user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $role = 'owner'; // Default role for admin
            $insertStmt = $conn->prepare("INSERT INTO users (name, email, password, role, is_admin) VALUES (?, ?, ?, ?, 1)");
            $insertStmt->bind_param("ssss", $name, $email, $hashed_password, $role);
            
            if ($insertStmt->execute()) {
                $message = "Admin user created successfully! You can now login.";
                $success = true;
            } else {
                $message = "Error creating user: " . $insertStmt->error;
            }
            $insertStmt->close();
        }
        $checkStmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Admin User - RoomFinder</title>
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
                <h2 class="text-2xl font-bold text-gray-700">Create Admin User</h2>
                <p class="text-gray-500 mt-2">Set up your admin account</p>
            </div>
            
            <?php if ($message): ?>
                <div class="<?php echo $success ? 'bg-green-50 border-green-200 text-green-700' : 'bg-red-50 border-red-200 text-red-700'; ?> border px-4 py-3 rounded-lg mb-6">
                    <i class="<?php echo $success ? 'ri-check-line' : 'ri-error-warning-line'; ?> mr-2"></i><?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
            
            <?php if (!$success): ?>
                <form method="POST" action="" class="space-y-6">
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">
                            <i class="ri-user-line mr-2 text-blue-500"></i>Full Name
                        </label>
                        <input type="text" name="name" required 
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:outline-none transition-colors"
                               placeholder="Enter your name" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">
                            <i class="ri-mail-line mr-2 text-blue-500"></i>Email Address
                        </label>
                        <input type="email" name="email" required 
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:outline-none transition-colors"
                               placeholder="admin@example.com" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">
                            <i class="ri-lock-line mr-2 text-blue-500"></i>Password
                        </label>
                        <input type="password" name="password" required 
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:outline-none transition-colors"
                               placeholder="Enter password (min 6 characters)">
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">
                            <i class="ri-lock-password-line mr-2 text-blue-500"></i>Confirm Password
                        </label>
                        <input type="password" name="confirm_password" required 
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:outline-none transition-colors"
                               placeholder="Confirm password">
                    </div>
                    
                    <button type="submit" 
                            class="w-full bg-gradient-to-r from-blue-500 to-blue-600 text-white py-3 rounded-lg font-semibold text-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                        <i class="ri-user-add-line mr-2"></i>Create Admin Account
                    </button>
                </form>
            <?php else: ?>
                <div class="text-center">
                    <div class="mb-6">
                        <i class="ri-checkbox-circle-line text-green-500 text-6xl"></i>
                    </div>
                    <a href="login.php" class="inline-block w-full bg-gradient-to-r from-blue-500 to-blue-600 text-white py-3 rounded-lg font-semibold text-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                        <i class="ri-login-box-line mr-2"></i>Go to Login
                    </a>
                    <p class="text-gray-500 text-sm mt-4">
                        <i class="ri-alert-line mr-1"></i>
                        For security, please delete this file (create_admin.php) after creating your admin account.
                    </p>
                </div>
            <?php endif; ?>
            
            <div class="mt-6 text-center">
                <a href="login.php" class="text-gray-500 hover:text-blue-500 transition-colors text-sm">
                    <i class="ri-arrow-left-line mr-1"></i>Back to Login
                </a>
            </div>
        </div>
    </div>
</body>
</html>

