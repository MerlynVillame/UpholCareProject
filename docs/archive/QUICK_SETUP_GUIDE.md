# Quick Setup Guide: Verification Codes System

## Overview
This system uses pre-generated verification codes (1000-9999) that are automatically assigned when the Super Admin approves admin registrations.

## Step-by-Step Setup

### Step 1: Run the Admin Registrations Migration
First, add the verification code columns to the `admin_registrations` table:

```sql
-- Run in phpMyAdmin or MySQL
USE db_upholcare;
SOURCE database/add_verification_code_to_admin_registrations.sql;
```

Or copy/paste the contents of `database/add_verification_code_to_admin_registrations.sql` into phpMyAdmin.

### Step 2: Create and Populate Verification Codes Table
Create the verification codes table and populate it with codes 1000-9999:

```sql
-- Run in phpMyAdmin or MySQL
USE db_upholcare;
SOURCE database/setup_verification_codes_complete.sql;
```

Or copy/paste the contents of `database/setup_verification_codes_complete.sql` into phpMyAdmin.

### Step 3: Verify Setup
Check that everything is set up correctly:

```sql
-- Check verification codes table
SELECT COUNT(*) as total_codes FROM admin_verification_codes;
-- Should return: 9000

-- Check available codes
SELECT COUNT(*) as available FROM admin_verification_codes WHERE status = 'available';
-- Should return: 9000 (initially)

-- Check admin_registrations table has verification_code column
DESCRIBE admin_registrations;
-- Should show: verification_code, verification_code_sent_at, etc.
```

## How It Works

1. **Super Admin approves pending admin** → System automatically:
   - Selects next available code (1000, 1001, 1002, etc.) from `admin_verification_codes` table
   - Marks code as "used" and links it to the registration
   - Stores code in `admin_registrations.verification_code`
   - Sends code via email to the admin
   - Code appears on verification page

2. **Admin receives code** → Can:
   - Check email for the code
   - View code on verification page
   - Enter code to complete verification

## Files Created

1. **database/create_admin_verification_codes_table.sql** - Creates table structure (advanced)
2. **database/setup_verification_codes_complete.sql** - Complete setup (recommended)
3. **database/populate_verification_codes_simple.sql** - Simple population script (alternative)
4. **database/README_VERIFICATION_CODES_SETUP.md** - Detailed documentation

## Troubleshooting

### Error: "No available codes found"
- Run `database/setup_verification_codes_complete.sql` to populate codes
- Check: `SELECT COUNT(*) FROM admin_verification_codes WHERE status = 'available';`

### Error: "Column verification_code not found"
- Run `database/add_verification_code_to_admin_registrations.sql` first
- Check: `DESCRIBE admin_registrations;`

### Codes Not Being Assigned
- Verify both migrations have been run
- Check application error logs
- Verify `admin_verification_codes` table exists and has available codes

## Benefits

✅ **Pre-generated codes** - All codes (1000-9999) are ready to use  
✅ **No duplicates** - Database ensures unique code assignment  
✅ **Automatic assignment** - Super Admin doesn't need to enter codes manually  
✅ **Full tracking** - Every code assignment is logged  
✅ **Email integration** - Codes are automatically sent via email  

## Next Steps

After setup:
1. Test by approving a pending admin registration
2. Verify the code is assigned from the dataset (1000-9999 range)
3. Check that email is sent successfully
4. Verify code appears on verification page

---

**Need Help?** Check `database/README_VERIFICATION_CODES_SETUP.md` for detailed documentation.

