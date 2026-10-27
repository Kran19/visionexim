<?php
$files = [
    'images/vision-exim-logo.jpg',
    'images/vision-exim-logo-square.jpg',
    'images/certificates/vision logo color (1).png'
];

foreach ($files as $f) {
    $full = __DIR__ . '/../' . $f;
    echo "$f exists: " . (file_exists($full) ? 'YES' : 'NO') . "\n";
}
