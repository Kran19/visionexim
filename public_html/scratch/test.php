<?php
require_once __DIR__ . '/../admin/includes/db.php';
$r = $conn->query("SELECT * FROM harvest_calendar WHERE id = 2");
print_r($r->fetch_assoc());
