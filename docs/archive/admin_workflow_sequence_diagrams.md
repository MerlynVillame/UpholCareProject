# Admin & Super Admin Sequence Diagrams

This document contains sequence diagrams for workflows specific to the **Admin** and **Super Admin** roles.

## 1. Admin Registration & Verification Workflow

This diagram illustrates the process of a new Admin signing up, being verified by the Super Admin, and finally approved.

```mermaid
sequenceDiagram
    autonumber
    actor AdminCandidate as Aspiring Admin
    participant Frontend as Frontend Interface
    participant Auth as AuthController
    participant DB as Database
    participant SuperAdmin as Super Admin (Control Panel)
    participant Email as EmailService

    %% 1. Registration Request
    Note over AdminCandidate, DB: Phase 1: Registration
    AdminCandidate->>Frontend: Fill Registration Form (Business Info, Permit)
    Frontend->>Auth: POST /auth/register_admin
    Auth->>DB: INSERT INTO admin_registrations (fullname, email, ... status='pending_verification')
    DB-->>Auth: Return registration_id
    Auth-->>Frontend: Redirect to Verification Page

    %% 2. Super Admin Verification Code
    Note over SuperAdmin, Email: Phase 2: Code Generation
    SuperAdmin->>SuperAdmin: Reviews Registration List
    SuperAdmin->>Auth: Click "Send Verification Code"
    Auth->>DB: UPDATE admin_registrations SET verification_code='123456', status='pending_verification'
    Auth->>Email: Send Verification Code to Admin Email
    Email-->>AdminCandidate: Receive Code (e.g., 123456)

    %% 3. Admin Enters Code
    Note over AdminCandidate, DB: Phase 3: Verification
    AdminCandidate->>Frontend: Enter Verification Code
    Frontend->>Auth: POST /auth/verify_admin
    Auth->>DB: SELECT * FROM admin_registrations WHERE email=? AND verification_code=?
    alt Code Matches
        Auth->>DB: UPDATE admin_registrations SET status='pending', verified_at=NOW()
        Auth-->>Frontend: Success Message (Wait for Approval)
    else Code Invalid
        Auth-->>Frontend: Error Message
    end

    %% 4. Final Approval
    Note over SuperAdmin, DB: Phase 4: Final Approval
    SuperAdmin->>SuperAdmin: View Verified Applications
    SuperAdmin->>Auth: Click "Approve & Convert"
    Auth->>DB: INSERT INTO users (username, password, role='admin', status='active')
    Auth->>DB: INSERT INTO control_panel_admins / admin_profiles (user_id, business_details...)
    Auth->>DB: UPDATE admin_registrations SET status='approved', admin_id=NEW_USER_ID
    Auth->>Email: Send "Account Approved" Email
    Email-->>AdminCandidate: Login Access Granted
```

**Database Tables Involved:**
- `admin_registrations`: Stores temporary registration data, verification codes, and business permit paths.
- `users`: Final storage for the approved admin account.
- `admin_profiles` / `control_panel_admins`: Stores business details linked to the user.

---

## 2. Inventory Management Workflow (Admin)

This diagram shows how an Admin manages the inventory, specifically adding new stock.

```mermaid
sequenceDiagram
    autonumber
    actor Admin
    participant Dashboard as Admin Dashboard
    participant InvController as InventoryController
    participant DB as Database

    %% 1. Add Inventory
    Note right of Admin: Add New Stock
    Admin->>Dashboard: Click "Add Leather Stock"
    Dashboard->>InvController: GET /admin/generateColorCode
    InvController->>DB: SELECT MAX(id) FROM inventory
    DB-->>InvController: Return Max ID
    InvController-->>Dashboard: Return New Code (e.g., INV-005)
    
    Admin->>Dashboard: Fill Stock Details (Name, Type, Price, Qty)
    Dashboard->>InvController: POST /admin/createInventory
    InvController->>DB: INSERT INTO inventory (color_code, fabric_type, price_per_unit, quantity...)
    DB-->>InvController: Success
    InvController-->>Dashboard: Inventory Updated
    
    %% 2. Update Stock (Auto-Calculation)
    Note right of Admin: Stock status is auto-calculated
    InvController->>DB: SELECT quantity FROM inventory
    
    alt quantity == 0
        InvController->>DB: UPDATE inventory SET status='out-of-stock'
    else quantity < 10
        InvController->>DB: UPDATE inventory SET status='low-stock'
    else quantity >= 10
        InvController->>DB: UPDATE inventory SET status='in-stock'
    end

    Dashboard->>Admin: Show Updated Table
```

**Database Tables Involved:**
- `inventory`: Stores the items, prices, quantities, and status.
- `store_locations`: (Optional) Links inventory to specific store branches.
