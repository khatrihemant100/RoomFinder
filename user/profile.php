<?php
session_start();
require '../db.php';

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$message = "";
$messageType = "";

// Get current user data
$stmt = $conn->prepare("SELECT name, email, role, profile_photo FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($current_name, $current_email, $current_role, $current_profile_photo);
$stmt->fetch();
$stmt->close();

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Update Profile Information
    if (isset($_POST["update_profile"])) {
        $name = trim($_POST["name"]);
        $email = trim($_POST["email"]);
        
        if (empty($name) || empty($email)) {
            $message = "Name and email are required.";
            $messageType = "error";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = "Invalid email format.";
            $messageType = "error";
        } else {
            // Check if email is already taken by another user
            $checkStmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $checkStmt->bind_param("si", $email, $user_id);
            $checkStmt->execute();
            $result = $checkStmt->get_result();
            
            if ($result->num_rows > 0) {
                $message = "Email already exists. Please use a different email.";
                $messageType = "error";
            } else {
                $updateStmt = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
                $updateStmt->bind_param("ssi", $name, $email, $user_id);
                
                if ($updateStmt->execute()) {
                    $_SESSION["name"] = $name;
                    $current_name = $name;
                    $current_email = $email;
                    $message = "Profile updated successfully!";
                    $messageType = "success";
                } else {
                    $message = "Error updating profile. Please try again.";
                    $messageType = "error";
                }
                $updateStmt->close();
            }
            $checkStmt->close();
        }
    }
    
    // Change Password
    if (isset($_POST["change_password"])) {
        $current_password = $_POST["current_password"];
        $new_password = $_POST["new_password"];
        $confirm_password = $_POST["confirm_password"];
        
        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            $message = "All password fields are required.";
            $messageType = "error";
        } elseif (strlen($new_password) < 6) {
            $message = "New password must be at least 6 characters long.";
            $messageType = "error";
        } elseif ($new_password !== $confirm_password) {
            $message = "New passwords do not match.";
            $messageType = "error";
        } else {
            // Verify current password
            $verifyStmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
            $verifyStmt->bind_param("i", $user_id);
            $verifyStmt->execute();
            $verifyStmt->bind_result($hashed_password);
            $verifyStmt->fetch();
            $verifyStmt->close();
            
            if (password_verify($current_password, $hashed_password)) {
                // Update password
                $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $updateStmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $updateStmt->bind_param("si", $new_hashed_password, $user_id);
                
                if ($updateStmt->execute()) {
                    $message = "Password changed successfully!";
                    $messageType = "success";
                } else {
                    $message = "Error changing password. Please try again.";
                    $messageType = "error";
                }
                $updateStmt->close();
            } else {
                $message = "Current password is incorrect.";
                $messageType = "error";
            }
        }
    }
    
    // Upload Profile Photo
    if (isset($_POST["upload_photo"]) && isset($_FILES["profile_photo"])) {
        $file = $_FILES["profile_photo"];
        
        if ($file["error"] === UPLOAD_ERR_OK) {
            // Validate file type
            $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            $file_type = $file["type"];
            
            if (!in_array($file_type, $allowed_types)) {
                $message = "Invalid file type. Only JPG, PNG, GIF, and WEBP are allowed.";
                $messageType = "error";
            } elseif ($file["size"] > 5000000) { // 5MB limit
                $message = "File size too large. Maximum size is 5MB.";
                $messageType = "error";
            } else {
                // Generate unique filename
                $file_extension = pathinfo($file["name"], PATHINFO_EXTENSION);
                $new_filename = "profile_" . $user_id . "_" . time() . "." . $file_extension;
                $upload_path = "../uploads/" . $new_filename;
                
                // Create uploads directory if it doesn't exist
                if (!file_exists("../uploads/")) {
                    mkdir("../uploads/", 0777, true);
                }
                
                // Delete old profile photo if exists
                if (!empty($current_profile_photo) && file_exists("../uploads/" . $current_profile_photo)) {
                    unlink("../uploads/" . $current_profile_photo);
                }
                
                // Move uploaded file
                if (move_uploaded_file($file["tmp_name"], $upload_path)) {
                    // Update database
                    $updateStmt = $conn->prepare("UPDATE users SET profile_photo = ? WHERE id = ?");
                    $updateStmt->bind_param("si", $new_filename, $user_id);
                    
                    if ($updateStmt->execute()) {
                        $_SESSION["profile_photo"] = $new_filename;
                        $current_profile_photo = $new_filename;
                        $message = "Profile photo updated successfully!";
                        $messageType = "success";
                    } else {
                        $message = "Error updating profile photo. Please try again.";
                        $messageType = "error";
                    }
                    $updateStmt->close();
                } else {
                    $message = "Error uploading file. Please try again.";
                    $messageType = "error";
                }
            }
        } else {
            $message = "Error uploading file. Please try again.";
            $messageType = "error";
        }
    }
}

// Get user statistics
$properties_count = 0;
$inquiries_count = 0;
$messages_count = 0;
$sent_inquiries_count = 0;
$properties_list = [];
$inquiries_list = [];
$sent_inquiries_list = [];

// Count properties (for owners)
if ($current_role === 'owner') {
    // Get properties count and list
    $countStmt = $conn->prepare("SELECT COUNT(*) FROM properties WHERE user_id = ?");
    $countStmt->bind_param("i", $user_id);
    $countStmt->execute();
    $countStmt->bind_result($properties_count);
    $countStmt->fetch();
    $countStmt->close();
    
    // Get properties list
    $propListStmt = $conn->prepare("SELECT id, title, location, price, status, created_at FROM properties WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
    $propListStmt->bind_param("i", $user_id);
    $propListStmt->execute();
    $prop_result = $propListStmt->get_result();
    while ($row = $prop_result->fetch_assoc()) {
        $properties_list[] = $row;
    }
    $propListStmt->close();
    
    // Count inquiries received
    $inquiryStmt = $conn->prepare("SELECT COUNT(*) FROM inquiries i JOIN properties p ON i.room_id = p.id WHERE p.user_id = ?");
    $inquiryStmt->bind_param("i", $user_id);
    $inquiryStmt->execute();
    $inquiryStmt->bind_result($inquiries_count);
    $inquiryStmt->fetch();
    $inquiryStmt->close();
    
    // Get inquiries list
    $inqListStmt = $conn->prepare("SELECT i.id, i.name, i.email, i.message, i.created_at, p.title as room_title, p.id as room_id 
                                   FROM inquiries i 
                                   JOIN properties p ON i.room_id = p.id 
                                   WHERE p.user_id = ? 
                                   ORDER BY i.created_at DESC LIMIT 5");
    $inqListStmt->bind_param("i", $user_id);
    $inqListStmt->execute();
    $inq_result = $inqListStmt->get_result();
    while ($row = $inq_result->fetch_assoc()) {
        $inquiries_list[] = $row;
    }
    $inqListStmt->close();
} else {
    // For seekers: count sent inquiries
    $sentInqStmt = $conn->prepare("SELECT COUNT(*) FROM inquiries WHERE email = ?");
    $sentInqStmt->bind_param("s", $current_email);
    $sentInqStmt->execute();
    $sentInqStmt->bind_result($sent_inquiries_count);
    $sentInqStmt->fetch();
    $sentInqStmt->close();
    
    // Get sent inquiries list
    $sentInqListStmt = $conn->prepare("SELECT i.id, i.message, i.created_at, p.title as room_title, p.id as room_id, p.location, p.price
                                       FROM inquiries i 
                                       JOIN properties p ON i.room_id = p.id 
                                       WHERE i.email = ? 
                                       ORDER BY i.created_at DESC LIMIT 5");
    $sentInqListStmt->bind_param("s", $current_email);
    $sentInqListStmt->execute();
    $sent_inq_result = $sentInqListStmt->get_result();
    while ($row = $sent_inq_result->fetch_assoc()) {
        $sent_inquiries_list[] = $row;
    }
    $sentInqListStmt->close();
}

// Count unread messages
$msgStmt = $conn->prepare("SELECT COUNT(*) FROM messages WHERE receiver_id = ? AND is_read = 0");
$msgStmt->bind_param("i", $user_id);
$msgStmt->execute();
$msgStmt->bind_result($messages_count);
$msgStmt->fetch();
$msgStmt->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Profile - RoomFinder</title>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
    <script src="https://cdn.tailwindcss.com/3.4.16"></script>
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
        .profile-photo {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #4A90E2;
        }
    </style>
</head>
<body class="bg-gray-50 font-sans min-h-screen">
    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="container mx-auto px-4 py-3 flex items-center justify-between">
            <a href="../index.php" class="text-2xl font-['Pacifico'] text-primary" style="color:#4A90E2;">RoomFinder</a>
            <div class="flex items-center space-x-4">
                <a href="../index.php" class="text-gray-700 hover:text-primary transition-colors">Home</a>
                <a href="profile.php" class="text-primary font-semibold">Profile</a>
                <a href="logout.php" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">Logout</a>
            </div>
        </div>
    </header>

    <div class="container mx-auto px-4 py-8 max-w-4xl">
        <h1 class="text-3xl font-bold mb-6" style="font-family:'Pacifico',cursive;color:#4A90E2;">My Profile</h1>
        
        <?php if ($message): ?>
            <div class="<?php echo $messageType === 'success' ? 'success' : 'error'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="grid md:grid-cols-3 gap-6 mb-8">
            <!-- Profile Photo Section -->
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <div class="text-center">
                    <div class="mb-4 flex justify-center">
                        <?php if (!empty($current_profile_photo)): ?>
                            <img src="../uploads/<?php echo htmlspecialchars($current_profile_photo); ?>" alt="Profile Photo" class="profile-photo">
                        <?php else: ?>
                            <div class="profile-photo bg-gray-200 flex items-center justify-center text-4xl text-gray-400">
                                <i class="ri-user-line"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <h3 class="text-xl font-bold mb-2"><?php echo htmlspecialchars($current_name); ?></h3>
                    <p class="text-gray-600 mb-2"><?php echo htmlspecialchars($current_email); ?></p>
                    <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold <?php echo $current_role === 'owner' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'; ?>">
                        <?php echo ucfirst($current_role); ?>
                    </span>
                    
                    <form method="POST" enctype="multipart/form-data" class="mt-4">
                        <label class="block mb-2">
                            <input type="file" name="profile_photo" accept="image/*" class="hidden" id="photoInput" onchange="this.form.submit()">
                            <button type="button" onclick="document.getElementById('photoInput').click()" class="w-full px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                                <i class="ri-camera-line"></i> Change Photo
                            </button>
                        </label>
                        <input type="hidden" name="upload_photo" value="1">
                    </form>
                </div>
            </div>

            <!-- Statistics Section -->
            <div class="md:col-span-2 bg-white p-6 rounded-lg shadow-lg">
                <h2 class="text-2xl font-bold mb-4" style="color:#4A90E2;">Statistics</h2>
                <div class="grid <?php echo $current_role === 'owner' ? 'grid-cols-3' : 'grid-cols-2'; ?> gap-4">
                    <?php if ($current_role === 'owner'): ?>
                        <div class="text-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors cursor-pointer" onclick="window.location.href='../list-property.php'">
                            <div class="text-3xl font-bold text-blue-600"><?php echo $properties_count; ?></div>
                            <div class="text-gray-600 mt-1">Properties</div>
                            <div class="text-xs text-blue-500 mt-1">Manage →</div>
                        </div>
                        <div class="text-center p-4 bg-green-50 rounded-lg">
                            <div class="text-3xl font-bold text-green-600"><?php echo $inquiries_count; ?></div>
                            <div class="text-gray-600 mt-1">Inquiries</div>
                            <div class="text-xs text-gray-500 mt-1">Received</div>
                        </div>
                    <?php else: ?>
                        <div class="text-center p-4 bg-green-50 rounded-lg">
                            <div class="text-3xl font-bold text-green-600"><?php echo $sent_inquiries_count; ?></div>
                            <div class="text-gray-600 mt-1">Inquiries</div>
                            <div class="text-xs text-gray-500 mt-1">Sent</div>
                        </div>
                    <?php endif; ?>
                    <div class="text-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors cursor-pointer" onclick="window.location.href='../messages.php'">
                        <div class="text-3xl font-bold text-purple-600"><?php echo $messages_count; ?></div>
                        <div class="text-gray-600 mt-1">Messages</div>
                        <?php if ($messages_count > 0): ?>
                            <div class="text-xs text-purple-500 mt-1">Unread</div>
                        <?php else: ?>
                            <div class="text-xs text-gray-500 mt-1">View →</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Update Profile Form -->
        <div class="bg-white p-6 rounded-lg shadow-lg mb-6">
            <h2 class="text-2xl font-bold mb-4" style="color:#4A90E2;">Update Profile Information</h2>
            <form method="POST" class="space-y-4">
                <div>
                    <label class="block mb-2 font-semibold">Full Name</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($current_name); ?>" 
                           class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                </div>
                <div>
                    <label class="block mb-2 font-semibold">Email Address</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($current_email); ?>" 
                           class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                </div>
                <button type="submit" name="update_profile" class="w-full px-4 py-3 bg-blue-500 text-white rounded-lg font-bold hover:bg-blue-600 transition-colors">
                    Update Profile
                </button>
            </form>
        </div>

        <!-- Role-Specific Content -->
        <?php if ($current_role === 'owner'): ?>
            <!-- Owner: Properties List -->
            <?php if (!empty($properties_list)): ?>
                <div class="bg-white p-6 rounded-lg shadow-lg mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-2xl font-bold" style="color:#4A90E2;">My Properties</h2>
                        <a href="../list-property.php" class="text-blue-500 hover:text-blue-600 font-semibold">View All →</a>
                    </div>
                    <div class="space-y-3">
                        <?php foreach ($properties_list as $prop): ?>
                            <div class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-lg"><?php echo htmlspecialchars($prop['title']); ?></h3>
                                        <p class="text-gray-600 text-sm"><i class="ri-map-pin-line"></i> <?php echo htmlspecialchars($prop['location']); ?></p>
                                        <p class="text-blue-600 font-bold">¥<?php echo number_format($prop['price']); ?></p>
                                        <span class="inline-block px-2 py-1 text-xs rounded <?php echo $prop['status'] === 'available' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'; ?>">
                                            <?php echo ucfirst(str_replace('_', ' ', $prop['status'])); ?>
                                        </span>
                                    </div>
                                    <a href="../find-rooms.php?id=<?php echo $prop['id']; ?>" class="ml-4 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors text-sm">
                                        View
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="bg-white p-6 rounded-lg shadow-lg mb-6">
                    <h2 class="text-2xl font-bold mb-4" style="color:#4A90E2;">My Properties</h2>
                    <p class="text-gray-600 mb-4">You haven't listed any properties yet.</p>
                    <a href="../list-property.php" class="inline-block px-6 py-3 bg-blue-500 text-white rounded-lg font-bold hover:bg-blue-600 transition-colors">
                        <i class="ri-add-line"></i> List Your First Property
                    </a>
                </div>
            <?php endif; ?>

            <!-- Owner: Recent Inquiries -->
            <?php if (!empty($inquiries_list)): ?>
                <div class="bg-white p-6 rounded-lg shadow-lg mb-6">
                    <h2 class="text-2xl font-bold mb-4" style="color:#4A90E2;">Recent Inquiries</h2>
                    <div class="space-y-3">
                        <?php foreach ($inquiries_list as $inq): ?>
                            <div class="p-4 border border-gray-200 rounded-lg">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h3 class="font-semibold"><?php echo htmlspecialchars($inq['name']); ?></h3>
                                        <p class="text-gray-600 text-sm"><i class="ri-mail-line"></i> <?php echo htmlspecialchars($inq['email']); ?></p>
                                        <p class="text-blue-600 text-sm mt-1"><i class="ri-home-line"></i> <?php echo htmlspecialchars($inq['room_title']); ?></p>
                                        <p class="text-gray-700 mt-2"><?php echo htmlspecialchars(substr($inq['message'], 0, 100)); ?>...</p>
                                        <p class="text-xs text-gray-500 mt-2"><?php echo date('M j, Y g:i A', strtotime($inq['created_at'])); ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <!-- Seeker: Sent Inquiries -->
            <?php if (!empty($sent_inquiries_list)): ?>
                <div class="bg-white p-6 rounded-lg shadow-lg mb-6">
                    <h2 class="text-2xl font-bold mb-4" style="color:#4A90E2;">My Inquiries</h2>
                    <div class="space-y-3">
                        <?php foreach ($sent_inquiries_list as $inq): ?>
                            <div class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-lg"><?php echo htmlspecialchars($inq['room_title']); ?></h3>
                                        <p class="text-gray-600 text-sm"><i class="ri-map-pin-line"></i> <?php echo htmlspecialchars($inq['location']); ?></p>
                                        <p class="text-blue-600 font-bold">¥<?php echo number_format($inq['price']); ?></p>
                                        <p class="text-gray-700 mt-2 text-sm"><?php echo htmlspecialchars(substr($inq['message'], 0, 100)); ?>...</p>
                                        <p class="text-xs text-gray-500 mt-2">Sent on <?php echo date('M j, Y g:i A', strtotime($inq['created_at'])); ?></p>
                                    </div>
                                    <a href="../find-rooms.php?id=<?php echo $inq['room_id']; ?>" class="ml-4 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors text-sm">
                                        View Room
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="bg-white p-6 rounded-lg shadow-lg mb-6">
                    <h2 class="text-2xl font-bold mb-4" style="color:#4A90E2;">My Inquiries</h2>
                    <p class="text-gray-600 mb-4">You haven't sent any inquiries yet.</p>
                    <a href="../find-rooms.php" class="inline-block px-6 py-3 bg-blue-500 text-white rounded-lg font-bold hover:bg-blue-600 transition-colors">
                        <i class="ri-search-line"></i> Find Rooms
                    </a>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Change Password Form -->
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <h2 class="text-2xl font-bold mb-4" style="color:#4A90E2;">Change Password</h2>
            <form method="POST" class="space-y-4">
                <div>
                    <label class="block mb-2 font-semibold">Current Password</label>
                    <input type="password" name="current_password" 
                           class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                </div>
                <div>
                    <label class="block mb-2 font-semibold">New Password</label>
                    <input type="password" name="new_password" 
                           class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                    <small class="text-gray-500">Must be at least 6 characters long</small>
                </div>
                <div>
                    <label class="block mb-2 font-semibold">Confirm New Password</label>
                    <input type="password" name="confirm_password" 
                           class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                </div>
                <button type="submit" name="change_password" class="w-full px-4 py-3 bg-blue-500 text-white rounded-lg font-bold hover:bg-blue-600 transition-colors">
                    Change Password
                </button>
            </form>
        </div>
    </div>
</body>
</html>

