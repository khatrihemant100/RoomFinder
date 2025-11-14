# Inquiry Email Flow - Complete Documentation

## Overview
When a user clicks "Inquiry" button on a room listing, fills the form, and submits it, an email is automatically sent to the room owner's email address.

## Complete Flow

### 1. User Action (Frontend)
- User clicks "Inquiry" button on a room card in `find-rooms.php`
- Modal form opens with fields:
  - Name (required)
  - Email (required)
  - Phone (required)
  - Preferred Visit Date (required)
  - Additional Message (optional)

### 2. Form Submission (JavaScript)
- Form data is collected via JavaScript
- Data is sent via POST request to `api/submit-inquiry.php`
- Includes `room_id` to identify which property

### 3. Server Processing (`api/submit-inquiry.php`)

#### Step 1: Validation
- Validates all required fields
- Validates email format
- Checks if room_id is valid

#### Step 2: Get Room Owner Information
```php
SELECT p.id, p.title, p.location, p.price, p.user_id, 
       u.name as owner_name, u.email as owner_email 
FROM properties p 
LEFT JOIN users u ON p.user_id = u.id 
WHERE p.id = ?
```
- Fetches room details and owner's email from database
- Uses JOIN to get owner's email from `users` table

#### Step 3: Save Inquiry to Database
```php
INSERT INTO inquiries (room_id, name, email, phone, visit_date, message) 
VALUES (?, ?, ?, ?, ?, ?)
```
- Saves inquiry data to `inquiries` table
- This happens BEFORE sending email (so inquiry is saved even if email fails)

#### Step 4: Send Email to Room Owner
- Loads `InquiryMailer` class from `api/InquiryMailer.php`
- Prepares email with:
  - **To:** Room owner's email (from database)
  - **Subject:** "New Room Inquiry - [Property Title]"
  - **Body:** HTML email template with all inquiry details
  - **Reply-To:** Inquirer's email (so owner can reply directly)

### 4. Email Content (`InquiryMailer.php`)

The email includes:
- **Property Details:**
  - Property Title
  - Location
  - Price

- **Inquiry Details:**
  - Inquirer's Name
  - Inquirer's Email (clickable)
  - Inquirer's Phone
  - Preferred Visit Date
  - Additional Message

- **Email Template:** Uses `15_mail/templates/inquiry_mail.html`

### 5. Response to User
- Success: "Your inquiry has been submitted successfully! The property owner has been notified via email."
- If email fails: "Your inquiry has been submitted successfully!" (inquiry still saved)

## Database Structure

### Required Tables:

1. **properties** table:
   - `id` (primary key)
   - `title`
   - `location`
   - `price`
   - `user_id` (foreign key to users table)

2. **users** table:
   - `id` (primary key)
   - `name`
   - `email` (used to send inquiry email)

3. **inquiries** table:
   - `id` (primary key)
   - `room_id` (foreign key to properties)
   - `name`
   - `email`
   - `phone`
   - `visit_date`
   - `message`
   - `created_at`

## Email Configuration

Email settings are loaded from `15_mail/.env` file:
```
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME=RoomFinder
MAIL_HOST=smtp.gmail.com
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_PORT=587
```

## Error Handling

1. **Database Errors:**
   - Connection failures
   - Missing tables
   - SQL errors
   - All logged to PHP error log

2. **Email Errors:**
   - Email sending failures don't prevent inquiry from being saved
   - Errors are logged but user still sees success message
   - Owner email not found is logged as warning

3. **Validation Errors:**
   - Missing required fields
   - Invalid email format
   - Room not found
   - All return clear error messages to user

## Testing

### Test the Complete Flow:

1. **Check Database:**
   - Run `docs/fix_database.sql` in phpMyAdmin
   - Verify `inquiries` table exists
   - Verify properties have valid `user_id` linked to users

2. **Check Email Configuration:**
   - Verify `15_mail/.env` file exists with correct settings
   - Test email sending with `api/test-inquiry.php`

3. **Test Inquiry Submission:**
   - Go to `find-rooms.php`
   - Click "Inquiry" on any room
   - Fill form and submit
   - Check room owner's email inbox

4. **Check Logs:**
   - PHP error log: `C:\xampp\php\logs\php_error_log`
   - Look for "Email sent successfully" or error messages

## Troubleshooting

### Email Not Sending:
1. Check `15_mail/.env` file exists and has correct credentials
2. Verify Gmail App Password is set (not regular password)
3. Check PHP error logs for specific error messages
4. Verify `InquiryMailer.php` file exists at `api/InquiryMailer.php`
5. Verify `vendor/autoload.php` exists in `15_mail/` directory

### Owner Email Not Found:
1. Check if property has valid `user_id`
2. Verify user exists in `users` table
3. Verify user has valid email address
4. Check database JOIN query in `api/submit-inquiry.php`

### Inquiry Not Saving:
1. Verify `inquiries` table exists
2. Check table structure matches expected columns
3. Check PHP error logs for SQL errors
4. Verify database connection in `api/submit-inquiry.php`

## Files Involved

1. **Frontend:**
   - `find-rooms.php` - Inquiry form and JavaScript

2. **Backend:**
   - `api/submit-inquiry.php` - Main handler
   - `api/InquiryMailer.php` - Email sender class

3. **Templates:**
   - `15_mail/templates/inquiry_mail.html` - Email template

4. **Configuration:**
   - `15_mail/.env` - Email settings

5. **Database:**
   - `docs/fix_database.sql` - Database schema

