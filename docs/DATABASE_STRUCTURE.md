# RoomFinder Database Structure

## Overview
यो document मा RoomFinder को database structure को complete information छ।

## Database Name
`roomfinder`

## Tables

### 1. `users` - User Accounts
Stores all user account information.

**Columns:**
- `id` (INT, PRIMARY KEY, AUTO_INCREMENT) - Unique user ID
- `name` (VARCHAR(100)) - User's full name
- `email` (VARCHAR(100), UNIQUE) - User's email address
- `password` (VARCHAR(255)) - Hashed password
- `role` (ENUM: 'owner', 'seeker') - User role (property owner or room seeker)
- `profile_photo` (VARCHAR(255), NULL) - Profile photo filename
- `is_verified` (TINYINT(1), DEFAULT 0) - Verification status (1 = verified owner)
- `is_admin` (TINYINT(1), DEFAULT 0) - Admin status (1 = admin user)
- `created_at` (TIMESTAMP) - Account creation date

**Indexes:**
- `idx_users_admin` - For admin queries
- `idx_users_verified` - For verified user queries

---

### 2. `properties` - Property/Room Listings
Stores all property/room listings posted by owners.

**Columns:**
- `id` (INT, PRIMARY KEY, AUTO_INCREMENT) - Unique property ID
- `user_id` (INT, FOREIGN KEY → users.id) - Owner's user ID
- `title` (VARCHAR(255)) - Property title
- `location` (VARCHAR(255)) - Property location/address
- `price` (DECIMAL(10,2)) - Monthly rent price
- `utilities_cost` (DECIMAL(10,2), DEFAULT 0.00) - Additional utilities cost
- `management_fee` (DECIMAL(10,2), DEFAULT 0.00) - Management fee
- `deposit` (DECIMAL(10,2), DEFAULT 0.00) - Security deposit
- `key_money` (DECIMAL(10,2), DEFAULT 0.00) - Key money (Japan-specific)
- `type` (VARCHAR(50)) - Property type (e.g., "Apartment", "House")
- `train_station` (VARCHAR(255), NULL) - Nearest train station
- `status` (VARCHAR(50), DEFAULT 'available') - Availability status
- `is_approved` (TINYINT(1), DEFAULT 1) - Admin approval status
- `description` (TEXT) - Detailed property description
- `image_url` (VARCHAR(255)) - Main property image filename
- `created_at` (TIMESTAMP) - Listing creation date

**Indexes:**
- `idx_properties_status` - For filtering by status
- `idx_properties_location` - For location searches
- `idx_properties_price` - For price sorting
- `idx_properties_approved` - For admin approval queries

---

### 3. `inquiries` - Property Inquiries
Stores inquiries submitted by seekers for properties.

**Columns:**
- `id` (INT, PRIMARY KEY, AUTO_INCREMENT) - Unique inquiry ID
- `property_id` (INT, FOREIGN KEY → properties.id) - Property being inquired about
- `name` (VARCHAR(100)) - Inquirer's name
- `email` (VARCHAR(100)) - Inquirer's email
- `phone` (VARCHAR(50)) - Inquirer's phone number
- `visit_date` (DATE) - Preferred visit date
- `message` (TEXT) - Additional message from inquirer
- `created_at` (TIMESTAMP) - Inquiry submission date

**Note:** Column was renamed from `room_id` to `property_id` for clarity.

---

### 4. `messages` - In-App Messages
Stores messages between users (in-app messaging system).

**Columns:**
- `id` (INT, PRIMARY KEY, AUTO_INCREMENT) - Unique message ID
- `sender_id` (INT, FOREIGN KEY → users.id) - Message sender's user ID
- `receiver_id` (INT, FOREIGN KEY → users.id) - Message receiver's user ID
- `property_id` (INT, FOREIGN KEY → properties.id, NULL) - Related property (optional)
- `subject` (VARCHAR(255), NULL) - Message subject
- `message` (TEXT) - Message content
- `is_read` (TINYINT(1), DEFAULT 0) - Read status (1 = read)
- `created_at` (TIMESTAMP) - Message creation date

**Indexes:**
- `idx_messages_conversation` - For conversation queries
- `idx_messages_unread` - For unread message queries

**Note:** Column was renamed from `room_id` to `property_id` for clarity.

---

### 5. `contacts` - Contact Form Submissions
Stores submissions from the contact form.

**Columns:**
- `id` (INT, PRIMARY KEY, AUTO_INCREMENT) - Unique contact ID
- `name` (VARCHAR(100)) - Contact's name
- `email` (VARCHAR(100)) - Contact's email
- `message` (TEXT) - Contact message
- `created_at` (TIMESTAMP) - Submission date

---

### 6. `admin_settings` - Admin Panel Settings
Stores admin panel configuration settings.

**Columns:**
- `id` (INT, PRIMARY KEY, AUTO_INCREMENT) - Unique setting ID
- `setting_key` (VARCHAR(100), UNIQUE) - Setting name/key
- `setting_value` (TEXT) - Setting value
- `updated_at` (TIMESTAMP) - Last update date

**Default Settings:**
- `site_name` - "RoomFinder"
- `site_email` - "admin@roomfinder.com"
- `items_per_page` - "20"
- `auto_approve_properties` - "0"

---

### 7. `admin_logs` - Admin Action Logs
Stores logs of admin actions for audit trail.

**Columns:**
- `id` (INT, PRIMARY KEY, AUTO_INCREMENT) - Unique log ID
- `admin_id` (INT, FOREIGN KEY → users.id) - Admin user who performed action
- `action` (VARCHAR(100)) - Action performed (e.g., "verify_user", "delete_property")
- `target_type` (VARCHAR(50)) - Type of target (e.g., "user", "property")
- `target_id` (INT, NULL) - ID of target item
- `details` (TEXT, NULL) - Additional details
- `ip_address` (VARCHAR(45), NULL) - Admin's IP address
- `created_at` (TIMESTAMP) - Action date/time

---

## Removed Tables

### `rooms` (DELETED)
This table was a legacy table that was replaced by `properties`. It has been removed from the database.

---

## Database Setup

### Initial Setup
Run `docs/database_cleanup_and_rename.sql` to:
1. Delete unused tables
2. Rename columns for clarity (`room_id` → `property_id`)
3. Ensure all required columns exist
4. Create missing tables
5. Create/update indexes

### Migration Notes
- `inquiries.room_id` → `inquiries.property_id`
- `messages.room_id` → `messages.property_id`
- `rooms` table deleted (use `properties` instead)

---

## Relationships

```
users (1) ──→ (many) properties
users (1) ──→ (many) messages (as sender)
users (1) ──→ (many) messages (as receiver)
properties (1) ──→ (many) inquiries
properties (1) ──→ (many) messages (optional)
users (1) ──→ (many) admin_logs
```

---

## Common Queries

### Get all properties with owner info:
```sql
SELECT p.*, u.name as owner_name, u.email as owner_email, u.is_verified
FROM properties p
LEFT JOIN users u ON p.user_id = u.id
WHERE p.status = 'available' AND p.is_approved = 1
ORDER BY p.created_at DESC;
```

### Get inquiries for a property:
```sql
SELECT i.*, p.title as property_title
FROM inquiries i
JOIN properties p ON i.property_id = p.id
WHERE p.id = ?
ORDER BY i.created_at DESC;
```

### Get messages between two users:
```sql
SELECT m.*, 
       s.name as sender_name, 
       r.name as receiver_name
FROM messages m
JOIN users s ON m.sender_id = s.id
JOIN users r ON m.receiver_id = r.id
WHERE (m.sender_id = ? AND m.receiver_id = ?) 
   OR (m.sender_id = ? AND m.receiver_id = ?)
ORDER BY m.created_at ASC;
```

---

## Notes
- All timestamps use `TIMESTAMP` type with `DEFAULT CURRENT_TIMESTAMP`
- Foreign keys use `ON DELETE CASCADE` or `ON DELETE SET NULL` as appropriate
- All text fields support UTF-8 (utf8mb4 charset)
- Boolean values use `TINYINT(1)` (0 = false, 1 = true)

