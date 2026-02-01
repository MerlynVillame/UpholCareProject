# Automatic Booking Status Update Setup Guide

## Overview

The system automatically updates booking statuses based on the `pickup_date` that customers input. When the pickup date arrives, the system will automatically transition bookings through the appropriate status workflow.

## How It Works

### Status Transitions Based on Pickup Date

1. **When pickup_date arrives and status is `approved`:**
   - If service option is `pickup` or `both` → Status changes to `for_pickup`
   - If service option is `delivery` only → Status remains `approved`

2. **When pickup_date arrives and status is `for_pickup`:**
   - Status automatically changes to `picked_up`
   - Then immediately transitions to `to_inspect` (ready for inspection)

### Automatic Execution

The status update runs automatically in two ways:

1. **On Admin Page Load** (Recommended for most setups)
   - Runs when admin accesses:
     - Dashboard (`/admin/dashboard`)
     - All Bookings page (`/admin/allBookings`)
   - No additional setup required
   - Updates happen in real-time as admins use the system

2. **Via Scheduled Cron Job** (Optional, for automated background updates)
   - Runs independently on a schedule
   - Useful if admins don't access the system daily
   - Ensures statuses are updated even when no one is logged in

## Setup Options

### Option 1: Automatic on Page Load (Default - Already Active)

**No setup required!** The system is already configured to check and update statuses automatically when admin pages load.

**How it works:**
- When an admin opens the dashboard or bookings page, the system checks all bookings
- Any bookings with `pickup_date` that has arrived are automatically updated
- Updates happen silently in the background

**Advantages:**
- ✅ No server configuration needed
- ✅ Works immediately
- ✅ Updates happen as admins use the system

**Disadvantages:**
- ⚠️ Only runs when admins access the system
- ⚠️ If no admin logs in for days, statuses won't update

---

### Option 2: Scheduled Cron Job (Recommended for Production)

Set up a cron job to run the status update script automatically on a schedule.

#### For Linux/Mac (crontab)

1. Open your crontab:
   ```bash
   crontab -e
   ```

2. Add one of these lines (choose based on how often you want updates):

   **Run daily at midnight:**
   ```bash
   0 0 * * * php /path/to/UphoCare/cron/auto_update_booking_status.php
   ```

   **Run every 6 hours:**
   ```bash
   0 */6 * * * php /path/to/UphoCare/cron/auto_update_booking_status.php
   ```

   **Run every hour:**
   ```bash
   0 * * * * php /path/to/UphoCare/cron/auto_update_booking_status.php
   ```

3. Replace `/path/to/UphoCare` with your actual project path.

4. Save and exit.

#### For Windows (Task Scheduler)

1. Open **Task Scheduler** (search for it in Windows)

2. Click **Create Basic Task**

3. Set up the task:
   - **Name:** Auto Update Booking Status
   - **Trigger:** Daily (or as needed)
   - **Action:** Start a program
   - **Program/script:** `php.exe` (or full path: `C:\xampp\php\php.exe`)
   - **Add arguments:** `"C:\xampp\htdocs\UphoCare\cron\auto_update_booking_status.php"`
   - **Start in:** `C:\xampp\htdocs\UphoCare`

4. Save the task

#### For Web Hosting (cPanel or similar)

1. Log into your hosting control panel

2. Find **Cron Jobs** or **Scheduled Tasks**

3. Add a new cron job:
   - **Command:** `php /home/username/public_html/UphoCare/cron/auto_update_booking_status.php`
   - **Schedule:** `0 0 * * *` (daily at midnight) or your preferred schedule

4. Save

---

### Option 3: HTTP API Endpoint (Alternative Cron Method)

If you prefer to trigger the update via HTTP request:

1. **Via curl (Linux/Mac):**
   ```bash
   0 0 * * * curl -s http://localhost/UphoCare/api/auto_update_status.php > /dev/null
   ```

2. **Via wget (Linux/Mac):**
   ```bash
   0 0 * * * wget -q -O - http://localhost/UphoCare/api/auto_update_status.php
   ```

3. **Via external cron service** (like EasyCron, Cronitor):
   - Set URL: `http://yourdomain.com/UphoCare/api/auto_update_status.php`
   - Set schedule: Daily at midnight

**Note:** For production, you may want to add API key authentication to the endpoint (see `api/auto_update_status.php` for instructions).

---

## Logs

### Cron Job Logs

If using the cron job script, logs are saved to:
```
UphoCare/logs/auto_status_update.log
```

The log file contains:
- Timestamp of each run
- Number of bookings checked
- Number of bookings updated
- Details of each updated booking
- Any errors encountered

### PHP Error Logs

All status updates are also logged to PHP error logs:
- Location: Check your PHP error log (usually in `php.ini` or server logs)
- Look for entries starting with `AutoStatusUpdate:`

---

## Testing

### Test the Automatic Update

1. Create a test booking with:
   - Status: `approved` or `for_pickup`
   - Service option: `pickup` or `both`
   - Pickup date: Today's date (or a past date)

2. Access the admin dashboard or bookings page

3. Check if the status has been automatically updated

### Test the Cron Job

1. Run the script manually:
   ```bash
   php cron/auto_update_booking_status.php
   ```

2. Check the output and log file:
   ```bash
   tail -f logs/auto_status_update.log
   ```

---

## Troubleshooting

### Statuses Not Updating

1. **Check pickup_date format:**
   - Must be in `YYYY-MM-DD` format
   - Check database: `SELECT id, status, pickup_date FROM bookings WHERE pickup_date IS NOT NULL;`

2. **Check service_option:**
   - Must be `pickup` or `both` for automatic `for_pickup` transition
   - Check: `SELECT id, status, service_option FROM bookings;`

3. **Check PHP error logs:**
   - Look for `AutoStatusUpdate:` entries
   - Check for database connection errors

4. **Verify cron job is running:**
   - Check cron logs: `grep CRON /var/log/syslog` (Linux)
   - Verify task is enabled in Windows Task Scheduler

### Database Connection Issues

If the cron job fails with database connection errors:
- Verify database credentials in `config/database.php`
- Ensure the cron job has access to the config files
- Check file permissions

### Permission Issues

If logs aren't being created:
```bash
chmod 755 logs/
chmod 644 logs/auto_status_update.log
```

---

## Status Flow Reference

```
Pending
  ↓ (Admin approves)
Approved
  ↓ (pickup_date arrives + service = pickup/both)
For Pickup
  ↓ (pickup_date arrives)
Picked Up
  ↓ (automatic)
To Inspect
  ↓ (Admin completes inspection)
For Repair
  ↓ (Admin starts repair)
Under Repair / In Progress
  ↓ (Admin marks complete)
Completed
  ↓ (Payment received)
Paid
```

---

## Support

For issues or questions:
1. Check the log files first
2. Review PHP error logs
3. Verify database connection
4. Test with a manual script run

---

**Last Updated:** 2025-01-03

