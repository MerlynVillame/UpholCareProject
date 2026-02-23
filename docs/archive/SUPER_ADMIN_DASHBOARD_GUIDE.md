# Super Admin Dashboard - Complete Guide

## Overview
The Super Admin Dashboard is a comprehensive monitoring and management system that allows you to oversee all admin registrations, track their activities, monitor sales, and manage the entire admin hierarchy.

---

## System Architecture

### User Hierarchy
```
Super Admin (You)
    ↓
Regular Admins (Approved by Super Admin)
    ↓
Customers (Managed by Admins)
```

### Database Tables Created

1. **control_panel_admins**
   - Stores all admin accounts (super_admins and regular admins)
   - Fields: id, email, password, fullname, role, status, last_login
   - Roles: `super_admin`, `admin`

2. **admin_registrations**
   - Tracks admin registration requests
   - Status: `pending`, `approved`, `rejected`
   - Super Admin approves/rejects these requests

3. **admin_sales_activity**
   - Logs all admin activities (bookings accepted/rejected, payments, etc.)
   - Used for monitoring admin performance

4. **super_admin_activity**
   - Logs super admin actions (approving/rejecting admins, etc.)
   - Provides audit trail for super admin operations

5. **login_logs**
   - Tracks all login attempts (customers, admins, super admins)
   - Used for security monitoring

6. **system_statistics**
   - Stores daily statistics summaries
   - Powers dashboard analytics

---

## Getting Started

### Step 1: Register as Super Admin

1. **Visit Registration Page**
   ```
   http://localhost/UphoCare/control-panel/register
   ```

2. **Fill in Registration Form**
   - Full Name (e.g., "John Doe")
   - Email (e.g., "superadmin@uphocare.com")
   - Password (minimum 8 characters)
   - Confirm Password

3. **Click "Register Super Admin"**
   - You'll be redirected to the login page
   - Use your email and password to log in

### Step 2: Access Super Admin Dashboard

1. **Login**
   ```
   http://localhost/UphoCare/control-panel/login
   ```

2. **You'll be redirected to**
   ```
   http://localhost/UphoCare/control-panel/superAdminDashboard
   ```

---

## Super Admin Dashboard Features

### 📊 Statistics Cards

**Top Row Overview:**
- **Total Customers**: Number of registered customers in the system
- **Active Admins**: Number of approved and active admin accounts
- **Pending Admin Registrations**: Admin requests waiting for your approval
- **Total Revenue**: Sum of all paid bookings (₱)

### 🚀 Quick Actions

1. **Manage Admin Registrations**
   - View all admin registration requests
   - Filter by status (pending, approved, rejected)
   - Approve or reject registrations

2. **View Admin Activities**
   - Monitor what admins are doing
   - Track booking acceptances/rejections
   - View sales performance
   - Filter by admin or activity type

3. **Register New Super Admin**
   - Create additional super admin accounts
   - (Only accessible to existing super admins)

### 📝 Pending Admin Registrations Table

Shows admins waiting for approval with:
- Full Name
- Email
- Username
- Phone
- Registration Date
- **Actions**:
  - ✅ **Approve**: Creates admin account and grants access
  - ❌ **Reject**: Denies registration (must provide reason)

### 📈 Recent Admin Activities

Displays latest admin actions:
- Admin name
- Activity type (Booking Accepted, Payment Received, etc.)
- Customer involved
- Transaction amount
- Date and time

---

## Admin Registrations Management

### Access Full Registration List
```
http://localhost/UphoCare/control-panel/adminRegistrations
```

### Features:
- **Filter by Status**: All, Pending, Approved, Rejected
- **View Details**: See all registration information
- **Approve/Reject**: Process pending requests
- **View Rejection Reasons**: See why admins were rejected

### Approval Process:
1. Review admin registration details
2. Click "Approve" button
3. Confirm approval
4. Admin account is created automatically
5. Admin can now log in and start managing bookings

### Rejection Process:
1. Click "Reject" button on pending registration
2. Enter rejection reason (required)
3. Admin is notified of rejection
4. Rejection reason is logged for audit

---

## Admin Activities Monitoring

### Access Activities Dashboard
```
http://localhost/UphoCare/control-panel/adminActivities
```

### Features:
- **Filter by Admin**: Select specific admin to monitor
- **Filter by Activity Type**:
  - Booking Accepted
  - Booking Rejected
  - Payment Received
  - Booking Completed
- **View Details**:
  - Admin name and email
  - Activity type with color-coded badges
  - Customer involved
  - Booking ID
  - Transaction amount
  - Date and time

### Activity Types Explained:

**🟢 Booking Accepted**
- Admin approved a customer booking
- Booking moves to active status

**🔴 Booking Rejected**
- Admin rejected a customer booking
- Customer is notified

**🔵 Payment Received**
- Admin confirmed payment from customer
- Amount is added to revenue

**🟣 Booking Completed**
- Service completed successfully
- Booking closed

---

## Security Features

### Login Tracking
- All login attempts are logged
- Failed login attempts are tracked
- IP addresses and user agents recorded
- Useful for security audits

### Session Management
- Active sessions are tracked
- Session timeout for security
- Concurrent login detection

### Activity Audit Trail
- All super admin actions logged
- Admin approval/rejection history
- Timestamps and reasons recorded
- Cannot be deleted or modified

---

## Admin Registration Flow

### For New Admins:
1. Admin fills out registration form on the website
2. Registration is saved with `pending` status
3. Super Admin receives notification (dashboard shows pending count)
4. Super Admin reviews registration
5. Super Admin approves/rejects:
   - **If Approved**: Admin account created, admin can log in
   - **If Rejected**: Admin is notified with reason

### For Super Admin:
1. Check dashboard for pending registrations
2. Click "Manage Admin Registrations"
3. Review admin details (name, email, phone, etc.)
4. Make decision:
   - Approve: Admin gets full access
   - Reject: Provide reason for rejection
5. Action is logged in super_admin_activity table

---

## Monitoring Admin Performance

### Sales Tracking
- View total sales per admin
- Filter by date range
- Compare admin performance
- Identify top performers

### Activity Metrics
- Number of bookings processed
- Acceptance vs rejection rate
- Response time (future feature)
- Customer satisfaction (future feature)

### Reports (Future Enhancement)
- Daily/Weekly/Monthly reports
- Admin performance comparisons
- Revenue analytics
- Customer growth tracking

---

## URLs Reference

| Page | URL |
|------|-----|
| Super Admin Registration | `/control-panel/register` |
| Login | `/control-panel/login` |
| Super Admin Dashboard | `/control-panel/superAdminDashboard` |
| Admin Registrations | `/control-panel/adminRegistrations` |
| Admin Activities | `/control-panel/adminActivities` |
| Logout | `/control-panel/logout` |

---

## Important Notes

### First Super Admin Registration
- ⚠️ **IMPORTANT**: The first person to access `/control-panel/register` becomes the Super Admin
- After the first Super Admin is created, registration is restricted
- Only existing Super Admins can create additional Super Admins
- Secure your registration URL immediately after creating your account

### Password Security
- Passwords are hashed using bcrypt
- Minimum 8 characters required
- Use strong passwords with mix of:
  - Uppercase letters
  - Lowercase letters
  - Numbers
  - Special characters

### Role Hierarchy
- **Super Admin**: Full access to everything
  - Approve/reject admin registrations
  - Monitor all admin activities
  - View system statistics
  - Create other super admins

- **Admin** (Regular): Limited to their functions
  - Accept/reject customer bookings
  - Process payments
  - Manage customer accounts
  - View their own statistics

- **Customer**: End users
  - Create bookings
  - Make payments
  - Track order status

---

## Troubleshooting

### Cannot Access Registration Page
**Problem**: Registration page shows error or redirects
**Solution**: 
- Check if you're already logged in (logout first)
- Verify database tables exist
- Check if a Super Admin already exists

### Dashboard Not Showing Statistics
**Problem**: Statistics show 0 or don't load
**Solution**:
- Verify database tables have data
- Check database connection
- Ensure you're logged in as Super Admin

### Approval/Rejection Not Working
**Problem**: Actions don't save
**Solution**:
- Check database permissions
- Verify foreign key relationships
- Check error logs in browser console

---

## Next Steps After Registration

1. ✅ **Register as Super Admin**
2. ✅ **Log in to Dashboard**
3. 📋 **Review Pending Admin Registrations**
4. ✅ **Approve First Admin**
5. 👀 **Monitor Admin Activities**
6. 📊 **Check Statistics Regularly**
7. 🔐 **Maintain Security Best Practices**

---

## Technical Details

### Authentication Flow
```
1. User enters credentials
2. System verifies against control_panel_admins table
3. Check role (super_admin or admin)
4. Create session with role information
5. Redirect to appropriate dashboard:
   - super_admin → /control-panel/superAdminDashboard
   - admin → /control-panel/dashboard
```

### Admin Approval Flow
```
1. Admin submits registration → admin_registrations (pending)
2. Super Admin reviews → superAdminDashboard
3. Super Admin approves → 
   a. Insert into control_panel_admins (role='admin')
   b. Update admin_registrations (status='approved')
   c. Log in super_admin_activity
4. Admin can now log in with credentials
```

### Activity Logging
```
When Admin performs action:
1. Action triggers in admin controller
2. Insert into admin_sales_activity table
3. Update system_statistics
4. Visible in Super Admin dashboard
```

---

## Support & Maintenance

### Regular Tasks
- Review pending registrations daily
- Monitor admin activities weekly
- Check login logs for security
- Update admin statuses as needed

### Backup Recommendations
- Backup database regularly
- Export admin_registrations periodically
- Keep audit trail of all activities
- Store rejection reasons for reference

---

## Congratulations! 🎉

You now have a fully functional Super Admin dashboard to monitor and manage your admin team. Start by registering as Super Admin and exploring the features!

**Remember**: With great power comes great responsibility. As Super Admin, you control who gets access to manage customer bookings and handle payments.

---

**Version**: 1.0  
**Last Updated**: November 6, 2025  
**System**: UphoCare Super Admin Control Panel

