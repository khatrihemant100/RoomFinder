<?php
session_start();
require_once 'db.php';

// API fetch for search (optional)
if (isset($_GET['api']) && $_GET['api'] == '1') {
    $location = isset($_GET['location']) ? trim($_GET['location']) : '';
    $rooms = [];
    if ($location !== '') {
        $stmt = $conn->prepare("SELECT p.*, u.is_verified, u.name as owner_name, u.role as owner_role 
                                FROM properties p 
                                LEFT JOIN users u ON p.user_id = u.id 
                                WHERE p.location LIKE ? 
                                ORDER BY p.created_at DESC");
        $like = '%' . $location . '%';
        $stmt->bind_param("s", $like);
        $stmt->execute();
        $res = $stmt->get_result();
        while($row = $res->fetch_assoc()) $rooms[] = $row;
        $stmt->close();
    }
    header('Content-Type: application/json');
    echo json_encode($rooms);
    exit;
}

// Fetch all rooms with owner verification status
$rooms = [];
$query = "SELECT p.*, u.is_verified, u.name as owner_name, u.role as owner_role 
          FROM properties p 
          LEFT JOIN users u ON p.user_id = u.id 
          ORDER BY p.created_at DESC";
$res = $conn->query($query);
while($row = $res->fetch_assoc()) $rooms[] = $row;
?>
<script>
  const dbRooms = <?php echo json_encode($rooms); ?>;
  const currentUserId = <?php echo isset($_SESSION["user_id"]) ? intval($_SESSION["user_id"]) : 'null'; ?>;
</script>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Find Rooms - RoomFinder</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<script src="https://cdn.tailwindcss.com/3.4.16"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAYoOAIrd7-WYQZzdYbsAjAatGEkKyB6oA&libraries=places"></script>
<style>
body { background: #fff !important; font-family: 'Inter', sans-serif; color: #333; min-height:100vh; }
.container { max-width:1200px; margin:0 auto; }
.rf-header { background:#fff; box-shadow:0 2px 8px rgba(74,144,226,0.08); padding:18px 0; margin-bottom:30px; }
.rf-header .container { display:flex; align-items:center; justify-content:space-between; }
.rf-header a.logo { font-family:'Pacifico',cursive; font-size:2rem; color:#4A90E2; text-decoration:none; }
.rf-header .nav { display:flex; gap:28px; }
.rf-header .nav a { color:#333; font-weight:500; text-decoration:none; transition:color 0.2s; }
.rf-header .nav a:hover { color:#4A90E2; }
.rf-header .user-area { display:flex; align-items:center; gap:12px; }
.rf-header .user-area span { color:#4A90E2; font-weight:600; background:#eaf4fb; padding:6px 16px; border-radius:8px; }
.rf-header .user-area a { background:#FF6B6B; color:#fff; padding:8px 18px; border-radius:8px; font-weight:500; text-decoration:none; transition:background 0.2s; }
.rf-header .user-area a:hover { background:#e74c3c; }
main { background:#fff; border-radius:12px; padding:30px; box-shadow:0 10px 30px rgba(0,0,0,0.08); margin-bottom:30px; }
.results { margin-top:30px; display:grid; grid-template-columns:repeat(auto-fill,minmax(280px,1fr)); gap:25px; }
.card { background:white; border-radius:12px; overflow:hidden; box-shadow:0 5px 15px rgba(0,0,0,0.1); transition: transform 0.3s ease, box-shadow 0.3s ease; display:flex; flex-direction:column; }
.card:hover { transform:translateY(-10px); box-shadow:0 15px 30px rgba(0,0,0,0.15); }
.image { height:200px; background-size:cover; background-position:center; position:relative; }
.rent-badge { position:absolute; top:15px; right:15px; background:linear-gradient(to right,#e74c3c,#c0392b); color:white; padding:8px 15px; border-radius:20px; font-weight:600; font-size:1.1rem; box-shadow:0 4px 10px rgba(0,0,0,0.2); }

/* ==== Room Status Badge ==== */
.status-badge {
  position:absolute;
  top:15px;
  left:15px;
  padding:6px 12px;
  border-radius:20px;
  color:white;
  font-weight:600;
  font-size:0.9rem;
  box-shadow:0 3px 6px rgba(0,0,0,0.2);
}
.status-Available { background:#2ecc71; }      /* Green */
.status-Not_available { background:#e74c3c; }  /* Red */
.status-Under_Maintenance { background:#f39c12; }    /* Orange */
.status-Reserved { background:#3498db; }       /* Blue */
.status- { background:#7f8c8d; }        /* Grey */

.card-content { padding:20px; flex:1; display:flex; flex-direction:column; }
.card h4 { color:#2c3e50; margin-bottom:10px; font-size:1.3rem; }
.card p { color:#7f8c8d; margin:8px 0; display:flex; align-items:center; }
.card p i { margin-right:10px; color:#3498db; width:20px; text-align:center; }
.details-btn { display:block; width:100%; text-align:center; background:#3498db; margin-top:15px; padding:10px; font-size:1rem; color:#fff; border:none; border-radius:8px; cursor:pointer; transition:background 0.2s; }
.details-btn:hover { background:#217dbb; }

/* Modal Styles - Enhanced Room Details Modal */
.modal { 
  display:none; 
  position:fixed; 
  z-index:9999; 
  left:0; 
  top:0; 
  width:100%; 
  height:100%; 
  overflow-y:auto; 
  overflow-x:hidden;
  background-color:rgba(0,0,0,0.7);
  backdrop-filter: blur(5px);
  padding: 20px;
  box-sizing: border-box;
}

.modal-content { 
  background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
  margin: 40px auto;
  padding: 0;
  border-radius: 24px;
  width: 90%;
  max-width: 800px;
  position: relative;
  box-shadow: 0 25px 60px rgba(0,0,0,0.3);
  animation: modalSlideIn 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
  overflow: hidden;
  border: 1px solid rgba(74, 144, 226, 0.1);
}

@keyframes modalSlideIn {
  0% {
    opacity: 0;
    transform: scale(0.8) translateY(-50px);
  }
  100% {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
}

.close-btn { 
  position:absolute; 
  top:20px; 
  right:20px; 
  font-size:32px; 
  cursor:pointer; 
  color:#666;
  background: rgba(255, 255, 255, 0.9);
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
  z-index: 10;
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.close-btn:hover { 
  color:#e74c3c;
  background: #fff;
  transform: rotate(90deg) scale(1.1);
  box-shadow: 0 6px 20px rgba(231, 76, 60, 0.3);
}

/* Room Detail Modal Specific Styles */
#roomModal .modal-content {
  max-width: 900px;
}

.room-detail-header {
  position: relative;
  height: 350px;
  overflow: hidden;
  background: linear-gradient(135deg, #4A90E2 0%, #357ABD 100%);
}

.room-detail-header::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(to bottom, rgba(0,0,0,0.1), rgba(0,0,0,0.4));
  z-index: 1;
}

#modalImage {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

.room-detail-body {
  padding: 40px;
  position: relative;
}

.room-detail-title {
  font-size: 2.5rem;
  font-weight: 700;
  color: #1f2937;
  margin-bottom: 20px;
  font-family: 'Pacifico', cursive;
  background: linear-gradient(135deg, #4A90E2 0%, #357ABD 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.room-detail-info-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 20px;
  margin-bottom: 30px;
}

.room-info-card {
  background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
  padding: 20px;
  border-radius: 16px;
  border: 2px solid #e5e7eb;
  transition: all 0.3s ease;
  box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}

.room-info-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 24px rgba(74, 144, 226, 0.15);
  border-color: #4A90E2;
}

.room-info-card .info-label {
  font-size: 0.85rem;
  color: #6b7280;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 8px;
  display: flex;
  align-items: center;
  gap: 8px;
}

.room-info-card .info-value {
  font-size: 1.3rem;
  font-weight: 700;
  color: #1f2937;
  display: flex;
  align-items: center;
  gap: 8px;
}

.room-info-card .info-value i {
  color: #4A90E2;
  font-size: 1.2rem;
}

.room-detail-description {
  background: #f9fafb;
  padding: 25px;
  border-radius: 16px;
  border-left: 4px solid #4A90E2;
  margin-bottom: 30px;
  line-height: 1.8;
  color: #374151;
  font-size: 1.05rem;
}

.room-detail-actions {
  display: flex;
  gap: 15px;
  flex-wrap: wrap;
  margin-top: 30px;
  padding-top: 30px;
  border-top: 2px solid #e5e7eb;
}

.action-btn {
  flex: 1;
  min-width: 150px;
  padding: 16px 24px;
  border: none;
  border-radius: 12px;
  font-size: 1.1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  text-decoration: none;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.action-btn-primary {
  background: linear-gradient(135deg, #4A90E2 0%, #357ABD 100%);
  color: white;
}

.action-btn-primary:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 24px rgba(74, 144, 226, 0.4);
}

.action-btn-success {
  background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
  color: white;
}

.action-btn-success:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 24px rgba(46, 204, 113, 0.4);
}

.action-btn-secondary {
  background: white;
  color: #4A90E2;
  border: 2px solid #4A90E2;
}

.action-btn-secondary:hover {
  background: #4A90E2;
  color: white;
  transform: translateY(-3px);
}

.owner-badge {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 10px 18px;
  background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
  color: white;
  border-radius: 25px;
  font-weight: 600;
  font-size: 0.95rem;
  box-shadow: 0 4px 12px rgba(46, 204, 113, 0.3);
  margin-bottom: 20px;
}

.status-badge-modal {
  display: inline-block;
  padding: 8px 16px;
  border-radius: 20px;
  font-weight: 600;
  font-size: 0.9rem;
  margin-left: 10px;
}

.status-Available { 
  background: #2ecc71; 
  color: white;
}

.status-Not_available { 
  background: #e74c3c; 
  color: white;
}

.status-Under_Maintenance { 
  background: #f39c12; 
  color: white;
}

.status-Reserved { 
  background: #3498db; 
  color: white;
}

@media (max-width: 768px) {
  .modal-content {
    width: 95%;
    margin: 20px auto;
  }
  
  .room-detail-header {
    height: 250px;
  }
  
  .room-detail-body {
    padding: 25px;
  }
  
  .room-detail-title {
    font-size: 1.8rem;
  }
  
  .room-detail-info-grid {
    grid-template-columns: 1fr;
  }
  
  .room-detail-actions {
    flex-direction: column;
  }
  
  .action-btn {
    width: 100%;
  }
}
/* Inquiry Modal Enhancement with Animations */
#inquiryModal {
  animation: fadeIn 0.3s ease;
}

#inquiryModal .modal-content {
  background: #fff;
  color: #333;
  border: 1.5px solid #e5e7eb;
  animation: slideDown 0.4s ease;
  box-shadow: 0 8px 32px 0 rgba(74, 144, 226, 0.10);
  max-height: 95vh;
  overflow-y: auto;
  display: flex;
  flex-direction: column;
  padding: 0;
  width: 90%;
  max-width: 900px;
  border-radius: 18px;
}

@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-50px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

@keyframes shake {
  0%, 100% { transform: translateX(0); }
  25% { transform: translateX(-10px); }
  75% { transform: translateX(10px); }
}

#inquiryModal h3 {
  text-align: center;
  font-size: 2rem;
  margin-bottom: 10px;
  font-family: 'Pacifico', cursive;
  color: #4A90E2;
  animation: fadeIn 0.6s ease;
}

#inquiryModal input,
#inquiryModal textarea {
  background: #f9f9f9;
  border: 1.5px solid #4A90E2;
  color: #333;
  transition: border 0.2s;
  border-radius: 8px;
}

#inquiryModal input:focus,
#inquiryModal textarea:focus {
  outline: none;
  border-color: #FF6B6B;
  background: #fff;
}

#inquiryModal input:hover,
#inquiryModal textarea:hover {
  border-color: #4A90E2;
}

#inquiryModal label {
  transition: all 0.3s ease;
}

#inquiryModal label:hover {
  transform: translateX(5px);
}

#inquiryModal button[type="submit"] {
  background: #4A90E2;
  color: #fff;
  font-weight: bold;
  font-size: 1.1rem;
  transition: background 0.3s;
  border: none;
  border-radius: 8px;
  cursor: pointer;
}

#inquiryModal button[type="submit"]:hover {
  background: #357ABD;
}

#inquiryModal button[type="submit"]:disabled {
  opacity: 0.7;
  cursor: not-allowed;
  transform: none;
}

#inquirySuccess {
  animation: slideInSuccess 0.5s ease;
}

@keyframes slideInSuccess {
  from {
    opacity: 0;
    transform: translateX(-20px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

#inquiryError {
  animation: shake 0.5s ease;
}

.close-btn {
  transition: all 0.3s ease;
}

.close-btn:hover {
  transform: rotate(90deg) scale(1.2);
  color: #333;
}

/* Responsive design for inquiry form */
@media (max-width: 768px) {
  #inquiryModal .modal-content {
    width: 95%;
    max-width: 100%;
  }
  
  #inquiryForm > div[style*="display:grid"] {
    display: block !important;
  }
  
  #inquiryForm > div[style*="display:grid"] > div {
    margin-bottom: 18px;
  }
}

</style>
</head>
<body>
<header class="rf-header">
<div class="container">
  <a href="index.php" class="logo">RoomFinder</a>
            <nav class="nav">
                <a href="index.php">Home</a>
                <a href="find-rooms.php">Find Rooms</a>
                <?php if(isset($_SESSION["role"]) && $_SESSION["role"] === 'owner'): ?>
                <a href="list-property.php">List Property</a>
                <?php endif; ?>
                <a href="about.php">About Us</a>
                <a href="contact.php">Contact</a>
                <?php if(isset($_SESSION["user_id"])): ?>
                <a href="messages.php" class="relative">
                    Messages
                    <?php
                    // Get unread message count
                    require_once 'db.php';
                    $unreadStmt = $conn->prepare("SELECT COUNT(*) FROM messages WHERE receiver_id = ? AND is_read = 0");
                    $unreadStmt->bind_param("i", $_SESSION["user_id"]);
                    $unreadStmt->execute();
                    $unreadStmt->bind_result($unread_count);
                    $unreadStmt->fetch();
                    $unreadStmt->close();
                    if ($unread_count > 0) {
                        echo '<span style="position:absolute;top:0;right:0;background:#FF6B6B;color:white;border-radius:50%;width:20px;height:20px;min-width:20px;min-height:20px;display:inline-flex;align-items:center;justify-content:center;font-size:11px;font-weight:bold;line-height:20px;transform:translate(50%,-50%);">' . $unread_count . '</span>';
                    }
                    ?>
                </a>
                <?php endif; ?>
            </nav>
  <div class="user-area" style="position:relative;">
    <?php if(isset($_SESSION["user_id"])): ?>
      <?php
      $user_name = $_SESSION["name"] ?? "User";
      $user_photo = $_SESSION["profile_photo"] ?? null;
      $user_initial = strtoupper(substr($user_name, 0, 1));
      ?>
      <button onclick="toggleUserDropdown()" style="display:flex;align-items:center;gap:8px;padding:8px 12px;border:none;background:transparent;cursor:pointer;border-radius:8px;" onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='transparent'">
        <div style="width:40px;height:40px;border-radius:50%;<?php echo $user_photo ? '' : 'background:#10b981;'; ?>display:flex;align-items:center;justify-content:center;overflow:hidden;">
          <?php if ($user_photo): ?>
            <img src="uploads/<?php echo htmlspecialchars($user_photo); ?>" alt="<?php echo htmlspecialchars($user_name); ?>" style="width:100%;height:100%;object-fit:cover;">
          <?php else: ?>
            <span style="color:white;font-weight:bold;font-size:18px;"><?php echo $user_initial; ?></span>
          <?php endif; ?>
        </div>
        <span style="font-weight:600;color:#374151;"><?php echo htmlspecialchars(strtoupper($user_name)); ?></span>
        <i class="ri-arrow-down-s-line" style="color:#6b7280;"></i>
      </button>
      <div id="userDropdown" style="display:none;position:absolute;right:0;top:100%;margin-top:8px;width:192px;background:white;border-radius:8px;box-shadow:0 4px 6px rgba(0,0,0,0.1);border:1px solid #e5e7eb;padding:8px 0;z-index:50;">
        <a href="index.php" style="display:flex;align-items:center;gap:12px;padding:8px 16px;color:#374151;text-decoration:none;" onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='transparent'">
          <i class="ri-dashboard-line" style="color:#6b7280;"></i>
          <span>Dashboard</span>
        </a>
        <a href="user/profile.php" style="display:flex;align-items:center;gap:12px;padding:8px 16px;color:#374151;text-decoration:none;" onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='transparent'">
          <i class="ri-user-line" style="color:#6b7280;"></i>
          <span>Profile</span>
        </a>
        <hr style="margin:8px 0;border:none;border-top:1px solid #e5e7eb;">
        <a href="user/logout.php" style="display:flex;align-items:center;gap:12px;padding:8px 16px;color:#ef4444;text-decoration:none;" onmouseover="this.style.background='#fef2f2'" onmouseout="this.style.background='transparent'">
          <i class="ri-logout-box-r-line" style="color:#ef4444;"></i>
          <span>Logout</span>
        </a>
      </div>
    <?php else: ?>
      <a href="user/login.php" style="background:#4A90E2;">Sign In</a>
      <a href="user/createaccount.php">Sign Up</a>
    <?php endif; ?>
  </div>
</div>
</header>

<div class="container">
<main>
<h2 style="font-family:'Pacifico',cursive;color:#4A90E2;text-align:center;font-size:2rem;margin-bottom:20px;">Find the Perfect Room</h2>

<form id="searchForm" class="bg-white shadow-lg rounded-xl p-6 mb-10 border border-gray-100 max-w-2xl mx-auto">
  <div class="grid md:grid-cols-3 gap-6">
    <div class="form-group">
      <label class="block mb-2 font-medium text-gray-600"><i class="fas fa-yen-sign text-blue-500 mr-1"></i> Rent</label>
      <input type="number" placeholder="¥" id="rent" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 outline-none" />
    </div>
    <div class="form-group">
      <label class="block mb-2 font-medium text-gray-600"><i class="fas fa-map-marker-alt text-red-500 mr-1"></i> Location</label>
      <input type="text" id="station" placeholder="e.g. Tokyo" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 outline-none" />
    </div>
    <div class="form-group">
      <label class="block mb-2 font-medium text-gray-600"><i class="fas fa-home text-green-500 mr-1"></i> Room Type</label>
      <input type="text" id="roomType" placeholder="e.g. Single, Flat" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 outline-none" />
    </div>
  </div>
  <div class="text-center mt-6">
    <button type="submit" id="search-btn" class="bg-blue-500 hover:bg-blue-600 text-white px-8 py-2.5 rounded-lg shadow font-medium transition"><i class="fas fa-search"></i> Search</button>
  </div>
</form>

<div class="flex items-center justify-between mb-4">
  <h3 id="results-title" class="text-xl font-semibold">Search Results</h3>
  <button id="toggleMapView" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
    <i class="ri-map-line mr-2"></i>View on Map
  </button>
</div>

<!-- Map Container -->
<div id="mapContainer" style="display:none;height:500px;margin-bottom:30px;border-radius:12px;overflow:hidden;box-shadow:0 4px 12px rgba(0,0,0,0.1);">
  <div id="map" style="width:100%;height:100%;"></div>
</div>

<div id="results" class="results"></div>
</main>

<!-- Inquiry Modal -->
<div id="inquiryModal" class="modal">
  <div class="modal-content">
    <div style="position:sticky;top:0;background:#fff;z-index:10;padding:30px 30px 20px 30px;border-bottom:1.5px solid #e5e7eb;">
      <span id="inquiryModalClose" class="close-btn" style="color:#aaa;">&times;</span>
      <h3 style="color:#4A90E2;font-family:'Pacifico',cursive;">📧 Room Inquiry Form</h3>
      <p style="text-align:center;color:#555;margin-top:10px;font-size:0.95rem;">Fill out the form below to contact the property owner</p>
    </div>
    <form id="inquiryForm" style="flex:1;padding:20px 30px 30px 30px;">
      <input type="hidden" id="inquiryRoomId" name="room_id" />
      <input type="hidden" id="inquiryRoomTitle" name="room_title" />
      
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:18px;">
        <div>
          <label style="display:block;margin-bottom:8px;font-weight:600;color:#333;font-size:0.95rem;">
            <i class="fas fa-user" style="color:#4A90E2;margin-right:8px;"></i> Your Name *
          </label>
          <input type="text" id="inqName" name="name" required 
                 style="width:100%;padding:12px;border-radius:8px;border:1.5px solid #4A90E2;background:#f9f9f9;color:#333;font-size:1rem;box-sizing:border-box;margin-bottom:0;" 
                 placeholder="Enter your full name" />
        </div>
        
        <div>
          <label style="display:block;margin-bottom:8px;font-weight:600;color:#333;font-size:0.95rem;">
            <i class="fas fa-envelope" style="color:#4A90E2;margin-right:8px;"></i> Email Address *
          </label>
          <input type="email" id="inqEmail" name="email" required 
                 style="width:100%;padding:12px;border-radius:8px;border:1.5px solid #4A90E2;background:#f9f9f9;color:#333;font-size:1rem;box-sizing:border-box;margin-bottom:0;" 
                 placeholder="your.email@example.com" />
        </div>
      </div>
      
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:18px;">
        <div>
          <label style="display:block;margin-bottom:8px;font-weight:600;color:#333;font-size:0.95rem;">
            <i class="fas fa-phone" style="color:#4A90E2;margin-right:8px;"></i> Phone Number *
          </label>
          <input type="tel" id="inqPhone" name="phone" required 
                 style="width:100%;padding:12px;border-radius:8px;border:1.5px solid #4A90E2;background:#f9f9f9;color:#333;font-size:1rem;box-sizing:border-box;margin-bottom:0;" 
                 placeholder="+81-XX-XXXX-XXXX" />
        </div>
        
        <div>
          <label style="display:block;margin-bottom:8px;font-weight:600;color:#333;font-size:0.95rem;">
            <i class="fas fa-calendar-alt" style="color:#4A90E2;margin-right:8px;"></i> Preferred Visit Date *
          </label>
          <input type="date" id="inqDate" name="visit_date" required 
                 style="width:100%;padding:12px;border-radius:8px;border:1.5px solid #4A90E2;background:#f9f9f9;color:#333;font-size:1rem;box-sizing:border-box;margin-bottom:0;" 
                 min="<?php echo date('Y-m-d'); ?>" />
          <small style="color:#666;font-size:0.85rem;display:block;margin-top:5px;">Select your preferred date</small>
        </div>
      </div>
      
      <div style="margin-bottom:18px;">
        <label style="display:block;margin-bottom:8px;font-weight:600;color:#333;font-size:0.95rem;">
          <i class="fas fa-comment-alt" style="color:#4A90E2;margin-right:8px;"></i> Additional Message
        </label>
        <textarea id="inqMessage" name="message" rows="5" 
                  style="width:100%;padding:12px;border-radius:8px;border:1.5px solid #4A90E2;background:#f9f9f9;color:#333;font-size:1rem;resize:vertical;box-sizing:border-box;margin-bottom:0;" 
                  placeholder="Tell the owner about your interest, questions, or special requirements..."></textarea>
        <small style="color:#666;font-size:0.85rem;display:block;margin-top:5px;">Optional: Add any additional information or questions</small>
      </div>
      
      <div id="inquirySuccess" style="display:none;color:#4f8a10;background:#dff2bf;padding:10px;border-radius:6px;margin-bottom:12px;text-align:center;">
        <i class="fas fa-check-circle" style="color:#4f8a10;margin-right:8px;"></i> <strong>Success!</strong> <span id="successMessage">Your inquiry has been submitted.</span>
      </div>
      
      <div id="inquiryError" style="display:none;color:#d8000c;background:#ffd2d2;padding:10px;border-radius:6px;margin-bottom:12px;text-align:center;">
        <i class="fas fa-exclamation-circle" style="color:#d8000c;margin-right:8px;"></i> <strong>Error:</strong> <span id="errorMessage"></span>
      </div>
      
      <button type="submit" id="inquirySubmitBtn" 
              style="width:100%;padding:12px;background:#4A90E2;color:#fff;border:none;border-radius:8px;font-size:1.1rem;cursor:pointer;transition:background 0.3s, transform 0.2s;font-weight:bold;margin-top:10px;"
              onmouseover="this.style.transform='scale(1.05)';this.style.boxShadow='0 6px 22px 0 rgba(74,144,226,0.10)';"
              onmouseout="this.style.transform='';this.style.boxShadow='';"
      >
        <i class="fas fa-paper-plane"></i> Send Inquiry
      </button>
      
      <p style="text-align:center;margin-top:15px;color:#555;font-size:0.9rem;">
        <i class="fas fa-info-circle" style="margin-right:5px;color:#4A90E2;"></i> The property owner will receive your inquiry via email
      </p>
    </form>
  </div>
</div>

<!-- Room Detail Modal -->
<div id="roomModal" class="modal">
  <div class="modal-content">
    <span id="modalClose" class="close-btn">&times;</span>
    
    <!-- Header with Image -->
    <div class="room-detail-header">
      <img id="modalImage" src="" alt="Room Image" />
    </div>
    
    <!-- Body Content -->
    <div class="room-detail-body">
      <h2 class="room-detail-title" id="modalTitle"></h2>
      
      <div id="modalOwner"></div>
      
      <!-- Info Grid -->
      <div class="room-detail-info-grid">
        <div class="room-info-card">
          <div class="info-label">
            <i class="fas fa-yen-sign"></i> Monthly Rent
          </div>
          <div class="info-value" id="modalRent"></div>
        </div>
        
        <div class="room-info-card">
          <div class="info-label">
            <i class="fas fa-map-marker-alt"></i> Location
          </div>
          <div class="info-value">
            <i class="fas fa-map-marker-alt"></i>
            <span id="modalStation"></span>
          </div>
        </div>
        
        <div class="room-info-card">
          <div class="info-label">
            <i class="fas fa-home"></i> Room Type
          </div>
          <div class="info-value">
            <i class="fas fa-home"></i>
            <span id="modalType"></span>
          </div>
        </div>
        
        <div class="room-info-card">
          <div class="info-label">
            <i class="fas fa-train"></i> Train Station
          </div>
          <div class="info-value">
            <i class="fas fa-train"></i>
            <span id="modalTrainStation"></span>
          </div>
        </div>
        
        <div class="room-info-card">
          <div class="info-label">
            <i class="fas fa-info-circle"></i> Status
          </div>
          <div class="info-value">
            <span id="modalStatus"></span>
          </div>
        </div>
      </div>
      
      <!-- Description -->
      <div class="room-detail-description">
        <h3 style="font-size: 1.3rem; font-weight: 600; color: #1f2937; margin-bottom: 15px; display: flex; align-items: center; gap: 10px;">
          <i class="fas fa-align-left" style="color: #4A90E2;"></i> Description
        </h3>
        <p id="modalDescription" style="margin: 0; color: #4b5563;"></p>
      </div>
      
      <!-- Action Buttons -->
      <div class="room-detail-actions">
        <button class="action-btn action-btn-primary" id="modalInquiryBtn">
          <i class="fas fa-envelope"></i> Send Inquiry
        </button>
        <a href="#" class="action-btn action-btn-secondary" id="modalMessageBtn" style="display: none;">
          <i class="fas fa-comments"></i> Message Owner
        </a>
      </div>
    </div>
  </div>
</div>


<script>
const rooms = typeof dbRooms !== "undefined" ? dbRooms : [];
const searchForm = document.getElementById('searchForm');
const resultsContainer = document.getElementById('results');

function displayRooms(roomsArray) {
  resultsContainer.innerHTML = '';
  if(roomsArray.length === 0){
    resultsContainer.innerHTML = `<div class="no-results"><h4>No rooms found</h4><p>Please try different search criteria</p></div>`;
    return;
  }

  roomsArray.forEach(room=>{
    const isOwner = currentUserId && room.user_id == currentUserId;
    const statusValue = room.status ? room.status.replace('_',' ') : 'Unknown';
    const statusClass = room.status ? `status-${room.status}` : 'status-unknown';
    const card = document.createElement('div');
    card.className = 'card';
    card.innerHTML = `
<div class="image">
  <img src="${room.image_url}" alt="Room Image" style="width:100%;height:200px;object-fit:cover;border-radius:8px 8px 0 0;">
  <div class="rent-badge">¥${room.price ? room.price.toLocaleString() : ''}</div>
  <div class="status-badge ${statusClass}">${statusValue}</div>
  ${(room.is_verified == 1 && room.owner_role === 'owner') ? '<div class="verified-badge" style="position:absolute;bottom:15px;right:15px;background:#4A90E2;color:white;padding:8px;border-radius:50%;width:36px;height:36px;display:flex;align-items:center;justify-content:center;box-shadow:0 3px 6px rgba(0,0,0,0.2);"><i class="ri-checkbox-circle-fill" style="font-size:20px;"></i></div>' : ''}
</div>
<div class="card-content">
  <h4>${room.title || ''}</h4>
  ${room.owner_name ? `<p class="flex items-center gap-2"><i class="fas fa-user text-blue-500"></i> <span>${room.owner_name}</span> ${(room.is_verified == 1 && room.owner_role === 'owner') ? '<i class="ri-checkbox-circle-fill text-blue-500" style="font-size:18px;" title="Verified Owner"></i>' : ''}</p>` : ''}
  <p><i class="fas fa-map-marker-alt"></i> ${room.location || ''}</p>
  <p><i class="fas fa-train"></i> ${room.train_station || ''}</p>
  <p><i class="fas fa-home"></i> ${room.type || ''}</p>
  <p>${room.description || ''}</p>
  <button class="details-btn" data-id="${room.id}">View Details <i class="fas fa-arrow-right"></i></button>
  <button class="inquiry-btn" data-id="${room.id}" style="margin-top:8px;background:#2ecc71;color:#fff;border:none;padding:10px;border-radius:8px;cursor:pointer;">Inquiry</button>
  ${!isOwner && currentUserId ? `<a href="messages.php?user_id=${room.user_id}&room_id=${room.id}" class="message-owner-btn" style="display:inline-block;margin-top:8px;background:#4A90E2;color:#fff;border:none;padding:10px;border-radius:8px;cursor:pointer;text-decoration:none;width:100%;text-align:center;"><i class="fas fa-envelope"></i> Message Owner</a>` : ''}
  ${isOwner ? `<button class="edit-btn" data-id="${room.id}" style="margin-top:8px;background:#f1c40f;color:#222;">Edit</button>
  <button class="delete-btn" data-id="${room.id}" style="margin-top:8px;background:#e74c3c;color:#fff;">Delete</button>` : ''}
</div>`;
    resultsContainer.appendChild(card);
  });

  // Details buttons
  document.querySelectorAll('.details-btn').forEach(btn=>{
    btn.addEventListener('click',function(){
      const roomId = parseInt(this.getAttribute('data-id'));
      const room = rooms.find(r=>r.id == roomId);
      if(room){
        // Set title
        document.getElementById('modalTitle').textContent = room.title || 'Room Details';
        
        // Set image with fallback
        const modalImage = document.getElementById('modalImage');
        modalImage.src = room.image_url || 'https://via.placeholder.com/800x350?text=No+Image';
        modalImage.onerror = function() {
          this.src = 'https://via.placeholder.com/800x350?text=No+Image';
        };
        
        // Owner info with verified badge
        const ownerDiv = document.getElementById('modalOwner');
        if (room.owner_name) {
          ownerDiv.innerHTML = `
            <div class="owner-badge">
              <i class="fas fa-user"></i>
              <span>${room.owner_name}</span>
              ${(room.is_verified == 1 && room.owner_role === 'owner') ? '<i class="ri-checkbox-circle-fill text-blue-500" style="font-size:20px;margin-left:8px;" title="Verified Owner"></i>' : ''}
            </div>
          `;
        } else {
          ownerDiv.innerHTML = '';
        }
        
        // Set rent with icon
        const rentValue = room.price ? `¥${room.price.toLocaleString()}` : 'Not specified';
        document.getElementById('modalRent').innerHTML = `<i class="fas fa-yen-sign"></i> ${rentValue}`;
        
        // Set location
        document.getElementById('modalStation').textContent = room.location || 'Not specified';
        
        // Set type
        document.getElementById('modalType').textContent = room.type || 'Not specified';
        
        // Set description
        const description = room.description || 'No description available.';
        document.getElementById('modalDescription').textContent = description;
        
        // Set train station
        document.getElementById('modalTrainStation').textContent = room.train_station || 'Not specified';
        
        // Set status with badge
        const statusValue = room.status ? room.status.replace('_', ' ') : 'Unknown';
        const statusClass = room.status ? `status-${room.status}` : 'status-';
        document.getElementById('modalStatus').innerHTML = `<span class="status-badge-modal ${statusClass}">${statusValue}</span>`;
        
        // Set up inquiry button
        const inquiryBtn = document.getElementById('modalInquiryBtn');
        inquiryBtn.onclick = function() {
          document.getElementById('roomModal').style.display = 'none';
          // Trigger inquiry modal
          document.getElementById('inquiryRoomId').value = roomId;
          document.getElementById('inquiryRoomTitle').value = room.title || '';
          <?php if(isset($_SESSION["user_id"])): ?>
          document.getElementById('inqName').value = '<?php echo htmlspecialchars($_SESSION["name"] ?? ""); ?>';
          <?php endif; ?>
          document.getElementById('inqEmail').value = '';
          document.getElementById('inqPhone').value = '';
          document.getElementById('inqDate').value = '';
          document.getElementById('inqMessage').value = '';
          document.getElementById('inquirySuccess').style.display = 'none';
          document.getElementById('inquiryError').style.display = 'none';
          document.getElementById('inquiryModal').style.display = 'block';
        };
        
        // Set up message button if user is logged in and not owner
        const messageBtn = document.getElementById('modalMessageBtn');
        const isOwner = currentUserId && room.user_id == currentUserId;
        if (!isOwner && currentUserId) {
          messageBtn.href = `messages.php?user_id=${room.user_id}&room_id=${room.id}`;
          messageBtn.style.display = 'flex';
        } else {
          messageBtn.style.display = 'none';
        }
        
        // Show modal
        document.getElementById('roomModal').style.display = 'block';
        document.body.style.overflow = 'hidden';
      }
    });
  });

  // Inquiry buttons
  document.querySelectorAll('.inquiry-btn').forEach(btn=>{
    btn.addEventListener('click',function(){
      const roomId = this.getAttribute('data-id');
      const room = rooms.find(r=>r.id == roomId);
      if(room){
        document.getElementById('inquiryRoomId').value = roomId;
        document.getElementById('inquiryRoomTitle').value = room.title || '';
        // Pre-fill form if user is logged in
        <?php if(isset($_SESSION["user_id"])): ?>
        document.getElementById('inqName').value = '<?php echo htmlspecialchars($_SESSION["name"] ?? ""); ?>';
        <?php endif; ?>
        // Reset form
        document.getElementById('inqEmail').value = '';
        document.getElementById('inqPhone').value = '';
        document.getElementById('inqDate').value = '';
        document.getElementById('inqMessage').value = '';
        document.getElementById('inquirySuccess').style.display = 'none';
        document.getElementById('inquiryError').style.display = 'none';
        document.getElementById('inquiryModal').style.display = 'block';
      }
    });
  });

  // Edit/Delete
  document.querySelectorAll('.edit-btn').forEach(btn=>{
    btn.addEventListener('click',()=>{ window.location.href='edit-room.php?id='+btn.getAttribute('data-id'); });
  });
  document.querySelectorAll('.delete-btn').forEach(btn=>{
    btn.addEventListener('click',()=>{
      if(confirm('Are you sure you want to delete this room?')) window.location.href='delete-room.php?id='+btn.getAttribute('data-id');
    });
  });
}

// Search
function handleSearch(e){
  e.preventDefault();
  const rent = parseInt(document.getElementById('rent').value) || Number.MAX_VALUE;
  const station = document.getElementById('station').value.trim().toLowerCase();
  const roomType = document.getElementById('roomType').value.trim().toLowerCase();
  const filteredRooms = rooms.filter(room=>{
    return (room.price <= rent) &&
           (station === '' || (room.location && room.location.toLowerCase().includes(station))) &&
           (roomType === '' || (room.type && room.type.toLowerCase().includes(roomType)));
  });
  displayRooms(filteredRooms);
  if (mapViewActive) updateMapMarkers(filteredRooms);
}

// Modal Close
document.getElementById('modalClose').onclick = function() {
  document.getElementById('roomModal').style.display = 'none';
  document.body.style.overflow = '';
};

document.getElementById('inquiryModalClose').onclick = function() {
  document.getElementById('inquiryModal').style.display = 'none';
  document.body.style.overflow = '';
};

// Close modal when clicking outside
window.onclick = function(event) {
  const roomModal = document.getElementById('roomModal');
  const inquiryModal = document.getElementById('inquiryModal');
  if (event.target == roomModal) {
    roomModal.style.display = 'none';
    document.body.style.overflow = '';
  }
  if (event.target == inquiryModal) {
    inquiryModal.style.display = 'none';
    document.body.style.overflow = '';
  }
};

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') {
    const roomModal = document.getElementById('roomModal');
    const inquiryModal = document.getElementById('inquiryModal');
    if (roomModal.style.display === 'block') {
      roomModal.style.display = 'none';
      document.body.style.overflow = '';
    }
    if (inquiryModal.style.display === 'block') {
      inquiryModal.style.display = 'none';
      document.body.style.overflow = '';
    }
  }
});

// Inquiry Form Submission
document.getElementById('inquiryForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  const formData = new FormData(this);
  const submitBtn = document.getElementById('inquirySubmitBtn');
  const successDiv = document.getElementById('inquirySuccess');
  const errorDiv = document.getElementById('inquiryError');
  const errorMessage = document.getElementById('errorMessage');
  
  // Hide previous messages
  successDiv.style.display = 'none';
  errorDiv.style.display = 'none';
  
  // Disable button and show loading
  submitBtn.disabled = true;
  const originalHTML = submitBtn.innerHTML;
  submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>Sending...</span>';
  submitBtn.style.opacity = '0.7';
  
  try {
    const res = await fetch('api/submit-inquiry.php', {
      method: 'POST',
      body: formData
    });
    
    // Read response body once as text first, then parse as JSON
    // This allows us to see the actual response if JSON parsing fails
    const responseText = await res.text();
    let data;
    
    // Try to parse as JSON
    try {
      data = JSON.parse(responseText);
    } catch (e) {
      // JSON parsing failed - log the actual response for debugging
      console.error("Failed to parse JSON response. Actual response:", responseText);
      console.error("Parse error:", e);
      
      // Check if response is ok to provide better error message
      if (!res.ok) {
        throw new Error(`HTTP error! status: ${res.status}. Server response: ${responseText.substring(0, 200)}`);
      } else {
        throw new Error("Server returned invalid JSON response: " + responseText.substring(0, 200));
      }
    }
    
    // Check if response is ok (after reading the body)
    if (!res.ok) {
      // Extract error message from data
      const errorText = data?.error || data?.debug || responseText || `HTTP ${res.status}`;
      throw new Error(`HTTP error! status: ${res.status}. ${errorText}`);
    }
    
    if (data.success) {
      // Show success message with animation
      successDiv.style.display = 'block';
      const successMsg = document.getElementById('successMessage');
      if (successMsg) {
        successMsg.textContent = data.message || 'Your inquiry has been submitted successfully!';
      } else {
        successDiv.innerHTML = '<i class="fas fa-check-circle" style="color:#4f8a10;margin-right:8px;"></i> <strong>Success!</strong> ' + (data.message || 'Your inquiry has been submitted successfully!');
      }
      successDiv.style.animation = 'slideInSuccess 0.5s ease';
      
      // Add success animation to button
      submitBtn.style.background = '#4f8a10';
      submitBtn.innerHTML = '<i class="fas fa-check"></i> <span>Sent Successfully!</span>';
      
      // Scroll to success message
      successDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
      
      // Reset form after 2 seconds and close modal after 3 seconds
      setTimeout(() => {
        this.reset();
        setTimeout(() => {
          document.getElementById('inquiryModal').style.display = 'none';
          // Reset button
          submitBtn.style.background = '';
          submitBtn.innerHTML = originalHTML;
        }, 1000);
      }, 2000);
    } else {
      // Show error message
      errorDiv.style.display = 'block';
      errorMessage.textContent = data.error || 'Failed to submit inquiry. Please try again.';
      errorDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
  } catch (error) {
    console.error('Inquiry Error:', error);
    errorDiv.style.display = 'block';
    let errorMsg = 'Network error. Please check your connection and try again.';
    
    if (error.message.includes('HTTP error')) {
      // Extract error details from message
      const errorMatch = error.message.match(/error! status: \d+\. (.+)/);
      if (errorMatch && errorMatch[1]) {
        errorMsg = errorMatch[1];
      } else {
        errorMsg = 'Server error occurred. Please check if the database is set up correctly.';
      }
    } else if (error.message.includes('invalid response')) {
      errorMsg = 'Server returned invalid response. Please check server configuration.';
    } else if (error.message) {
      errorMsg = 'Error: ' + error.message;
    }
    
    errorMessage.textContent = errorMsg;
    errorDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    
    // Add shake animation to error
    errorDiv.style.animation = 'shake 0.5s ease';
    setTimeout(() => {
      errorDiv.style.animation = '';
    }, 500);
  } finally {
    // Re-enable button
    submitBtn.disabled = false;
    submitBtn.innerHTML = originalHTML;
    submitBtn.style.opacity = '1';
  }
});

searchForm.addEventListener('submit', handleSearch);
window.onload = ()=>{ displayRooms(rooms); };

// Google Maps Integration
let map;
let markers = [];
let mapViewActive = false;

// Initialize map
function initMap() {
  map = new google.maps.Map(document.getElementById('map'), {
    zoom: 12,
    center: { lat: 35.6762, lng: 139.6503 }, // Tokyo default
    mapTypeControl: true,
    streetViewControl: true,
    fullscreenControl: true
  });
}

// Toggle map view
document.getElementById('toggleMapView')?.addEventListener('click', function() {
  const mapContainer = document.getElementById('mapContainer');
  const resultsContainer = document.getElementById('results');
  mapViewActive = !mapViewActive;
  
  if (mapViewActive) {
    mapContainer.style.display = 'block';
    resultsContainer.style.display = 'none';
    this.innerHTML = '<i class="ri-list-check mr-2"></i>View List';
    if (!map) initMap();
    updateMapMarkers(rooms);
  } else {
    mapContainer.style.display = 'none';
    resultsContainer.style.display = 'grid';
    this.innerHTML = '<i class="ri-map-line mr-2"></i>View on Map';
  }
});

// Update map markers
function updateMapMarkers(roomsArray) {
  // Clear existing markers
  markers.forEach(marker => marker.setMap(null));
  markers = [];
  
  if (!map || !roomsArray || roomsArray.length === 0) return;
  
  const bounds = new google.maps.LatLngBounds();
  
  roomsArray.forEach(room => {
    if (!room.location) return;
    
    // Geocode location
    const geocoder = new google.maps.Geocoder();
    geocoder.geocode({ address: room.location }, (results, status) => {
      if (status === 'OK' && results[0]) {
        const position = results[0].geometry.location;
        const marker = new google.maps.Marker({
          position: position,
          map: map,
          title: room.title || 'Room',
          icon: {
            url: 'http://maps.google.com/mapfiles/ms/icons/blue-dot.png'
          }
        });
        
        const infoWindow = new google.maps.InfoWindow({
          content: `
            <div style="padding:10px;max-width:250px;">
              <h4 style="font-weight:bold;margin-bottom:8px;color:#4A90E2;">${room.title || 'Room'}</h4>
              <p style="margin:4px 0;color:#666;"><i class="fas fa-map-marker-alt"></i> ${room.location}</p>
              <p style="margin:4px 0;color:#666;"><i class="fas fa-yen-sign"></i> ¥${room.price ? room.price.toLocaleString() : 'N/A'}</p>
              ${(room.is_verified == 1 && room.owner_role === 'owner') ? '<p style="margin:4px 0;color:#4A90E2;"><i class="ri-checkbox-circle-fill" style="font-size:18px;"></i></p>' : ''}
              <a href="find-rooms.php?id=${room.id}" style="display:inline-block;margin-top:8px;color:#4A90E2;text-decoration:underline;">View Details</a>
            </div>
          `
        });
        
        marker.addListener('click', () => {
          infoWindow.open(map, marker);
        });
        
        markers.push(marker);
        bounds.extend(position);
        map.fitBounds(bounds);
      }
    });
  });
}

// Toggle user dropdown menu
function toggleUserDropdown() {
  const dropdown = document.getElementById('userDropdown');
  if (dropdown) {
    dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
  }
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
  const dropdown = document.getElementById('userDropdown');
  const button = event.target.closest('[onclick="toggleUserDropdown()"]');
  if (dropdown && !dropdown.contains(event.target) && !button) {
    dropdown.style.display = 'none';
  }
});
</script>
</body>
</html>
