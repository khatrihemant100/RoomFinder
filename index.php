<?php
 session_start(); 
?>
<!DOCTYPE html>
<html lang="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RoomFinder - Find Your Perfect Room</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flag-icons@7.2.3/css/flag-icons.min.css">
    <script src="https://cdn.tailwindcss.com/3.4.16"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { primary: '#4A90E2', secondary: '#FF6B6B' },
                    borderRadius: {
                        'none': '0px', 'sm': '4px', DEFAULT: '8px', 'md': '12px', 'lg': '16px',
                        'xl': '20px', '2xl': '24px', '3xl': '32px', 'full': '9999px', 'button': '8px'
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="styles.css">
    

<style>
  /* AI Chat Button & Chat Window */
  #ai-chat-btn {
    position: fixed;
    bottom: 32px;
    right: 32px;
    z-index: 9999;
    background: #4A90E2;
    color: #fff;
    border: 5px solid #4A90E2;
    border-radius: 50%;
    width: 64px;
    height: 64px;
    box-shadow: 0 4px 16px rgba(74,144,226,0.18);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background 0.2s;
    overflow: visible;
  }
  #ai-chat-btn .ai-logo,
  #ai-chat-btn .ai-bot {
    position: relative;
    z-index: 1;
  }
  #ai-chat-window {
    position: fixed;
    bottom: 110px;
    right: 32px;
    width: 350px;
    max-width: 95vw;
    height: 480px;
    background: #fff;
    border-radius: 18px;
    box-shadow: 0 8px 32px 0 rgba(74,144,226,0.18);
    z-index: 9999;
    display: none;
    flex-direction: column;
    overflow: hidden;
    border: 1.5px solid #4A90E2;
    animation: fadeIn 0.2s;
  }
  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(30px);}
    to { opacity: 1; transform: translateY(0);}
  }
  #ai-chat-header {
    background: #4A90E2;
    color: #fff;
    padding: 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
  }
  #ai-chat-header .ai-logo {
    font-size: 1.5rem;
    margin-right: 8px;
  }
  #ai-chat-header .ai-bot {
    font-size: 1.3rem;
    margin-left: 2px;
  }
  #ai-chat-header .close-btn {
    background: transparent;
    border: none;
    color: #fff;
    font-size: 1.5rem;
    cursor: pointer;
    margin-left: 8px;
  }
  #ai-chat-messages {
    flex: 1;
    padding: 16px;
    overflow-y: auto;
    background: #f8fafc;
    font-size: 1rem;
  }
  .ai-msg, .user-msg {
    margin-bottom: 12px;
    display: flex;
    align-items: flex-end;
  }
  .ai-msg .bubble {
    background: #eaf4fb;
    color: #222;
    border-radius: 12px 12px 12px 4px;
    padding: 10px 14px;
    max-width: 80%;
    font-size: 1rem;
    margin-right: auto;
    box-shadow: 0 2px 8px rgba(74,144,226,0.06);
  }
  .user-msg {
    justify-content: flex-end;
  }
  .user-msg .bubble {
    background: #4A90E2;
    color: #fff;
    border-radius: 12px 12px 4px 12px;
    padding: 10px 14px;
    max-width: 80%;
    font-size: 1rem;
    margin-left: auto;
    box-shadow: 0 2px 8px rgba(74,144,226,0.10);
  }
  #ai-chat-input-area {
    display: flex;
    border-top: 1px solid #e5e7eb;
    background: #fff;
    padding: 10px;
  }
  #ai-chat-input {
    flex: 1;
    border: none;
    border-radius: 8px;
    padding: 10px 12px;
    font-size: 1rem;
    background: #f3f4f6;
    margin-right: 8px;
    outline: none;
  }
  #ai-chat-send {
    background: #4A90E2;
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 0 18px;
    font-size: 1.1rem;
    cursor: pointer;
    transition: background 0.2s;
  }
  #ai-chat-send:hover {
    background: #357ABD;
  }

  /* Register Modal Styles with Animations */
  .register-modal-overlay {
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    right: 0 !important;
    bottom: 0 !important;
    width: 100vw !important;
    height: 100vh !important;
    min-height: 100vh !important;
    z-index: 10000 !important;
    background: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(4px);
    display: none;
    align-items: flex-start;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease-out;
    overflow-y: auto;
    overflow-x: hidden;
    padding: 40px 20px;
    box-sizing: border-box;
    margin: 0;
    -webkit-overflow-scrolling: touch;
  }
  
  .register-modal-overlay.show {
    display: flex !important;
    animation: fadeInOverlay 0.3s ease-out forwards;
  }

  @keyframes fadeInOverlay {
    from {
      opacity: 0;
    }
    to {
      opacity: 1;
    }
  }

  .register-modal-content {
    background: #fff;
    max-width: 500px;
    width: 100%;
    min-width: 300px;
    padding: 40px 32px;
    border-radius: 24px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    position: relative;
    text-align: center;
    margin: auto;
    transform: scale(0.8);
    opacity: 0;
    box-sizing: border-box;
    flex-shrink: 0;
    overflow: visible;
  }
  
  .register-modal-overlay.show .register-modal-content {
    animation: modalSlideIn 0.4s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
  }
  
  @media (max-width: 640px) {
    .register-modal-overlay {
      padding: 20px 10px;
    }
    .register-modal-content {
      width: 100%;
      padding: 32px 24px;
      max-width: 100%;
    }
  }
  
  @media (max-height: 600px) {
    .register-modal-overlay {
      align-items: flex-start;
      padding-top: 20px;
    }
  }

  @keyframes modalSlideIn {
    0% {
      transform: scale(0.8) translateY(30px);
      opacity: 0;
    }
    100% {
      transform: scale(1) translateY(0);
      opacity: 1;
    }
  }

  .register-modal-close {
    position: absolute;
    top: 16px;
    right: 16px;
    font-size: 1.5rem;
    background: #f3f4f6;
    border: none;
    cursor: pointer;
    color: #666;
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: all 0.3s ease;
  }

  .register-modal-close:hover {
    background: #e5e7eb;
    color: #333;
    transform: rotate(90deg) scale(1.1);
  }

  .register-modal-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 20px;
    background: linear-gradient(135deg, #4A90E2 0%, #357ABD 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    color: white;
    box-shadow: 0 8px 24px rgba(74, 144, 226, 0.3);
    animation: iconBounce 0.6s ease-out 0.2s both;
  }

  @keyframes iconBounce {
    0% {
      transform: scale(0);
      opacity: 0;
    }
    50% {
      transform: scale(1.1);
    }
    100% {
      transform: scale(1);
      opacity: 1;
    }
  }

  .register-modal-title {
    font-size: 1.75rem;
    font-weight: bold;
    margin-bottom: 12px;
    color: #1f2937;
    animation: fadeInUp 0.5s ease-out 0.3s both;
  }

  .register-modal-text {
    color: #6b7280;
    margin-bottom: 32px;
    line-height: 1.7;
    font-size: 1rem;
    animation: fadeInUp 0.5s ease-out 0.4s both;
  }

  @keyframes fadeInUp {
    from {
      opacity: 0;
      transform: translateY(20px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  .register-modal-buttons {
    display: flex;
    gap: 12px;
    justify-content: center;
    flex-wrap: wrap;
    animation: fadeInUp 0.5s ease-out 0.5s both;
  }

  .register-modal-btn-primary {
    padding: 14px 28px;
    background: linear-gradient(135deg, #4A90E2 0%, #357ABD 100%);
    color: white;
    border-radius: 12px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(74, 144, 226, 0.3);
    white-space: nowrap;
  }

  .register-modal-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(74, 144, 226, 0.4);
    background: linear-gradient(135deg, #357ABD 0%, #2a5f94 100%);
  }

  .register-modal-btn-primary:active {
    transform: translateY(0);
  }

  .register-modal-btn-secondary {
    padding: 14px 28px;
    border: 2px solid #e5e7eb;
    background: white;
    color: #6b7280;
    border-radius: 12px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
    white-space: nowrap;
  }

  .register-modal-btn-secondary:hover {
    border-color: #d1d5db;
    background: #f9fafb;
    color: #374151;
    transform: translateY(-2px);
  }

  .register-modal-btn-secondary:active {
    transform: translateY(0);
  }

  /* Modal close animation */
  .register-modal-overlay.closing {
    animation: fadeOutOverlay 0.3s ease-out forwards;
  }

  .register-modal-overlay.closing .register-modal-content {
    animation: modalSlideOut 0.3s ease-out forwards;
  }

  @keyframes fadeOutOverlay {
    from {
      opacity: 1;
    }
    to {
      opacity: 0;
    }
  }

  @keyframes modalSlideOut {
    from {
      transform: scale(1) translateY(0);
      opacity: 1;
    }
    to {
      transform: scale(0.8) translateY(-30px);
      opacity: 0;
    }
  }
</style>
</head>
<body class="bg-gray-50 min-h-screen">

<!-- AI Chat Button -->
<button id="ai-chat-btn" title="Chat with RoomFinder AI">
  <span class="neon-border"></span>
  <span class="ai-logo"><i class="ri-home-4-line"></i></span>
  <span class="ai-bot"><i class="ri-robot-2-line"></i></span>
</button>

<!-- AI Chat Window -->
<div id="ai-chat-window">
  <div id="ai-chat-header">
    <span>
      <span class="ai-logo"><i class="ri-home-4-line"></i></span>
      <span style="font-weight:bold;">RoomFinder AI</span>
      <span class="ai-bot"><i class="ri-robot-2-line"></i></span>
    </span>
    <button class="close-btn" id="ai-chat-close" title="Close">&times;</button>
  </div>
  <div id="ai-chat-messages">
    <div class="ai-msg">
      <div class="bubble">
        👋 Hi! I am RoomFinder AI.<br>
        Ask me anything about rooms, locations, prices, or how to use this site!
      </div>
    </div>
  </div>
  <form id="ai-chat-input-area" autocomplete="off">
    <input type="text" id="ai-chat-input" placeholder="Type your message..." required />
    <button type="submit" id="ai-chat-send"><i class="ri-send-plane-2-line"></i></button>
  </form>
</div>

    <!-- Header Start -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="container mx-auto px-4 py-3 flex items-center justify-between">
            <a href="index.php" class="text-2xl font-['Pacifico'] text-primary">RoomFinder</a>
            <div class="hidden md:flex items-center space-x-6">
                <a href="index.php" class="text-gray-700 hover:text-primary transition-colors" data-i18n="home">Home</a>
                <a href="find-rooms.php" class="text-gray-700 hover:text-primary transition-colors" data-i18n="find_rooms">Find Rooms</a>
                <?php if(isset($_SESSION["role"]) && $_SESSION["role"] === 'owner'): ?>
                <a href="list-property.php" class="text-gray-700 hover:text-primary transition-colors" data-i18n="list_property">List Property</a>
                <?php endif; ?>
                <a href="about.php" class="text-gray-700 hover:text-primary transition-colors" data-i18n="about_us">About Us</a>
                <a href="contact.php" class="text-gray-700 hover:text-primary transition-colors" data-i18n="contact">Contact</a>
                <?php if(isset($_SESSION["user_id"])): ?>
                <a href="messages.php" class="text-gray-700 hover:text-primary transition-colors relative">
                    Messages
                    <?php
                    // Get unread message count
                    if (isset($_SESSION["user_id"])) {
                        require_once 'db.php';
                        $unreadStmt = $conn->prepare("SELECT COUNT(*) FROM messages WHERE receiver_id = ? AND is_read = 0");
                        $unreadStmt->bind_param("i", $_SESSION["user_id"]);
                        $unreadStmt->execute();
                        $unreadStmt->bind_result($unread_count);
                        $unreadStmt->fetch();
                        $unreadStmt->close();
                        if ($unread_count > 0) {
                            echo '<span class="absolute top-0 right-0 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold" style="min-width: 20px; min-height: 20px; line-height: 20px; transform: translate(50%, -50%);">' . $unread_count . '</span>';
                        }
                    }
                    ?>
                </a>
                <?php endif; ?>
            </div>
            <div class="flex items-center space-x-4">
                <!-- Language Selector -->
                <div class="lang-selector relative hidden md:block">
                    <button onclick="toggleLangDropdown(this)" class="current-lang flex items-center space-x-2 px-3 py-2 text-gray-700 hover:text-primary transition-colors rounded-lg hover:bg-gray-50">
                        <span class="fi fi-gb fis" style="font-size: 1.2rem;"></span>
                        <span>EN</span>
                        <i class="ri-arrow-down-s-line text-sm"></i>
                    </button>
                    <div class="lang-dropdown hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-50">
                        <button onclick="setLanguage('en')" class="w-full text-left px-4 py-2 hover:bg-gray-100 flex items-center space-x-3 transition-colors">
                            <span class="fi fi-gb fis" style="font-size: 1.2rem;"></span>
                            <div>
                                <div class="font-medium">English</div>
                                <div class="text-xs text-gray-500">EN</div>
                        </div>
                    </button>
                        <button onclick="setLanguage('ja')" class="w-full text-left px-4 py-2 hover:bg-gray-100 flex items-center space-x-3 transition-colors">
                            <span class="fi fi-jp fis" style="font-size: 1.2rem;"></span>
                            <div>
                                <div class="font-medium">日本語</div>
                                <div class="text-xs text-gray-500">JA</div>
                            </div>
                        </button>
                        <button onclick="setLanguage('ne')" class="w-full text-left px-4 py-2 hover:bg-gray-100 flex items-center space-x-3 transition-colors">
                            <span class="fi fi-np fis" style="font-size: 1.2rem;"></span>
                            <div>
                                <div class="font-medium">नेपाली</div>
                                <div class="text-xs text-gray-500">NE</div>
                            </div>
                        </button>
                    </div>
                </div>
                <?php if(isset($_SESSION["user_id"])): ?>
                        <?php 
                    // Get user name, profile photo, and admin status
                    $user_name = $_SESSION["name"] ?? "User";
                    $user_photo = $_SESSION["profile_photo"] ?? null;
                    $user_initial = strtoupper(substr($user_name, 0, 1));
                    $is_admin = false;
                    
                        // If name is not set or empty, try to get from database
                    if (empty($user_name) || $user_name === "User") {
                            require_once 'db.php';
                        $stmt = $conn->prepare("SELECT name, profile_photo, is_admin FROM users WHERE id = ?");
                            $stmt->bind_param("i", $_SESSION["user_id"]);
                            $stmt->execute();
                        $stmt->bind_result($dbName, $dbPhoto, $dbIsAdmin);
                            if ($stmt->fetch() && !empty(trim($dbName))) {
                                $_SESSION["name"] = trim($dbName);
                            $_SESSION["profile_photo"] = $dbPhoto;
                            $_SESSION["is_admin"] = $dbIsAdmin ?? 0;
                            $user_name = trim($dbName);
                            $user_photo = $dbPhoto;
                            $is_admin = ($dbIsAdmin ?? 0) == 1;
                            $user_initial = strtoupper(substr($user_name, 0, 1));
                            }
                            $stmt->close();
                        } else {
                        // Check admin status from session or database
                        if (isset($_SESSION["is_admin"])) {
                            $is_admin = $_SESSION["is_admin"] == 1;
                        } else {
                            require_once 'db.php';
                            $stmt = $conn->prepare("SELECT is_admin FROM users WHERE id = ?");
                            $stmt->bind_param("i", $_SESSION["user_id"]);
                            $stmt->execute();
                            $stmt->bind_result($dbIsAdmin);
                            if ($stmt->fetch()) {
                                $_SESSION["is_admin"] = $dbIsAdmin ?? 0;
                                $is_admin = ($dbIsAdmin ?? 0) == 1;
                            }
                            $stmt->close();
                        }
                    }
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
                            <?php if ($is_admin): ?>
                            <hr class="my-2 border-gray-200">
                            <a href="admin/login.php" class="flex items-center space-x-3 px-4 py-2 hover:bg-blue-50 transition-colors">
                                <i class="ri-admin-line text-blue-500"></i>
                                <span class="text-blue-600 font-semibold">Admin Panel</span>
                            </a>
                            <?php endif; ?>
                            <hr class="my-2 border-gray-200">
                            <a href="user/logout.php" class="flex items-center space-x-3 px-4 py-2 hover:bg-red-50 transition-colors">
                                <i class="ri-logout-box-r-line text-red-500"></i>
                                <span class="text-red-500">Logout</span>
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="user/login.php" class="hidden md:block px-4 py-2 text-primary border border-primary rounded-button hover:bg-primary hover:text-white transition-colors whitespace-nowrap" data-i18n="sign_in">Sign In</a>
                    <a href="user/createaccount.php" class="px-4 py-2 bg-primary text-white rounded-button hover:bg-primary/90 transition-colors whitespace-nowrap" data-i18n="sign_up">Sign Up</a>
                <?php endif; ?>
                <button class="md:hidden w-10 h-10 flex items-center justify-center text-gray-700">
                    <i class="ri-menu-line text-xl"></i>
                </button>
            </div>
        </div>
    </header>

    <!-- नेपाली नोट: मुख्य हिरो सेसन (मुख्य शीर्षक र खोजी) -->
    <section class="hero-section w-full py-16 md:py-24 relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-r from-blue-50/50 to-transparent"></div>
        <div class="container mx-auto px-4 relative z-10">
            <div class="w-full max-w-xl fade-in-up">
                <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 mb-4 animate-fade-in" data-i18n="find_perfect_room">Find Your Perfect Room</h1>
                <p class="text-lg text-gray-700 mb-8" data-i18n="discover">Discover thousands of rooms and apartments for rent. Whether you're looking to list your property or find your next home, we've got you covered.</p>
                
                <div class="flex flex-col md:flex-row gap-4 mb-8">
                    <a href="find-rooms.php" class="px-6 py-3 bg-primary text-white rounded-button text-center hover:bg-primary/90 transition-colors whitespace-nowrap" data-i18n="find_room_btn">Find a Room</a>
                    
                    <?php if(isset($_SESSION["role"]) && $_SESSION["role"] === 'owner'): ?>
                        <a href="list-property.php" class="px-6 py-3 border border-primary text-primary rounded-button text-center hover:bg-gray-50 transition-colors whitespace-nowrap" data-i18n="list_property_btn">List Your Property</a>
                    <?php else: ?>
                        <button onclick="showRegisterNotification()" class="px-6 py-3 border border-primary text-primary rounded-button text-center hover:bg-gray-50 transition-colors whitespace-nowrap" data-i18n="register_owner">List Your Property</button>
                    <?php endif; ?>
                </div>
                
                <div class="bg-white rounded-xl shadow-2xl p-6 border border-gray-100 hover:shadow-3xl transition-all duration-300">
                  <form id="location-search-form" class="flex flex-col md:flex-row gap-4">
                    <div class="flex-grow">
                      <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                          <i class="ri-map-pin-line text-blue-500 text-xl"></i>
                        </div>
                        <input type="text" name="location" id="location-input" class="w-full pl-12 pr-4 py-4 border-2 border-gray-200 rounded-lg bg-gray-50 custom-input focus:bg-white focus:border-blue-500 transition-all duration-300 text-lg" placeholder="Enter location to search..." required>
                      </div>
                    </div>
                    <button type="submit" class="px-8 py-4 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-300 transform hover:scale-105 shadow-lg whitespace-nowrap font-semibold text-lg">
                      <i class="ri-search-line mr-2"></i>Search
                    </button>
                  </form>
                </div>
                
                <!-- Enhanced Modal/Dialog for search results -->
                <div id="search-modal" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;z-index:9999;background:rgba(0,0,0,0.5);backdrop-filter:blur(4px);align-items:center;justify-content:center;padding:20px;box-sizing:border-box;overflow:hidden;">
                  <div id="modal-content-box" style="background:#fff;max-width:900px;width:100%;height:85vh;border-radius:20px;box-shadow:0 20px 60px rgba(0,0,0,0.3);position:relative;display:flex;flex-direction:column;overflow:hidden;margin:auto;">
                    <!-- Modal Header -->
                    <div id="modal-header" style="background:linear-gradient(135deg, #4A90E2 0%, #5B9BD5 100%);padding:20px 24px;display:flex;justify-content:space-between;align-items:center;border-bottom:2px solid rgba(255,255,255,0.1);flex-shrink:0;min-height:80px;box-sizing:border-box;">
                      <div>
                        <h3 style="font-size:1.5rem;font-weight:bold;color:#fff;margin:0;display:flex;align-items:center;gap:10px;">
                          <i class="ri-search-line"></i> Search Results
                        </h3>
                        <p id="results-count" style="color:rgba(255,255,255,0.9);margin:4px 0 0 0;font-size:0.9rem;"></p>
                      </div>
                      <button id="close-modal" style="background:rgba(255,255,255,0.2);border:none;color:#fff;width:36px;height:36px;border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:1.5rem;transition:all 0.3s;flex-shrink:0;" onmouseover="this.style.background='rgba(255,255,255,0.3)'" onmouseout="this.style.background='rgba(255,255,255,0.2)'">
                        <i class="ri-close-line"></i>
                      </button>
                    </div>
                    
                    <!-- Filters Section -->
                    <div id="modal-filters" style="padding:16px 24px;background:#f8f9fa;border-bottom:1px solid #e9ecef;display:none;flex-shrink:0;box-sizing:border-box;">
                      <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:12px;">
                        <div>
                          <label style="display:block;font-size:0.85rem;color:#666;margin-bottom:4px;font-weight:500;">Sort By</label>
                          <select id="sort-filter" style="width:100%;padding:8px;border:1px solid #ddd;border-radius:8px;font-size:0.9rem;">
                            <option value="newest">Newest First</option>
                            <option value="price-low">Price: Low to High</option>
                            <option value="price-high">Price: High to Low</option>
                            <option value="name">Name A-Z</option>
                          </select>
                        </div>
                        <div>
                          <label style="display:block;font-size:0.85rem;color:#666;margin-bottom:4px;font-weight:500;">Max Price</label>
                          <input type="number" id="price-filter" placeholder="Any" style="width:100%;padding:8px;border:1px solid #ddd;border-radius:8px;font-size:0.9rem;">
                        </div>
                        <div>
                          <label style="display:block;font-size:0.85rem;color:#666;margin-bottom:4px;font-weight:500;">Room Type</label>
                          <input type="text" id="type-filter" placeholder="Any" style="width:100%;padding:8px;border:1px solid #ddd;border-radius:8px;font-size:0.9rem;">
                        </div>
                      </div>
                    </div>
                    
                    <!-- Results Container -->
                    <div id="modal-results" style="flex:1;overflow-y:auto;overflow-x:hidden;padding:20px 24px;min-height:0;box-sizing:border-box;-webkit-overflow-scrolling:touch;">
                      <!-- Results will be inserted here -->
                    </div>
                  </div>
                </div>
                
                <!-- Notification Modal for Register as Room Owner -->
                <div id="register-notification-modal" class="register-modal-overlay" style="display:none;">
                  <div class="register-modal-content">
                    <button id="close-register-modal" class="register-modal-close" onclick="closeRegisterNotification()" title="Close">
                      <i class="ri-close-line"></i>
                    </button>
                    <div class="register-modal-icon">
                      <i class="ri-user-add-line"></i>
                    </div>
                    <h3 class="register-modal-title">Account Required</h3>
                    <p class="register-modal-text">To register as a Room Owner, you need to create an account first. Please create your account to get started with listing your properties.</p>
                    <div class="register-modal-buttons">
                      <a href="user/createaccount.php?role=owner" class="register-modal-btn-primary">
                        <i class="ri-user-add-fill"></i> Create Account
                      </a>
                      <button onclick="closeRegisterNotification()" class="register-modal-btn-secondary">
                        <i class="ri-close-line"></i> Cancel
                      </button>
                    </div>
                  </div>
                </div>
            </div>
        </div>
    </section>

    <!-- नेपाली नोट: Role चयन सेसन (Owner/Seeker) -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold mb-4" data-i18n="choose_role">Choose Your Role</h2>
                <p class="text-gray-600 max-w-2xl mx-auto" data-i18n="role_desc">Whether you're looking to rent out your property or find your next home, RoomFinder has the tools you need.</p>
            </div>
            
            <div class="grid md:grid-cols-2 gap-8 max-w-4xl mx-auto">
                <div class="bg-white rounded-lg shadow-lg overflow-hidden transition-all duration-300 hover:shadow-2xl hover:transform hover:scale-105 fade-in-up">
                    <div class="h-48 bg-gray-200 relative overflow-hidden" style="background-image: url('https://readdy.ai/api/search-image?query=A%20person%20holding%20keys%20to%20a%20new%20apartment%2C%20standing%20in%20a%20doorway%20with%20a%20welcoming%20smile.%20The%20scene%20shows%20a%20well-maintained%20property%20with%20good%20lighting%2C%20clean%20interiors%2C%20and%20a%20sense%20of%20ownership%20and%20pride.%20The%20image%20conveys%20the%20concept%20of%20property%20management%20and%20renting%20out%20spaces.&width=600&height=400&seq=2&orientation=landscape'); background-size: cover; background-position: center;">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 hover:opacity-100 transition-opacity duration-300"></div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold mb-2">Room Owner</h3>
                        <p class="text-gray-600 mb-4">List your property, manage bookings, and connect with potential tenants. Our platform makes property management simple and efficient.</p>
                        <ul class="mb-6 space-y-2">
                            <li class="flex items-start">
                                <div class="w-5 h-5 flex items-center justify-center text-primary mt-0.5">
                                    <i class="ri-check-line"></i>
                                </div>
                                <span class="ml-2">List multiple properties</span>
                            </li>
                            <li class="flex items-start">
                                <div class="w-5 h-5 flex items-center justify-center text-primary mt-0.5">
                                    <i class="ri-check-line"></i>
                                </div>
                                <span class="ml-2">Manage bookings and inquiries</span>
                            </li>
                            <li class="flex items-start">
                                <div class="w-5 h-5 flex items-center justify-center text-primary mt-0.5">
                                    <i class="ri-check-line"></i>
                                </div>
                                <span class="ml-2">Get verified owner badge</span>
                            </li>
                        </ul>
                        <?php if(isset($_SESSION["role"]) && $_SESSION["role"] === 'owner'): ?>
                        <a href="list-property.php" class="block w-full px-4 py-3 bg-primary text-white text-center rounded-button hover:bg-primary/90 transition-colors whitespace-nowrap">List Your Property</a>
                        <?php else: ?>
                        <button onclick="showRegisterNotification()" class="block w-full px-4 py-3 bg-primary text-white text-center rounded-button hover:bg-primary/90 transition-colors whitespace-nowrap">Register as Room Owner</button>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow-lg overflow-hidden transition-all duration-300 hover:shadow-2xl hover:transform hover:scale-105 fade-in-up" style="animation-delay: 0.1s;">
                    <div class="h-48 bg-gray-200 relative overflow-hidden" style="background-image: url('https://readdy.ai/api/search-image?query=A%20person%20looking%20at%20apartment%20listings%20on%20a%20smartphone%20or%20tablet%2C%20sitting%20in%20a%20coffee%20shop%20or%20comfortable%20space.%20The%20individual%20appears%20focused%20and%20engaged%20in%20searching%20for%20accommodation.%20The%20scene%20conveys%20the%20modern%20approach%20to%20finding%20housing%20through%20digital%20platforms.&width=600&height=400&seq=3&orientation=landscape'); background-size: cover; background-position: center;">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 hover:opacity-100 transition-opacity duration-300"></div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold mb-2">Room Seeker</h3>
                        <p class="text-gray-600 mb-4">Find your perfect room with our advanced search tools. Filter by location, budget, amenities, and more to discover your ideal living space.</p>
                        <ul class="mb-6 space-y-2">
                            <li class="flex items-start">
                                <div class="w-5 h-5 flex items-center justify-center text-primary mt-0.5">
                                    <i class="ri-check-line"></i>
                                </div>
                                <span class="ml-2">Personalized search filters</span>
                            </li>
                            <li class="flex items-start">
                                <div class="w-5 h-5 flex items-center justify-center text-primary mt-0.5">
                                    <i class="ri-check-line"></i>
                                </div>
                                <span class="ml-2">Save favorite properties</span>
                            </li>
                            <li class="flex items-start">
                                <div class="w-5 h-5 flex items-center justify-center text-primary mt-0.5">
                                    <i class="ri-check-line"></i>
                                </div>
                                <span class="ml-2">Direct messaging with owners</span>
                            </li>
                        </ul>
                        <a href="find-rooms.php" class="block w-full px-4 py-3 bg-primary text-white text-center rounded-button hover:bg-primary/90 transition-colors whitespace-nowrap">Register as Seeker</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- नेपाली नोट: Features सेसन (मुख्य सुविधाहरू) -->
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold mb-4">Powerful Features</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Our platform offers a range of tools to make finding or listing a room as simple as possible.</p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white p-6 rounded-lg shadow-sm hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 fade-in-up">
                    <div class="w-12 h-12 flex items-center justify-center bg-gradient-to-br from-blue-400 to-blue-600 text-white rounded-full mb-4">
                        <i class="ri-map-pin-line text-xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Interactive Map Search</h3>
                    <p class="text-gray-600">Find rooms in your preferred location with our interactive map interface powered by Google Maps.</p>
                    <a href="find-rooms.php" class="mt-4 inline-block text-primary hover:underline font-medium">
                        View Map <i class="ri-arrow-right-line"></i>
                    </a>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow-sm hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 fade-in-up" style="animation-delay: 0.1s;">
                    <div class="w-12 h-12 flex items-center justify-center bg-gradient-to-br from-green-400 to-green-600 text-white rounded-full mb-4">
                        <i class="ri-message-3-line text-xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">In-App Messaging</h3>
                    <p class="text-gray-600">Communicate directly with property owners or potential tenants through our secure messaging system.</p>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow-sm hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 fade-in-up" style="animation-delay: 0.2s;">
                    <div class="w-12 h-12 flex items-center justify-center bg-gradient-to-br from-purple-400 to-purple-600 text-white rounded-full mb-4">
                        <i class="ri-ai-generate text-xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">AI Recommendations</h3>
                    <p class="text-gray-600">Get personalized room suggestions based on your preferences and previous searches.</p>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow-sm hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 fade-in-up" style="animation-delay: 0.3s;">
                    <div class="w-12 h-12 flex items-center justify-center bg-gradient-to-br from-red-400 to-red-600 text-white rounded-full mb-4">
                        <i class="ri-shield-check-line text-xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Verified Owners</h3>
                    <p class="text-gray-600">Look for the verification badge to ensure you're dealing with trusted property owners.</p>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow-sm hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 fade-in-up" style="animation-delay: 0.4s;">
                    <div class="w-12 h-12 flex items-center justify-center bg-gradient-to-br from-yellow-400 to-yellow-600 text-white rounded-full mb-4">
                        <i class="ri-translate-2 text-xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Multilingual Support</h3>
                    <p class="text-gray-600">Use our platform in multiple languages including English, Nepali, and Japanese.</p>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow-sm hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 fade-in-up" style="animation-delay: 0.5s;">
                    <div class="w-12 h-12 flex items-center justify-center bg-gradient-to-br from-indigo-400 to-indigo-600 text-white rounded-full mb-4">
                        <i class="ri-calculator-line text-xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Rent Calculator</h3>
                    <p class="text-gray-600">Calculate total costs including utilities and additional fees for better financial planning.</p>
                    <a href="rent-calculator.php" class="mt-4 inline-block text-primary hover:underline font-medium">
                        Try Calculator <i class="ri-arrow-right-line"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

   

    <!-- नेपाली नोट: How It Works सेसन (कसरी काम गर्छ) -->
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold mb-4">How It Works</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Our platform makes finding or listing a room simple and efficient.</p>
            </div>
            
            <div class="grid md:grid-cols-2 gap-8 max-w-4xl mx-auto">
                <!-- For Room Seekers -->
                <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 fade-in-up">
                    <h3 class="text-xl font-bold mb-4 text-center text-primary">For Room Seekers</h3>
                    
                    <div class="space-y-6">
                        <div class="flex group">
                            <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-full font-bold shadow-lg group-hover:scale-110 transition-transform duration-300">1</div>
                            <div class="ml-4">
                                <h4 class="font-semibold">Create an account</h4>
                                <p class="text-gray-600 text-sm mt-1">Sign up and complete your profile with your preferences.</p>
                            </div>
                        </div>
                        
                        <div class="flex group">
                            <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-full font-bold shadow-lg group-hover:scale-110 transition-transform duration-300">2</div>
                            <div class="ml-4">
                                <h4 class="font-semibold">Search for rooms</h4>
                                <p class="text-gray-600 text-sm mt-1">Use filters to find properties that match your requirements.</p>
                            </div>
                        </div>
                        
                        <div class="flex group">
                            <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-full font-bold shadow-lg group-hover:scale-110 transition-transform duration-300">3</div>
                            <div class="ml-4">
                                <h4 class="font-semibold">Contact owners</h4>
                                <p class="text-gray-600 text-sm mt-1">Message property owners directly through our platform.</p>
                            </div>
                        </div>
                        
                        <div class="flex group">
                            <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-full font-bold shadow-lg group-hover:scale-110 transition-transform duration-300">4</div>
                            <div class="ml-4">
                                <h4 class="font-semibold">Schedule viewings</h4>
                                <p class="text-gray-600 text-sm mt-1">Arrange to see the property and finalize your rental agreement.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- For Room Owners -->
                <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 fade-in-up" style="animation-delay: 0.1s;">
                    <h3 class="text-xl font-bold mb-4 text-center text-primary">For Room Owners</h3>
                    
                    <div class="space-y-6">
                        <div class="flex group">
                            <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-full font-bold shadow-lg group-hover:scale-110 transition-transform duration-300">1</div>
                            <div class="ml-4">
                                <h4 class="font-semibold">Create an account</h4>
                                <p class="text-gray-600 text-sm mt-1">Sign up as a property owner and verify your identity.</p>
                            </div>
                        </div>
                        
                        <div class="flex group">
                            <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-full font-bold shadow-lg group-hover:scale-110 transition-transform duration-300">2</div>
                            <div class="ml-4">
                                <h4 class="font-semibold">List your property</h4>
                                <p class="text-gray-600 text-sm mt-1">Add details, photos, and set your rental terms.</p>
                            </div>
                        </div>
                        
                        <div class="flex group">
                            <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-full font-bold shadow-lg group-hover:scale-110 transition-transform duration-300">3</div>
                            <div class="ml-4">
                                <h4 class="font-semibold">Manage inquiries</h4>
                                <p class="text-gray-600 text-sm mt-1">Respond to messages and arrange viewings with interested renters.</p>
                            </div>
                        </div>
                        
                        <div class="flex group">
                            <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-full font-bold shadow-lg group-hover:scale-110 transition-transform duration-300">4</div>
                            <div class="ml-4">
                                <h4 class="font-semibold">Secure your tenant</h4>
                                <p class="text-gray-600 text-sm mt-1">Select your preferred tenant and finalize the rental agreement.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistics Counter Section -->
    <section class="py-16 bg-gradient-to-r from-blue-600 to-blue-500 text-white">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-4 gap-8 text-center">
                <div class="fade-in-up">
                    <div class="text-5xl font-bold mb-2 counter" data-target="<?php 
                        require_once 'db.php';
                        $countStmt = $conn->prepare("SELECT COUNT(*) FROM properties");
                        $countStmt->execute();
                        $countStmt->bind_result($totalRooms);
                        $countStmt->fetch();
                        $countStmt->close();
                        echo $totalRooms ?? 0;
                    ?>">0</div>
                    <div class="text-xl opacity-90">Properties Listed</div>
                </div>
                <div class="fade-in-up" style="animation-delay: 0.1s;">
                    <div class="text-5xl font-bold mb-2 counter" data-target="<?php 
                        $userStmt = $conn->prepare("SELECT COUNT(*) FROM users");
                        $userStmt->execute();
                        $userStmt->bind_result($totalUsers);
                        $userStmt->fetch();
                        $userStmt->close();
                        echo $totalUsers ?? 0;
                    ?>">0</div>
                    <div class="text-xl opacity-90">Active Users</div>
                </div>
                <div class="fade-in-up" style="animation-delay: 0.2s;">
                    <div class="text-5xl font-bold mb-2 counter" data-target="95">0</div>
                    <div class="text-xl opacity-90">Success Rate</div>
                </div>
                <div class="fade-in-up" style="animation-delay: 0.3s;">
                    <div class="text-5xl font-bold mb-2 counter" data-target="24">0</div>
                    <div class="text-xl opacity-90">Support Hours</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold mb-4">What Our Users Say</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Don't just take our word for it. Here's what our community has to say about RoomFinder.</p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-gradient-to-br from-blue-50 to-white p-6 rounded-xl shadow-lg hover:shadow-xl transition-shadow fade-in-up">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white font-bold text-xl mr-4">
                            S
                        </div>
                        <div>
                            <h4 class="font-semibold"> Hemant&Aakash</h4>
                            <p class="text-sm text-gray-500">Room Seeker</p>
                        </div>
                    </div>
                    <div class="flex mb-3">
                        <i class="ri-star-fill text-yellow-400"></i>
                        <i class="ri-star-fill text-yellow-400"></i>
                        <i class="ri-star-fill text-yellow-400"></i>
                        <i class="ri-star-fill text-yellow-400"></i>
                        <i class="ri-star-fill text-yellow-400"></i>
                    </div>
                    <p class="text-gray-600 italic">"RoomFinder made finding my perfect apartment so easy! The search filters are amazing and I found exactly what I was looking for within days."</p>
                </div>
                
                <div class="bg-gradient-to-br from-green-50 to-white p-6 rounded-xl shadow-lg hover:shadow-xl transition-shadow fade-in-up" style="animation-delay: 0.1s;">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-green-400 to-green-600 flex items-center justify-center text-white font-bold text-xl mr-4">
                            M
                        </div>
                        <div>
                            <h4 class="font-semibold">Prabin</h4>
                            <p class="text-sm text-gray-500">Room Owner</p>
                        </div>
                    </div>
                    <div class="flex mb-3">
                        <i class="ri-star-fill text-yellow-400"></i>
                        <i class="ri-star-fill text-yellow-400"></i>
                        <i class="ri-star-fill text-yellow-400"></i>
                        <i class="ri-star-fill text-yellow-400"></i>
                        <i class="ri-star-fill text-yellow-400"></i>
                    </div>
                    <p class="text-gray-600 italic">"As a property owner, RoomFinder has been a game-changer. I've rented out all my properties quickly and the messaging system is so convenient!"</p>
                </div>
                
                <div class="bg-gradient-to-br from-purple-50 to-white p-6 rounded-xl shadow-lg hover:shadow-xl transition-shadow fade-in-up" style="animation-delay: 0.2s;">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center text-white font-bold text-xl mr-4">
                            E
                        </div>
                        <div>
                            <h4 class="font-semibold">Khan Thu</h4>
                            <p class="text-sm text-gray-500">Room Seeker</p>
                        </div>
                    </div>
                    <div class="flex mb-3">
                        <i class="ri-star-fill text-yellow-400"></i>
                        <i class="ri-star-fill text-yellow-400"></i>
                        <i class="ri-star-fill text-yellow-400"></i>
                        <i class="ri-star-fill text-yellow-400"></i>
                        <i class="ri-star-fill text-yellow-400"></i>
                    </div>
                    <p class="text-gray-600 italic">"The AI recommendations feature is incredible! It suggested properties I never would have found on my own. Highly recommend!"</p>
                </div>
            </div>
        </div>
    </section>

    
    <!-- नेपाली नोट: CTA सेसन (Call to Action) -->
    <section class="py-16 bg-gradient-to-r from-blue-600 via-blue-500 to-blue-600 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-0 left-0 w-72 h-72 bg-white rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 right-0 w-96 h-96 bg-white rounded-full blur-3xl"></div>
        </div>
        <div class="container mx-auto px-4 text-center relative z-10 fade-in-up">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">Ready to Find Your Perfect Room?</h2>
            <p class="text-white/90 max-w-2xl mx-auto mb-8 text-lg">Join thousands of satisfied users who have found their ideal living situation through RoomFinder.</p>
            
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="find-rooms.php" class="px-8 py-4 bg-white text-primary rounded-button hover:bg-gray-100 transition-all duration-300 transform hover:scale-105 shadow-lg whitespace-nowrap font-semibold">Find a Room</a>
                <?php if(isset($_SESSION["role"]) && $_SESSION["role"] === 'owner'): ?>
                <a href="list-property.php" class="px-8 py-4 border-2 border-white text-white rounded-button hover:bg-white/10 transition-all duration-300 transform hover:scale-105 whitespace-nowrap font-semibold">List Your Property</a>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- नेपाली नोट: Footer (पाद लेख) -->
    <footer class="bg-gradient-to-b from-gray-900 to-gray-800 text-white pt-12 pb-6">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-4 gap-8 mb-8">
                <div>
                    <a href="#" class="text-2xl font-['Pacifico'] text-white mb-4 inline-block">RoomFinder</a>
                    <p class="text-gray-400 mb-4">Find your perfect room or tenant with our easy-to-use platform.</p>
                    <div class="flex space-x-4">
                        <a href="#" class="w-12 h-12 flex items-center justify-center bg-gray-800 rounded-full hover:bg-blue-600 transition-all duration-300 transform hover:scale-110 hover:shadow-lg">
                            <i class="ri-facebook-fill text-lg"></i>
                        </a>
                        <a href="#" class="w-12 h-12 flex items-center justify-center bg-gray-800 rounded-full hover:bg-blue-400 transition-all duration-300 transform hover:scale-110 hover:shadow-lg">
                            <i class="ri-twitter-x-fill text-lg"></i>
                        </a>
                        <a href="#" class="w-12 h-12 flex items-center justify-center bg-gray-800 rounded-full hover:bg-pink-600 transition-all duration-300 transform hover:scale-110 hover:shadow-lg">
                            <i class="ri-instagram-fill text-lg"></i>
                        </a>
                        <a href="#" class="w-12 h-12 flex items-center justify-center bg-gray-800 rounded-full hover:bg-blue-700 transition-all duration-300 transform hover:scale-110 hover:shadow-lg">
                            <i class="ri-linkedin-fill text-lg"></i>
                        </a>
                    </div>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Home</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Find Rooms</a></li>
                        <?php if(isset($_SESSION["role"]) && $_SESSION["role"] === 'owner'): ?>
                        <li><a href="list-property.php" class="text-gray-400 hover:text-white transition-colors">List Property</a></li>
                        <?php endif; ?>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">How It Works</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">About Us</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Contact</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold mb-4">Support</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Help Center</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Safety Center</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Community Guidelines</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Terms of Service</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Privacy Policy</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Cookie Policy</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold mb-4">Subscribe</h3>
                    <p class="text-gray-400 mb-4">Stay updated with the latest properties and features.</p>
                    <form class="mb-4">
                        <div class="flex">
                            <input type="email" placeholder="Your email" class="px-4 py-2 w-full rounded-l-button border-none focus:outline-none text-gray-900">
                            <button type="submit" class="bg-primary text-white px-4 py-2 rounded-r-button hover:bg-primary/90 transition-colors whitespace-nowrap">Subscribe</button>
                        </div>
                    </form>
                    <div class="flex flex-wrap gap-2">
                        <div class="w-8 h-8 flex items-center justify-center">
                            <i class="ri-visa-fill text-2xl text-gray-400"></i>
                        </div>
                        <div class="w-8 h-8 flex items-center justify-center">
                            <i class="ri-mastercard-fill text-2xl text-gray-400"></i>
                        </div>
                        <div class="w-8 h-8 flex items-center justify-center">
                            <i class="ri-paypal-fill text-2xl text-gray-400"></i>
                        </div>
                        <div class="w-8 h-8 flex items-center justify-center">
                            <i class="ri-apple-fill text-2xl text-gray-400"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="border-t border-gray-800 pt-6 flex flex-col md:flex-row justify-between items-center">
                <p class="text-gray-400 text-sm mb-4 md:mb-0">© 2025 RoomFinder. All rights reserved.</p>
                <div class="flex space-x-4 items-center">
                    <a href="#" class="text-gray-400 hover:text-white transition-colors text-sm">Terms</a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors text-sm">Privacy</a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors text-sm">Cookies</a>
                    <span class="text-gray-600">|</span>
                    <a href="admin/login.php" class="text-gray-500 hover:text-gray-300 transition-colors text-xs" title="Admin Login">
                        <i class="ri-admin-line"></i> Admin
                    </a>
                </div>
            </div>
        </div>
    </footer>

<!-- नेपाली नोट: बाह्य JavaScript फाइल लिंक (main.js) -->
    <script src="lang.js"></script>
    <script src="main.js"></script>
    <script>
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

<script>
// Enhanced Location search modal with filters and better design
let allSearchResults = [];
let filteredResults = [];

document.getElementById('location-search-form').onsubmit = async function(e) {
  e.preventDefault();
  const location = document.getElementById('location-input').value.trim();
  if (!location) return;
  const modal = document.getElementById('search-modal');
  const resultsDiv = document.getElementById('modal-results');
  const filtersDiv = document.getElementById('modal-filters');
  const resultsCount = document.getElementById('results-count');
  
  // Prevent body scroll when modal is open
  document.body.style.overflow = 'hidden';
  
  // Show loading state
  resultsDiv.innerHTML = `
    <div style="text-align:center;padding:60px 20px;">
      <div style="display:inline-block;width:50px;height:50px;border:4px solid #f3f3f3;border-top:4px solid #4A90E2;border-radius:50%;animation:spin 1s linear infinite;margin-bottom:16px;"></div>
      <p style="color:#666;font-size:1.1rem;margin:0;">Searching for rooms in <strong>${location}</strong>...</p>
    </div>
  `;
  modal.style.display = 'flex';
  filtersDiv.style.display = 'none';

  // Fetch rooms from API
  try {
    const res = await fetch('find-rooms.php?api=1&location=' + encodeURIComponent(location));
    const rooms = await res.json();
    allSearchResults = rooms;
    filteredResults = [...rooms];
    
    if (rooms.length === 0) {
      resultsDiv.innerHTML = `
        <div style="text-align:center;padding:60px 20px;">
          <div style="font-size:4rem;color:#ddd;margin-bottom:16px;">
            <i class="ri-search-line"></i>
          </div>
          <h4 style="font-size:1.3rem;color:#333;margin-bottom:8px;">No rooms found</h4>
          <p style="color:#666;margin:0;">We couldn't find any rooms in <strong>${location}</strong>. Try searching for a different location.</p>
        </div>
      `;
      resultsCount.textContent = '0 results found';
    } else {
      filtersDiv.style.display = 'block';
      displaySearchResults(rooms);
      resultsCount.textContent = `${rooms.length} result${rooms.length !== 1 ? 's' : ''} found`;
      
      // Recalculate height after filters are shown
      setTimeout(() => {
        recalculateModalHeight();
      }, 200);
    }
  } catch (err) {
    resultsDiv.innerHTML = `
      <div style="text-align:center;padding:60px 20px;color:#e74c3c;">
        <div style="font-size:3rem;margin-bottom:16px;"><i class="ri-error-warning-line"></i></div>
        <h4 style="font-size:1.2rem;margin-bottom:8px;">Error loading results</h4>
        <p style="color:#666;">Please try again later.</p>
      </div>
    `;
    resultsCount.textContent = 'Error';
  }
};

function displaySearchResults(rooms) {
  const resultsDiv = document.getElementById('modal-results');
  const resultsCount = document.getElementById('results-count');
  
  // Reset scroll position
  resultsDiv.scrollTop = 0;
  
  if (rooms.length === 0) {
    resultsDiv.innerHTML = `
      <div style="text-align:center;padding:40px 20px;">
        <p style="color:#666;">No rooms match your filters. Try adjusting your search criteria.</p>
      </div>
    `;
    resultsCount.textContent = '0 results found';
    return;
  }
  
  resultsCount.textContent = `${rooms.length} result${rooms.length !== 1 ? 's' : ''} found`;
  
  // Force recalculation of height
  setTimeout(() => {
    recalculateModalHeight();
  }, 100);
  
  resultsDiv.innerHTML = rooms.map(room => {
    // Fix: Use image_url instead of image, and handle both cases
    let imageUrl = '';
    if (room.image_url) {
      // If image_url already contains 'uploads/', use as is, otherwise add it
      imageUrl = room.image_url.startsWith('uploads/') ? room.image_url : `uploads/${room.image_url}`;
    } else if (room.image) {
      // Fallback to image field if image_url doesn't exist
      imageUrl = room.image.startsWith('uploads/') ? room.image : `uploads/${room.image}`;
    } else {
      imageUrl = 'https://via.placeholder.com/300x200?text=No+Image';
    }
    
    const verifiedBadge = (room.is_verified == 1 && room.owner_role === 'owner') 
      ? '<i class="ri-checkbox-circle-fill" style="color:#4A90E2;font-size:18px;margin-left:6px;" title="Verified Owner"></i>' 
      : '';
    
    return `
      <div style="background:#fff;border:1px solid #e9ecef;border-radius:12px;padding:16px;margin-bottom:16px;transition:all 0.3s;cursor:pointer;display:flex;gap:16px;box-shadow:0 2px 4px rgba(0,0,0,0.05);" 
           onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.1)';this.style.transform='translateY(-2px)'" 
           onmouseout="this.style.boxShadow='0 2px 4px rgba(0,0,0,0.05)';this.style.transform=''"
           onclick="window.location.href='find-rooms.php?id=${room.id}'">
        <div style="flex-shrink:0;width:220px;height:160px;border-radius:12px;overflow:hidden;background:linear-gradient(135deg, #f0f0f0 0%, #e0e0e0 100%);position:relative;box-shadow:0 4px 12px rgba(0,0,0,0.15);border:2px solid #e9ecef;">
          <img src="${imageUrl}" alt="${room.title || 'Room'}" 
               style="width:100%;height:100%;object-fit:cover;transition:transform 0.4s ease;display:block;cursor:pointer;"
               onerror="this.onerror=null;this.src='https://via.placeholder.com/400x300/4A90E2/ffffff?text=No+Image+Available';this.style.objectFit='contain';this.style.padding='20px';"
               onmouseover="this.style.transform='scale(1.1)'"
               onmouseout="this.style.transform='scale(1)'"
               loading="lazy">
          ${room.price ? `
          <div style="position:absolute;top:8px;right:8px;background:rgba(74,144,226,0.95);color:#fff;padding:6px 12px;border-radius:20px;font-weight:bold;font-size:0.85rem;box-shadow:0 2px 8px rgba(0,0,0,0.2);backdrop-filter:blur(5px);">
            ¥${parseInt(room.price).toLocaleString()}
          </div>
          ` : ''}
        </div>
        <div style="flex:1;display:flex;flex-direction:column;justify-content:space-between;">
          <div>
            <div style="display:flex;align-items:center;margin-bottom:8px;">
              <h4 style="font-size:1.2rem;font-weight:bold;color:#333;margin:0;flex:1;">${room.title || 'Untitled Room'}</h4>
              ${verifiedBadge}
            </div>
            <div style="display:flex;align-items:center;gap:16px;margin-bottom:8px;flex-wrap:wrap;">
              <div style="display:flex;align-items:center;color:#4A90E2;font-size:0.95rem;">
                <i class="ri-map-pin-line" style="margin-right:4px;"></i>
                <span>${room.location || 'Location not specified'}</span>
              </div>
              ${room.train_station ? `
              <div style="display:flex;align-items:center;color:#666;font-size:0.9rem;">
                <i class="ri-train-line" style="margin-right:4px;"></i>
                <span>${room.train_station}</span>
              </div>
              ` : ''}
            </div>
            <div style="display:flex;align-items:center;gap:20px;margin-bottom:12px;flex-wrap:wrap;">
              ${room.type ? `
              <div style="display:flex;align-items:center;color:#666;font-size:0.9rem;background:#f0f7ff;padding:4px 10px;border-radius:6px;">
                <i class="ri-home-line" style="margin-right:4px;color:#4A90E2;"></i>
                <span>${room.type}</span>
              </div>
              ` : ''}
            </div>
            ${room.description ? `
            <p style="color:#666;font-size:0.9rem;margin:0;line-height:1.5;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
              ${room.description}
            </p>
            ` : ''}
          </div>
          <div style="margin-top:12px;display:flex;gap:8px;">
            <a href="find-rooms.php?id=${room.id}" 
               style="padding:8px 16px;background:#4A90E2;color:#fff;border-radius:8px;text-decoration:none;font-size:0.9rem;font-weight:500;transition:all 0.3s;"
               onmouseover="this.style.background='#357ABD'"
               onmouseout="this.style.background='#4A90E2'">
              <i class="ri-eye-line" style="margin-right:4px;"></i>View Details
            </a>
            ${room.user_id ? `
            <a href="messages.php?user_id=${room.user_id}&room_id=${room.id}" 
               style="padding:8px 16px;background:#fff;color:#4A90E2;border:1px solid #4A90E2;border-radius:8px;text-decoration:none;font-size:0.9rem;font-weight:500;transition:all 0.3s;"
               onmouseover="this.style.background='#f0f7ff'"
               onmouseout="this.style.background='#fff'">
              <i class="ri-message-3-line" style="margin-right:4px;"></i>Message
            </a>
            ` : ''}
          </div>
        </div>
      </div>
    `;
  }).join('');
}

// Function to recalculate modal results height
function recalculateModalHeight() {
  const modalBox = document.getElementById('modal-content-box');
  const header = document.getElementById('modal-header');
  const filters = document.getElementById('modal-filters');
  const results = document.getElementById('modal-results');
  
  if (modalBox && header && results) {
    const headerHeight = header.offsetHeight;
    const filtersHeight = filters && filters.style.display !== 'none' ? filters.offsetHeight : 0;
    const availableHeight = modalBox.offsetHeight - headerHeight - filtersHeight;
    results.style.maxHeight = availableHeight + 'px';
    results.style.height = availableHeight + 'px';
  }
}

// Filter and Sort functionality
document.getElementById('sort-filter')?.addEventListener('change', function() {
  applyFilters();
});

document.getElementById('price-filter')?.addEventListener('input', function() {
  applyFilters();
});

document.getElementById('type-filter')?.addEventListener('input', function() {
  applyFilters();
});

// Recalculate on window resize
window.addEventListener('resize', function() {
  const modal = document.getElementById('search-modal');
  if (modal && modal.style.display === 'flex') {
    recalculateModalHeight();
  }
});

function applyFilters() {
  const sortBy = document.getElementById('sort-filter')?.value || 'newest';
  const maxPrice = parseInt(document.getElementById('price-filter')?.value) || Infinity;
  const roomType = document.getElementById('type-filter')?.value.toLowerCase().trim() || '';
  
  filteredResults = allSearchResults.filter(room => {
    const priceMatch = !room.price || parseInt(room.price) <= maxPrice;
    const typeMatch = !roomType || (room.type && room.type.toLowerCase().includes(roomType));
    return priceMatch && typeMatch;
  });
  
  // Sort results
  filteredResults.sort((a, b) => {
    switch(sortBy) {
      case 'price-low':
        return (parseInt(a.price) || 0) - (parseInt(b.price) || 0);
      case 'price-high':
        return (parseInt(b.price) || 0) - (parseInt(a.price) || 0);
      case 'name':
        return (a.title || '').localeCompare(b.title || '');
      case 'newest':
      default:
        return new Date(b.created_at || 0) - new Date(a.created_at || 0);
    }
  });
  
  displaySearchResults(filteredResults);
}

// Close modal function
function closeSearchModal() {
  const modal = document.getElementById('search-modal');
  modal.style.display = 'none';
  document.body.style.overflow = '';
}

// Close modal
document.getElementById('close-modal').onclick = closeSearchModal;

document.getElementById('search-modal').onclick = function(e) {
  if (e.target === this) {
    closeSearchModal();
  }
};

// Close on Escape key
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') {
    const modal = document.getElementById('search-modal');
    if (modal && modal.style.display === 'flex') {
      closeSearchModal();
    }
  }
});

// Add CSS animation for spinner
const style = document.createElement('style');
style.textContent = `
  @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }
`;
document.head.appendChild(style);

// Register as Room Owner notification functions with smooth animations
function showRegisterNotification() {
  const modal = document.getElementById('register-notification-modal');
  if (!modal) return;
  
  // Remove any previous state
  modal.classList.remove('closing');
  modal.style.display = 'flex';
  modal.style.opacity = '0';
  
  // Scroll to top of overlay to ensure modal is visible
  modal.scrollTop = 0;
  
  // Force reflow to ensure display is applied
  void modal.offsetHeight;
  
  // Add show class for animation
  setTimeout(() => {
    modal.classList.add('show');
    // Ensure modal content is visible by scrolling if needed
    const content = modal.querySelector('.register-modal-content');
    if (content) {
      content.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
  }, 10);
  
  // Prevent body scroll when modal is open
  document.body.style.overflow = 'hidden';
  document.body.style.position = 'fixed';
  document.body.style.width = '100%';
}

function closeRegisterNotification() {
  const modal = document.getElementById('register-notification-modal');
  if (!modal) return;
  
  modal.classList.add('closing');
  modal.classList.remove('show');
  
  // Restore body scroll immediately
  document.body.style.overflow = '';
  document.body.style.position = '';
  document.body.style.width = '';
  
  // Wait for animation to complete before hiding
  setTimeout(() => {
    modal.style.display = 'none';
    modal.classList.remove('closing');
    modal.scrollTop = 0; // Reset scroll position
  }, 300);
}

// Close modal when clicking outside
document.getElementById('register-notification-modal').addEventListener('click', function(e) {
  if (e.target === this) {
    closeRegisterNotification();
  }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
  const modal = document.getElementById('register-notification-modal');
  if (e.key === 'Escape' && modal.style.display === 'flex') {
    closeRegisterNotification();
  }
});

// RoomFinder AI Chat - Using server-side API (secure)
async function askGeminiAI(userMessage) {
  if (!userMessage) return "Please enter a message.";
  
  try {
    const res = await fetch("api/ai-chat.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ message: userMessage })
    });
    
    const data = await res.json();
    
    if (data.success && data.response) {
      return data.response;
    } else if (data.error) {
      return "AI Error: " + data.error;
    } else {
      return "Sorry, I couldn't understand. Please try again.";
    }
  } catch (error) {
    console.error("AI Chat Error:", error);
    return "Sorry, AI service is not available right now. Please try again later.";
  }
}

// Show/hide chat window
const chatBtn = document.getElementById('ai-chat-btn');
const chatWin = document.getElementById('ai-chat-window');
const chatClose = document.getElementById('ai-chat-close');
chatBtn.onclick = () => chatWin.style.display = 'flex';
chatClose.onclick = () => chatWin.style.display = 'none';

// Chat logic (Gemini live)
const chatForm = document.getElementById('ai-chat-input-area');
const chatInput = document.getElementById('ai-chat-input');
const chatMessages = document.getElementById('ai-chat-messages');

chatForm.onsubmit = async function(e) {
  e.preventDefault();
  const msg = chatInput.value.trim();
  if (!msg) return;
  // Show user message
  const userDiv = document.createElement('div');
  userDiv.className = 'user-msg';
  userDiv.innerHTML = `<div class="bubble">${msg}</div>`;
  chatMessages.appendChild(userDiv);
  chatMessages.scrollTop = chatMessages.scrollHeight;
  chatInput.value = '';
  chatInput.disabled = true;

  // Show loading
  const aiDiv = document.createElement('div');
  aiDiv.className = 'ai-msg';
  aiDiv.innerHTML = `<div class="bubble">Thinking...</div>`;
  chatMessages.appendChild(aiDiv);
  chatMessages.scrollTop = chatMessages.scrollHeight;

  // Get Gemini AI response
  let aiReply;
  try {
    aiReply = await askGeminiAI(msg);
  } catch (err) {
    aiReply = "Sorry, AI service is not available right now.";
  }
  aiDiv.innerHTML = `<div class="bubble">${aiReply}</div>`;
  chatMessages.scrollTop = chatMessages.scrollHeight;
  chatInput.disabled = false;
  chatInput.focus();
};

// Counter Animation
function animateCounter(element) {
    const target = parseInt(element.getAttribute('data-target'));
    const duration = 2000; // 2 seconds
    const increment = target / (duration / 16); // 60fps
    let current = 0;
    
    const timer = setInterval(() => {
        current += increment;
        if (current >= target) {
            element.textContent = target;
            clearInterval(timer);
        } else {
            element.textContent = Math.floor(current);
        }
    }, 16);
}

// Intersection Observer for animations
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.animation = 'fadeInUp 0.6s ease-out forwards';
            
            // Animate counters if they exist
            const counters = entry.target.querySelectorAll('.counter');
            counters.forEach(counter => {
                if (!counter.classList.contains('animated')) {
                    counter.classList.add('animated');
                    animateCounter(counter);
                }
            });
            
            observer.unobserve(entry.target);
        }
    });
}, observerOptions);

// Observe all fade-in-up elements
document.addEventListener('DOMContentLoaded', () => {
    const fadeElements = document.querySelectorAll('.fade-in-up');
    fadeElements.forEach(el => {
        observer.observe(el);
    });
    
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href !== '#' && href.length > 1) {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }
        });
    });
});
</script>
</body>
</html>