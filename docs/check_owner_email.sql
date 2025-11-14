-- Simple Query to Check Room Owner Emails
-- यो query ले room र owner को email देखाउँछ

-- Step 1: Database select गर्नुहोस्
USE roomfinder;

-- Step 2: Properties र Users table बाट data fetch गर्नुहोस्
SELECT 
    p.id AS room_id,           -- Room को ID
    p.title AS room_title,     -- Room को title/name
    u.email AS owner_email     -- Room owner को email
FROM properties p              -- properties table (p = short name)
LEFT JOIN users u              -- users table (u = short name) 
    ON p.user_id = u.id        -- properties.user_id = users.id (link गर्न)
LIMIT 5;                       -- पहिलो 5 rows मात्र देखाउँछ

-- यो query ले यो देखाउँछ:
-- 1. कुन room को ID कति छ
-- 2. Room को name के हो
-- 3. त्यो room को owner को email के हो

-- Example Output:
-- room_id | room_title        | owner_email
-- --------|-------------------|------------------
-- 1       | Cozy Apartment    | owner@email.com
-- 2       | Modern Studio     | owner2@email.com
-- 3       | Spacious Room     | NULL (यदि owner email छैन भने)

