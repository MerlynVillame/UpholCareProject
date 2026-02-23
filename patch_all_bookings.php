<?php
$file = 'c:/xampp/htdocs/UphoCare/views/admin/all_bookings.php';
$content = file_get_contents($file);

// Use a regex to match the category cell, account for any whitespace/newlines
$pattern = '/<td class="text-center">\s*<span class="badge badge-light border text-primary px-2 py-1">\s*<\?php echo htmlspecialchars\(\$booking\[\'category_name\'\]\);\s*\?>\s*<\/span>\s*<\/td>\s*<td>/i';

$replacement = '<td class="text-center">
                                                    <span class="badge badge-light border text-primary px-2 py-1"><?php echo htmlspecialchars($booking[\'category_name\']); ?></span>
                                                </td>
                                                <td>
                                                    <?php if (($booking[\'booking_type\'] ?? \'personal\') === \'personal\'): ?>
                                                        <span class="badge badge-success px-2 py-1 shadow-sm" style="font-size: 0.7rem;">
                                                            <i class="fas fa-user-circle mr-1"></i> LOCAL
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge badge-primary px-2 py-1 shadow-sm" style="font-size: 0.7rem;">
                                                            <i class="fas fa-briefcase mr-1"></i> BUSINESS
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>';

$newContent = preg_replace($pattern, $replacement, $content);

if ($newContent !== $content) {
    file_put_contents($file, $newContent);
    echo "Successfully updated the file using regex.";
} else {
    echo "Could not find the target content even with regex.";
}
