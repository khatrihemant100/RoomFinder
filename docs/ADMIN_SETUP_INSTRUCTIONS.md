# Admin Panel Setup Instructions

## Step 1: Run Database Setup

First, run the SQL file to add admin functionality to your database:

```sql
-- Run this file: docs/add_admin_panel.sql
```

You can run it in phpMyAdmin or MySQL command line.

## Step 2: Create Admin User

You have two options:

### Option A: Using the Setup Script (Easiest)

1. Go to: `http://localhost/RoomFinder/admin/create_admin.php`
2. Fill in the form:
   - **Name**: Your full name
   - **Email**: Your email address (this will be your login email)
   - **Password**: Choose a strong password (minimum 6 characters)
   - **Confirm Password**: Enter the same password again
3. Click "Create Admin Account"
4. **IMPORTANT**: After creating the admin account, delete the file `admin/create_admin.php` for security!

### Option B: Using SQL (Manual)

If you already have a user account, you can make it an admin:

```sql
-- Make existing user an admin
UPDATE users SET is_admin = 1 WHERE email = 'your-email@example.com';
```

Or create a new admin user directly:

```sql
-- Create new admin user (replace with your details)
INSERT INTO users (name, email, password, role, is_admin) 
VALUES (
    'Admin Name', 
    'admin@example.com', 
    '$2y$10$YourHashedPasswordHere',  -- Use password_hash() in PHP
    'owner', 
    1
);
```

## Step 3: Login to Admin Panel

1. Go to: `http://localhost/RoomFinder/admin/login.php`
2. Enter your credentials:
   - **Email**: The email you used when creating the admin account
   - **Password**: The password you set
3. Click "Sign In"

## Default Login Credentials

**There are NO default credentials!** You must create your own admin account using one of the methods above.

## Troubleshooting

### Can't login?
- Make sure you ran `docs/add_admin_panel.sql` first
- Verify the user has `is_admin = 1` in the database
- Check that the email and password are correct
- Make sure the password was hashed using `password_hash()` function

### Check if user is admin:
```sql
SELECT id, name, email, is_admin FROM users WHERE email = 'your-email@example.com';
```

### Reset admin password:
If you need to reset the password, you can use the `create_admin.php` script again (if it still exists), or update directly in database:

```sql
-- Update password (replace with your email and new hashed password)
UPDATE users 
SET password = '$2y$10$NewHashedPasswordHere' 
WHERE email = 'your-email@example.com' AND is_admin = 1;
```

## Security Notes

1. **Delete `create_admin.php`** after creating your admin account
2. Use a strong password (at least 8 characters, mix of letters, numbers, symbols)
3. Don't share your admin credentials
4. Change your password regularly

