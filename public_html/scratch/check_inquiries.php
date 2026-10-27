<?php
require_once __DIR__ . '/../admin/includes/db.php';

echo "<h3>1. Inquiries Table Structure</h3>";
$r = $conn->query("SHOW TABLES LIKE 'inquiries'");
if ($r->num_rows === 0) {
    echo "<b style='color:red'>TABLE 'inquiries' DOES NOT EXIST!</b>";
} else {
    echo "Table exists. Columns:<br>";
    $r2 = $conn->query("DESCRIBE inquiries");
    echo "<table border='1' cellpadding='5'><tr><th>Field</th><th>Type</th><th>Null</th><th>Default</th></tr>";
    while($row = $r2->fetch_assoc()) {
        echo "<tr><td>{$row['Field']}</td><td>{$row['Type']}</td><td>{$row['Null']}</td><td>{$row['Default']}</td></tr>";
    }
    echo "</table>";
}

echo "<br><h3>2. Test Direct Insert</h3>";
$test = $conn->query("INSERT INTO inquiries (contact_name, email, phone, requested_product, message, source, status, created_at) VALUES ('Test User','test@test.com','9999999999','Green Cardamom','Test message','website','new',NOW())");
if ($test) {
    echo "<b style='color:green'>Insert SUCCESS! ID: " . $conn->insert_id . "</b>";
} else {
    echo "<b style='color:red'>Insert FAILED: " . $conn->error . "</b>";
}

echo "<br><br><h3>3. All Tables in DB</h3>";
$r3 = $conn->query("SHOW TABLES");
while($row = $r3->fetch_row()) echo $row[0] . "<br>";
