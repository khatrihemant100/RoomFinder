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
                            echo '<span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">' . $unread_count . '</span>';
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
                    // Get user name and profile photo
                    $user_name = $_SESSION["name"] ?? "User";
                    $user_photo = $_SESSION["profile_photo"] ?? null;
                    $user_initial = strtoupper(substr($user_name, 0, 1));
                    
                    // If name is not set or empty, try to get from database
                    if (empty($user_name) || $user_name === "User") {
                        require_once 'db.php';
                        $stmt = $conn->prepare("SELECT name, profile_photo FROM users WHERE id = ?");
                        $stmt->bind_param("i", $_SESSION["user_id"]);
                        $stmt->execute();
                        $stmt->bind_result($dbName, $dbPhoto);
                        if ($stmt->fetch() && !empty(trim($dbName))) {
                            $_SESSION["name"] = trim($dbName);
                            $_SESSION["profile_photo"] = $dbPhoto;
                            $user_name = trim($dbName);
                            $user_photo = $dbPhoto;
                            $user_initial = strtoupper(substr($user_name, 0, 1));
                        }
                        $stmt->close();
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
    <section class="hero-section w-full py-16 md:py-24">
        <div class="container mx-auto px-4">
            <div class="w-full max-w-xl">
                <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 mb-4" data-i18n="find_perfect_room">Find Your Perfect Room</h1>
                <p class="text-lg text-gray-700 mb-8" data-i18n="discover">Discover thousands of rooms and apartments for rent. Whether you're looking to list your property or find your next home, we've got you covered.</p>
                
                <div class="flex flex-col md:flex-row gap-4 mb-8">
                    <a href="find-rooms.php" class="px-6 py-3 bg-primary text-white rounded-button text-center hover:bg-primary/90 transition-colors whitespace-nowrap" data-i18n="find_room_btn">Find a Room</a>
                    
                    <?php if(isset($_SESSION["role"]) && $_SESSION["role"] === 'owner'): ?>
                        <a href="list-property.php" class="px-6 py-3 border border-primary text-primary rounded-button text-center hover:bg-gray-50 transition-colors whitespace-nowrap" data-i18n="list_property_btn">List Your Property</a>
                    <?php else: ?>
                        <button onclick="showRegisterNotification()" class="px-6 py-3 border border-primary text-primary rounded-button text-center hover:bg-gray-50 transition-colors whitespace-nowrap" data-i18n="register_owner">Register as Room Owner</button>
                    <?php endif; ?>
                </div>
                
                <div class="bg-white rounded-lg shadow-lg p-4">
                  <form id="location-search-form" class="flex flex-col md:flex-row gap-4">
                    <div class="flex-grow">
                      <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none w-10 h-10">
                          <i class="ri-map-pin-line text-gray-400"></i>
                        </div>
                        <input type="text" name="location" id="location-input" class="w-full pl-10 pr-4 py-3 border-none rounded bg-gray-50 custom-input" placeholder="Enter location" required>
                      </div>
                    </div>
                    <button type="submit" class="px-6 py-3 bg-primary text-white rounded-button hover:bg-primary/90 transition-colors whitespace-nowrap">Search</button>
                  </form>
                </div>
                
                <!-- Modal/Dialog for search results -->
                <div id="search-modal" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;z-index:9999;background:rgba(0,0,0,0.3);align-items:center;justify-content:center;">
                  <div style="background:#fff;max-width:600px;width:95vw;padding:24px 16px;border-radius:16px;box-shadow:0 8px 32px rgba(0,0,0,0.18);position:relative;">
                    <button id="close-modal" style="position:absolute;top:12px;right:16px;font-size:1.5rem;background:none;border:none;cursor:pointer;">&times;</button>
                    <h3 style="font-size:1.3rem;font-weight:bold;margin-bottom:12px;">Search Results</h3>
                    <div id="modal-results"></div>
                  </div>
                </div>
                
                <!-- Notification Modal for Register as Room Owner -->
                <div id="register-notification-modal" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;z-index:10000;background:rgba(0,0,0,0.5);align-items:center;justify-content:center;">
                  <div style="background:#fff;max-width:500px;width:95vw;padding:32px 24px;border-radius:20px;box-shadow:0 8px 32px rgba(0,0,0,0.25);position:relative;text-align:center;">
                    <button id="close-register-modal" style="position:absolute;top:12px;right:16px;font-size:1.8rem;background:none;border:none;cursor:pointer;color:#666;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:50%;transition:background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">&times;</button>
                    <div style="font-size:3rem;margin-bottom:16px;">🔔</div>
                    <h3 style="font-size:1.5rem;font-weight:bold;margin-bottom:12px;color:#333;">Account Required</h3>
                    <p style="color:#666;margin-bottom:24px;line-height:1.6;">To register as a Room Owner, you need to create an account first. Please create your account to get started with listing your properties.</p>
                    <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
                      <a href="user/createaccount.php?role=owner" class="px-6 py-3 bg-primary text-white rounded-button hover:bg-primary/90 transition-colors whitespace-nowrap" style="text-decoration:none;display:inline-block;">Create Account</a>
                      <button onclick="closeRegisterNotification()" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-button hover:bg-gray-50 transition-colors whitespace-nowrap">Cancel</button>
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
                <div class="bg-white rounded-lg shadow-lg overflow-hidden transition-transform hover:transform hover:scale-105">
                    <div class="h-48 bg-gray-200" style="background-image: url('https://readdy.ai/api/search-image?query=A%20person%20holding%20keys%20to%20a%20new%20apartment%2C%20standing%20in%20a%20doorway%20with%20a%20welcoming%20smile.%20The%20scene%20shows%20a%20well-maintained%20property%20with%20good%20lighting%2C%20clean%20interiors%2C%20and%20a%20sense%20of%20ownership%20and%20pride.%20The%20image%20conveys%20the%20concept%20of%20property%20management%20and%20renting%20out%20spaces.&width=600&height=400&seq=2&orientation=landscape'); background-size: cover; background-position: center;"></div>
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
                
                <div class="bg-white rounded-lg shadow-lg overflow-hidden transition-transform hover:transform hover:scale-105">
                    <div class="h-48 bg-gray-200" style="background-image: url('https://readdy.ai/api/search-image?query=A%20person%20looking%20at%20apartment%20listings%20on%20a%20smartphone%20or%20tablet%2C%20sitting%20in%20a%20coffee%20shop%20or%20comfortable%20space.%20The%20individual%20appears%20focused%20and%20engaged%20in%20searching%20for%20accommodation.%20The%20scene%20conveys%20the%20modern%20approach%20to%20finding%20housing%20through%20digital%20platforms.&width=600&height=400&seq=3&orientation=landscape'); background-size: cover; background-position: center;"></div>
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
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <div class="w-12 h-12 flex items-center justify-center bg-primary/10 text-primary rounded-full mb-4">
                        <i class="ri-map-pin-line text-xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Interactive Map Search</h3>
                    <p class="text-gray-600">Find rooms in your preferred location with our interactive map interface powered by Google Maps.</p>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <div class="w-12 h-12 flex items-center justify-center bg-primary/10 text-primary rounded-full mb-4">
                        <i class="ri-message-3-line text-xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">In-App Messaging</h3>
                    <p class="text-gray-600">Communicate directly with property owners or potential tenants through our secure messaging system.</p>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <div class="w-12 h-12 flex items-center justify-center bg-primary/10 text-primary rounded-full mb-4">
                        <i class="ri-ai-generate text-xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">AI Recommendations</h3>
                    <p class="text-gray-600">Get personalized room suggestions based on your preferences and previous searches.</p>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <div class="w-12 h-12 flex items-center justify-center bg-primary/10 text-primary rounded-full mb-4">
                        <i class="ri-shield-check-line text-xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Verified Owners</h3>
                    <p class="text-gray-600">Look for the verification badge to ensure you're dealing with trusted property owners.</p>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <div class="w-12 h-12 flex items-center justify-center bg-primary/10 text-primary rounded-full mb-4">
                        <i class="ri-translate-2 text-xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Multilingual Support</h3>
                    <p class="text-gray-600">Use our platform in multiple languages including English, Nepali, and Japanese.</p>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <div class="w-12 h-12 flex items-center justify-center bg-primary/10 text-primary rounded-full mb-4">
                        <i class="ri-calculator-line text-xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Rent Calculator</h3>
                    <p class="text-gray-600">Calculate total costs including utilities and additional fees for better financial planning.</p>
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
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <h3 class="text-xl font-bold mb-4 text-center">For Room Seekers</h3>
                    
                    <div class="space-y-6">
                        <div class="flex">
                            <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center bg-primary text-white rounded-full font-bold">1</div>
                            <div class="ml-4">
                                <h4 class="font-semibold">Create an account</h4>
                                <p class="text-gray-600 text-sm mt-1">Sign up and complete your profile with your preferences.</p>
                            </div>
                        </div>
                        
                        <div class="flex">
                            <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center bg-primary text-white rounded-full font-bold">2</div>
                            <div class="ml-4">
                                <h4 class="font-semibold">Search for rooms</h4>
                                <p class="text-gray-600 text-sm mt-1">Use filters to find properties that match your requirements.</p>
                            </div>
                        </div>
                        
                        <div class="flex">
                            <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center bg-primary text-white rounded-full font-bold">3</div>
                            <div class="ml-4">
                                <h4 class="font-semibold">Contact owners</h4>
                                <p class="text-gray-600 text-sm mt-1">Message property owners directly through our platform.</p>
                            </div>
                        </div>
                        
                        <div class="flex">
                            <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center bg-primary text-white rounded-full font-bold">4</div>
                            <div class="ml-4">
                                <h4 class="font-semibold">Schedule viewings</h4>
                                <p class="text-gray-600 text-sm mt-1">Arrange to see the property and finalize your rental agreement.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- For Room Owners -->
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <h3 class="text-xl font-bold mb-4 text-center">For Room Owners</h3>
                    
                    <div class="space-y-6">
                        <div class="flex">
                            <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center bg-primary text-white rounded-full font-bold">1</div>
                            <div class="ml-4">
                                <h4 class="font-semibold">Create an account</h4>
                                <p class="text-gray-600 text-sm mt-1">Sign up as a property owner and verify your identity.</p>
                            </div>
                        </div>
                        
                        <div class="flex">
                            <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center bg-primary text-white rounded-full font-bold">2</div>
                            <div class="ml-4">
                                <h4 class="font-semibold">List your property</h4>
                                <p class="text-gray-600 text-sm mt-1">Add details, photos, and set your rental terms.</p>
                            </div>
                        </div>
                        
                        <div class="flex">
                            <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center bg-primary text-white rounded-full font-bold">3</div>
                            <div class="ml-4">
                                <h4 class="font-semibold">Manage inquiries</h4>
                                <p class="text-gray-600 text-sm mt-1">Respond to messages and arrange viewings with interested renters.</p>
                            </div>
                        </div>
                        
                        <div class="flex">
                            <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center bg-primary text-white rounded-full font-bold">4</div>
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

    

    
    <!-- नेपाली नोट: CTA सेसन (Call to Action) -->
    <section class="py-16 bg-primary">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold text-white mb-4">Ready to Find Your Perfect Room?</h2>
            <p class="text-white/80 max-w-2xl mx-auto mb-8">Join thousands of satisfied users who have found their ideal living situation through RoomFinder.</p>
            
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="find-rooms.php" class="px-6 py-3 bg-white text-primary rounded-button hover:bg-gray-100 transition-colors whitespace-nowrap">Find a Room</a>
                <?php if(isset($_SESSION["role"]) && $_SESSION["role"] === 'owner'): ?>
                <a href="list-property.php" class="px-6 py-3 border border-white text-white rounded-button hover:bg-primary/90 transition-colors whitespace-nowrap">List Your Property</a>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- नेपाली नोट: Footer (पाद लेख) -->
    <footer class="bg-gray-900 text-white pt-12 pb-6">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-4 gap-8 mb-8">
                <div>
                    <a href="#" class="text-2xl font-['Pacifico'] text-white mb-4 inline-block">RoomFinder</a>
                    <p class="text-gray-400 mb-4">Find your perfect room or tenant with our easy-to-use platform.</p>
                    <div class="flex space-x-4">
                        <a href="#" class="w-10 h-10 flex items-center justify-center bg-gray-800 rounded-full hover:bg-primary transition-colors">
                            <i class="ri-facebook-fill"></i>
                        </a>
                        <a href="#" class="w-10 h-10 flex items-center justify-center bg-gray-800 rounded-full hover:bg-primary transition-colors">
                            <i class="ri-twitter-x-fill"></i>
                        </a>
                        <a href="#" class="w-10 h-10 flex items-center justify-center bg-gray-800 rounded-full hover:bg-primary transition-colors">
                            <i class="ri-instagram-fill"></i>
                        </a>
                        <a href="#" class="w-10 h-10 flex items-center justify-center bg-gray-800 rounded-full hover:bg-primary transition-colors">
                            <i class="ri-linkedin-fill"></i>
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
                <div class="flex space-x-4">
                    <a href="#" class="text-gray-400 hover:text-white transition-colors text-sm">Terms</a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors text-sm">Privacy</a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors text-sm">Cookies</a>
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
// Location search modal (as before)
document.getElementById('location-search-form').onsubmit = async function(e) {
  e.preventDefault();
  const location = document.getElementById('location-input').value.trim();
  if (!location) return;
  const modal = document.getElementById('search-modal');
  const resultsDiv = document.getElementById('modal-results');
  resultsDiv.innerHTML = '<div style="text-align:center;padding:24px;">Searching...</div>';
  modal.style.display = 'flex';

  // Fetch rooms from API
  try {
    const res = await fetch('find-rooms.php?api=1&location=' + encodeURIComponent(location));
    const rooms = await res.json();
    if (rooms.length === 0) {
      resultsDiv.innerHTML = '<div style="text-align:center;padding:24px;">No rooms found for <b>' + location + '</b>.</div>';
    } else {
      resultsDiv.innerHTML = rooms.map(room => `
        <div style="border-bottom:1px solid #eee;padding:12px 0;display:flex;gap:12px;align-items:flex-start;">
          <img src="${room.image ? room.image : 'no-image.png'}" alt="Room Image" style="width:90px;height:70px;object-fit:cover;border-radius:8px;">
          <div style="flex:1;">
            <div style="font-weight:bold;font-size:1.1rem;">${room.title || ''}</div>
            <div style="color:#4A90E2;">${room.location || ''}</div>
            <div>Type: ${room.type || ''}</div>
            <div>Rent: ${room.price ? '¥' + room.price : ''}</div>
            <div style="margin-top:4px;">
              <a href="find-rooms.php?id=${room.id}" style="color:#4A90E2;text-decoration:underline;">View Details</a>
            </div>
          </div>
        </div>
      `).join('');
    }
  } catch (err) {
    resultsDiv.innerHTML = '<div style="color:red;">Error fetching rooms.</div>';
  }
};
document.getElementById('close-modal').onclick = function() {
  document.getElementById('search-modal').style.display = 'none';
};
document.getElementById('search-modal').onclick = function(e) {
  if (e.target === this) this.style.display = 'none';
};

// Register as Room Owner notification functions
function showRegisterNotification() {
  const modal = document.getElementById('register-notification-modal');
  modal.style.display = 'flex';
}

function closeRegisterNotification() {
  const modal = document.getElementById('register-notification-modal');
  modal.style.display = 'none';
}

document.getElementById('close-register-modal').onclick = closeRegisterNotification;
document.getElementById('register-notification-modal').onclick = function(e) {
  if (e.target === this) closeRegisterNotification();
};

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
</script>
</body>
</html>