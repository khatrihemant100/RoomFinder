// Language translations
const translations = {
  en: {
    home: "Home",
    find_rooms: "Find Rooms",
    list_property: "List Property",
    about_us: "About Us",
    contact: "Contact",
    sign_in: "Sign In",
    sign_up: "Sign Up",
    logout: "Logout",
    find_perfect_room: "Find Your Perfect Room",
    discover: "Discover thousands of rooms and apartments for rent. Whether you're looking to list your property or find your next home, we've got you covered.",
    find_room_btn: "Find a Room",
    list_property_btn: "List Your Property",
    register_owner: "Register as Room Owner",
    choose_role: "Choose Your Role",
    role_desc: "Whether you're looking to rent out your property or find your next home, RoomFinder has the tools you need."
  },
  ja: {
    home: "ホーム",
    find_rooms: "部屋を探す",
    list_property: "物件を掲載する",
    about_us: "私たちについて",
    contact: "お問い合わせ",
    sign_in: "サインイン",
    sign_up: "サインアップ",
    logout: "ログアウト",
    find_perfect_room: "理想の部屋を見つけよう",
    discover: "何千もの部屋やアパートを発見。物件を掲載したい場合も、次の住まいを探している場合も、お手伝いします。",
    find_room_btn: "部屋を探す",
    list_property_btn: "物件を掲載する",
    register_owner: "オーナーとして登録",
    choose_role: "役割を選択",
    role_desc: "物件を貸し出したい場合も、次の住まいを探している場合も、RoomFinderには必要なツールがあります。"
  },
  ne: {
    home: "गृहपृष्ठ",
    find_rooms: "कोठा खोज्नुहोस्",
    list_property: "सम्पत्ति सूचीबद्ध गर्नुहोस्",
    about_us: "हाम्रो बारेमा",
    contact: "सम्पर्क गर्नुहोस्",
    sign_in: "साइन इन गर्नुहोस्",
    sign_up: "साइन अप गर्नुहोस्",
    logout: "लगआउट",
    find_perfect_room: "आफ्नो उत्तम कोठा खोज्नुहोस्",
    discover: "हजारौं कोठाहरू र अपार्टमेन्टहरू भाडामा खोज्नुहोस्। तपाईं आफ्नो सम्पत्ति सूचीबद्ध गर्न चाहनुहुन्छ वा आफ्नो अर्को घर खोज्न चाहनुहुन्छ, हामीले तपाईंलाई सहयोग गर्छौं।",
    find_room_btn: "कोठा खोज्नुहोस्",
    list_property_btn: "आफ्नो सम्पत्ति सूचीबद्ध गर्नुहोस्",
    register_owner: "कोठा मालिकको रूपमा दर्ता गर्नुहोस्",
    choose_role: "आफ्नो भूमिका छान्नुहोस्",
    role_desc: "तपाईं आफ्नो सम्पत्ति भाडामा दिन चाहनुहुन्छ वा आफ्नो अर्को घर खोज्न चाहनुहुन्छ, RoomFinder मा तपाईंलाई चाहिने उपकरणहरू छन्।"
  }
};

// Set language function
function setLanguage(lang) {
  // Save to localStorage
  localStorage.setItem('selectedLanguage', lang);
  
  // Update HTML lang attribute
  document.documentElement.lang = lang;
  
  // Get translations
  const t = translations[lang] || translations.en;
  
  // Update elements with data-i18n attribute
  document.querySelectorAll('[data-i18n]').forEach(element => {
    const key = element.getAttribute('data-i18n');
    if (t[key]) {
      element.textContent = t[key];
    }
  });
  
  // Update elements with data-i18n-html attribute (for HTML content)
  document.querySelectorAll('[data-i18n-html]').forEach(element => {
    const key = element.getAttribute('data-i18n-html');
    if (t[key]) {
      element.innerHTML = t[key];
    }
  });
  
  // Update placeholder attributes
  document.querySelectorAll('[data-i18n-placeholder]').forEach(element => {
    const key = element.getAttribute('data-i18n-placeholder');
    if (t[key]) {
      element.placeholder = t[key];
    }
  });
  
  // Update current language display
  const langSelectors = document.querySelectorAll('.current-lang');
  langSelectors.forEach(selector => {
    const langData = getLangData(lang);
    const flagSpan = selector.querySelector('span.fi');
    const codeSpan = selector.querySelector('span:not(.fi):not(.ri-arrow-down-s-line)');
    if (flagSpan) {
      flagSpan.className = `fi ${langData.flagClass} fis`;
      flagSpan.style.fontSize = '1.2rem';
    }
    if (codeSpan && !codeSpan.querySelector('i')) {
      codeSpan.textContent = langData.code;
    }
  });
  
  // Close dropdown if open
  const dropdowns = document.querySelectorAll('.lang-dropdown');
  dropdowns.forEach(dropdown => {
    dropdown.classList.add('hidden');
  });
}

// Get language data
function getLangData(lang) {
  const langMap = {
    en: { flag: '<span class="fi fi-gb fis" style="font-size: 1.2rem;"></span>', code: 'EN', name: 'English', flagClass: 'fi-gb' },
    ja: { flag: '<span class="fi fi-jp fis" style="font-size: 1.2rem;"></span>', code: 'JA', name: '日本語', flagClass: 'fi-jp' },
    ne: { flag: '<span class="fi fi-np fis" style="font-size: 1.2rem;"></span>', code: 'NE', name: 'नेपाली', flagClass: 'fi-np' }
  };
  return langMap[lang] || langMap.en;
}

// Initialize language on page load
document.addEventListener('DOMContentLoaded', function() {
  const savedLang = localStorage.getItem('selectedLanguage') || 'en';
  setLanguage(savedLang);
});

// Toggle language dropdown
function toggleLangDropdown(element) {
  const dropdown = element.nextElementSibling;
  const allDropdowns = document.querySelectorAll('.lang-dropdown');
  
  // Close all other dropdowns
  allDropdowns.forEach(dd => {
    if (dd !== dropdown) {
      dd.classList.add('hidden');
    }
  });
  
  // Toggle current dropdown
  if (dropdown) {
    dropdown.classList.toggle('hidden');
  }
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
  if (!event.target.closest('.lang-selector')) {
    document.querySelectorAll('.lang-dropdown').forEach(dropdown => {
      dropdown.classList.add('hidden');
    });
  }
});
