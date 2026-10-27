<?php
require_once __DIR__ . '/../admin/includes/db.php';
$r = $conn->query("SELECT id, name, slug, status, is_featured FROM products ORDER BY sort_order ASC");
if (!$r) {
    echo "ERROR: " . $conn->error;
    exit(1);
}
echo "Total: " . $r->num_rows . "\n";
while ($row = $r->fetch_assoc()) {
    echo json_encode($row) . "\n";
}
$r2 = $conn->query("SELECT COUNT(*) c FROM products WHERE status='active' AND is_featured=1");
$row = $r2->fetch_assoc();
echo "Featured active: " . $row['c'] . "\n";
