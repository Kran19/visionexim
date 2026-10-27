<?php
require_once __DIR__ . '/../admin/includes/db.php';
$r = $conn->query("DESCRIBE admin_users");
while($row = $r->fetch_assoc()) echo $row['Field'].' | '.$row['Type'].'<br>';
