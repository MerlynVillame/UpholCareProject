# Database Table Relationships - db_upholcare

## Overview
Yes, the tables in the `db_upholcare` database **ARE connected to each other** through **Foreign Key (FK) relationships**. This ensures data integrity and allows related data to be linked together.

---

## 🔗 FOREIGN KEY RELATIONSHIPS

### Main Application Tables

#### 1. **bookings** Table (Central Hub)
The `bookings` table is the **central table** that connects to multiple other tables:

```sql
-- bookings connects to:
FOREIGN KEY (user_id) → users(id) ON DELETE SET NULL
FOREIGN KEY (service_id) → services(id) ON DELETE SET NULL
FOREIGN KEY (store_location_id) → store_locations(id) ON DELETE SET NULL
FOREIGN KEY (selected_color_id) → inventory(id) ON DELETE SET NULL
```

**Meaning:**
- Each booking belongs to a **user** (customer)
- Each booking is for a specific **service**
- Each booking is associated with a **store location**
- Each booking can have a selected **inventory color/fabric**

---

#### 2. **services** Table
```sql
FOREIGN KEY (category_id) → service_categories(id) ON DELETE SET NULL
```

**Meaning:**
- Each service belongs to a **service category**

---

#### 3. **payments** Table
```sql
FOREIGN KEY (booking_id) → bookings(id) ON DELETE SET NULL
```

**Meaning:**
- Each payment is linked to a **booking**

---

#### 4. **quotations** Table
```sql
FOREIGN KEY (booking_id) → bookings(id) ON DELETE SET NULL
```

**Meaning:**
- Each quotation is linked to a **booking**

---

#### 5. **notifications** Table
```sql
FOREIGN KEY (user_id) → users(id) ON DELETE CASCADE
```

**Meaning:**
- Each notification belongs to a **user**
- If user is deleted, notifications are also deleted (CASCADE)

---

#### 6. **inventory** Table
```sql
FOREIGN KEY (store_location_id) → store_locations(id) ON DELETE SET NULL
```

**Meaning:**
- Each inventory item can be assigned to a **store location**

---

#### 7. **admin_verification_codes** Table
```sql
FOREIGN KEY (admin_registration_id) → admin_registrations(id) ON DELETE SET NULL
```

**Meaning:**
- Each verification code can be linked to an **admin registration**

---

### Control Panel Tables

#### 8. **control_panel_sessions** Table
```sql
FOREIGN KEY (admin_id) → control_panel_admins(id) ON DELETE CASCADE
```

**Meaning:**
- Each session belongs to a **control panel admin**
- If admin is deleted, sessions are also deleted (CASCADE)

---

## 📊 VISUAL RELATIONSHIP DIAGRAM

```
┌─────────────────┐
│     users       │ (Parent Table)
│   (id: PK)      │
└────────┬────────┘
         │
         ├─────────────────────────────────────┐
         │                                     │
         │                                     │
    ┌────▼──────────┐                    ┌────▼──────────┐
    │   bookings    │                    │ notifications │
    │ (user_id: FK) │                    │ (user_id: FK) │
    └────┬──────────┘                    └───────────────┘
         │
         ├──────────────┬──────────────┬─────────────────┐
         │              │              │                 │
    ┌────▼────┐    ┌────▼────┐    ┌────▼────┐      ┌─────▼─────┐
    │services │    │payments │    │quotations│      │inventory │
    │(id: PK) │    │(booking │    │(booking │      │(id: PK)  │
    └────┬────┘    │_id: FK) │    │_id: FK) │      └─────┬─────┘
         │         └─────────┘    └─────────┘            │
         │                                               │
    ┌────▼──────────┐                              ┌────▼──────────┐
    │service_       │                              │store_        │
    │categories     │                              │locations     │
    │(id: PK)       │                              │(id: PK)      │
    └───────────────┘                              └──────────────┘

┌──────────────────────┐
│ control_panel_admins │ (Parent Table)
│     (id: PK)         │
└──────────┬───────────┘
           │
           │
    ┌──────▼──────────┐
    │control_panel_   │
    │sessions         │
    │(admin_id: FK)   │
    └─────────────────┘

┌──────────────────────┐
│ admin_registrations  │ (Parent Table)
│     (id: PK)         │
└──────────┬───────────┘
           │
           │
    ┌──────▼──────────────┐
    │admin_verification_  │
    │codes                │
    │(admin_registration_ │
    │ id: FK)             │
    └─────────────────────┘
```

---

## 🔍 DETAILED RELATIONSHIP TABLE

| Child Table | Foreign Key Column | References | Parent Table | On Delete Action |
|-------------|-------------------|------------|--------------|------------------|
| **bookings** | `user_id` | `users.id` | **users** | SET NULL |
| **bookings** | `service_id` | `services.id` | **services** | SET NULL |
| **bookings** | `store_location_id` | `store_locations.id` | **store_locations** | SET NULL |
| **bookings** | `selected_color_id` | `inventory.id` | **inventory** | SET NULL |
| **services** | `category_id` | `service_categories.id` | **service_categories** | SET NULL |
| **payments** | `booking_id` | `bookings.id` | **bookings** | SET NULL |
| **quotations** | `booking_id` | `bookings.id` | **bookings** | SET NULL |
| **notifications** | `user_id` | `users.id` | **users** | CASCADE |
| **inventory** | `store_location_id` | `store_locations.id` | **store_locations** | SET NULL |
| **admin_verification_codes** | `admin_registration_id` | `admin_registrations.id` | **admin_registrations** | SET NULL |
| **control_panel_sessions** | `admin_id` | `control_panel_admins.id` | **control_panel_admins** | CASCADE |

---

## 📝 ON DELETE ACTIONS EXPLAINED

### SET NULL
- When parent record is deleted, foreign key value becomes `NULL`
- Used when: Child record can exist without parent
- Example: If a user is deleted, their bookings remain but `user_id` becomes NULL

### CASCADE
- When parent record is deleted, child records are also deleted
- Used when: Child records cannot exist without parent
- Example: If a user is deleted, their notifications are also deleted

---

## 🔗 SOFT REFERENCES (No Foreign Key)

Some tables reference other tables but **without formal foreign keys** (soft references):

| Table | Column | References | Type |
|-------|--------|------------|------|
| **login_logs** | `user_id` | `users.id` | Soft reference |
| **system_activities** | `admin_id` | `users.id` | Soft reference |

**Why soft references?**
- These are logging/audit tables
- They keep records even if the referenced user is deleted
- No cascade delete needed for historical data

---

## 🎯 KEY RELATIONSHIPS SUMMARY

### Core Relationships:
1. **users** ← **bookings** (One user can have many bookings)
2. **users** ← **notifications** (One user can have many notifications)
3. **services** ← **bookings** (One service can have many bookings)
4. **service_categories** ← **services** (One category can have many services)
5. **store_locations** ← **bookings** (One store can have many bookings)
6. **store_locations** ← **inventory** (One store can have many inventory items)
7. **bookings** ← **payments** (One booking can have many payments)
8. **bookings** ← **quotations** (One booking can have many quotations)
9. **inventory** ← **bookings** (One inventory item can be in many bookings)

### Control Panel Relationships:
10. **control_panel_admins** ← **control_panel_sessions** (One admin can have many sessions)
11. **admin_registrations** ← **admin_verification_codes** (One registration can have one code)

---

## ✅ BENEFITS OF THESE CONNECTIONS

1. **Data Integrity**: Prevents orphaned records (bookings without users, etc.)
2. **Referential Integrity**: Ensures foreign keys always point to valid records
3. **Cascade Operations**: Automatic cleanup when parent records are deleted
4. **Query Efficiency**: Easy to JOIN related tables
5. **Data Consistency**: Prevents invalid relationships

---

## 🔍 EXAMPLE QUERIES USING RELATIONSHIPS

### Get booking with user and service info:
```sql
SELECT 
    b.id as booking_id,
    u.fullname as customer_name,
    s.service_name,
    sl.store_name,
    inv.color_name
FROM bookings b
LEFT JOIN users u ON b.user_id = u.id
LEFT JOIN services s ON b.service_id = s.id
LEFT JOIN store_locations sl ON b.store_location_id = sl.id
LEFT JOIN inventory inv ON b.selected_color_id = inv.id
WHERE b.id = 1;
```

### Get all notifications for a user:
```sql
SELECT n.*, u.fullname
FROM notifications n
INNER JOIN users u ON n.user_id = u.id
WHERE u.id = 1;
```

### Get all bookings with payments:
```sql
SELECT 
    b.id as booking_id,
    b.total_amount,
    SUM(p.amount) as total_paid
FROM bookings b
LEFT JOIN payments p ON b.id = p.booking_id
GROUP BY b.id;
```

---

## ⚠️ IMPORTANT NOTES

1. **Foreign Keys are Enforced**: MySQL will prevent invalid relationships
2. **ON DELETE SET NULL**: Allows records to exist without parent (bookings can exist without user)
3. **ON DELETE CASCADE**: Automatically deletes child records (notifications deleted with user)
4. **Indexes**: Foreign key columns are automatically indexed for performance
5. **Soft References**: Some tables use soft references (no FK) for audit/logging purposes

---

**Generated**: December 2025  
**Database**: db_upholcare  
**Total Foreign Keys**: 11 formal relationships + 2 soft references

