# Quick Database Check Guide

## आफ्नो Database मा यो Check गर्नुहोस्:

### Step 1: Tables Check गर्नुहोस्

phpMyAdmin मा left side मा `roomfinder` database देखाउँछ:
- ✅ `properties` - Room listings को लागि
- ✅ `users` - User accounts को लागि  
- ❓ `inquiries` - Inquiry form submissions को लागि (यो table चाहिन्छ!)
- ℹ️ `contacts` - Contact form को लागि (optional)
- ℹ️ `rooms` - (यो optional हो)

### Step 2: Inquiries Table Check गर्नुहोस्

**यदि `inquiries` table छैन भने:**

1. SQL tab मा जानुहोस्
2. `docs/fix_database_simple.sql` file को content copy गर्नुहोस्
3. Run गर्नुहोस्

**यदि `inquiries` table छ भने:**
- ✅ Good! Inquiry save हुन्छ

### Step 3: Properties Table Check गर्नुहोस्

1. `properties` table click गर्नुहोस्
2. Structure tab मा जानुहोस्
3. यो columns हुनुपर्छ:
   - ✅ `id`
   - ✅ `title`
   - ✅ `location`
   - ✅ `price`
   - ✅ `user_id` (यो important छ - owner को ID)
   - ❓ `train_station` (optional)
   - ❓ `status` (optional)

### Step 4: Users Table Check गर्नुहोस्

1. `users` table click गर्नुहोस्
2. Structure tab मा जानुहोस्
3. यो columns हुनुपर्छ:
   - ✅ `id`
   - ✅ `name`
   - ✅ `email` (यो important छ - email पठाउन)
   - ✅ `password`

### Step 5: Owner Email Check गर्नुहोस्

SQL tab मा यो query run गर्नुहोस्:

```sql
SELECT 
    p.id,
    p.title,
    p.user_id,
    u.name AS owner_name,
    u.email AS owner_email
FROM properties p
LEFT JOIN users u ON p.user_id = u.id
LIMIT 5;
```

**Check गर्नुहोस्:**
- `owner_email` column मा email देखाउँछ?
- यदि NULL छ भने, त्यो room को लागि email पठाउन सकिँदैन!

### Step 6: Quick Fix (यदि चाहिन्छ भने)

**यदि `inquiries` table छैन:**

```sql
CREATE TABLE inquiries (
  id int(11) NOT NULL AUTO_INCREMENT,
  room_id int(11) DEFAULT NULL,
  name varchar(100) DEFAULT NULL,
  email varchar(100) DEFAULT NULL,
  phone varchar(50) DEFAULT NULL,
  visit_date date DEFAULT NULL,
  message text DEFAULT NULL,
  created_at timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (id),
  KEY room_id (room_id),
  CONSTRAINT inquiries_ibfk_1 FOREIGN KEY (room_id) REFERENCES properties (id) ON DELETE CASCADE
);
```

**यदि properties मा `user_id` NULL छ:**

```sql
-- पहिले users देखाउँछ
SELECT id, name, email FROM users;

-- अब room को owner set गर्नुहोस्
UPDATE properties SET user_id = 1 WHERE id = 1;
```

**यदि users मा email छैन:**

```sql
-- User को email add गर्नुहोस्
UPDATE users SET email = 'owner@example.com' WHERE id = 1;
```

## Complete Verification Query

SQL tab मा यो run गर्नुहोस् (सबै एकैचोटी check गर्छ):

```sql
-- 1. Tables check
SHOW TABLES;

-- 2. Properties with owner emails
SELECT 
    p.id AS 'Room ID',
    p.title AS 'Room Name',
    u.email AS 'Owner Email',
    CASE 
        WHEN u.email IS NULL THEN '❌ No Email'
        ELSE '✅ OK'
    END AS 'Status'
FROM properties p
LEFT JOIN users u ON p.user_id = u.id;
```

## Summary

Inquiry email system को लागि चाहिने:
1. ✅ `inquiries` table (inquiry save गर्न)
2. ✅ `properties.user_id` (room को owner identify गर्न)
3. ✅ `users.email` (owner लाई email पठाउन)

यी सबै छ भने, inquiry email system काम गर्छ!

