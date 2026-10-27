<?php
require_once __DIR__ . '/../includes/product-functions.php';
$featured_products = ve_get_featured_products(4);
echo "Count: " . count($featured_products) . "\n";
foreach ($featured_products as $p) {
    echo "- " . $p['name'] . " => " . ve_product_url($p['slug']) . "\n";
}
