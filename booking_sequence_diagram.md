# Sequence Diagram: Customer Booking Process

This sequence diagram illustrates the end-to-end flow of the Customer Booking Process, aligned with the database schema and system documentation.

```mermaid
sequenceDiagram
    autonumber
    actor Customer
    participant Frontend as Frontend Interface
    participant CController as CustomerController
    participant AController as AdminController
    participant DB as Database
    participant Email as EmailService

    %% 1. Booking Creation
    Note over Customer, DB: Phase 1: Booking Request
    Customer->>Frontend: Fills Booking Form & Submits
    Frontend->>CController: POST /customer/createBooking
    CController->>DB: INSERT INTO bookings (user_id, service_id, status='pending')
    DB-->>CController: Return booking_id
    CController->>Email: Send "Booking Received" Email (No Price)
    Email-->>Customer: Receive Email #1 (Queue Number Assigned)
    CController-->>Frontend: Return Success
    Frontend-->>Customer: Show Confirmation Message

    %% 2. Admin Review & Pricing
    Note over AController, Customer: Phase 2: Admin Review & Quotation
    AController->>DB: SELECT * FROM bookings WHERE status='pending'
    DB-->>AController: Return Pending Bookings
    
    rect rgb(240, 248, 255)
        Note right of AController: Admin reviews request
        AController->>DB: INSERT INTO quotations (booking_id, total_amount, status='sent')
        AController->>DB: UPDATE bookings SET status='approved' WHERE id=booking_id
        AController->>Email: Send "Booking Approved" Email (With Price Breakdown)
        Email-->>Customer: Receive Email #2 (Status: Approved, Ready for Repair)
    end

    %% 3. Customer Acceptance & Payment
    Note over Customer, DB: Phase 3: Acceptance & Payment
    Customer->>Frontend: View Quotation & Accept
    Frontend->>CController: POST /customer/acceptQuotation
    CController->>DB: UPDATE quotations SET status='accepted'
    
    Customer->>Frontend: Select Payment Method & Upload Proof
    Frontend->>CController: POST /customer/submitPayment
    CController->>DB: INSERT INTO payments (booking_id, amount, status='pending')
    CController-->>Frontend: Payment Submitted

    %% 4. Payment Verification
    Note over AController, Customer: Phase 4: Payment Verification
    AController->>DB: SELECT * FROM payments WHERE status='pending'
    
    rect rgb(240, 255, 240)
        Note right of AController: Admin verifies payment proof
        AController->>DB: UPDATE payments SET status='paid'
        AController->>DB: UPDATE bookings SET status='confirmed'
        AController-->>Customer: Notify Payment Confirmed
    end

    %% 5. Service Fulfillment
    Note over AController, Customer: Phase 5: Service Fulfillment
    AController->>DB: UPDATE bookings SET status='in_progress'
    Note right of AController: Repair work is being done
    
    AController->>DB: UPDATE bookings SET status='completed'
    AController-->>Customer: Notify Service Completed / Ready for Pickup
```

## Database Interaction Details

The diagram above interacts with the following tables based on the schema:

| Step | Action | Table(s) Affected | Columns |
|------|--------|-------------------|---------|
| 1 | Create Booking | `bookings` | `id`, `user_id`, `service_id`, `status` |
| 2 | Create Quotation | `quotations` | `booking_id`, `total_amount`, `status` |
| 2 | Approve Booking | `bookings` | `status` ('approved') |
| 3 | Accept Quotation | `quotations` | `status` ('accepted') |
| 3 | Submit Payment | `payments` | `booking_id`, `amount`, `payment_status` |
| 4 | Verify Payment | `payments`, `bookings` | `payment_status` ('paid'), `status` ('confirmed') |
| 5 | Update Progress | `bookings` | `status` ('in_progress', 'completed') |
