# Room Owner Email Check - Step by Step Guide

## Problem: Owner Email नभेटिएको

यदि inquiry submit गर्दा email पठाउन सकिँदैन भने, यो check गर्नुहोस्:

## Step 1: phpMyAdmin मा जानुहोस्

1. Browser मा खोल्नुहोस्: `http://localhost/phpmyadmin`
2. Left side मा `roomfinder` database select गर्नुहोस्

## Step 2: Properties Table Check गर्नुहोस्

SQL tab मा यो query run गर्नुहोस्:

```sql
SELECT id, title, user_id FROM properties LIMIT 5;
```

**यो देखाउँछ:**
- Room को ID
- Room को name
- Room को owner को user_id

**Check गर्नुहोस्:**
- `user_id` column मा number छ? (जस्तै: 1, 2, 3)
- यदि NULL छ भने, room को owner छैन!

## Step 3: Users Table Check गर्नुहोस्

```sql
SELECT id, name, email FROM users LIMIT 5;
```

**यो देखाउँछ:**
- User को ID
- User को name
- User को email

**Check गर्नुहोस्:**
- Email column मा valid email छ? (जस्तै: user@example.com)
- यदि NULL वा empty छ भने, email पठाउन सकिँदैन!

## Step 4: दुबै जोडेर Check गर्नुहोस्

```sql
SELECT 
    properties.id AS 'Room ID',
    properties.title AS 'Room Name',
    properties.user_id AS 'Owner User ID',
    users.name AS 'Owner Name',
    users.email AS 'Owner Email'
FROM properties
LEFT JOIN users ON properties.user_id = users.id
LIMIT 10;
```

**यो देखाउँछ:**
- Room को details
- Room owner को name
- Room owner को email

**Check गर्नुहोस्:**
- "Owner Email" column मा email छ?
- यदि NULL छ भने, त्यो room को लागि email पठाउन सकिँदैन!

## Step 5: Fix गर्नुहोस् (यदि email छैन भने)

### Option 1: User को email add गर्नुहोस्

```sql
-- User को email update गर्नुहोस्
UPDATE users 
SET email = 'owner@example.com' 
WHERE id = 1;
```

### Option 2: Room को user_id fix गर्नुहोस्

```sql
-- Room को owner set गर्नुहोस्
UPDATE properties 
SET user_id = 1 
WHERE id = 1;
```

## Step 6: Test गर्नुहोस्

फेरि query run गर्नुहोस्:

```sql
SELECT 
    properties.id,
    properties.title,
    users.email AS owner_email
FROM properties
LEFT JOIN users ON properties.user_id = users.id
WHERE properties.id = 1;  -- आफ्नो room को ID use गर्नुहोस्
```

यदि "owner_email" देखाउँछ भने, अब email पठाउन सकिन्छ!

## Common Problems:

### Problem 1: user_id NULL छ
**Solution:** Room को owner assign गर्नुहोस्
```sql
UPDATE properties SET user_id = 1 WHERE id = 1;
```

### Problem 2: User को email NULL छ
**Solution:** User को email add गर्नुहोस्
```sql
UPDATE users SET email = 'newemail@example.com' WHERE id = 1;
```

### Problem 3: user_id गलत छ (user exist गर्दैन)
**Solution:** Valid user_id use गर्नुहोस्
```sql
-- पहिले users देखाउँछ
SELECT id, name, email FROM users;

-- अब room को user_id fix गर्नुहोस्
UPDATE properties SET user_id = 2 WHERE id = 1;
```

## Quick Check Query (सबै rooms को लागि):

```sql
-- सबै rooms र owner emails देखाउँछ
SELECT 
    p.id AS room_id,
    p.title AS room_title,
    u.name AS owner_name,
    u.email AS owner_email,
    CASE 
        WHEN u.email IS NULL THEN '❌ Email Missing'
        WHEN u.email = '' THEN '❌ Email Empty'
        ELSE '✅ OK'
    END AS status
FROM properties p
LEFT JOIN users u ON p.user_id = u.id
ORDER BY p.id;
```

यो query ले सबै rooms देखाउँछ र owner email छ कि छैन भनेर status देखाउँछ!

