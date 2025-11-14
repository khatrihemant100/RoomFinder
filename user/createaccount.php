<?php
session_start();
require '../db.php';

$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $role = trim($_POST["role"] ?? '');

    // Validate and normalize role (database accepts 'owner' or 'seeker')
    if ($role === 'landlord' || $role === 'owner') {
        $role = 'owner';
    } elseif ($role === 'tenant' || $role === 'seeker') {
        $role = 'seeker';
    } else {
        $role = ''; // Invalid role
    }

    // Validate all required fields
    if (empty($name) || empty($email) || empty($password) || empty($role)) {
        $message = "<div class='error'>All required fields must be filled. Please select a valid role.</div>";
    } elseif ($name && $email && $password && $role) {
        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = "<div class='error'>Invalid email format.</div>";
        } elseif (strlen($password) < 6) {
            $message = "<div class='error'>Password must be at least 6 characters long.</div>";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Only insert columns that exist in users table: id, name, email, password, role
            try {
                $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
                if (!$stmt) {
                    $message = "<div class='error'>Database error: " . htmlspecialchars($conn->error) . "</div>";
                } else {
                    $stmt->bind_param("ssss", $name, $email, $hashed_password, $role);
                    
                    // Check if email already exists before attempting insert
                    $checkStmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
                    $checkStmt->bind_param("s", $email);
                    $checkStmt->execute();
                    $result = $checkStmt->get_result();
                    
                    if ($result->num_rows > 0) {
                        $message = "<div class='error'>Email already exists. Please use a different email or <a href='login.php'>login here</a>.</div>";
                        $checkStmt->close();
                        $stmt->close();
                    } else {
                        $checkStmt->close();
                        
                        // Now try to insert
                        if ($stmt->execute()) {
                            $message = "<div class='success'>User account created successfully. <a href='login.php'>Login here</a>.</div>";
                        } else {
                            // Get detailed error message
                            $errorMsg = $stmt->error;
                            if (strpos($errorMsg, 'Duplicate entry') !== false || strpos($errorMsg, 'email') !== false) {
                                $message = "<div class='error'>Email already exists. Please use a different email or <a href='login.php'>login here</a>.</div>";
                            } elseif (strpos($errorMsg, 'role') !== false) {
                                $message = "<div class='error'>Invalid role. Please select a valid role.</div>";
                            } else {
                                $message = "<div class='error'>Error: " . htmlspecialchars($errorMsg) . "</div>";
                            }
                            error_log("Account creation error: " . $errorMsg);
                        }
                        $stmt->close();
                    }
                }
            } catch (mysqli_sql_exception $e) {
                // Handle SQL exceptions (like duplicate email)
                $errorMsg = $e->getMessage();
                if (strpos($errorMsg, 'Duplicate entry') !== false || strpos($errorMsg, 'email') !== false) {
                    $message = "<div class='error'>Email already exists. Please use a different email or <a href='login.php'>login here</a>.</div>";
                } else {
                    $message = "<div class='error'>Database error occurred. Please try again.</div>";
                }
                error_log("Account creation exception: " . $errorMsg);
            } catch (Exception $e) {
                // Handle any other exceptions
                $message = "<div class='error'>An error occurred. Please try again.</div>";
                error_log("Account creation general exception: " . $e->getMessage());
            }
        }
    } else {
        $message = "<div class='error'>All required fields must be filled.</div>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Create User Account</title>
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
    <script>
        function toggleFields() {
            const role = document.getElementById("role").value;
            const landlordFields = document.getElementById("landlordFields");
            const tenantFields = document.getElementById("tenantFields");

            landlordFields.style.display = (role === "owner" || role === "landlord") ? "block" : "none";
            tenantFields.style.display = (role === "seeker" || role === "tenant") ? "block" : "none";
        }
        
        // Auto-toggle fields on page load if role is pre-selected
        window.onload = function() {
            toggleFields();
        };
    </script>
</head>
<body class="bg-white font-sans min-h-screen flex items-center justify-center py-8">
<div class="form-container bg-white p-8 rounded-2xl shadow-lg max-w-lg w-full mx-4 border border-gray-200">
    <h2 class="text-3xl text-center mb-6 font-bold" style="font-family:'Pacifico',cursive;color:#4A90E2;">Create New Account</h2>
    <?php echo $message; ?>
    <form method="POST" class="space-y-4">
        <input type="text" name="name" placeholder="Full Name" class="w-full p-3 border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 outline-none" required>
        <input type="email" name="email" placeholder="Email" class="w-full p-3 border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 outline-none" required>
        <input type="password" name="password" placeholder="Password" class="w-full p-3 border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 outline-none" required>

        <select name="role" id="role" onchange="toggleFields()" class="w-full p-3 border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 outline-none" required>
            <option value="">Select Role</option>
            <option value="seeker" <?php echo (isset($_GET['role']) && $_GET['role'] === 'seeker') ? 'selected' : ''; ?>>Room Seeker</option>
            <option value="owner" <?php echo (isset($_GET['role']) && $_GET['role'] === 'owner') ? 'selected' : ''; ?>>Room Owner</option>
        </select>
        <?php if (isset($_POST["role"]) && empty($role) && !empty($_POST["role"])): ?>
            <div class='error text-red-500 text-sm mt-1'>Invalid role selected. Please choose Room Seeker or Room Owner.</div>
        <?php endif; ?>

        <!-- Landlord Fields -->
        <div id="landlordFields" class="space-y-3" style="display:none;">
            <input type="number" name="room_capacity" placeholder="Room Capacity" class="w-full p-3 border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 outline-none">
            <select name="room_type" class="w-full p-3 border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 outline-none">
                <option value="">Select Room Type</option>
                <option value="single">Single</option>
                <option value="double">Double</option>
                <option value="shared">Shared</option>
            </select>
            <input type="text" name="location" placeholder="Location" class="w-full p-3 border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 outline-none">
            <input type="number" name="rent" placeholder="Mobile Number..." class="w-full p-3 border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 outline-none">
        </div>

        <!-- Tenant Fields -->
        <div id="tenantFields" class="space-y-3" style="display:none;">
            <input type="number" name="budget_min" placeholder="Minimum Budget" class="w-full p-3 border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 outline-none">
            <input type="number" name="budget_max" placeholder="Maximum Budget" class="w-full p-3 border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 outline-none">
            <input type="text" name="preferred_location" placeholder="Preferred Location" class="w-full p-3 border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 outline-none">
            <input type="number" name="roommate_count" placeholder="Number of Roommates" class="w-full p-3 border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 outline-none">
            <select name="preferred_room_type" class="w-full p-3 border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-400 outline-none">
                <option value="">Preferred Room Type</option>
                <option value="single">Single</option>
                <option value="double">Double</option>
                <option value="shared">Shared</option>
            </select>
        </div>

        <button type="submit" class="w-full p-3 bg-blue-500 text-white rounded-lg font-bold hover:bg-blue-600 transition-colors">Create Account</button>
    </form>
    <a class="block text-center mt-4 text-blue-500 hover:text-blue-600 hover:underline" href="login.php">Already have an account? Login</a>
</div>
</body>
</html>
