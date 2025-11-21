# Store Rating System Setup Guide

## Overview
This guide explains how to set up the star rating system that allows customers to rate stores.

## Database Setup

### Step 1: Create Store Ratings Table
Run the SQL script to create the `store_ratings` table:

```sql
-- Run this file: database/create_store_ratings_table.sql
```

Or manually execute:
```sql
USE db_upholcare;

CREATE TABLE IF NOT EXISTS store_ratings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    store_id INT NOT NULL,
    user_id INT NOT NULL,
    rating DECIMAL(2, 1) NOT NULL COMMENT 'Rating from 1.0 to 5.0',
    review_text TEXT NULL COMMENT 'Optional review text',
    status ENUM('active', 'hidden') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (store_id) REFERENCES store_locations(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_store_user_rating (store_id, user_id),
    INDEX idx_store_id (store_id),
    INDEX idx_user_id (user_id),
    INDEX idx_rating (rating),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Step 2: Create Triggers (Optional but Recommended)
The triggers automatically update the store's average rating when ratings are added, updated, or deleted.

The SQL script includes three triggers:
1. `update_store_rating_on_insert` - Updates rating when a new rating is added
2. `update_store_rating_on_update` - Updates rating when a rating is updated
3. `update_store_rating_on_delete` - Updates rating when a rating is deleted

## Features

### 1. Star Rating Interface
- Interactive 5-star rating system
- Click stars to select rating (1-5)
- Hover effect to preview rating
- Visual feedback (filled/empty stars)
- Rating labels: Poor, Fair, Good, Very Good, Excellent

### 2. Review System
- Optional review text
- Customers can leave written feedback
- Reviews are displayed with ratings
- Shows customer name and date

### 3. Rating Management
- One rating per customer per store
- Customers can update their existing rating
- Ratings are automatically calculated and displayed
- Average rating is shown on store details

### 4. Display Features
- Average rating displayed on store cards
- Total number of ratings shown
- Recent reviews displayed in store details modal
- Ratings sorted by date (newest first)

## Usage

### For Customers

1. **View Store Details:**
   - Go to Store Locations page
   - Click on a store marker or "View Details" button
   - Store details modal will open

2. **Rate a Store:**
   - In the store details modal, scroll to "Rate This Store" section
   - Click on stars to select your rating (1-5)
   - Optionally add a review text
   - Click "Submit Rating" button

3. **Update Rating:**
   - If you've already rated a store, you'll see your current rating
   - Click "Update Rating" to change your rating
   - Modify your rating and/or review
   - Click "Update Rating" to save changes

4. **View Reviews:**
   - Scroll to "Recent Reviews" section in store details
   - See all customer reviews and ratings
   - Reviews show rating, text, customer name, and date

### For Administrators

1. **View Store Ratings:**
   - Store ratings are automatically displayed on store locations page
   - Average rating is calculated automatically
   - Total ratings count is shown

2. **Monitor Ratings:**
   - Check `store_ratings` table for all ratings
   - Ratings are linked to stores and customers
   - Status field can be used to hide inappropriate ratings

## API Endpoints

### Submit Rating
- **URL:** `/customer/submitStoreRating`
- **Method:** POST
- **Parameters:**
  - `store_id` (required): Store ID
  - `rating` (required): Rating from 1 to 5
  - `review_text` (optional): Review text
- **Response:** JSON with success status and message

### Get User Rating
- **URL:** `/customer/getUserRating?store_id={store_id}`
- **Method:** GET
- **Parameters:**
  - `store_id` (required): Store ID
- **Response:** JSON with user's rating for the store

### Get Store Reviews
- **URL:** `/customer/getStoreReviews?store_id={store_id}`
- **Method:** GET
- **Parameters:**
  - `store_id` (required): Store ID
- **Response:** JSON with list of reviews (latest 10)

## Database Schema

### store_ratings Table
- `id`: Primary key
- `store_id`: Foreign key to store_locations
- `user_id`: Foreign key to users
- `rating`: Rating value (1.0 to 5.0)
- `review_text`: Optional review text
- `status`: Rating status (active/hidden)
- `created_at`: Creation timestamp
- `updated_at`: Update timestamp

### store_locations Table
- `rating`: Average rating (automatically updated by triggers)
- Other store information fields

## Troubleshooting

### Ratings Not Updating
1. Check if triggers are created:
   ```sql
   SHOW TRIGGERS LIKE 'update_store_rating%';
   ```

2. Manually update rating if triggers fail:
   ```sql
   UPDATE store_locations 
   SET rating = (
       SELECT AVG(rating)
       FROM store_ratings
       WHERE store_id = store_locations.id AND status = 'active'
   );
   ```

### Rating Form Not Showing
1. Check if user is logged in (customer role required)
2. Check browser console for JavaScript errors
3. Verify AJAX endpoints are accessible

### Database Errors
1. Check if `store_ratings` table exists
2. Verify foreign key constraints
3. Check database connection
4. Review error logs for detailed messages

## Security

- Ratings are tied to user accounts (prevents spam)
- One rating per customer per store (prevents multiple ratings)
- XSS protection for review text (HTML escaped)
- SQL injection prevention (prepared statements)
- User authentication required (customer role)

## Future Enhancements

- Admin moderation of reviews
- Report inappropriate reviews
- Filter reviews by rating
- Sort reviews by helpfulness
- Reply to reviews (store response)
- Rating analytics and reports

