# Email Setup Instructions for RoomFinder
## Inquiry Email System Setup Guide

---

## 📧 **Email Configuration**

The inquiry system sends emails to property owners when someone submits an inquiry. Follow these steps to configure email:

### **Step 1: Create .env File**

1. Go to `15_mail/` directory
2. Create a file named `.env` (copy from `.env.example` if it exists)
3. Add your email configuration:

```env
# SMTP Server Settings
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_ENCRYPTION=tls

# SMTP Authentication
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password

# Email From Settings
MAIL_FROM_ADDRESS=noreply@roomfinder.com
MAIL_FROM_NAME=RoomFinder
```

### **Step 2: Gmail Setup (Recommended)**

If using Gmail:

1. **Enable 2-Step Verification:**
   - Go to Google Account > Security
   - Enable 2-Step Verification

2. **Generate App Password:**
   - Go to Google Account > Security > 2-Step Verification
   - Scroll down to "App passwords"
   - Select "Mail" and "Other (Custom name)"
   - Enter "RoomFinder" as the name
   - Copy the generated 16-character password
   - Use this password in `MAIL_PASSWORD` (NOT your regular Gmail password)

3. **Update .env file:**
   ```env
   MAIL_USERNAME=your-email@gmail.com
   MAIL_PASSWORD=xxxx xxxx xxxx xxxx  # The 16-character app password
   ```

### **Step 3: Other Email Providers**

#### **Outlook/Hotmail:**
```env
MAIL_HOST=smtp-mail.outlook.com
MAIL_PORT=587
MAIL_ENCRYPTION=tls
```

#### **Yahoo:**
```env
MAIL_HOST=smtp.mail.yahoo.com
MAIL_PORT=587
MAIL_ENCRYPTION=tls
```

#### **Custom SMTP:**
```env
MAIL_HOST=mail.yourdomain.com
MAIL_PORT=587  # or 465 for SSL
MAIL_ENCRYPTION=tls  # or ssl for port 465
```

---

## ✅ **Testing Email**

1. Submit an inquiry from `find-rooms.php`
2. Check the property owner's email inbox
3. Check PHP error logs if email doesn't arrive

---

## 🔧 **Troubleshooting**

### **Email not sending:**
- Check `.env` file exists in `15_mail/` directory
- Verify email credentials are correct
- Check PHP error logs: `C:\xampp\php\logs\php_error_log`
- For Gmail: Make sure you're using App Password, not regular password

### **"Class not found" error:**
- Run `composer install` in `15_mail/` directory
- Make sure `vendor/` folder exists

### **SMTP connection error:**
- Check firewall settings
- Verify SMTP host and port
- Try different encryption (tls/ssl)

---

## 📝 **Files Created/Modified**

1. **`api/InquiryMailer.php`** - Email sending class
2. **`api/submit-inquiry.php`** - Updated to send emails
3. **`15_mail/templates/inquiry_mail.html`** - Email template
4. **`find-rooms.php`** - Enhanced inquiry form

---

## 🎨 **Email Template**

The email template is located at:
- `15_mail/templates/inquiry_mail.html`

You can customize the email design by editing this file.

---

**Note:** Make sure `.env` file is in `.gitignore` to keep your credentials secure!

