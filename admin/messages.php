<?php
require 'auth.php';

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $message_id = intval($_GET['id'] ?? 0);
    if ($message_id > 0) {
        $conn->query("DELETE FROM messages WHERE id = $message_id");
        $conn->query("INSERT INTO admin_logs (admin_id, action, target_type, target_id, details) VALUES ({$_SESSION['admin_id']}, 'delete_message', 'message', $message_id, 'Message deleted')");
        header("Location: messages.php");
        exit();
    }
}

// Get all messages with pagination
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$per_page = 20;
$offset = ($page - 1) * $per_page;

$total_result = $conn->query("SELECT COUNT(*) as total FROM messages");
$total_messages = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_messages / $per_page);

$messages = $conn->query("
    SELECT m.*, 
           s.name as sender_name, s.email as sender_email,
           r.name as receiver_name, r.email as receiver_email,
           p.title as room_title
    FROM messages m
    LEFT JOIN users s ON m.sender_id = s.id
    LEFT JOIN users r ON m.receiver_id = r.id
    LEFT JOIN properties p ON m.property_id = p.id
    ORDER BY m.created_at DESC
    LIMIT $per_page OFFSET $offset
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Messages - Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
    <script src="https://cdn.tailwindcss.com/3.4.16"></script>
</head>
<body class="bg-gray-50 min-h-screen">
    <?php include 'sidebar.php'; ?>
    
    <div class="ml-64 p-8">
        <div class="mb-8">
            <h2 class="text-3xl font-bold text-gray-800">Manage Messages</h2>
            <p class="text-gray-600 mt-2">Total: <?php echo $total_messages; ?> messages</p>
        </div>

        <!-- Messages Table -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">From</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">To</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Subject</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Message</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Room</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Date</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php while ($message = $messages->fetch_assoc()): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <p class="font-medium text-gray-800"><?php echo htmlspecialchars($message['sender_name'] ?? 'Unknown'); ?></p>
                                <p class="text-sm text-gray-500"><?php echo htmlspecialchars($message['sender_email'] ?? ''); ?></p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-medium text-gray-800"><?php echo htmlspecialchars($message['receiver_name'] ?? 'Unknown'); ?></p>
                                <p class="text-sm text-gray-500"><?php echo htmlspecialchars($message['receiver_email'] ?? ''); ?></p>
                            </td>
                            <td class="px-6 py-4 text-gray-600"><?php echo htmlspecialchars($message['subject'] ?? 'No subject'); ?></td>
                            <td class="px-6 py-4">
                                <p class="text-gray-600 max-w-xs truncate"><?php echo htmlspecialchars(substr($message['message'] ?? '', 0, 50)); ?>...</p>
                            </td>
                            <td class="px-6 py-4">
                                <?php if ($message['room_title']): ?>
                                    <span class="px-3 py-1 bg-blue-100 text-blue-600 rounded text-sm">
                                        <?php echo htmlspecialchars($message['room_title']); ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-gray-400 text-sm">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-gray-600"><?php echo date('M j, Y g:i A', strtotime($message['created_at'])); ?></td>
                            <td class="px-6 py-4">
                                <a href="?action=delete&id=<?php echo $message['id']; ?>" 
                                   class="px-3 py-1 bg-red-100 text-red-600 rounded text-sm hover:bg-red-200"
                                   onclick="return confirm('Are you sure you want to delete this message?')">
                                    <i class="ri-delete-bin-line"></i> Delete
                                </a>
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
                    <a href="?page=<?php echo $i; ?>" 
                       class="px-4 py-2 <?php echo $i == $page ? 'bg-blue-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-100'; ?> rounded-lg">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

