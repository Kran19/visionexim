<?php
require_once __DIR__ . '/includes/product-functions.php';

$slug = trim($_GET['slug'] ?? '');
$product = ve_get_product_by_slug($slug);

if (!$product) {
    http_response_code(404);
    $page_title = 'Product Not Found | Vision Exim';
    include 'includes/header.php';
    include 'includes/navbar.php';
    ?>
    <section class="inner_banner banner-products">
        <div class="container text-center py-5">
            <h2>Product Not Found</h2>
            <p class="mt-3">This product may be unpublished or the link is incorrect.</p>
            <a href="<?= htmlspecialchars(ve_url('our-products.php')) ?>" class="hero-btn mt-4 d-inline-block">Browse Products</a>
        </div>
    </section>
    <?php
    include 'includes/footer.php';
    exit;
}

$page_title = !empty($product['seo_title'])
    ? $product['seo_title']
    : htmlspecialchars($product['name']) . ' | Vision Exim';

$product_name = $product['name'];
$product_image = ve_product_image_url($product['image'] ?? null);
$product_url = 'http' . ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 's' : '') . '://'
    . ($_SERVER['HTTP_HOST'] ?? 'localhost') . ve_product_url($product['slug']);
$description = !empty($product['full_description'])
    ? $product['full_description']
    : ($product['short_description'] ?? '');
$wa_text = rawurlencode("Hello,\nI want to know about the product {$product_name}.\n\n{$product_url}");

include 'includes/header.php';
include 'includes/navbar.php';
?>

<section class="inner_banner banner-products">
    <div class="container">
        <div class="title text-center">
            <h2><?php include __DIR__ . '/includes/product-butterfly-svg.php'; ?> <?= htmlspecialchars($product_name) ?></h2>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center">
                <li class="breadcrumb-item"><a href="<?= htmlspecialchars(ve_url('index.php')) ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= htmlspecialchars(ve_url('our-products.php')) ?>">Our Products</a></li>
                <?php if (!empty($product['category_name'])): ?>
                <li class="breadcrumb-item"><?= htmlspecialchars($product['category_name']) ?></li>
                <?php endif; ?>
                <li class="breadcrumb-item"><?= htmlspecialchars($product_name) ?></li>
            </ol>
        </nav>
    </div>
</section>

<img src="https://morisoverseas.com/images/top-shape.png" alt="" class="d-block min-vw-100">

<section class="product-details-section bg-white">
    <div class="container">
        <div class="row gy-5">
            <!-- Product Image Column -->
            <div class="col-lg-5 col-md-12" data-aos="fade-right">
                <div class="product-detail-img">
                    <div class="product-detail-img-inner">
                        <img src="<?= htmlspecialchars($product_image) ?>" alt="<?= htmlspecialchars($product_name) ?>" class="img-fluid" id="mainProductImg">
                    </div>
                    <?php 
                    $gallery = [];

                    // Always start with the main image
                    if (!empty($product['image'])) {
                        $gallery[] = ve_product_image_url($product['image']);
                    }

                    // Add real gallery images from DB
                    if (!empty($product['gallery_images'])) {
                        $decoded = json_decode($product['gallery_images'], true);
                        if (is_array($decoded)) {
                            foreach ($decoded as $g) {
                                $g_url = ve_product_image_url($g);
                                if (!in_array($g_url, $gallery)) {
                                    $gallery[] = $g_url;
                                }
                            }
                        }
                    }
                    ?>
                    <?php if (count($gallery) > 1): ?>
                    <div class="product-gallery-thumbs mt-3">
                        <?php foreach ($gallery as $index => $img_url): 
                            $active_class = ($index === 0) ? 'active' : '';
                        ?>
                        <img src="<?= htmlspecialchars($img_url) ?>" alt="Product thumbnail" class="img-thumbnail cursor-pointer <?= $active_class ?>" onclick="switchMainImage(this, '<?= htmlspecialchars($img_url) ?>')">
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Product Details Column -->
            <div class="col-lg-7 col-md-12" data-aos="fade-left">
                <!-- Product Title -->
                <div class="product-detail-header mb-4">
                    <h1 class="product-title"><?= htmlspecialchars($product_name) ?></h1>
                </div>
                
                <!-- Product Details Table -->
                <div class="product-attributes mb-4">
                    <table class="table table-borderless product-details-table">
                        <tbody>
                            <tr>
                                <td class="attr-label">Product:</td>
                                <td class="attr-value"><?= htmlspecialchars($product_name) ?></td>
                            </tr>
                            <?php if (!empty($product['origin_country'])): ?>
                            <tr>
                                <td class="attr-label">Origin:</td>
                                <td class="attr-value"><?= htmlspecialchars($product['origin_country']) ?></td>
                            </tr>
                            <?php endif; ?>
                            <?php if (!empty($product['origin_state'])): ?>
                            <tr>
                                <td class="attr-label">Harvesting Season:</td>
                                <td class="attr-value"><?= htmlspecialchars($product['origin_state']) ?></td>
                            </tr>
                            <?php endif; ?>
                            <?php if (!empty($product['quality_standard'])): ?>
                            <tr>
                                <td class="attr-label">Quality:</td>
                                <td class="attr-value"><?= htmlspecialchars($product['quality_standard']) ?></td>
                            </tr>
                            <?php endif; ?>
                            <?php if (!empty($product['moq'])): ?>
                            <tr>
                                <td class="attr-label">Purity:</td>
                                <td class="attr-value"><?= htmlspecialchars($product['moq']) ?></td>
                            </tr>
                            <?php endif; ?>
                            <tr>
                                <td class="attr-label">GMO Status:</td>
                                <td class="attr-value">Non-GMO</td>
                            </tr>
                            <?php if (!empty($product['packaging'])): ?>
                            <tr>
                                <td class="attr-label">Packaging:</td>
                                <td class="attr-value"><?= htmlspecialchars($product['packaging']) ?></td>
                            </tr>
                            <?php endif; ?>
                            <?php if (!empty($product['hs_code'])): ?>
                            <tr>
                                <td class="attr-label">HS Code:</td>
                                <td class="attr-value"><?= htmlspecialchars($product['hs_code']) ?></td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Enquiry Button -->
                <div class="product-enquiry-btn mb-4">
                    <button class="btn btn-enquiry" data-bs-toggle="modal" href="#InqueryModal">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="2" y="7" width="20" height="12" rx="2" ry="2"></rect>
                            <path d="M16 21H8a2 2 0 01-2-2V7m18 0V9a2 2 0 01-2 2M2 9a2 2 0 012-2h16a2 2 0 012 2"></path>
                        </svg>
                        Product Enquiry
                    </button>
                </div>
                
                <!-- Short Description -->
                <?php if (!empty($product['short_description'])): ?>
                <div class="product-short-desc mb-4">
                    <p class="fw-semibold text-muted"><?= nl2br(htmlspecialchars($product['short_description'])) ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Full Description Section -->
        <?php if (!empty($description)): ?>
        <div class="row mt-5 pt-5 border-top">
            <div class="col-lg-8">
                <h3 class="mb-4">Product Description</h3>
                <div class="product-full-description">
                    <?= nl2br(htmlspecialchars($description)) ?>
                </div>
            </div>
            
            <!-- Related Products Sidebar -->
            <div class="col-lg-4 ps-lg-4">
                <div class="related-products-section">
                    <h4 class="related-products-title mb-4">Related Products</h4>
                    <div class="related-products-list">
                        <?php 
                        // Get other products from same category
                        $related = ve_get_products_by_category($product['category_id'], 7, $product['id']);
                        if (!empty($related)):
                            foreach ($related as $rel):
                                $rel_url = ve_product_url($rel['slug']);
                        ?>
                        <a href="<?= htmlspecialchars($rel_url) ?>" class="related-product-item">
                            <span class="related-product-arrow">&gt;</span>
                            <span class="related-product-name"><?= htmlspecialchars($rel['name']) ?></span>
                        </a>
                        <?php 
                            endforeach;
                        else:
                        ?>
                        <p class="text-muted small">No related products available.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<div class="modal fade inquery-modal" id="InqueryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form class="needs-validation" id="productenq_form" novalidate>
                <input type="hidden" name="subject" value="<?= htmlspecialchars($product_name) ?>">
                <div class="inquiry_heading">
                    <h2>Product Enquiry</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div id="emailMsg"></div>
                <div class="form-feild"><input type="text" name="name" class="form-control" placeholder="Full Name*" required /></div>
                <div class="form-feild"><input type="text" name="company" class="form-control" placeholder="Company Name" /></div>
                <div class="form-feild"><input type="email" name="email" class="form-control" placeholder="Email Address*" required /></div>
                <div class="form-feild"><input type="text" name="phone" class="form-control" placeholder="Phone Number" /></div>
                <div class="form-feild"><input type="text" name="country" class="form-control" placeholder="Country &amp; Region" /></div>
                <div class="form-feild"><input type="text" name="quantity" class="form-control" placeholder="Quantity (e.g. 100 kg, 5 MT)" /></div>
                <div class="form-feild"><textarea class="form-control" name="message" placeholder="Message"></textarea></div>
                <div class="form-submit">
                    <button type="submit" class="btn" id="btn_submit"><span>Submit</span></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="logo_shapewrp">
    <img src="https://morisoverseas.com/images/bottom-shape.png" alt="" class="d-block min-vw-100">
</div>

<script>
document.getElementById('productenq_form').addEventListener('submit', function(e) {
    e.preventDefault();
    var form = this;
    var btn = document.getElementById('btn_submit');
    var msgDiv = document.getElementById('emailMsg');

    if (!form.checkValidity()) {
        form.classList.add('was-validated');
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<span>Sending...</span>';
    msgDiv.innerHTML = '';

    var data = new FormData(form);

    fetch('<?= htmlspecialchars(ve_url('submit-inquiry.php')) ?>', {
        method: 'POST',
        body: data
    })
    .then(function(res) { return res.json(); })
    .then(function(json) {
        if (json.success) {
            msgDiv.innerHTML = '<div style="color:#1a7a3c;background:#eafaf1;padding:10px 14px;border-radius:8px;margin-bottom:10px;font-size:14px;">✅ ' + json.message + '</div>';
            form.reset();
            form.classList.remove('was-validated');
            setTimeout(function() {
                var modal = bootstrap.Modal.getInstance(document.getElementById('InqueryModal'));
                if (modal) modal.hide();
                msgDiv.innerHTML = '';
            }, 2500);
        } else {
            msgDiv.innerHTML = '<div style="color:#c0191a;background:#fdf0f0;padding:10px 14px;border-radius:8px;margin-bottom:10px;font-size:14px;">❌ ' + json.message + '</div>';
        }
    })
    .catch(function() {
        msgDiv.innerHTML = '<div style="color:#c0191a;background:#fdf0f0;padding:10px 14px;border-radius:8px;margin-bottom:10px;font-size:14px;">❌ Server error. Please try again.</div>';
    })
    .finally(function() {
        btn.disabled = false;
        btn.innerHTML = '<span>Submit</span>';
    });
});

function switchMainImage(thumbElement, imageUrl) {
    const mainImg = document.getElementById('mainProductImg');
    if (mainImg) {
        mainImg.src = imageUrl;
    }
    // Remove active class from all sibling thumbnails
    const container = thumbElement.parentElement;
    if (container) {
        const thumbs = container.querySelectorAll('.img-thumbnail');
        thumbs.forEach(t => t.classList.remove('active'));
    }
    // Add active class to clicked thumbnail
    thumbElement.classList.add('active');
}
</script>

<?php include 'includes/footer.php'; ?>
