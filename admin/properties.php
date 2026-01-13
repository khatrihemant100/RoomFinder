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
    <style>
        .line-clamp-1 {
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
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

        <!-- Properties Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
            <?php while ($property = $properties->fetch_assoc()): ?>
                <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                    <!-- Image Section -->
                    <div class="relative h-48 overflow-hidden bg-gradient-to-br from-gray-200 to-gray-300">
                        <?php 
                        $imageUrl = $property['image_url'] ?? '';
                        if ($imageUrl && (strpos($imageUrl, 'http') === 0 || strpos($imageUrl, 'uploads/') === 0)) {
                            $fullImageUrl = strpos($imageUrl, 'http') === 0 ? $imageUrl : '../' . $imageUrl;
                        ?>
                            <img src="<?php echo htmlspecialchars($fullImageUrl); ?>" 
                                 alt="<?php echo htmlspecialchars($property['title'] ?? 'Property'); ?>"
                                 class="w-full h-full object-cover transition-transform duration-500 hover:scale-110"
                                 onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 400 300\'%3E%3Crect fill=\'%23e5e7eb\' width=\'400\' height=\'300\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\' fill=\'%239ca3af\' font-family=\'Arial\' font-size=\'24\'%3ENo Image%3C/text%3E%3C/svg%3E';">
                        <?php } else { ?>
                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-100 to-purple-100">
                                <div class="text-center">
                                    <i class="ri-home-4-line text-6xl text-blue-400 mb-2"></i>
                                    <p class="text-gray-500 text-sm">No Image</p>
                                </div>
                            </div>
                        <?php } ?>
                        <!-- Price Badge -->
                        <div class="absolute top-3 right-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white px-4 py-2 rounded-full shadow-lg font-bold text-sm">
                            ¥<?php echo number_format($property['price'] ?? 0); ?>
                        </div>
                        <!-- Status Badge -->
                        <div class="absolute top-3 left-3">
                            <?php if ($property['is_approved']): ?>
                                <span class="px-3 py-1 bg-green-500 text-white rounded-full text-xs font-semibold shadow-lg flex items-center gap-1">
                                    <i class="ri-checkbox-circle-fill"></i> Approved
                                </span>
                            <?php else: ?>
                                <span class="px-3 py-1 bg-orange-500 text-white rounded-full text-xs font-semibold shadow-lg flex items-center gap-1">
                                    <i class="ri-time-line"></i> Pending
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Content Section -->
                    <div class="p-5">
                        <!-- Title & Type -->
                        <div class="mb-4">
                            <h3 class="text-xl font-bold text-gray-800 mb-1 line-clamp-1">
                                <?php echo htmlspecialchars($property['title'] ?? 'Untitled Property'); ?>
                            </h3>
                            <?php if ($property['type']): ?>
                                <span class="inline-flex items-center px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-xs font-medium">
                                    <i class="ri-home-line mr-1"></i>
                                    <?php echo htmlspecialchars($property['type']); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Location -->
                        <div class="flex items-center text-gray-600 mb-3">
                            <i class="ri-map-pin-line mr-2 text-blue-500"></i>
                            <span class="text-sm line-clamp-1"><?php echo htmlspecialchars($property['location'] ?? 'Location not specified'); ?></span>
                        </div>
                        
                        <!-- Owner Info -->
                        <div class="border-t border-gray-200 pt-3 mb-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs text-gray-500 mb-1">Owner</p>
                                    <p class="font-semibold text-gray-800 text-sm">
                                        <?php echo htmlspecialchars($property['owner_name'] ?? 'Unknown'); ?>
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-gray-500 mb-1">Created</p>
                                    <p class="text-sm text-gray-600">
                                        <?php echo isset($property['created_at']) ? date('M j, Y', strtotime($property['created_at'])) : '-'; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Actions -->
                        <div class="flex gap-2 pt-3 border-t border-gray-200">
                            <a href="../find-rooms.php?id=<?php echo $property['id']; ?>" 
                               target="_blank"
                               class="flex-1 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors text-center text-sm font-medium">
                                <i class="ri-eye-line mr-1"></i> View
                            </a>
                            <?php if (!$property['is_approved']): ?>
                                <a href="?action=approve&id=<?php echo $property['id']; ?>" 
                                   class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors text-sm font-medium"
                                   onclick="return confirm('Approve this property?')"
                                   title="Approve">
                                    <i class="ri-check-line"></i>
                                </a>
                            <?php else: ?>
                                <a href="?action=reject&id=<?php echo $property['id']; ?>" 
                                   class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors text-sm font-medium"
                                   onclick="return confirm('Reject this property?')"
                                   title="Reject">
                                    <i class="ri-close-line"></i>
                                </a>
                            <?php endif; ?>
                            <a href="?action=delete&id=<?php echo $property['id']; ?>" 
                               class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors text-sm font-medium"
                               onclick="return confirm('Delete this property?')"
                               title="Delete">
                                <i class="ri-delete-bin-line"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
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

