# Detailed System Sequence Diagrams

These sequence diagrams are designed to match the specific visual style of your reference images. They represent individual processes for Customers, Admins, and Super Admins, showing the direct interaction with the database tables.

## 1. Sequence Diagram Involving Customer Registration

This diagram illustrates the process of a new customer adding their details to the system.

```mermaid
sequenceDiagram
    actor User as User<br>(customer)
    participant DB as :users

    User->>DB: [NewUser]:=Register_Customer('fullname', 'email', 'password', 'phone', role='customer')
    activate DB
    DB-->>User: X Account Created Successfully
    deactivate DB
```

## 2. Sequence Diagram Involving Customer Adding New Booking

This diagram shows a customer creating a new repair reservation.

```mermaid
sequenceDiagram
    actor User as User<br>(customer)
    participant DB as :bookings

    User->>DB: [NewBooking]:=Create_Booking('user_id', 'service_id', 'booking_date', 'notes')
    activate DB
    DB-->>User: X Booking Submitted (Pending)
    deactivate DB
```

## 3. Sequence Diagram Involving Admin adding New Inventory

This diagram shows an admin adding new leather/fabric stock to the system.

```mermaid
sequenceDiagram
    actor User as User<br>(admin)
    participant DB as :inventory

    User->>DB: [NewStock]:=Add_Inventory('color_name', 'fabric_type', 'price_per_unit', 'quantity')
    activate DB
    DB-->>User: X Inventory Stock Added
    deactivate DB
```

## 4. Sequence Diagram Involving Admin Viewing Pending Bookings

This diagram shows an admin retrieving a list of pending bookings.

```mermaid
sequenceDiagram
    actor User as User<br>(admin)
    participant DB as :bookings

    User->>DB: [RetrieveBookings]:=Get_Pending_Requests(status='pending')
    activate DB
    DB-->>User: X Display Pending Bookings List
    deactivate DB
```

## 5. Sequence Diagram Involving Admin Sending Quotation

This diagram shows an admin creating a price quotation for a booking.

```mermaid
sequenceDiagram
    actor User as User<br>(admin)
    participant DB as :quotations

    User->>DB: [NewQuote]:=Create_Quotation('booking_id', 'total_amount', 'labor_fee', 'status'='sent')
    activate DB
    DB-->>User: X Quotation Sent to Customer
    deactivate DB
```

## 6. Sequence Diagram Involving Super Admin Approving Registration

This diagram shows the super admin approving a business admin's registration.

```mermaid
sequenceDiagram
    actor User as User<br>(super_admin)
    participant DB as :admin_registrations

    User->>DB: [UpdateStatus]:=Approve_Admin('registration_id', status='approved')
    activate DB
    DB-->>User: X Admin Registration Verified
    deactivate DB
```

## 7. Sequence Diagram Involving Customer Making Payment

This diagram shows a customer submitting a payment record.

```mermaid
sequenceDiagram
    actor User as User<br>(customer)
    participant DB as :payments

    User->>DB: [NewPayment]:=Submit_Payment('booking_id', 'amount', 'payment_method', 'proof_file')
    activate DB
    DB-->>User: X Payment Submitted for Verification
    deactivate DB
```
