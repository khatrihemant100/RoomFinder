-- Very Simple Query - Step by Step
-- यो सबैभन्दा सरल तरिका हो

-- 1. पहिले properties table देखाउँछ
SELECT id, title, user_id FROM properties LIMIT 5;

-- 2. अब users table देखाउँछ  
SELECT id, name, email FROM users LIMIT 5;

-- 3. अब दुबै जोडेर देखाउँछ (यो मुख्य query हो)
SELECT 
    properties.id AS 'Room ID',
    properties.title AS 'Room Name',
    users.email AS 'Owner Email'
FROM properties
LEFT JOIN users ON properties.user_id = users.id
LIMIT 5;

-- यदि "Owner Email" column मा NULL देखाउँछ भने:
-- - Room को owner को email database मा छैन
-- - वा user_id गलत छ
-- - त्यसैले email पठाउन सकिँदैन

