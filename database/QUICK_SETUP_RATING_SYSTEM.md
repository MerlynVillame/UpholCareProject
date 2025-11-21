# Quick Setup: Store Rating System

## Problem
You're seeing the error: "Rating system is not set up yet. Please run the database migration"

## Solution
Run the setup script in your browser to create the required database table.

## Step 1: Run the Setup Script

Open your browser and navigate to:
```
http://localhost/UphoCare/database/create_store_ratings_table.php
```

## Step 2: Click "Create Store Ratings Table"

The script will:
1. ✅ Check if the table exists
2. ✅ Create the `store_ratings` table
3. ✅ Create triggers (optional - may fail, that's OK)
4. ✅ Show you the results

## Step 3: Verify Setup

After running the script, you should see:
- ✅ Table created successfully
- ✅ Triggers created (or warnings if they couldn't be created - this is OK)

## Step 4: Test the Rating System

1. Go to Store Locations page
2. Click "View Details" on any store
3. You should now be able to:
   - Rate the store (1-5 stars)
   - Add a review (optional)
   - See your rating
   - See other customers' reviews

## Troubleshooting

### Table Creation Fails
**Error:** "Table creation failed"

**Possible causes:**
- `store_locations` table doesn't exist
- `users` table doesn't exist
- Database permissions issue

**Solution:**
1. Make sure you have `store_locations` and `users` tables
2. Check database permissions
3. Run the SQL manually in phpMyAdmin if needed

### Triggers Fail to Create
**Error:** "Could not create trigger"

**This is OK!** Triggers are optional. The system will still work - it will update ratings manually when customers submit ratings.

### Rating Still Not Working
1. Check if table exists:
   ```sql
   SHOW TABLES LIKE 'store_ratings';
   ```

2. Check table structure:
   ```sql
   DESCRIBE store_ratings;
   ```

3. Clear browser cache and try again

## Manual Setup (Alternative)

If the PHP script doesn't work, you can run the SQL manually in phpMyAdmin:

1. Open phpMyAdmin
2. Select `db_upholcare` database
3. Go to SQL tab
4. Paste and run the SQL from `database/create_store_ratings_table.sql`

## What Gets Created

### store_ratings Table
- Stores customer ratings (1-5 stars)
- Stores review text (optional)
- Links to stores and users
- Prevents duplicate ratings (one per customer per store)

### Triggers (Optional)
- Automatically update store average rating when:
  - New rating is added
  - Rating is updated
  - Rating is deleted

**Note:** Triggers are optional. If they can't be created, the system will update ratings manually (already implemented in the controller).

## After Setup

Once the table is created:
- ✅ Customers can rate stores
- ✅ Ratings are displayed on store cards
- ✅ Reviews are shown in store details
- ✅ Store average rating updates automatically
- ✅ Customers can update their ratings

## Need Help?

If you're still having issues:
1. Check the browser console for JavaScript errors
2. Check server error logs
3. Verify database connection
4. Make sure you're logged in as a customer

