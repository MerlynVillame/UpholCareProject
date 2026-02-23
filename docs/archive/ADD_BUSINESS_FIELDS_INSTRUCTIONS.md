# How to Add Business Name to Admin Registration Database

## Quick Fix - Add Business Name Only

### Step 1: Open phpMyAdmin

Go to: `http://localhost/phpmyadmin`

### Step 2: Select Database

Click on `db_upholcare` in the left sidebar

### Step 3: Run SQL

1. Click on the **SQL** tab at the top
2. Copy and paste this SQL:

```sql
USE db_upholcare;

ALTER TABLE `admin_registrations`
ADD COLUMN `business_name` VARCHAR(255) NULL AFTER `phone`;
```

3. Click **Go** button

### Step 4: Verify

You should see a success message. The `business_name` column has been added!

---

## Complete Solution - Add All Business Fields

If you want to add ALL business fields (not just business_name), run this instead:

```sql
USE db_upholcare;

ALTER TABLE `admin_registrations`
ADD COLUMN `business_name` VARCHAR(255) NULL AFTER `phone`,
ADD COLUMN `business_address` TEXT NULL AFTER `business_name`,
ADD COLUMN `business_city` VARCHAR(100) NULL DEFAULT 'Bohol' AFTER `business_address`,
ADD COLUMN `business_province` VARCHAR(100) NULL DEFAULT 'Bohol' AFTER `business_city`,
ADD COLUMN `business_latitude` DECIMAL(10, 8) NULL AFTER `business_province`,
ADD COLUMN `business_longitude` DECIMAL(11, 8) NULL AFTER `business_latitude`,
ADD COLUMN `business_permit_path` VARCHAR(255) NULL AFTER `business_longitude`,
ADD COLUMN `business_permit_filename` VARCHAR(255) NULL AFTER `business_permit_path`;

ALTER TABLE `admin_registrations`
ADD INDEX `idx_business_location` (`business_latitude`, `business_longitude`),
ADD INDEX `idx_business_city` (`business_city`);
```

---

## Alternative: Run Migration Script in Browser

1. Open your browser
2. Go to: `http://localhost/UphoCare/database/run_business_fields_migration.php`
3. The script will automatically add all missing columns
4. You'll see a success message when done

---

## Verify It Worked

Run this SQL to check:

```sql
DESCRIBE admin_registrations;
```

You should see `business_name` (and other business fields if you added them) in the list.

---

## Troubleshooting

### Error: "Duplicate column name 'business_name'"

- The column already exists! You can skip this step.

### Error: "Table 'admin_registrations' doesn't exist"

- You need to create the admin_registrations table first
- Run: `database/create_super_admin_system.sql`

### Error: "Access denied"

- Check your database user permissions
- Make sure you're using the correct database user
