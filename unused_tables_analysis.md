# Unused Tables Analysis - db_upholcare Database

## Summary
This document identifies tables that exist in the `db_upholcare` database but are **NOT actively used** in the current codebase.

---

## ✅ TABLES THAT ARE USED (Active)

### Core Application Tables:
1. **users** - ✅ Used in `models/User.php`
2. **bookings** - ✅ Used in `models/Booking.php`
3. **services** - ✅ Used in `models/Service.php`
4. **service_categories** - ✅ Used in `models/Service.php`
5. **store_locations** - ✅ Used in `models/Store.php`
6. **inventory** - ✅ Used in `models/Inventory.php`
7. **notifications** - ✅ Used in `controllers/CustomerController.php`
8. **payments** - ✅ Used in `controllers/CustomerController.php`
9. **quotations** - ✅ Used in `models/Quotation.php` and `controllers/CustomerController.php`
10. **repair_items** - ✅ Used in `controllers/CustomerController.php` and `controllers/AdminController_RepairWorkflow.php`
11. **store_ratings** - ✅ Used in `controllers/CustomerController.php` (created dynamically if missing)
12. **store_compliance_reports** - ✅ Used in `controllers/ControlPanelController.php`

### Control Panel Tables:
13. **control_panel_admins** - ✅ Used in control panel system
14. **login_logs** - ✅ Used in control panel system
15. **system_activities** - ✅ Used in control panel system
16. **system_statistics** - ✅ Used in control panel system

### Admin Registration System:
17. **admin_registrations** - ✅ Used extensively in `controllers/AuthController.php` and `controllers/ControlPanelController.php`
18. **admin_verification_codes** - ✅ Used in `controllers/ControlPanelController.php`
19. **super_admin_activity** - ✅ Used in `controllers/ControlPanelController.php`

---

## ❌ TABLES THAT ARE NOT USED (Unused/Orphaned)

Based on codebase analysis, the following tables exist in the database but are **NOT referenced** in any model, controller, or view files:

### 1. **admin_booking_activity**
- **Status**: ❌ NOT USED
- **Purpose**: Appears to be for tracking admin booking activities
- **Found in**: Database only
- **Recommendation**: Can be safely removed if no historical data is needed

### 2. **admin_sales_activity**
- **Status**: ❌ NOT USED
- **Purpose**: Appears to be for tracking admin sales activities
- **Found in**: Database only
- **Recommendation**: Can be safely removed if no historical data is needed

### 3. **admin_verification_keys**
- **Status**: ❌ NOT USED
- **Purpose**: Appears to be for storing verification keys (different from verification_codes)
- **Found in**: Database only
- **Note**: The system uses `admin_verification_codes` instead
- **Recommendation**: Can be safely removed if not needed

### 4. **main_admin_panel**
- **Status**: ❌ NOT USED
- **Purpose**: Unknown - appears to be a legacy table
- **Found in**: Database only
- **Recommendation**: Can be safely removed if no data is needed

### 5. **role_permissions**
- **Status**: ❌ NOT USED
- **Purpose**: Appears to be for role-based access control (RBAC)
- **Found in**: Database only
- **Note**: The system currently uses simple role enum ('admin', 'customer') in users table
- **Recommendation**: Can be safely removed if RBAC is not implemented

---

## 📊 Summary Statistics

- **Total Tables in Database**: 23
- **Tables Actively Used**: 19
- **Tables Not Used**: 5
- **Usage Rate**: 82.6%

---

## 🗑️ Recommended Actions

### Option 1: Remove Unused Tables (Recommended if no data needed)
```sql
-- Backup first!
DROP TABLE IF EXISTS `admin_booking_activity`;
DROP TABLE IF EXISTS `admin_sales_activity`;
DROP TABLE IF EXISTS `admin_verification_keys`;
DROP TABLE IF EXISTS `main_admin_panel`;
DROP TABLE IF EXISTS `role_permissions`;
```

### Option 2: Keep for Future Use
If you plan to implement features that might use these tables:
- Keep `role_permissions` if planning RBAC system
- Keep `admin_booking_activity` and `admin_sales_activity` if planning activity tracking
- Keep `admin_verification_keys` if planning alternative verification system

### Option 3: Archive and Remove
1. Export data from unused tables
2. Store in backup file
3. Drop the tables
4. Document what was removed

---

## ⚠️ Important Notes

1. **Before Removing**: Always backup your database first!
2. **Check Dependencies**: Verify no foreign keys reference these tables
3. **Test Environment**: Test removal in development environment first
4. **Documentation**: Update any documentation that references these tables

---

## 🔍 How to Verify

To verify a table is truly unused, search the codebase:
```bash
# Search for table references
grep -r "admin_booking_activity" .
grep -r "admin_sales_activity" .
grep -r "admin_verification_keys" .
grep -r "main_admin_panel" .
grep -r "role_permissions" .
```

If no results are found, the table is likely unused.

---

**Generated**: December 2025
**Database**: db_upholcare
**Analysis Method**: Codebase grep search across models, controllers, and views

