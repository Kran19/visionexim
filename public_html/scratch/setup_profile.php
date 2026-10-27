<?php
require_once __DIR__ . '/../admin/includes/db.php';

// Add phone column if not exists
$conn->query("ALTER TABLE admin_users ADD COLUMN IF NOT EXISTS phone VARCHAR(20) DEFAULT NULL");
$conn->query("ALTER TABLE admin_users ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP");

echo "<h3>admin_users columns:</h3>";
$r = $conn->query("DESCRIBE admin_users");
while($row = $r->fetch_assoc()) echo $row['Field'].' | '.$row['Type'].'<br>';

echo "<br><h3>admin_users data:</h3>";
$r2 = $conn->query("SELECT id, name, email, phone, created_at FROM admin_users");
while($row = $r2->fetch_assoc()) {
    echo "ID: {$row['id']} | Name: {$row['name']} | Email: {$row['email']}<br>";
}

echo "<br><b style='color:green'>Done!</b> <a href='/vision_exim/admin/profile.php'>Go to Profile Page</a>";
