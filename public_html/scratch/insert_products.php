<?php
require_once __DIR__ . '/../admin/includes/db.php';

$items = [
    ['Mustard Seeds',   'mustard-seeds',   5, '1207.10', 'Yellow/Black mustard seeds, cleaned and sorted for export.', 'India', 'Gujarat'],
    ['Sesame Seeds',    'sesame-seeds',    5, '1207.40', 'Natural white and black sesame seeds, export quality.',      'India', 'Gujarat'],
    ['Coriander Seeds', 'coriander-seeds', 5, '0909.21', 'Whole coriander seeds with rich aroma and flavor.',          'India', 'Rajasthan'],
    ['Fennel Seeds',    'fennel-seeds',    5, '0909.61', 'Premium fennel seeds with sweet aroma, export grade.',       'India', 'Gujarat'],
];

foreach ($items as $p) {
    $name = $p[0]; $slug = $p[1]; $cat = $p[2]; $hs = $p[3]; $desc = $p[4]; $country = $p[5]; $state = $p[6];
    $check = $conn->prepare("SELECT id FROM products WHERE slug = ?");
    $check->bind_param('s', $slug);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) {
        echo "Already exists: $name<br>";
        $check->close();
        continue;
    }
    $check->close();

    $stmt = $conn->prepare("INSERT INTO products (name, slug, category_id, hs_code, short_description, origin_country, origin_state, status, sort_order) VALUES (?,?,?,?,?,?,?,'active',0)");
    $stmt->bind_param('ssissss', $name, $slug, $cat, $hs, $desc, $country, $state);
    if ($stmt->execute()) {
        echo "Inserted: $name (ID: {$conn->insert_id})<br>";
    } else {
        echo "Error on $name: {$stmt->error}<br>";
    }
    $stmt->close();
}

echo "<br>Done. <a href='/vision_exim/pure-ground-spices.php'>Go to Pure Ground Spices</a>";
