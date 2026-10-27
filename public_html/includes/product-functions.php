<?php
/**
 * Vision Exim — Frontend product helpers
 */

require_once __DIR__ . '/config.php';

function ve_db(): mysqli
{
    global $conn;
    if (!isset($conn) || !($conn instanceof mysqli)) {
        require_once __DIR__ . '/../admin/includes/db.php';
    }
    return $conn;
}

function ve_product_image_url(?string $image_path): string
{
    if (!empty($image_path)) {
        return ve_url(ltrim($image_path, '/'));
    }
    return ve_url('aaa.webp');
}

function ve_product_url(string $slug): string
{
    return ve_url('product.php?slug=' . urlencode($slug));
}

/** @return array<int, array<string, mixed>> */
function ve_get_featured_products(int $limit = 4): array
{
    $conn = ve_db();
    $products = [];
    $sql = "SELECT p.id, p.name, p.slug, p.short_description, p.image,
                   c.name AS category_name
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.status = 'active' AND p.is_featured = 1
            ORDER BY p.sort_order ASC, p.name ASC
            LIMIT ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        return $products;
    }
    $stmt->bind_param('i', $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    $stmt->close();
    return $products;
}

function ve_get_product_by_slug(string $slug): ?array
{
    $slug = trim($slug);
    if ($slug === '') {
        return null;
    }
    $conn = ve_db();
    $sql = "SELECT p.*, c.name AS category_name, c.slug AS category_slug
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.slug = ? AND p.status = 'active'
            LIMIT 1";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        return null;
    }
    $stmt->bind_param('s', $slug);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $row ?: null;
}

/** @return array<int, array<string, mixed>> */
function ve_get_all_categories(): array
{
    $conn = ve_db();
    $categories = [];
    $result = $conn->query("SELECT id, name, slug, image, description FROM categories WHERE status = 'active' ORDER BY sort_order ASC, name ASC");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
    }
    return $categories;
}

/** @return array<int, array<string, mixed>> */
function ve_get_parent_categories(): array
{
    $conn = ve_db();
    $categories = [];
    $result = $conn->query("SELECT id, name, slug, image, description FROM categories WHERE status = 'active' AND parent_id IS NULL ORDER BY sort_order ASC, name ASC");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
    }
    return $categories;
}

/** @return array<int, array<string, mixed>> */
function ve_get_subcategories(int $parent_id): array
{
    $conn = ve_db();
    $categories = [];
    $stmt = $conn->prepare("SELECT id, name, slug, image, description FROM categories WHERE status = 'active' AND parent_id = ? ORDER BY sort_order ASC, name ASC");
    if ($stmt) {
        $stmt->bind_param('i', $parent_id);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
        $stmt->close();
    }
    return $categories;
}

/** @return array<int, array<string, mixed>> */
function ve_get_products_by_category(int $category_id, int $limit = 3, int $exclude_id = 0): array
{
    $conn = ve_db();
    $products = [];
    $sql = "SELECT p.id, p.name, p.slug, p.short_description, p.image
            FROM products p
            WHERE p.category_id = ? AND p.status = 'active' AND p.id != ?
            ORDER BY p.sort_order ASC, p.name ASC
            LIMIT ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        return $products;
    }
    $stmt->bind_param('iii', $category_id, $exclude_id, $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    $stmt->close();
    return $products;
}
