<?php
require 'auth.php';

// Get statistics
$stats = [];

// Total users
$result = $conn->query("SELECT COUNT(*) as total FROM users");
$stats['total_users'] = $result->fetch_assoc()['total'];

// Total properties
$result = $conn->query("SELECT COUNT(*) as total FROM properties");
$stats['total_properties'] = $result->fetch_assoc()['total'];

// Pending properties (not approved)
$result = $conn->query("SELECT COUNT(*) as total FROM properties WHERE is_approved = 0");
$stats['pending_properties'] = $result->fetch_assoc()['total'];

// Total messages
$result = $conn->query("SELECT COUNT(*) as total FROM messages");
$stats['total_messages'] = $result->fetch_assoc()['total'];

// Verified owners
$result = $conn->query("SELECT COUNT(*) as total FROM users WHERE is_verified = 1 AND role = 'owner'");
$stats['verified_owners'] = $result->fetch_assoc()['total'];

// Recent properties (last 7 days) - check if created_at exists
$recent_properties_count = 0;
$check_column = $conn->query("SHOW COLUMNS FROM properties LIKE 'created_at'");
if ($check_column && $check_column->num_rows > 0) {
    $result = $conn->query("SELECT COUNT(*) as total FROM properties WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
    if ($result) {
        $recent_properties_count = $result->fetch_assoc()['total'];
    }
}
$stats['recent_properties'] = $recent_properties_count;

// Recent users (last 7 days) - check if created_at exists
$recent_users_count = 0;
$check_user_column = $conn->query("SHOW COLUMNS FROM users LIKE 'created_at'");
if ($check_user_column && $check_user_column->num_rows > 0) {
    $result = $conn->query("SELECT COUNT(*) as total FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
    if ($result) {
        $recent_users_count = $result->fetch_assoc()['total'];
    }
} else {
    // If created_at doesn't exist, just show 0 or get total users
    $recent_users_count = 0;
}
$stats['recent_users'] = $recent_users_count;

// Get recent activities
$recent_properties_query = "SELECT p.*, u.name as owner_name FROM properties p LEFT JOIN users u ON p.user_id = u.id";
$check_prop_column = $conn->query("SHOW COLUMNS FROM properties LIKE 'created_at'");
if ($check_prop_column && $check_prop_column->num_rows > 0) {
    $recent_properties_query .= " ORDER BY p.created_at DESC";
} else {
    $recent_properties_query .= " ORDER BY p.id DESC";
}
$recent_properties_query .= " LIMIT 5";
$recent_properties = $conn->query($recent_properties_query);

$recent_users_query = "SELECT * FROM users";
$check_user_col = $conn->query("SHOW COLUMNS FROM users LIKE 'created_at'");
if ($check_user_col && $check_user_col->num_rows > 0) {
    $recent_users_query .= " ORDER BY created_at DESC";
} else {
    $recent_users_query .= " ORDER BY id DESC";
}
$recent_users_query .= " LIMIT 5";
$recent_users = $conn->query($recent_users_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - RoomFinder</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
    <script src="https://cdn.tailwindcss.com/3.4.16"></script>
    <style>
        .stat-card {
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <div class="ml-64 p-8">
        <div class="mb-8">
            <h2 class="text-3xl font-bold text-gray-800">Dashboard</h2>
            <p class="text-gray-600 mt-2">Welcome back, <?php echo htmlspecialchars($admin['name']); ?>!</p>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="stat-card bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Total Users</p>
                        <p class="text-3xl font-bold text-gray-800 mt-2"><?php echo $stats['total_users']; ?></p>
                        <p class="text-green-600 text-sm mt-2">
                            <i class="ri-arrow-up-line"></i> +<?php echo $stats['recent_users']; ?> this week
                        </p>
                    </div>
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="ri-user-line text-blue-600 text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Total Properties</p>
                        <p class="text-3xl font-bold text-gray-800 mt-2"><?php echo $stats['total_properties']; ?></p>
                        <p class="text-green-600 text-sm mt-2">
                            <i class="ri-arrow-up-line"></i> +<?php echo $stats['recent_properties']; ?> this week
                        </p>
                    </div>
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="ri-home-line text-green-600 text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Pending Properties</p>
                        <p class="text-3xl font-bold text-gray-800 mt-2"><?php echo $stats['pending_properties']; ?></p>
                        <p class="text-orange-600 text-sm mt-2">Needs approval</p>
                    </div>
                    <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center">
                        <i class="ri-time-line text-orange-600 text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Total Messages</p>
                        <p class="text-3xl font-bold text-gray-800 mt-2"><?php echo $stats['total_messages']; ?></p>
                        <p class="text-gray-500 text-sm mt-2">All conversations</p>
                    </div>
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center">
                        <i class="ri-message-3-line text-purple-600 text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Properties -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-800">Recent Properties</h3>
                    <a href="properties.php" class="text-blue-500 hover:text-blue-600 text-sm">View All</a>
                </div>
                <div class="space-y-4">
                    <?php while ($property = $recent_properties->fetch_assoc()): ?>
                        <div class="flex items-center space-x-4 p-3 hover:bg-gray-50 rounded-lg transition-colors">
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="ri-home-line text-blue-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($property['title'] ?? 'Untitled'); ?></p>
                                <p class="text-sm text-gray-500">by <?php echo htmlspecialchars($property['owner_name'] ?? 'Unknown'); ?></p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-gray-800">¥<?php echo number_format($property['price'] ?? 0); ?></p>
                                <?php if (isset($property['created_at'])): ?>
                                    <p class="text-xs text-gray-500"><?php echo date('M j, Y', strtotime($property['created_at'])); ?></p>
                                <?php else: ?>
                                    <p class="text-xs text-gray-500">-</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <!-- Recent Users -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-800">Recent Users</h3>
                    <a href="users.php" class="text-blue-500 hover:text-blue-600 text-sm">View All</a>
                </div>
                <div class="space-y-4">
                    <?php while ($user = $recent_users->fetch_assoc()): ?>
                        <div class="flex items-center space-x-4 p-3 hover:bg-gray-50 rounded-lg transition-colors">
                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                <span class="text-green-600 font-bold"><?php echo strtoupper(substr($user['name'] ?? 'U', 0, 1)); ?></span>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($user['name'] ?? 'Unknown'); ?></p>
                                <p class="text-sm text-gray-500"><?php echo htmlspecialchars($user['email'] ?? ''); ?></p>
                            </div>
                            <div class="text-right">
                                <span class="px-2 py-1 bg-blue-100 text-blue-600 rounded text-xs font-semibold">
                                    <?php echo ucfirst($user['role'] ?? 'user'); ?>
                                </span>
                                <?php if (isset($user['created_at'])): ?>
                                    <p class="text-xs text-gray-500 mt-1"><?php echo date('M j, Y', strtotime($user['created_at'])); ?></p>
                                <?php else: ?>
                                    <p class="text-xs text-gray-500 mt-1">-</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

