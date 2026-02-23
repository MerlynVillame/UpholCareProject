<?php
$file = 'c:/xampp/htdocs/UphoCare/views/admin/all_bookings.php';
$content = file_get_contents($file);

$newContent = str_replace('<td colspan="8" class="text-center py-4">', '<td colspan="9" class="text-center py-4">', $content);

if ($newContent !== $content) {
    file_put_contents($file, $newContent);
    echo "Successfully updated colspan in all_bookings.php.";
} else {
    echo "Could not find colspan=\"8\" in all_bookings.php.";
}
