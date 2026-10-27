<?php
require_once __DIR__ . '/../admin/includes/db.php';
$r = $conn->query("SELECT id, name, image, gallery_images FROM products ORDER BY id");
while($row = $r->fetch_assoc()) {
    echo "<b>{$row['id']} | {$row['name']}</b><br>";
    echo "Main image: " . ($row['image'] ?: 'NONE') . "<br>";
    echo "Gallery: " . ($row['gallery_images'] ?: 'NONE') . "<br><br>";
}
