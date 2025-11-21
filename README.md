UphoCare – Data Export (Download) Guide

This guide shows you how to fetch data from the database and download it as a file (CSV) to your computer (typically the Downloads folder) from within the UphoCare app.

Prerequisites
- PHP 7.4+ (XAMPP on Windows is fine)
- MySQL/MariaDB (same DB you already use for UphoCare)
- UphoCare configured and running at http://localhost/UphoCare/

What you will build
- An endpoint (controller method) that queries the database
- Outputs a CSV with proper headers so the browser downloads it automatically
- Works for any dataset (example uses customer bookings)

1) Add an Export Action (Controller)
Add this method to controllers/CustomerController.php (anywhere in the class):

```
/**
 * Download my bookings as CSV
 */
public function downloadMyBookingsCsv() {
    $userId = $this->currentUser()['id'];
    $db = Database::getInstance()->getConnection();

    $sql = "SELECT b.id, bn.booking_number, s.service_name, b.status, b.total_amount, b.created_at
            FROM bookings b
            LEFT JOIN booking_numbers bn ON b.booking_number_id = bn.id
            LEFT JOIN services s ON b.service_id = s.id
            WHERE b.user_id = ?
            ORDER BY b.created_at DESC";

    $stmt = $db->prepare($sql);
    $stmt->execute([$userId]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Prepare CSV headers
    $filename = 'my_bookings_' . date('Ymd_His') . '.csv';
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=' . $filename);
    header('Pragma: no-cache');
    header('Expires: 0');

    $output = fopen('php://output', 'w');

    // Optional: column headers
    fputcsv($output, ['ID', 'Booking Number', 'Service', 'Status', 'Total Amount', 'Created At']);

    foreach ($rows as $row) {
        fputcsv($output, [
            $row['id'],
            $row['booking_number'] ?? '',
            $row['service_name'] ?? '',
            $row['status'] ?? '',
            number_format((float)($row['total_amount'] ?? 0), 2),
            $row['created_at'] ?? ''
        ]);
    }

    fclose($output);
    exit; // important to stop further output
}
```

Notes:
- This is a read-only query; adjust columns as needed.
- You can copy this pattern for any export (repairs, users, payments).

2) Add a Route/Link to Trigger the Download
Add a button or link anywhere in your views (for example in views/customer/bookings.php near the header):

```
<a href="<?php echo BASE_URL; ?>customer/downloadMyBookingsCsv" class="btn btn-outline-secondary">
    <i class="fas fa-file-csv mr-1"></i> Download CSV
</a>
```

Clicking this will call the controller action and your browser will download the CSV file to your Downloads folder.

3) Export as JSON (Optional)
If you prefer JSON downloads instead of CSV, switch headers:

```
header('Content-Type: application/json');
header('Content-Disposition: attachment; filename=my_bookings_' . date('Ymd_His') . '.json');

echo json_encode($rows, JSON_PRETTY_PRINT);
exit;
```

4) Common Issues
- If you see HTML in the CSV: make sure nothing else is echoed before headers (no spaces/prints). Keep exit; at the end.
- If Windows opens the CSV in Excel with wrong characters: ensure charset=utf-8 is in the Content-Type header.
- If download doesn’t start: verify the URL http://localhost/UphoCare/customer/downloadMyBookingsCsv and that you’re logged in as a customer.

5) Adapting for Admin Exports
- Copy the method into AdminController.php.
- Change the SQL to remove the WHERE b.user_id = ? filter or target specific filters/date ranges.
- Add an admin page button: <?php echo BASE_URL; ?>admin/downloadAllBookingsCsv

That’s it. You now have a simple, reliable way to fetch data from the database and download it to your machine.


