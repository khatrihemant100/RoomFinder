# 🚀 RoomFinder - Quick Start Guide
## नेपाली: RoomFinder - छिटो सुरु गाइड

---

## 📋 **Project Summary (प्रोजेक्ट सारांश)**

**RoomFinder** एक room/property listing platform हो जहाँ:
- **Users** ले room/property list गर्न सक्छन् (Owners)
- **Users** ले room search गर्न सक्छन् (Seekers)
- **AI Chat** feature छ (Gemini API)
- **Multi-language** support छ (English, Japanese, Nepali)
- **Email** notifications छन्

---

## 🗂️ **Main Folders (मुख्य फोल्डरहरू)**

### **1. Root Files (मुख्य फाइलहरू)**
```
index.php          → Homepage (मुख्य पृष्ठ)
find-rooms.php     → Room search (कोठा खोज)
list-property.php  → List property (सम्पत्ति सूचीबद्ध)
contact.php        → Contact form (सम्पर्क)
about.php          → About page (हाम्रो बारेमा)
db.php             → Database connection (डाटाबेस)
```

### **2. user/ (प्रयोगकर्ता)**
```
login.php          → Login (लगइन)
createaccount.php  → Register (दर्ता)
logout.php         → Logout (लगआउट)
```

### **3. api/ (API)**
```
ai-chat.php        → AI chat API
submit-inquiry.php → Inquiry form API
InquiryMailer.php  → Email sending
```

### **4. lang/ (भाषा)**
```
en.json → English
ja.json → Japanese (日本語)
ne.json → Nepali (नेपाली)
mm.json → Myanmar
```

### **5. uploads/ (चित्रहरू)**
```
Room images uploaded by users
```

### **6. scraper/ (स्क्रापर)**
```
Python scripts to scrape room data from SUUMO
```

### **7. docs/ (कागजात)**
```
SQL files, documentation, fixes
```

### **8. 15_mail/ (इमेल)**
```
Email functionality with PHPMailer
```

---

## 🔑 **Key Files to Know (जान्नुपर्ने मुख्य फाइलहरू)**

| File | Purpose | When to Edit |
|------|---------|--------------|
| `index.php` | Homepage | Add new sections, change design |
| `find-rooms.php` | Search page | Add filters, change layout |
| `list-property.php` | Add room form | Add new fields |
| `db.php` | Database connection | Change DB credentials |
| `lang.js` | Language switching | Add new translations |
| `user/createaccount.php` | Registration | Change form fields |
| `api/ai-chat.php` | AI chat | Change API key |

---

## 🎯 **Common Tasks (सामान्य कार्यहरू)**

### **Add New Page:**
1. Create `newpage.php` in root
2. Copy header from `index.php`
3. Add link in navigation
4. Done!

### **Change Database:**
1. Edit `db.php`
2. Update credentials
3. Done!

### **Add New Language:**
1. Create `lang/xx.json`
2. Add translations
3. Update `lang.js`
4. Add flag in language selector

### **Change Design:**
1. Edit CSS files (`styles.css`, `find.css`)
2. Or use Tailwind classes in PHP files
3. Done!

---

## 📊 **Database Tables (डाटाबेस तालिकाहरू)**

1. **users** - User accounts
2. **properties** - Room listings
3. **inquiries** - Inquiry form submissions
4. **contacts** - Contact form submissions

---

## 🎨 **Design System (डिजाइन प्रणाली)**

- **Colors:** Blue (#4A90E2), Red (#FF6B6B)
- **Fonts:** Pacifico (titles), Inter (body)
- **Framework:** Tailwind CSS
- **Icons:** RemixIcon, Font Awesome

---

## ⚡ **Quick Commands (छिटो आदेशहरू)**

### **Start Development:**
1. Start XAMPP
2. Open `http://localhost/RoomFinder/`
3. Done!

### **Check Database:**
- Open phpMyAdmin
- Select `roomfinder` database
- Check tables

### **Test Features:**
- Register account → `user/createaccount.php`
- List property → `list-property.php`
- Search rooms → `find-rooms.php`
- Contact form → `contact.php`

---

## 📚 **Documentation Files (कागजात फाइलहरू)**

- `PROJECT_STRUCTURE.md` → Complete structure guide
- `ANALYSIS_REPORT.md` → Project analysis
- `README_FIXES.md` → Fixes applied
- `QUICK_START.md` → This file!

---

## 🆘 **Need Help? (मद्दत चाहिएको?)**

1. Check `PROJECT_STRUCTURE.md` for detailed info
2. Check `docs/` folder for SQL fixes
3. Check `ANALYSIS_REPORT.md` for known issues
4. Ask me! 😊

---

**Happy Coding! 🚀**

