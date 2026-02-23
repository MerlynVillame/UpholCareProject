# Automatic Table Creation for Store Ratings

## Overview
The rating system now automatically creates the `store_ratings` table when needed. You don't need to manually run any migrations - the system will create the table automatically when a customer tries to rate a store.

## How It Works

### Automatic Creation
When a customer tries to submit a rating:
1. System checks if `store_ratings` table exists
2. If it doesn't exist, the system automatically creates it
3. Table is created with all required fields and indexes
4. Foreign keys are added if possible (optional)
5. Rating is saved successfully

### Manual Setup (Alternative)
If you prefer to set up the table manually, you can:
1. Run the PHP script: `http://localhost/UphoCare/database/create_store_ratings_table.php`
2. Or run the SQL file in phpMyAdmin: `database/create_store_ratings_table.sql`

## Table Structure

### store_ratings Table
- `id`: Primary key
- `store_id`: Store ID (INT, NOT NULL)
- `user_id`: User ID (INT, NOT NULL)
- `rating`: Rating value (DECIMAL(2,1), 1.0 to 5.0)
- `review_text`: Optional review text (TEXT, NULL)
- `status`: Rating status (ENUM: 'active', 'hidden')
- `created_at`: Creation timestamp
- `updated_at`: Update timestamp
- `unique_store_user_rating`: Unique constraint (one rating per user per store)

### Indexes
- `idx_store_id`: Index on store_id
- `idx_user_id`: Index on user_id
- `idx_rating`: Index on rating
- `idx_created_at`: Index on created_at

### Foreign Keys (Optional)
- `fk_store_ratings_store_id`: Links to store_locations(id)
- `fk_store_ratings_user_id`: Links to users(id)

**Note:** Foreign keys are optional. The table will work without them, but they help maintain data integrity.

## Benefits of Automatic Creation

1. **No Manual Setup Required**: Table is created automatically when needed
2. **Error Handling**: System handles table creation errors gracefully
3. **Fallback Support**: If automatic creation fails, helpful error messages guide you
4. **Flexible**: Works even if foreign keys can't be created

## Testing

To test the automatic creation:
1. Log in as a customer
2. Go to Store Locations page
3. Click "View Details" on any store
4. Try to submit a rating
5. The table will be created automatically if it doesn't exist

## Troubleshooting

### Table Creation Fails
If automatic creation fails:
1. Check database permissions
2. Verify `store_locations` and `users` tables exist
3. Run the manual setup script: `database/create_store_ratings_table.php`
4. Check error logs for detailed error messages

### Foreign Keys Fail
If foreign keys can't be created:
- This is OK - the table will still work
- Foreign keys are optional
- The system will function normally without them

### Rating Still Not Working
1. Check if table was created:
   ```sql
   SHOW TABLES LIKE 'store_ratings';
   ```

2. Check table structure:
   ```sql
   DESCRIBE store_ratings;
   ```

3. Check for errors in browser console and server logs

## Manual Verification

To verify the table was created:
```sql
-- Check if table exists
SHOW TABLES LIKE 'store_ratings';

-- Check table structure
DESCRIBE store_ratings;

-- Check if there are any ratings
SELECT COUNT(*) FROM store_ratings;

-- Check ratings for a specific store
SELECT * FROM store_ratings WHERE store_id = 1;
```

## Next Steps

Once the table is created (automatically or manually):
1. ✅ Customers can rate stores
2. ✅ Ratings are displayed on store cards
3. ✅ Reviews are shown in store details
4. ✅ Store average rating updates automatically
5. ✅ Customers can update their ratings

The rating system is now fully functional!

