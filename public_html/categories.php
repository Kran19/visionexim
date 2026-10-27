<?php
$page_title = "Categories - Vision Exim";
require_once 'includes/product-functions.php';
$categories = ve_get_parent_categories();
include 'includes/header.php';
include 'includes/navbar.php';
?>

<section class="inner_banner banner-products">
    <div class="container">
        <div class="title text-center">
            <h2>
                <?php include __DIR__ . '/includes/product-butterfly-svg.php'; ?>
                Categories
            </h2>
            <p>Discover our extensive range of premium, export-quality spices and agricultural products, sourced directly from the finest farms.</p>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center">
                <li class="breadcrumb-item"><a href="<?= htmlspecialchars(ve_url('index.php')) ?>">Home</a></li>
                <li class="breadcrumb-item">Categories</li>
            </ol>
        </nav>
    </div>
</section>

<section class="product-section bg-white">
    <div class="container">
        <div class="product-box-wpr">
            <?php if (empty($categories)): ?>
                <p class="text-center text-muted py-5">No categories found.</p>
            <?php else: ?>
                <?php foreach ($categories as $i => $cat): ?>
                    <?php
                        $num = str_pad($i + 1, 2, '0', STR_PAD_LEFT);
                        $is_even = ($i % 2 !== 0);
                        $cat_image = !empty($cat['image'])
                            ? ve_url(ltrim((string)$cat['image'], '/'))
                            : ve_url('aaa.webp');
                        $cat_link = ve_url('pure-ground-spices.php?category=' . urlencode((string)$cat['slug']));
                    ?>
                    <div class="product-box">
                        <span><?= $num ?></span>
                        <div class="row align-items-center">
                            <div class="col-md-6<?= $is_even ? ' order-md-2' : '' ?>">
                                <div class="product-box-text">
                                    <h2><?= htmlspecialchars($cat['name']) ?></h2>
                                    <?php if (!empty($cat['description'])): ?>
                                        <p data-aos="fade-up"><?= htmlspecialchars($cat['description']) ?></p>
                                    <?php endif; ?>
                                    
                                    <?php
                                    $subs = ve_get_subcategories((int)$cat['id']);
                                    if (!empty($subs)): ?>
                                        <div class="subcategory-links mb-4" data-aos="fade-up" style="display: flex; flex-wrap: wrap; gap: 15px; margin-top: 15px;">
                                            <?php foreach ($subs as $sub): ?>
                                                <a href="<?= ve_url('pure-ground-spices.php?category=' . urlencode($sub['slug'])) ?>" class="sub-link" style="font-size: 14px; font-weight: 600; color: var(--primary); text-decoration: none; display: inline-flex; align-items: center; transition: color 0.3s;">
                                                    <span style="margin-right: 6px;">&rarr;</span><?= htmlspecialchars($sub['name']) ?>
                                                </a>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                    <div data-aos="fade-up">
                                        <a href="<?= $cat_link ?>" class="btn">VIEW PRODUCTS
                                            <svg width="7" height="12" viewBox="0 0 7 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M1 10.5203L6 6.02026L1 1.52026" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6<?= $is_even ? ' order-md-1' : '' ?>">
                                <div class="product-img align-items-center <?= $is_even ? 'justify-content-start' : 'justify-content-end' ?> d-flex" data-aos="zoom-in">
                                    <img src="<?= htmlspecialchars($cat_image) ?>" alt="<?= htmlspecialchars($cat['name']) ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
