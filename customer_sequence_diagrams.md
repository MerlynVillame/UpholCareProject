# Customer Process Sequence Diagrams

This document contains sequence diagrams for all **Customer** processes, strictly separating them from other roles.

## 1. Customer Registration
Process of creating a new customer account.

```mermaid
sequenceDiagram
    actor User as User<br>(customer)
    participant DB as :users

    User->>DB: [NewUser]:=Register_Customer('fullname', 'email', 'password', 'phone')
    activate DB
    DB-->>User: X Account Created
    deactivate DB
```

## 2. Customer Login
Process of authenticating into the system.

```mermaid
sequenceDiagram
    actor User as User<br>(customer)
    participant DB as :users

    User->>DB: [UserInfo]:=Authenticate_User('email', 'password')
    activate DB
    DB-->>User: X Login Successful & Session Started
    deactivate DB
```

## 3. Create Repair Reservation (Booking)
Process of submitting a new booking request.

```mermaid
sequenceDiagram
    actor User as User<br>(customer)
    participant DB as :bookings

    User->>DB: [NewBooking]:=Create_Booking('service_details', 'pickup_date', 'notes')
    activate DB
    DB-->>User: X Booking Submitted (Status: Pending)
    deactivate DB
```

## 4. View Own Bookings
Process of retrieving booking history.

```mermaid
sequenceDiagram
    actor User as User<br>(customer)
    participant DB as :bookings

    User->>DB: [MyBookings]:=Get_Customer_Bookings('user_id')
    activate DB
    DB-->>User: X Display List of Bookings
    deactivate DB
```

## 5. Accept Quotation
Process of accepting a price quote sent by the admin.

```mermaid
sequenceDiagram
    actor User as User<br>(customer)
    participant DB as :quotations

    User->>DB: [UpdateStatus]:=Accept_Quotation('quotation_id', status='accepted')
    activate DB
    DB-->>User: X Quotation Accepted
    deactivate DB
```

## 6. Submit Payment
Process of uploading payment proof.

```mermaid
sequenceDiagram
    actor User as User<br>(customer)
    participant DB as :payments

    User->>DB: [NewPayment]:=Submit_Payment('booking_id', 'amount', 'proof_image')
    activate DB
    DB-->>User: X Payment Submitted for Verification
    deactivate DB
```
