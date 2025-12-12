<?php
require 'auth.php';

// Handle actions
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $user_id = intval($_GET['id'] ?? 0);
    
    if ($user_id > 0) {
        switch ($action) {
            case 'verify':
                $updateStmt = $conn->prepare("UPDATE users SET is_verified = 1 WHERE id = ?");
                $updateStmt->bind_param("i", $user_id);
                $updateStmt->execute();
                $updateStmt->close();
                
                // Log action
                $logStmt = $conn->prepare("INSERT INTO admin_logs (admin_id, action, target_type, target_id, details) VALUES (?, 'verify_user', 'user', ?, 'User verified')");
                $logStmt->bind_param("ii", $_SESSION['admin_id'], $user_id);
                $logStmt->execute();
                $logStmt->close();
                break;
            case 'unverify':
                $updateStmt = $conn->prepare("UPDATE users SET is_verified = 0 WHERE id = ?");
                $updateStmt->bind_param("i", $user_id);
                $updateStmt->execute();
                $updateStmt->close();
                
                $logStmt = $conn->prepare("INSERT INTO admin_logs (admin_id, action, target_type, target_id, details) VALUES (?, 'unverify_user', 'user', ?, 'User verification removed')");
                $logStmt->bind_param("ii", $_SESSION['admin_id'], $user_id);
                $logStmt->execute();
                $logStmt->close();
                break;
            case 'delete':
                $deleteStmt = $conn->prepare("DELETE FROM users WHERE id = ?");
                $deleteStmt->bind_param("i", $user_id);
                $deleteStmt->execute();
                $deleteStmt->close();
                
                $logStmt = $conn->prepare("INSERT INTO admin_logs (admin_id, action, target_type, target_id, details) VALUES (?, 'delete_user', 'user', ?, 'User deleted')");
                $logStmt->bind_param("ii", $_SESSION['admin_id'], $user_id);
                $logStmt->execute();
                $logStmt->close();
                break;
        }
        header("Location: users.php");
        exit();
    }
}

// Get all users with pagination
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$per_page = 20;
$offset = ($page - 1) * $per_page;

$search = $_GET['search'] ?? '';
$role_filter = $_GET['role'] ?? '';

$where = "1=1";
if ($search) {
    $where .= " AND (name LIKE '%$search%' OR email LIKE '%$search%')";
}
if ($role_filter) {
    $where .= " AND role = '$role_filter'";
}

$total_result = $conn->query("SELECT COUNT(*) as total FROM users WHERE $where");
$total_users = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_users / $per_page);

// Check if created_at column exists, otherwise use id
$check_user_col = $conn->query("SHOW COLUMNS FROM users LIKE 'created_at'");
$order_by = ($check_user_col && $check_user_col->num_rows > 0) ? "ORDER BY created_at DESC" : "ORDER BY id DESC";
$users = $conn->query("SELECT * FROM users WHERE $where $order_by LIMIT $per_page OFFSET $offset");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
    <script src="https://cdn.tailwindcss.com/3.4.16"></script>
</head>
<body class="bg-gray-50 min-h-screen">
    <?php include 'sidebar.php'; ?>
    
    <div class="ml-64 p-8">
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-bold text-gray-800">Manage Users</h2>
                <p class="text-gray-600 mt-2">Total: <?php echo $total_users; ?> users</p>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
            <form method="GET" class="flex gap-4">
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                       placeholder="Search by name or email..." 
                       class="flex-1 px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <select name="role" class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Roles</option>
                    <option value="owner" <?php echo $role_filter === 'owner' ? 'selected' : ''; ?>>Owner</option>
                    <option value="seeker" <?php echo $role_filter === 'seeker' ? 'selected' : ''; ?>>Seeker</option>
                </select>
                <button type="submit" class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                    <i class="ri-search-line mr-2"></i>Search
                </button>
                <a href="users.php" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    Reset
                </a>
            </form>
        </div>

        <!-- Users Table -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">User</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Email</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Role</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Status</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Joined</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php while ($user = $users->fetch_assoc()): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                        <span class="text-blue-600 font-bold"><?php echo strtoupper(substr($user['name'] ?? 'U', 0, 1)); ?></span>
                                    </div>
                                    <span class="font-medium text-gray-800"><?php echo htmlspecialchars($user['name'] ?? 'Unknown'); ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-600"><?php echo htmlspecialchars($user['email'] ?? ''); ?></td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 bg-blue-100 text-blue-600 rounded-full text-sm font-semibold">
                                    <?php echo ucfirst($user['role'] ?? 'user'); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <?php if ($user['is_verified']): ?>
                                    <span class="px-3 py-1 bg-green-100 text-green-600 rounded-full text-sm font-semibold">
                                        <i class="ri-shield-check-fill"></i> Verified
                                    </span>
                                <?php else: ?>
                                    <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-sm">Not Verified</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                <?php if (isset($user['created_at'])): ?>
                                    <?php echo date('M j, Y', strtotime($user['created_at'])); ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2">
                                    <?php if ($user['role'] === 'owner'): ?>
                                        <?php if ($user['is_verified']): ?>
                                            <a href="?action=unverify&id=<?php echo $user['id']; ?>" 
                                               class="px-3 py-1 bg-yellow-100 text-yellow-600 rounded text-sm hover:bg-yellow-200"
                                               onclick="return confirm('Remove verification?')">
                                                <i class="ri-shield-cross-line"></i> Unverify
                                            </a>
                                        <?php else: ?>
                                            <a href="?action=verify&id=<?php echo $user['id']; ?>" 
                                               class="px-3 py-1 bg-green-100 text-green-600 rounded text-sm hover:bg-green-200"
                                               onclick="return confirm('Verify this owner?')">
                                                <i class="ri-shield-check-line"></i> Verify
                                            </a>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <a href="?action=delete&id=<?php echo $user['id']; ?>" 
                                       class="px-3 py-1 bg-red-100 text-red-600 rounded text-sm hover:bg-red-200"
                                       onclick="return confirm('Are you sure you want to delete this user?')">
                                        <i class="ri-delete-bin-line"></i> Delete
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="mt-6 flex justify-center space-x-2">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&role=<?php echo urlencode($role_filter); ?>" 
                       class="px-4 py-2 <?php echo $i == $page ? 'bg-blue-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-100'; ?> rounded-lg">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

