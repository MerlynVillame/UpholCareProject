<?php
$file = 'c:/xampp/htdocs/UphoCare/views/admin/all_bookings.php';
$content = file_get_contents($file);

// 1. RE-ORDER ACTIVE BOOKINGS BODY
// Find the sequence: Category Cell -> Mode Cell -> Service Option Cell
// And change to: Category Cell -> Service Option Cell -> Mode Cell

$searchActive = '/(<td class="text-center">\s*<span class="badge badge-light border text-primary px-2 py-1">.*?<\/td>\s*)<td>\s*(<\?php if \((\$booking\[\'booking_type\'\] ?? \'personal\') === \'personal\'\): \?>.*?<\/td>\s*)(<td>\s*<\?php\s*\$serviceOption = strtolower.*?<\/div>\s*<\/td>)/is';

// Replacement swaps the 2nd and 3rd capture groups
$replacementActive = '$1$3$2';

$newContent = preg_replace($searchActive, $replacementActive, $content);

if ($newContent !== $content) {
    echo "Reordered active bookings columns.\n";
    $content = $newContent;
} else {
    echo "Could not reorder active bookings columns (maybe pattern didn't match).\n";
}

// 2. RE-ORDER COMPLETED BOOKINGS BODY
// Similar pattern for completed bookings
$searchCompleted = '/(<td class="text-center">\s*<span class="badge badge-light border text-primary px-2 py-1">.*?<\/td>\s*)<td>\s*(<\?php if \((\$booking\[\'booking_type\'\] ?? \'personal\') === \'personal\'\): \?>.*?<\/td>\s*)(<td>\s*<\?php\s*\$serviceOption = strtolower.*?<\/div>\s*<\/td>)/is';

// The pattern is slightly different due to $serviceOptionValue sometimes being used, but in my view_file it looked identical for the config part.
// Actually, let's use a simpler match for the Service Option cell start.

$newContent = preg_replace($searchCompleted, $replacementActive, $content);

if ($newContent !== $content) {
    echo "Reordered completed bookings columns.\n";
    $content = $newContent;
} else {
    echo "Could not reorder completed bookings columns.\n";
}

file_put_contents($file, $content);
echo "Finished alignment fix.\n";
