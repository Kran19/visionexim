<?php
require_once __DIR__ . '/../admin/includes/db.php';

echo "<h3>harvest_calendar rows:</h3>";
$r = $conn->query("SELECT h.id, h.spice_name, h.product_id, p.name as product_name, p.image FROM harvest_calendar h LEFT JOIN products p ON h.product_id = p.id ORDER BY h.id");
echo "<table border='1' cellpadding='6'><tr><th>ID</th><th>Spice Name</th><th>product_id</th><th>Linked Product</th><th>Image</th></tr>";
while($row = $r->fetch_assoc()) {
    echo "<tr>
        <td>{$row['id']}</td>
        <td>{$row['spice_name']}</td>
        <td>{$row['product_id']}</td>
        <td>".($row['product_name'] ?? '<b style=color:red>NOT LINKED</b>')."</td>
        <td>".($row['image'] ?? '<i>no image</i>')."</td>
    </tr>";
}
echo "</table>";

echo "<br><h3>Auto-link now:</h3>";
$harvest = $conn->query("SELECT id, spice_name FROM harvest_calendar WHERE product_id IS NULL OR product_id = 0");
if($harvest && $harvest->num_rows > 0) {
    while($row = $harvest->fetch_assoc()) {
        $name = $conn->real_escape_string($row['spice_name']);
        $match = $conn->query("SELECT id, name FROM products WHERE name LIKE '%$name%' OR '$name' LIKE CONCAT('%',name,'%') LIMIT 1");
        if($match && $match->num_rows > 0) {
            $prod = $match->fetch_assoc();
            $conn->query("UPDATE harvest_calendar SET product_id = {$prod['id']} WHERE id = {$row['id']}");
            echo "✅ Linked: <b>{$row['spice_name']}</b> → <b>{$prod['name']}</b> (ID: {$prod['id']})<br>";
        } else {
            echo "❌ No match for: <b>{$row['spice_name']}</b><br>";
        }
    }
} else {
    echo "All rows already linked.<br>";
}

echo "<br><a href='/vision_exim/admin/modules/harvest/list.php'>Go to Harvest Chart</a>";
