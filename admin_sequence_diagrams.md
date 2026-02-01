# Admin Process Sequence Diagrams

This document contains sequence diagrams for all **Admin** (Business Owner) processes.

## 1. Admin Registration (Submission)
Process of applying for an admin account.

```mermaid
sequenceDiagram
    actor User as User<br>(admin)
    participant DB as :admin_registrations

    User->>DB: [NewApp]:=Submit_Registration('business_name', 'permit_file', 'contact_info')
    activate DB
    DB-->>User: X Application Submitted (Pending Verification)
    deactivate DB
```

## 2. Admin Verify Email (Enter Code)
Process of entering the verification code sent by email.

```mermaid
sequenceDiagram
    actor User as User<br>(admin)
    participant DB as :admin_registrations

    User->>DB: [Verify]:=Enter_Code('email', 'verification_code')
    activate DB
    DB-->>User: X Code Verified (Pending Final Approval)
    deactivate DB
```

## 3. Admin Login
Process of authenticating into the admin dashboard.

```mermaid
sequenceDiagram
    actor User as User<br>(admin)
    participant DB as :users

    User->>DB: [UserInfo]:=Authenticate_Admin('email', 'password')
    activate DB
    DB-->>User: X Login Successful
    deactivate DB
```

## 4. View Pending Bookings
Process of retrieving new booking requests.

```mermaid
sequenceDiagram
    actor User as User<br>(admin)
    participant DB as :bookings

    User->>DB: [PendingList]:=Get_Bookings(status='pending')
    activate DB
    DB-->>User: X Display Pending Bookings
    deactivate DB
```

## 5. Send Quotation
Process of creating and sending a price quote.

```mermaid
sequenceDiagram
    actor User as User<br>(admin)
    participant DB as :quotations

    User->>DB: [NewQuote]:=Create_Quotation('booking_id', 'breakdown', 'total_amount')
    activate DB
    DB-->>User: X Quotation Sent to Customer
    deactivate DB
```

## 6. Confirm Payment
Process of verifying a customer's payment proof.

```mermaid
sequenceDiagram
    actor User as User<br>(admin)
    participant DB as :payments

    User->>DB: [UpdateStatus]:=Verify_Payment('payment_id', status='paid')
    activate DB
    DB-->>User: X Payment Marked as Paid
    deactivate DB
```

## 7. Update Booking Status (Work Progress)
Process of updating the status of a repair (e.g., In Progress, Completed).

```mermaid
sequenceDiagram
    actor User as User<br>(admin)
    participant DB as :bookings

    User->>DB: [UpdateStatus]:=Set_Booking_Status('booking_id', status='in_progress')
    activate DB
    DB-->>User: X Status Updated
    deactivate DB
```

## 8. Add Inventory Stock
Process of adding new materials to the inventory.

```mermaid
sequenceDiagram
    actor User as User<br>(admin)
    participant DB as :inventory

    User->>DB: [NewStock]:=Add_Inventory('color', 'type', 'quantity', 'price')
    activate DB
    DB-->>User: X Inventory Updated
    deactivate DB
```

## 9. Manage Services (Update Price)
Process of updating service details.

```mermaid
sequenceDiagram
    actor User as User<br>(admin)
    participant DB as :services

    User->>DB: [UpdateService]:=Edit_Service_Price('service_id', 'new_price')
    activate DB
    DB-->>User: X Service Price Updated
    deactivate DB
```
