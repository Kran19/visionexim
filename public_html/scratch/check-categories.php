<?php
require_once __DIR__ . '/../admin/includes/db.php';
$r = $conn->query("SELECT id, name, slug, parent_id, status FROM categories ORDER BY sort_order ASC, name ASC");
if (!$r) {
    echo "ERROR: " . $conn->error;
    exit(1);
}
echo "Total categories: " . $r->num_rows . "\n";
while ($row = $r->fetch_assoc()) {
    echo json_encode($row) . "\n";
}
