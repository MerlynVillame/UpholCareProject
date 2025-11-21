# How to Run Database Migrations

## Option 1: Using phpMyAdmin (Recommended)

### Step 1: Add Verification Code Columns
1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Select database: `db_upholcare`
3. Click on "SQL" tab
4. Open file: `database/add_verification_code_to_admin_registrations.sql`
5. Copy and paste the entire contents into the SQL tab
6. Click "Go" to execute
7. You should see: "✅ Verification code fields added to admin_registrations table!"

### Step 2: Create Verification Codes Table
1. In phpMyAdmin, make sure you're still on database: `db_upholcare`
2. Click on "SQL" tab
3. Open file: `database/setup_verification_codes_complete.sql`
4. Copy and paste the entire contents into the SQL tab
5. Click "Go" to execute
6. You should see: "✅ Admin Verification Codes System Setup Complete!"
7. You should see: "✅ 9000 verification codes (1000-9999) are now available!"

## Option 2: Using PHP Script (Command Line)

### Run the migration script:
```bash
php database/run_migrations.php
```

Or visit in browser:
```
http://localhost/UphoCare/database/run_migrations.php
```

## Option 3: Using MySQL Command Line (if MySQL is in PATH)

### For XAMPP (Windows):
```bash
cd C:\xampp\mysql\bin
mysql.exe -u root -p db_upholcare < "C:\xampp\htdocs\UphoCare\database\add_verification_code_to_admin_registrations.sql"
mysql.exe -u root -p db_upholcare < "C:\xampp\htdocs\UphoCare\database\setup_verification_codes_complete.sql"
```

## Verification

After running migrations, verify with these SQL queries:

```sql
-- Check verification_code column exists
DESCRIBE admin_registrations;

-- Check verification codes table
SELECT COUNT(*) as total_codes FROM admin_verification_codes;
-- Should return: 9000

-- Check available codes
SELECT COUNT(*) as available FROM admin_verification_codes WHERE status = 'available';
-- Should return: 9000 (initially)

-- View sample codes
SELECT * FROM admin_verification_codes ORDER BY verification_code LIMIT 10;
```

## Troubleshooting

### Error: "Duplicate column name 'verification_code'"
- The column already exists. This is okay, the migration will skip it.

### Error: "Table 'admin_verification_codes' already exists"
- The table already exists. This is okay, the migration will skip it.

### Error: "Unknown database 'db_upholcare'"
- Make sure the database name is correct in your configuration.
- Check your database name in `config/database.php`

### Codes not populating
- The INSERT statement might take a moment to complete.
- Check if codes exist: `SELECT COUNT(*) FROM admin_verification_codes;`
- If count is 0, try running the population script again.

## Success Indicators

✅ You should see:
- "Verification code fields added to admin_registrations table!"
- "Admin Verification Codes System Setup Complete!"
- "9000 verification codes (1000-9999) are now available!"
- 9000 rows in `admin_verification_codes` table
- `verification_code` column in `admin_registrations` table

