# Super Admin Activity Table Connections

## Overview

The `super_admin_activity` table **HAS connections** to other tables through foreign keys. Here's the complete relationship structure:

---

## 🔗 FOREIGN KEY RELATIONSHIPS

### 1. **super_admin_id** → **users(id)**

```sql
FOREIGN KEY (super_admin_id) REFERENCES users(id) ON DELETE CASCADE
```

**Connection:**

- `super_admin_activity.super_admin_id` → `users.id`
- **Meaning**: Each activity record is linked to the super admin user who performed the action
- **On Delete**: CASCADE (if user is deleted, their activity records are also deleted)

**Example:**

- Super Admin "Maria" (users.id = 1) approves an admin
- Record created: `super_admin_id = 1`, `super_admin_name = "Maria"`

---

### 2. **target_admin_id** → **users(id)** (Optional)

```sql
FOREIGN KEY (target_admin_id) REFERENCES users(id) ON DELETE SET NULL
```

**Connection:**

- `super_admin_activity.target_admin_id` → `users.id`
- **Meaning**: Links to the target admin user being acted upon (if applicable)
- **On Delete**: SET NULL (if target user is deleted, the reference becomes NULL)
- **Note**: This column may be NULL if the target is not yet a user (e.g., pending registration)

**Example:**

- Super Admin approves admin registration for "Merlyn"
- If Merlyn already has a user account: `target_admin_id = [merlyn's user id]`
- If Merlyn is still pending: `target_admin_id = NULL`

---

## 📊 VISUAL RELATIONSHIP DIAGRAM

```
┌─────────────────┐
│     users       │ (Parent Table)
│   (id: PK)      │
└────────┬────────┘
         │
         ├──────────────────────────────┐
         │                              │
    ┌────▼──────────────────┐    ┌─────▼──────────────┐
    │ super_admin_activity  │    │ admin_registrations│
    │                        │    │                    │
    │ super_admin_id (FK) ───┼────┤ (soft reference)   │
    │ target_admin_id (FK) ──┼────┤                    │
    └────────────────────────┘    └────────────────────┘
```

---

## 🔍 DETAILED CONNECTIONS

### Direct Foreign Key Connections:

| Column            | Foreign Key | References | Table     | On Delete |
| ----------------- | ----------- | ---------- | --------- | --------- |
| `super_admin_id`  | ✅ YES      | `users.id` | **users** | CASCADE   |
| `target_admin_id` | ✅ YES      | `users.id` | **users** | SET NULL  |

### Soft References (No Foreign Key):

| Column              | Soft Reference   | Table                    | Relationship            |
| ------------------- | ---------------- | ------------------------ | ----------------------- |
| `target_admin_name` | Email/Name match | **admin_registrations**  | Links via name/email    |
| `super_admin_id`    | User lookup      | **control_panel_admins** | Can link via user email |

---

## 📋 TABLE STRUCTURE

```sql
CREATE TABLE super_admin_activity (
    id INT AUTO_INCREMENT PRIMARY KEY,
    super_admin_id INT NOT NULL,              -- FK → users(id)
    super_admin_name VARCHAR(100) NOT NULL,
    action_type ENUM(
        'admin_approved',
        'admin_rejected',
        'admin_deactivated',
        'customer_approved',
        'customer_rejected',
        'system_config'
    ) NOT NULL,
    target_admin_id INT NULL,                 -- FK → users(id) [optional]
    target_admin_name VARCHAR(100) NULL,     -- Soft ref → admin_registrations
    description TEXT NULL,
    action_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_super_admin_id (super_admin_id),
    INDEX idx_action_date (action_date),

    FOREIGN KEY (super_admin_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (target_admin_id) REFERENCES users(id) ON DELETE SET NULL
);
```

---

## 🔄 HOW CONNECTIONS WORK

### Connection Flow:

1. **Super Admin Performs Action:**

   ```
   Super Admin (users.id = 1) approves admin registration
   ↓
   Record created in super_admin_activity:
   - super_admin_id = 1 (FK → users.id = 1)
   - target_admin_name = "Merlyn" (soft ref to admin_registrations)
   - target_admin_id = NULL (if Merlyn not yet a user)
   ```

2. **After Admin is Approved:**

   ```
   Admin registration approved → User account created
   ↓
   target_admin_id can be updated to point to new user.id
   ```

3. **Querying with JOINs:**

   ```sql
   -- Get activity with super admin details
   SELECT
       saa.*,
       u.fullname as super_admin_fullname,
       u.email as super_admin_email
   FROM super_admin_activity saa
   INNER JOIN users u ON saa.super_admin_id = u.id;

   -- Get activity with target admin details (if exists)
   SELECT
       saa.*,
       target_u.fullname as target_admin_fullname
   FROM super_admin_activity saa
   LEFT JOIN users target_u ON saa.target_admin_id = target_u.id;
   ```

---

## ✅ VERIFICATION QUERIES

### Check Foreign Keys:

```sql
-- Show all foreign keys for super_admin_activity
SELECT
    CONSTRAINT_NAME,
    COLUMN_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM information_schema.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = 'db_upholcare'
AND TABLE_NAME = 'super_admin_activity'
AND REFERENCED_TABLE_NAME IS NOT NULL;
```

### Check Connections:

```sql
-- Verify super_admin_id connections
SELECT
    saa.id,
    saa.super_admin_id,
    saa.super_admin_name,
    u.fullname as user_fullname,
    u.email as user_email
FROM super_admin_activity saa
LEFT JOIN users u ON saa.super_admin_id = u.id
LIMIT 10;

-- Verify target_admin_id connections
SELECT
    saa.id,
    saa.target_admin_id,
    saa.target_admin_name,
    u.fullname as target_user_fullname
FROM super_admin_activity saa
LEFT JOIN users u ON saa.target_admin_id = u.id
WHERE saa.target_admin_id IS NOT NULL
LIMIT 10;
```

---

## 🔧 FIXING MISSING CONNECTIONS

If foreign keys are missing, run:

```sql
-- File: database/fix_super_admin_activity_connections.sql
```

This script will:

1. ✅ Add `super_admin_id` → `users(id)` foreign key
2. ✅ Add `target_admin_id` → `users(id)` foreign key (if column exists)
3. ✅ Verify all connections are properly set up

---

## 📝 SUMMARY

**Yes, `super_admin_activity` table IS connected:**

✅ **Connected to `users` table:**

- Via `super_admin_id` (who performed action)
- Via `target_admin_id` (target of action, if applicable)

✅ **Soft references:**

- `target_admin_name` links to `admin_registrations` (via name/email)
- Can link to `control_panel_admins` (via user lookup)

✅ **Foreign Key Actions:**

- `super_admin_id`: CASCADE (delete activities if user deleted)
- `target_admin_id`: SET NULL (keep activity if target user deleted)

---

**Generated**: December 2025  
**Database**: db_upholcare  
**Table**: super_admin_activity
