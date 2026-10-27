<?php
require_once __DIR__ . '/includes/product-functions.php';

$conn = ve_db();

// Get category filter from URL
$cat_slug = trim($_GET['category'] ?? '');

// Fetch category info if slug provided
$category = null;
if (!empty($cat_slug)) {
    $stmt = $conn->prepare("SELECT * FROM categories WHERE slug = ? AND status = 'active' LIMIT 1");
    $stmt->bind_param('s', $cat_slug);
    $stmt->execute();
    $category = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

$page_title = $category ? htmlspecialchars($category['name']) . " | Vision Exim" : "Our Products | Vision Exim";

// Fetch products — filtered by category if provided
$products = [];
if ($category) {
    $stmt = $conn->prepare("SELECT id, name, slug, image FROM products WHERE status = 'active' AND (category_id = ? OR category_id IN (SELECT id FROM categories WHERE parent_id = ?)) ORDER BY sort_order ASC, name ASC");
    $stmt->bind_param('ii', $category['id'], $category['id']);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) $products[] = $row;
    $stmt->close();
} else {
    $res = $conn->query("SELECT id, name, slug, image FROM products WHERE status = 'active' ORDER BY sort_order ASC, name ASC");
    if ($res) while ($row = $res->fetch_assoc()) $products[] = $row;
}

include 'includes/header.php';
include 'includes/navbar.php';
?>

<section class="inner_banner banner-products">
    <div class="container">
        <div class="title text-center">
            <h2>
                <?php include __DIR__ . '/includes/product-butterfly-svg.php'; ?>
                <?= $category ? htmlspecialchars($category['name']) : 'Our Products' ?>
            </h2>
            <p><?= $category && !empty($category['description']) ? htmlspecialchars($category['description']) : 'Premium quality products processed with care to retain natural aroma, flavor, and purity for global markets.' ?></p>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center">
                <li class="breadcrumb-item"><a href="<?= htmlspecialchars(ve_url('index.php')) ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= htmlspecialchars(ve_url('our-products.php')) ?>">Our Products</a></li>
                <?php if ($category): ?>
                <li class="breadcrumb-item"><?= htmlspecialchars($category['name']) ?></li>
                <?php endif; ?>
            </ol>
        </nav>
    </div>
</section>

<img src="https://morisoverseas.com/images/top-shape.png" alt="" class="d-block min-vw-100">

<section class="product-collections-section bg-white">
    <div class="container">
        <div class="row justify-content-center">
            <?php if (empty($products)): ?>
            <div class="col-12 text-center py-5">
                <p class="text-muted">No products available in this category at the moment.</p>
                <a href="<?= htmlspecialchars(ve_url('our-products.php')) ?>" class="btn mt-3">Browse All Categories</a>
            </div>
            <?php else: ?>
            <?php foreach ($products as $p):
                $p_url  = ve_product_url($p['slug']);
                $p_img  = ve_product_image_url($p['image'] ?? null);
                $p_name = htmlspecialchars($p['name']);
            ?>
            <div class="col-lg-3 col-md-4" data-aos="zoom-in">
                <div class="collections-card">
                    <div class="collections-img">
                        <img src="<?= htmlspecialchars($p_img) ?>" alt="<?= $p_name ?>">
                    </div>
                    <h5><a href="<?= htmlspecialchars($p_url) ?>"><?= $p_name ?></a></h5>
                    <a href="<?= htmlspecialchars($p_url) ?>" class="btn">VIEW Details
                        <svg width="7" height="12" viewBox="0 0 7 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M1 10.5203L6 6.02026L1 1.52026" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<div class="logo_shapewrp">
    <img src="https://morisoverseas.com/images/bottom-shape.png" alt="Bottom Shape" class="d-block min-vw-100">
</div>

<?php include 'includes/footer.php'; ?>
