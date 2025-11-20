<?php
session_start();
require 'db.php';

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: user/login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

// Get all conversations (unique users you've messaged or received messages from)
$conversations_query = "
    SELECT DISTINCT 
        CASE 
            WHEN m.sender_id = ? THEN m.receiver_id
            ELSE m.sender_id
        END as other_user_id,
        u.name as other_user_name,
        u.profile_photo as other_user_photo,
        u.role as other_user_role,
        (SELECT message FROM messages m2 
         WHERE (m2.sender_id = ? AND m2.receiver_id = other_user_id) 
            OR (m2.sender_id = other_user_id AND m2.receiver_id = ?)
         ORDER BY m2.created_at DESC LIMIT 1) as last_message,
        (SELECT created_at FROM messages m2 
         WHERE (m2.sender_id = ? AND m2.receiver_id = other_user_id) 
            OR (m2.sender_id = other_user_id AND m2.receiver_id = ?)
         ORDER BY m2.created_at DESC LIMIT 1) as last_message_time,
        (SELECT COUNT(*) FROM messages m3 
         WHERE m3.receiver_id = ? AND m3.sender_id = other_user_id AND m3.is_read = 0) as unread_count
    FROM messages m
    JOIN users u ON (CASE WHEN m.sender_id = ? THEN m.receiver_id ELSE m.sender_id END = u.id)
    WHERE m.sender_id = ? OR m.receiver_id = ?
    GROUP BY other_user_id, u.name, u.profile_photo, u.role
    ORDER BY last_message_time DESC
";

$stmt = $conn->prepare($conversations_query);
$stmt->bind_param("iiiiiiiii", $user_id, $user_id, $user_id, $user_id, $user_id, $user_id, $user_id, $user_id, $user_id);
$stmt->execute();
$conversations_result = $stmt->get_result();
$conversations = [];
while ($row = $conversations_result->fetch_assoc()) {
    $conversations[] = $row;
}
$stmt->close();

// Get selected conversation
$selected_user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : null;
$selected_room_id = isset($_GET['room_id']) ? (int)$_GET['room_id'] : null;
$selected_user = null;
$selected_room = null;
$messages = [];

// Get room info if room_id is provided
if ($selected_room_id) {
    $roomStmt = $conn->prepare("SELECT id, title, location, price FROM properties WHERE id = ?");
    $roomStmt->bind_param("i", $selected_room_id);
    $roomStmt->execute();
    $room_result = $roomStmt->get_result();
    if ($room_row = $room_result->fetch_assoc()) {
        $selected_room = $room_row;
    }
    $roomStmt->close();
}

if ($selected_user_id) {
    // Get selected user info
    $userStmt = $conn->prepare("SELECT id, name, email, profile_photo, role FROM users WHERE id = ?");
    $userStmt->bind_param("i", $selected_user_id);
    $userStmt->execute();
    $userStmt->bind_result($selected_user['id'], $selected_user['name'], $selected_user['email'], $selected_user['profile_photo'], $selected_user['role']);
    $userStmt->fetch();
    $userStmt->close();
    
    // Get messages between current user and selected user
    $messagesStmt = $conn->prepare("
        SELECT m.*, 
               s.name as sender_name, s.profile_photo as sender_photo,
               r.name as receiver_name, r.profile_photo as receiver_photo
        FROM messages m
        JOIN users s ON m.sender_id = s.id
        JOIN users r ON m.receiver_id = r.id
        WHERE (m.sender_id = ? AND m.receiver_id = ?) OR (m.sender_id = ? AND m.receiver_id = ?)
        ORDER BY m.created_at ASC
    ");
    $messagesStmt->bind_param("iiii", $user_id, $selected_user_id, $selected_user_id, $user_id);
    $messagesStmt->execute();
    $messages_result = $messagesStmt->get_result();
    while ($row = $messages_result->fetch_assoc()) {
        $messages[] = $row;
    }
    $messagesStmt->close();
    
    // Mark messages as read
    $updateStmt = $conn->prepare("UPDATE messages SET is_read = 1 WHERE receiver_id = ? AND sender_id = ? AND is_read = 0");
    $updateStmt->bind_param("ii", $user_id, $selected_user_id);
    $updateStmt->execute();
    $updateStmt->close();
}

// Get unread count for header
$unreadStmt = $conn->prepare("SELECT COUNT(*) FROM messages WHERE receiver_id = ? AND is_read = 0");
$unreadStmt->bind_param("i", $user_id);
$unreadStmt->execute();
$unreadStmt->bind_result($total_unread);
$unreadStmt->fetch();
$unreadStmt->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Messages - RoomFinder</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
    <script src="https://cdn.tailwindcss.com/3.4.16"></script>
    <style>
        .message-bubble {
            max-width: 70%;
            word-wrap: break-word;
        }
        .message-sent {
            background: #4A90E2;
            color: white;
            border-radius: 18px 18px 4px 18px;
        }
        .message-received {
            background: #e5e7eb;
            color: #333;
            border-radius: 18px 18px 18px 4px;
        }
        .conversation-item:hover {
            background: #f3f4f6;
        }
        .unread-badge {
            background: #FF6B6B;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="container mx-auto px-4 py-3 flex items-center justify-between">
            <a href="index.php" class="text-2xl font-['Pacifico'] text-primary" style="color:#4A90E2;">RoomFinder</a>
            <div class="hidden md:flex items-center space-x-6">
                <a href="index.php" class="text-gray-700 hover:text-primary transition-colors">Home</a>
                <a href="find-rooms.php" class="text-gray-700 hover:text-primary transition-colors">Find Rooms</a>
                <?php if(isset($_SESSION["role"]) && $_SESSION["role"] === 'owner'): ?>
                <a href="list-property.php" class="text-gray-700 hover:text-primary transition-colors">List Property</a>
                <?php endif; ?>
                <a href="about.php" class="text-gray-700 hover:text-primary transition-colors">About Us</a>
                <a href="contact.php" class="text-gray-700 hover:text-primary transition-colors">Contact</a>
                <a href="messages.php" class="text-primary font-semibold relative">
                    Messages
                    <?php if ($total_unread > 0): ?>
                        <span class="unread-badge absolute -top-1 -right-1"><?php echo $total_unread; ?></span>
                    <?php endif; ?>
                </a>
            </div>
            <div class="flex items-center space-x-4">
                <?php if(isset($_SESSION["user_id"])): ?>
                    <?php
                    $user_name = $_SESSION["name"] ?? "User";
                    $user_photo = $_SESSION["profile_photo"] ?? null;
                    $user_initial = strtoupper(substr($user_name, 0, 1));
                    ?>
                    <!-- User Profile Dropdown -->
                    <div class="relative">
                        <button onclick="toggleUserDropdown()" class="flex items-center space-x-2 px-3 py-2 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="w-10 h-10 rounded-full <?php echo $user_photo ? '' : 'bg-green-500'; ?> flex items-center justify-center overflow-hidden">
                                <?php if ($user_photo): ?>
                                    <img src="uploads/<?php echo htmlspecialchars($user_photo); ?>" alt="<?php echo htmlspecialchars($user_name); ?>" class="w-full h-full object-cover">
                                <?php else: ?>
                                    <span class="text-white font-bold text-lg"><?php echo $user_initial; ?></span>
                                <?php endif; ?>
                            </div>
                            <span class="hidden md:block text-gray-700 font-semibold"><?php echo htmlspecialchars(strtoupper($user_name)); ?></span>
                            <i class="ri-arrow-down-s-line text-gray-600"></i>
                        </button>
                        <!-- Dropdown Menu -->
                        <div id="userDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-50">
                            <a href="index.php" class="flex items-center space-x-3 px-4 py-2 hover:bg-gray-100 transition-colors">
                                <i class="ri-dashboard-line text-gray-600"></i>
                                <span class="text-gray-700">Dashboard</span>
                            </a>
                            <a href="user/profile.php" class="flex items-center space-x-3 px-4 py-2 hover:bg-gray-100 transition-colors">
                                <i class="ri-user-line text-gray-600"></i>
                                <span class="text-gray-700">Profile</span>
                            </a>
                            <hr class="my-2 border-gray-200">
                            <a href="user/logout.php" class="flex items-center space-x-3 px-4 py-2 hover:bg-red-50 transition-colors">
                                <i class="ri-logout-box-r-line text-red-500"></i>
                                <span class="text-red-500">Logout</span>
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="user/login.php" class="px-4 py-2 text-primary border border-primary rounded-button hover:bg-primary hover:text-white transition-colors">Sign In</a>
                    <a href="user/createaccount.php" class="px-4 py-2 bg-primary text-white rounded-button hover:bg-primary/90 transition-colors">Sign Up</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <div class="container mx-auto px-4 py-8 max-w-6xl">
        <h1 class="text-3xl font-bold mb-6" style="font-family:'Pacifico',cursive;color:#4A90E2;">Messages</h1>
        
        <div class="bg-white rounded-lg shadow-lg overflow-hidden" style="height: 600px;">
            <div class="flex h-full">
                <!-- Conversations List -->
                <div class="w-1/3 border-r border-gray-200 overflow-y-auto">
                    <div class="p-4 border-b border-gray-200">
                        <h2 class="text-xl font-bold">Conversations</h2>
                    </div>
                    <?php if (empty($conversations)): ?>
                        <div class="p-4 text-center text-gray-500">
                            <i class="ri-message-3-line text-4xl mb-2"></i>
                            <p>No conversations yet</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($conversations as $conv): ?>
                            <a href="?user_id=<?php echo $conv['other_user_id']; ?>" 
                               class="block p-4 border-b border-gray-100 conversation-item <?php echo ($selected_user_id == $conv['other_user_id']) ? 'bg-blue-50' : ''; ?>">
                                <div class="flex items-center space-x-3">
                                    <div class="relative">
                                        <?php if (!empty($conv['other_user_photo'])): ?>
                                            <img src="uploads/<?php echo htmlspecialchars($conv['other_user_photo']); ?>" 
                                                 alt="<?php echo htmlspecialchars($conv['other_user_name']); ?>" 
                                                 class="w-12 h-12 rounded-full object-cover">
                                        <?php else: ?>
                                            <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center">
                                                <i class="ri-user-line text-xl text-gray-400"></i>
                                            </div>
                                        <?php endif; ?>
                                        <?php if ($conv['unread_count'] > 0): ?>
                                            <span class="unread-badge absolute -top-1 -right-1"><?php echo $conv['unread_count']; ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between">
                                            <h3 class="font-semibold text-gray-900 truncate"><?php echo htmlspecialchars($conv['other_user_name']); ?></h3>
                                            <?php if ($conv['last_message_time']): ?>
                                                <span class="text-xs text-gray-500"><?php echo date('M j', strtotime($conv['last_message_time'])); ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <p class="text-sm text-gray-600 truncate <?php echo ($conv['unread_count'] > 0) ? 'font-semibold' : ''; ?>">
                                            <?php echo htmlspecialchars(substr($conv['last_message'], 0, 50)); ?>
                                            <?php echo strlen($conv['last_message']) > 50 ? '...' : ''; ?>
                                        </p>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Messages Area -->
                <div class="flex-1 flex flex-col">
                    <?php if ($selected_user): ?>
                        <!-- Chat Header -->
                        <div class="p-4 border-b border-gray-200 bg-white">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <?php if (!empty($selected_user['profile_photo'])): ?>
                                        <img src="uploads/<?php echo htmlspecialchars($selected_user['profile_photo']); ?>" 
                                             alt="<?php echo htmlspecialchars($selected_user['name']); ?>" 
                                             class="w-10 h-10 rounded-full object-cover">
                                    <?php else: ?>
                                        <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center">
                                            <i class="ri-user-line text-lg text-gray-400"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <h3 class="font-semibold"><?php echo htmlspecialchars($selected_user['name']); ?></h3>
                                        <p class="text-sm text-gray-500"><?php echo ucfirst($selected_user['role']); ?></p>
                                    </div>
                                </div>
                                <?php if ($selected_room_id): ?>
                                    <a href="find-rooms.php?id=<?php echo $selected_room_id; ?>" class="text-sm text-blue-500 hover:text-blue-600">
                                        View Room <i class="ri-arrow-right-line"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Messages List -->
                        <div class="flex-1 overflow-y-auto p-4 space-y-4" id="messagesContainer">
                            <?php foreach ($messages as $msg): ?>
                                <div class="flex <?php echo ($msg['sender_id'] == $user_id) ? 'justify-end' : 'justify-start'; ?>">
                                    <div class="message-bubble message-<?php echo ($msg['sender_id'] == $user_id) ? 'sent' : 'received'; ?> px-4 py-2">
                                        <?php if ($msg['sender_id'] != $user_id): ?>
                                            <div class="text-xs font-semibold mb-1"><?php echo htmlspecialchars($msg['sender_name']); ?></div>
                                        <?php endif; ?>
                                        <p><?php echo nl2br(htmlspecialchars($msg['message'])); ?></p>
                                        <div class="text-xs mt-1 opacity-75">
                                            <?php echo date('M j, g:i A', strtotime($msg['created_at'])); ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Message Input -->
                        <div class="p-4 border-t border-gray-200 bg-white">
                            <?php if ($selected_room): ?>
                                <div class="mb-3 p-3 bg-blue-50 rounded-lg border border-blue-200">
                                    <p class="text-sm text-gray-600 mb-1">About room:</p>
                                    <p class="font-semibold text-blue-900"><?php echo htmlspecialchars($selected_room['title']); ?></p>
                                    <p class="text-sm text-gray-600"><?php echo htmlspecialchars($selected_room['location']); ?> - ¥<?php echo number_format($selected_room['price']); ?></p>
                                </div>
                            <?php endif; ?>
                            <form id="messageForm" class="flex space-x-2">
                                <input type="hidden" name="receiver_id" value="<?php echo $selected_user['id']; ?>">
                                <?php if ($selected_room_id): ?>
                                    <input type="hidden" name="room_id" value="<?php echo $selected_room_id; ?>">
                                    <input type="hidden" name="subject" value="Inquiry about: <?php echo htmlspecialchars($selected_room['title']); ?>">
                                <?php endif; ?>
                                <input type="text" name="message" id="messageInput" 
                                       placeholder="Type a message..." 
                                       class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400"
                                       required>
                                <button type="submit" class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                                    <i class="ri-send-plane-fill"></i> Send
                                </button>
                            </form>
                        </div>
                    <?php else: ?>
                        <!-- No Conversation Selected -->
                        <div class="flex-1 flex items-center justify-center text-gray-500">
                            <div class="text-center">
                                <i class="ri-message-3-line text-6xl mb-4"></i>
                                <p class="text-xl">Select a conversation to start messaging</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-scroll to bottom
        const messagesContainer = document.getElementById('messagesContainer');
        if (messagesContainer) {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }

        // Send message via AJAX
        document.getElementById('messageForm')?.addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const messageInput = document.getElementById('messageInput');
            const message = messageInput.value.trim();
            
            if (!message) return;
            
            try {
                const response = await fetch('api/send-message.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Reload page to show new message
                    window.location.reload();
                } else {
                    alert('Error sending message: ' + (data.error || 'Unknown error'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error sending message. Please try again.');
            }
        });

        // Auto-refresh messages every 5 seconds
        <?php if ($selected_user_id): ?>
        setInterval(function() {
            fetch('api/get-messages.php?user_id=<?php echo $selected_user_id; ?>')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.new_messages) {
                        window.location.reload();
                    }
                })
                .catch(error => console.error('Error:', error));
        }, 5000);
        <?php endif; ?>
        
        // Toggle user dropdown menu
        function toggleUserDropdown() {
            const dropdown = document.getElementById('userDropdown');
            dropdown.classList.toggle('hidden');
        }
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('userDropdown');
            const button = event.target.closest('[onclick="toggleUserDropdown()"]');
            if (dropdown && !dropdown.contains(event.target) && !button) {
                dropdown.classList.add('hidden');
            }
        });
    </script>
</body>
</html>

