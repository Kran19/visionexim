<?php
require_once __DIR__ . '/../admin/includes/db.php';
echo "<h3>harvest_calendar columns:</h3>";
$r = $conn->query("DESCRIBE harvest_calendar");
while($row = $r->fetch_assoc()) echo $row['Field'].' | '.$row['Type'].'<br>';

echo "<h3>harvest_calendar rows:</h3>";
$r2 = $conn->query("SELECT id, spice_name FROM harvest_calendar ORDER BY id");
while($row = $r2->fetch_assoc()) echo $row['id'].' | '.$row['spice_name'].'<br>';

echo "<h3>products (id, name, slug, image):</h3>";
$r3 = $conn->query("SELECT id, name, slug, image FROM products ORDER BY id");
while($row = $r3->fetch_assoc()) echo $row['id'].' | '.$row['name'].' | '.$row['slug'].' | '.$row['image'].'<br>';
