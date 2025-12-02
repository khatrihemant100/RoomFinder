<?php
require 'auth.php';

// Handle actions
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $property_id = intval($_GET['id'] ?? 0);
    
    if ($property_id > 0) {
        switch ($action) {
            case 'approve':
                $conn->query("UPDATE properties SET is_approved = 1 WHERE id = $property_id");
                $conn->query("INSERT INTO admin_logs (admin_id, action, target_type, target_id, details) VALUES ({$_SESSION['admin_id']}, 'approve_property', 'property', $property_id, 'Property approved')");
                break;
            case 'reject':
                $conn->query("UPDATE properties SET is_approved = 0 WHERE id = $property_id");
                $conn->query("INSERT INTO admin_logs (admin_id, action, target_type, target_id, details) VALUES ({$_SESSION['admin_id']}, 'reject_property', 'property', $property_id, 'Property rejected')");
                break;
            case 'delete':
                $conn->query("DELETE FROM properties WHERE id = $property_id");
                $conn->query("INSERT INTO admin_logs (admin_id, action, target_type, target_id, details) VALUES ({$_SESSION['admin_id']}, 'delete_property', 'property', $property_id, 'Property deleted')");
                break;
        }
        header("Location: properties.php");
        exit();
    }
}

// Get all properties with pagination
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$per_page = 20;
$offset = ($page - 1) * $per_page;

$search = $_GET['search'] ?? '';
$status_filter = $_GET['status'] ?? '';

$where = "1=1";
if ($search) {
    $where .= " AND (title LIKE '%$search%' OR location LIKE '%$search%')";
}
if ($status_filter === 'pending') {
    $where .= " AND is_approved = 0";
} elseif ($status_filter === 'approved') {
    $where .= " AND is_approved = 1";
}

$total_result = $conn->query("SELECT COUNT(*) as total FROM properties WHERE $where");
$total_properties = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_properties / $per_page);

// Check if created_at column exists, otherwise use id
$check_prop_col = $conn->query("SHOW COLUMNS FROM properties LIKE 'created_at'");
$order_by = ($check_prop_col && $check_prop_col->num_rows > 0) ? "ORDER BY p.created_at DESC" : "ORDER BY p.id DESC";
$properties = $conn->query("SELECT p.*, u.name as owner_name, u.email as owner_email FROM properties p LEFT JOIN users u ON p.user_id = u.id WHERE $where $order_by LIMIT $per_page OFFSET $offset");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Properties - Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
    <script src="https://cdn.tailwindcss.com/3.4.16"></script>
</head>
<body class="bg-gray-50 min-h-screen">
    <?php include 'sidebar.php'; ?>
    
    <div class="ml-64 p-8">
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-bold text-gray-800">Manage Properties</h2>
                <p class="text-gray-600 mt-2">Total: <?php echo $total_properties; ?> properties</p>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
            <form method="GET" class="flex gap-4">
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                       placeholder="Search by title or location..." 
                       class="flex-1 px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <select name="status" class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Status</option>
                    <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="approved" <?php echo $status_filter === 'approved' ? 'selected' : ''; ?>>Approved</option>
                </select>
                <button type="submit" class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                    <i class="ri-search-line mr-2"></i>Search
                </button>
                <a href="properties.php" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    Reset
                </a>
            </form>
        </div>

        <!-- Properties Table -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Property</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Owner</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Location</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Price</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Status</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Created</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php while ($property = $properties->fetch_assoc()): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <?php if ($property['image_url']): ?>
                                        <img src="../<?php echo htmlspecialchars($property['image_url']); ?>" 
                                             alt="Property" class="w-16 h-16 object-cover rounded-lg">
                                    <?php else: ?>
                                        <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                                            <i class="ri-home-line text-gray-400 text-2xl"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <p class="font-medium text-gray-800"><?php echo htmlspecialchars($property['title'] ?? 'Untitled'); ?></p>
                                        <p class="text-sm text-gray-500"><?php echo htmlspecialchars($property['type'] ?? ''); ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-gray-800"><?php echo htmlspecialchars($property['owner_name'] ?? 'Unknown'); ?></p>
                                <p class="text-sm text-gray-500"><?php echo htmlspecialchars($property['owner_email'] ?? ''); ?></p>
                            </td>
                            <td class="px-6 py-4 text-gray-600"><?php echo htmlspecialchars($property['location'] ?? ''); ?></td>
                            <td class="px-6 py-4">
                                <span class="font-semibold text-gray-800">¥<?php echo number_format($property['price'] ?? 0); ?></span>
                            </td>
                            <td class="px-6 py-4">
                                <?php if ($property['is_approved']): ?>
                                    <span class="px-3 py-1 bg-green-100 text-green-600 rounded-full text-sm font-semibold">
                                        <i class="ri-check-line"></i> Approved
                                    </span>
                                <?php else: ?>
                                    <span class="px-3 py-1 bg-orange-100 text-orange-600 rounded-full text-sm font-semibold">
                                        <i class="ri-time-line"></i> Pending
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                <?php if (isset($property['created_at'])): ?>
                                    <?php echo date('M j, Y', strtotime($property['created_at'])); ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2">
                                    <a href="../find-rooms.php?id=<?php echo $property['id']; ?>" 
                                       target="_blank"
                                       class="px-3 py-1 bg-blue-100 text-blue-600 rounded text-sm hover:bg-blue-200">
                                        <i class="ri-eye-line"></i> View
                                    </a>
                                    <?php if (!$property['is_approved']): ?>
                                        <a href="?action=approve&id=<?php echo $property['id']; ?>" 
                                           class="px-3 py-1 bg-green-100 text-green-600 rounded text-sm hover:bg-green-200"
                                           onclick="return confirm('Approve this property?')">
                                            <i class="ri-check-line"></i> Approve
                                        </a>
                                    <?php else: ?>
                                        <a href="?action=reject&id=<?php echo $property['id']; ?>" 
                                           class="px-3 py-1 bg-yellow-100 text-yellow-600 rounded text-sm hover:bg-yellow-200"
                                           onclick="return confirm('Reject this property?')">
                                            <i class="ri-close-line"></i> Reject
                                        </a>
                                    <?php endif; ?>
                                    <a href="?action=delete&id=<?php echo $property['id']; ?>" 
                                       class="px-3 py-1 bg-red-100 text-red-600 rounded text-sm hover:bg-red-200"
                                       onclick="return confirm('Are you sure you want to delete this property?')">
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
                    <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status_filter); ?>" 
                       class="px-4 py-2 <?php echo $i == $page ? 'bg-blue-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-100'; ?> rounded-lg">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

