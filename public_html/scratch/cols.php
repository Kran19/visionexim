<?php
require_once __DIR__ . '/../admin/includes/db.php';
$r = $conn->query("DESCRIBE inquiries");
while($row = $r->fetch_assoc()) echo $row['Field'] . '<br>';
