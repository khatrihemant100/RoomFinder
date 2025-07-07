const translations = {
  ja: {
    heading: "完璧な部屋を見つけよう",
    "label-rent": "家賃",
    "label-location": "場所",
    "label-type": "部屋タイプ",
    "search-btn": "検索",
    "ai-btn": "AIおすすめ部屋",
    "results-title": "検索結果",
    details: "詳細を見る",
  },
  ne: {
    heading: "उत्तम कोठा खोज्नुहोस्",
    "label-rent": "भाडा",
    "label-location": "स्थान",
    "label-type": "कोठा प्रकार",
    "search-btn": "खोज्नुहोस्",
    "ai-btn": "AI सिफारिस कोठा",
    "results-title": "खोज परिणाम",
    details: "विवरण हेर्नुहोस्",
  },
  en: {
    heading: "Find the Perfect Room",
    "label-rent": "Rent",
    "label-location": "Location",
    "label-type": "Room Type",
    "search-btn": "Search",
    "ai-btn": "AI Recommended Rooms",
    "results-title": "Search Results",
    details: "View Details",
  }
};

// Define rooms data
const rooms = typeof dbRooms !== "undefined" ? dbRooms : [];

// DOM Elements
const languageSelect = document.getElementById('language');
const searchForm = document.getElementById('searchForm');
const resultsContainer = document.getElementById('results');
const aiRecommendationBox = document.getElementById('ai-recommendation');

// Initialize the app
function initApp() {
  updateLanguage(languageSelect.value);
  displayRooms(rooms);
  
  // Set up event listeners
  languageSelect.addEventListener('change', function() {
    updateLanguage(this.value);
  });
  
  searchForm.addEventListener('submit', handleSearch);
  
  document.getElementById('ai-btn').addEventListener('click', handleAIRecommendation);
  
  document.getElementById('modalClose').addEventListener('click', function() {
    document.getElementById('roomModal').style.display = 'none';
  });
  
  window.addEventListener('click', function(e) {
    const modal = document.getElementById('roomModal');
    if (e.target === modal) {
      modal.style.display = 'none';
    }
  });
}

// Update UI language
function updateLanguage(lang) {
  const trans = translations[lang];
  
  // Update form labels and buttons
  document.getElementById('heading').textContent = trans.heading;
  document.getElementById('label-rent').textContent = trans['label-rent'];
  document.getElementById('label-location').textContent = trans['label-location'];
  document.getElementById('label-type').textContent = trans['label-type'];
  document.getElementById('search-btn').innerHTML = `<i class="fas fa-search"></i> ${trans['search-btn']}`;
  document.getElementById('ai-btn').innerHTML = `<i class="fas fa-robot"></i> ${trans['ai-btn']}`;
  document.getElementById('results-title').textContent = trans['results-title'];
  
  // Update station dropdown placeholder
  document.querySelector('#station option[value=""]').textContent = 
    lang === 'ja' ? '駅を選択' : 
    lang === 'ne' ? 'स्थान छान्नुहोस्' : 
    'Select Station';
  
  // Update room type placeholder
  document.getElementById('roomType').placeholder = 
    lang === 'ja' ? '例: 1K, 2LDK, etc.' : 
    lang === 'ne' ? 'उदा: १के, २एलडिके, etc.' : 
    'e.g. 1K, 2LDK, etc.';
  
  // Re-display rooms with new language
  displayRooms(rooms);
}

// Display rooms in the UI
function displayRooms(roomsArray) {
  resultsContainer.innerHTML = '';
  if (roomsArray.length === 0) {
    resultsContainer.innerHTML = `
      <div class="no-results">
        <h4>No rooms found</h4>
        <p>Please try different search criteria</p>
      </div>
    `;
    return;
  }
  roomsArray.forEach(room => {
    const isOwner = currentUserId && room.user_id == currentUserId;
    const card = document.createElement('div');
    card.className = 'card';
    card.innerHTML = `
      <div class="image" style="background-image:  url('${room.image}')">
        <div class="rent-badge">¥${room.price ? room.price.toLocaleString() : ''}</div>
      </div>
      <div class="card-content">
        <h4>${room.title || ''}</h4>
        <p><i class="fas fa-map-marker-alt"></i> ${room.location || ''}</p>
        <p><i class="fas fa-home"></i> ${room.type || ''}</p>
        <p>${room.description || ''}</p>
        <button class="details-btn" data-id="${room.id}">
          View Details <i class="fas fa-arrow-right"></i>
        </button>
        ${isOwner ? `
          <button class="edit-btn" data-id="${room.id}" style="margin-top:8px;background:#f1c40f;color:#222;">Edit</button>
          <button class="delete-btn" data-id="${room.id}" style="margin-top:8px;background:#e74c3c;color:#fff;">Delete</button>
        ` : ''}
      </div>
    `;
    resultsContainer.appendChild(card);
  });

  // Event handlers
  document.querySelectorAll('.edit-btn').forEach(button => {
    button.addEventListener('click', function() {
      const roomId = this.getAttribute('data-id');
      window.location.href = 'edit-room.php?id=' + roomId;
    });
  });
  document.querySelectorAll('.delete-btn').forEach(button => {
    button.addEventListener('click', function() {
      const roomId = this.getAttribute('data-id');
      if (confirm('Are you sure you want to delete this room?')) {
        window.location.href = 'delete-room.php?id=' + roomId;
      }
    });
  });
}

// Handle search form submission
function handleSearch(e) {
  e.preventDefault();
  aiRecommendationBox.style.display = 'none';
  
  // Show loading state
  resultsContainer.innerHTML = `
    <div class="loading">
      <div class="spinner"></div>
      <p>Searching for rooms...</p>
    </div>
  `;
  
  // Simulate search delay
  setTimeout(() => {
    const rent = parseInt(document.getElementById('rent').value) || Number.MAX_VALUE;
    const station = document.getElementById('station').value;
    const roomType = document.getElementById('roomType').value.toLowerCase();
    const lang = languageSelect.value;
    
    const filteredRooms = rooms.filter(room => {
      const roomStation = room.station.en; // Search by English station name
      const roomTypeText = room.type[lang] || room.type.en;
      
      return room.rent <= rent &&
             (station === '' || roomStation === station) &&
             (roomType === '' || roomTypeText.toLowerCase().includes(roomType));
    });
    
    displayRooms(filteredRooms);
  }, 800);
}

// Handle AI recommendation
function handleAIRecommendation() {
  aiRecommendationBox.style.display = 'block';
  
  // Show loading state
  resultsContainer.innerHTML = `
    <div class="loading">
      <div class="spinner"></div>
      <p>Analyzing preferences...</p>
    </div>
  `;
  
  // Simulate AI processing
  setTimeout(() => {
    // AI recommends rooms under 100,000 yen in popular locations
    const recommendedRooms = rooms.filter(room => 
      room.rent <= 100000 && 
      (room.station.en === "Tokyo" || room.station.en === "Osaka" || room.station.en === "Kyoto")
    );
    
    displayRooms(recommendedRooms.length > 0 ? recommendedRooms : rooms);
  }, 1500);
}

// (Optional) Prevent default submit for demo
// document.getElementById('room-form').addEventListener('submit', function(e) {
//     e.preventDefault();
//     alert('Room listed successfully! (Demo)');
// });

// Initialize the application when DOM is loaded
document.addEventListener('DOMContentLoaded', initApp);
// Edit button handler
document.querySelectorAll('.edit-btn').forEach(button => {
  button.addEventListener('click', function() {
    const roomId = this.getAttribute('data-id');
    window.location.href = 'edit-room.php?id=' + roomId;
  });
});

// Delete button handler
document.querySelectorAll('.delete-btn').forEach(button => {
  button.addEventListener('click', function() {
    const roomId = this.getAttribute('data-id');
    if (confirm('Are you sure you want to delete this room?')) {
      window.location.href = 'delete-room.php?id=' + roomId;
    }
  });
});