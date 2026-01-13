# Scraped Data Import Guide

## Overview
This script imports room data from JSON files (scraped from Suumo) into the RoomFinder database.

## Files
- `import-scraped-data.php` - Main import script
- JSON files in `../data/` folder (e.g., `suumo_14.json`, `suumo_13_13104.json`)

## How to Use

### 1. Make sure your database is set up
- Database name: `roomfinder`
- Table: `properties` (should already exist)

### 2. Run the import script
Open your browser and navigate to:
```
http://localhost/RoomFinder/scraper/import-scraped-data.php
```

Or run from command line:
```bash
cd c:\xampp\htdocs\RoomFinder\scraper
php import-scraped-data.php
```

### 3. Check the results
- The script will show how many properties were inserted
- Check `find-rooms.php` to see the imported rooms

## What the script does:
- ✅ Reads all `suumo_*.json` files from the `data/` folder
- ✅ Maps scraped data to database fields:
  - Title, Location, Train Station
  - Price (rent)
  - Type (layout)
  - Management Fee, Deposit, Key Money
  - Description (auto-generated from layout, area, age, access)
- ✅ Adds placeholder images for rooms
- ✅ Sets status as "available" by default
- ✅ Uses user_id = 1 for scraped properties

## Notes:
- Images: Currently uses placeholder images. You can update them later through the admin panel.
- Duplicates: The script doesn't check for duplicates. If you run it multiple times, it will insert duplicates.
- User ID: All scraped properties are assigned to user_id = 1. Make sure this user exists in your database.

## Troubleshooting:
- If you get "Connection failed": Check your database credentials in `db.php`
- If no JSON files found: Make sure JSON files are in the `data/` folder
- If properties don't show: Check `find-rooms.php` and verify the database connection
