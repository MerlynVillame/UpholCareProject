# Super Admin Process Sequence Diagrams

This document contains sequence diagrams for all **Super Admin** processes.

## 1. Super Admin Login
Process of authenticating into the control panel.

```mermaid
sequenceDiagram
    actor User as User<br>(super_admin)
    participant DB as :control_panel_admins

    User->>DB: [Auth]:=Login_Super_Admin('username', 'password')
    activate DB
    DB-->>User: X Control Panel Access Granted
    deactivate DB
```

## 2. View Admin Applications
Process of viewing the list of pending admin registrations.

```mermaid
sequenceDiagram
    actor User as User<br>(super_admin)
    participant DB as :admin_registrations

    User->>DB: [ListApps]:=Get_Applications(status='pending_verification')
    activate DB
    DB-->>User: X Display Applications List
    deactivate DB
```

## 3. Send Verification Code
Process of generating and storing a verification code for an admin applicant.

```mermaid
sequenceDiagram
    actor User as User<br>(super_admin)
    participant DB as :admin_registrations

    User->>DB: [SetCode]:=Generate_Code('registration_id', 'code=123456')
    activate DB
    DB-->>User: X Code Saved & Email Sent
    deactivate DB
```

## 4. Approve Admin Registration
Process of finalizing the approval of an admin account.

```mermaid
sequenceDiagram
    actor User as User<br>(super_admin)
    participant DB as :admin_registrations

    User->>DB: [Approve]:=Approve_Application('registration_id')
    activate DB
    DB-->>User: X Application Approved
    deactivate DB
```

## 5. View System Statistics
Process of gathering system-wide data.

```mermaid
sequenceDiagram
    actor User as User<br>(super_admin)
    participant DB as :bookings

    User->>DB: [Stats]:=Get_System_Stats('total_bookings', 'total_revenue')
    activate DB
    DB-->>User: X Display Dashboard Stats
    deactivate DB
```
