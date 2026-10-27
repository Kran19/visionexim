<?php 
/**
 * Vision Exim — Edit Spice Product
 */
include '../../includes/auth.php';
include '../../includes/db.php';

$page_title = 'Edit Product';
$current_module = 'products';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: list.php?error=' . urlencode('Invalid product ID.'));
    exit;
}

// Fetch current product record
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$product) {
    header('Location: list.php?error=' . urlencode('Product not found.'));
    exit;
}

$errors = [];
$old_name         = $product['name'];
$old_slug         = $product['slug'];
$old_category     = $product['category_id'];
$old_hs           = $product['hs_code'];
$old_short        = $product['short_description'];
$old_full         = $product['full_description'];
$old_moq          = $product['moq'];
$old_packaging    = $product['packaging'];
$old_quality      = $product['quality_standard'];
$old_origin_state = $product['origin_state'];
$old_sort         = $product['sort_order'];
$old_featured     = $product['is_featured'];
$old_status       = $product['status'];
$old_image        = $product['image'];
$old_seo_title    = $product['seo_title'];
$old_seo_desc     = $product['seo_description'];

// Fetch active categories
$categories = [];
$res = $conn->query("SELECT id, name, parent_id FROM categories WHERE status = 'active' ORDER BY sort_order ASC, name ASC");
if ($res) {
    $temp_cats = [];
    while ($row = $res->fetch_assoc()) {
        $temp_cats[] = $row;
    }
    
    $parents = [];
    $children = [];
    foreach ($temp_cats as $c) {
        if ($c['parent_id'] === null) {
            $parents[$c['id']] = $c;
            $parents[$c['id']]['children'] = [];
        } else {
            $children[] = $c;
        }
    }
    
    foreach ($children as $child) {
        if (isset($parents[$child['parent_id']])) {
            $parents[$child['parent_id']]['children'][] = $child;
        } else {
            $parents[$child['id']] = $child;
            $parents[$child['id']]['children'] = [];
        }
    }
    
    foreach ($parents as $p) {
        $categories[] = [
            'id' => $p['id'],
            'name' => $p['name']
        ];
        if (!empty($p['children'])) {
            foreach ($p['children'] as $child) {
                $categories[] = [
                    'id' => $child['id'],
                    'name' => '— ' . $child['name']
                ];
            }
        }
    }
}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name             = trim($_POST['name'] ?? '');
    $slug             = trim($_POST['slug'] ?? '');
    $category_id      = (int)($_POST['category_id'] ?? 0);
    $hs_code          = trim($_POST['hs_code'] ?? '');
    $short_description= trim($_POST['short_description'] ?? '');
    $full_description = trim($_POST['full_description'] ?? '');
    $moq              = trim($_POST['moq'] ?? '');
    $packaging        = trim($_POST['packaging'] ?? '');
    $quality_standard = trim($_POST['quality_standard'] ?? '');
    $origin_state     = trim($_POST['origin_state'] ?? '');
    $status           = trim($_POST['status'] ?? 'draft');
    $sort_order       = (int)($_POST['sort_order'] ?? 0);
    $is_featured      = isset($_POST['is_featured']) ? 1 : 0;
    $seo_title        = trim($_POST['seo_title'] ?? '');
    $seo_description  = trim($_POST['seo_description'] ?? '');

    $old_name         = $name;
    $old_slug         = $slug;
    $old_category     = $category_id;
    $old_hs           = $hs_code;
    $old_short        = $short_description;
    $old_full         = $full_description;
    $old_moq          = $moq;
    $old_packaging    = $packaging;
    $old_quality      = $quality_standard;
    $old_origin_state = $origin_state;
    $old_sort         = $sort_order;
    $old_featured     = $is_featured;
    $old_status       = $status;
    $old_seo_title    = $seo_title;
    $old_seo_desc     = $seo_description;

    // Validations
    if (empty($name)) {
        $errors[] = 'Product name is required.';
    }
    if (empty($slug)) {
        $errors[] = 'Product slug is required.';
    }
    if ($category_id <= 0) {
        $errors[] = 'Please select a product category.';
    }

    // Check unique slug (excluding current)
    if (empty($errors)) {
        $check = $conn->prepare("SELECT id FROM products WHERE slug = ? AND id != ?");
        $check->bind_param('si', $slug, $id);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            $errors[] = 'A product with this slug already exists. Please choose a different name or edit the slug.';
        }
        $check->close();
    }

    // Process Featured Image Upload
    $image_path = $old_image;
    if (empty($errors) && isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['featured_image'];
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
        $max_size = 3 * 1024 * 1024; // 3MB

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Upload failed with error ' . $file['error'];
        } elseif (!in_array($file['type'], $allowed_types)) {
            $errors[] = 'Invalid file type. Only JPG, PNG, WEBP are allowed.';
        } elseif ($file['size'] > $max_size) {
            $errors[] = 'Featured image exceeds 3MB limit.';
        } else {
            $upload_dir = '../../../upload/products/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $new_filename = uniqid('prod_') . '.' . $ext;
            $destination = $upload_dir . $new_filename;

            if (move_uploaded_file($file['tmp_name'], $destination)) {
                // Delete old image
                if (!empty($old_image) && file_exists('../../../' . $old_image)) {
                    @unlink('../../../' . $old_image);
                }
                $image_path = 'upload/products/' . $new_filename;
            } else {
                $errors[] = 'Failed to save uploaded featured image.';
            }
        }
    }

    // Handle Image Removal via Post flag
    if (empty($errors) && isset($_POST['remove_image']) && $_POST['remove_image'] === '1') {
        if (!empty($old_image) && file_exists('../../../' . $old_image)) {
            @unlink('../../../' . $old_image);
        }
        $image_path = null;
    }

    // Process Existing Gallery Images from Hidden Input
    $existing_gallery = [];
    if (isset($_POST['existing_gallery_json'])) {
        $decoded = json_decode($_POST['existing_gallery_json'], true);
        if (is_array($decoded)) {
            $existing_gallery = $decoded;
        }
    }

    // Process New Gallery Images Uploads
    $new_gallery_paths = [];
    if (empty($errors) && isset($_FILES['gallery_images'])) {
        $files = $_FILES['gallery_images'];
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
        $max_size = 3 * 1024 * 1024; // 3MB

        if (isset($files['name'][0]) && !empty($files['name'][0])) {
            $upload_dir = '../../../upload/products/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            for ($i = 0; $i < count($files['name']); $i++) {
                if ($files['error'][$i] !== UPLOAD_ERR_OK) {
                    if ($files['error'][$i] !== UPLOAD_ERR_NO_FILE) {
                        $errors[] = 'Gallery image ' . ($i + 1) . ' upload failed with error ' . $files['error'][$i];
                    }
                    continue;
                }

                if (!in_array($files['type'][$i], $allowed_types)) {
                    $errors[] = 'Invalid file type for gallery image ' . ($i + 1) . '. Only JPG, PNG, WEBP are allowed.';
                    continue;
                }

                if ($files['size'][$i] > $max_size) {
                    $errors[] = 'Gallery image ' . ($i + 1) . ' exceeds 3MB limit.';
                    continue;
                }

                $ext = pathinfo($files['name'][$i], PATHINFO_EXTENSION);
                $new_filename = uniqid('prod_gal_') . '.' . $ext;
                $destination = $upload_dir . $new_filename;

                if (move_uploaded_file($files['tmp_name'][$i], $destination)) {
                    $new_gallery_paths[] = 'upload/products/' . $new_filename;
                } else {
                    $errors[] = 'Failed to save uploaded gallery image ' . ($i + 1);
                }
            }
        }
    }

    // Combine existing kept gallery images and new uploaded ones
    $final_gallery = array_merge($existing_gallery, $new_gallery_paths);
    $gallery_json = !empty($final_gallery) ? json_encode($final_gallery) : null;

    // Update in DB
    if (empty($errors)) {
        $origin_country = 'India';
        $stmt = $conn->prepare("UPDATE products SET name = ?, slug = ?, category_id = ?, hs_code = ?, short_description = ?, full_description = ?, moq = ?, packaging = ?, quality_standard = ?, origin_state = ?, origin_country = ?, image = ?, gallery_images = ?, status = ?, sort_order = ?, is_featured = ?, seo_title = ?, seo_description = ? WHERE id = ?");
        
        $stmt->bind_param('ssisssssssssssiissi', 
            $name, $slug, $category_id, $hs_code, $short_description, $full_description,
            $moq, $packaging, $quality_standard, $origin_state, $origin_country,
            $image_path, $gallery_json, $status, $sort_order, $is_featured, $seo_title, $seo_description, $id
        );

        if ($stmt->execute()) {
            header('Location: list.php?success=' . urlencode('Product "' . $name . '" updated successfully!'));
            exit;
        } else {
            $errors[] = 'Database error: ' . $conn->error;
        }
        $stmt->close();
    }
}

include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 
include '../../includes/navbar.php'; 
?>

<main class="lg:ml-[270px] pt-[70px] min-h-screen sidebar-transition">
    <div class="p-6 lg:p-8 page-content">

        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div>
                <div class="flex items-center gap-2 text-[12px] text-gray-400 dark:text-slate-500 mb-1">
                    <a href="../../dashboard.php" class="hover:text-spice-green-600 transition-colors">Dashboard</a>
                    <span>/</span>
                    <a href="list.php" class="hover:text-spice-green-600 transition-colors">Products</a>
                    <span>/</span>
                    <span class="text-gray-600 dark:text-slate-400 font-medium">Edit Product</span>
                </div>
                <h1 class="text-[22px] font-bold text-spice-dark dark:text-white">Edit Product: <?= htmlspecialchars($product['name']) ?></h1>
                <p class="text-[13px] text-gray-400 dark:text-slate-500 mt-1">Modify spice product specifications and assets</p>
            </div>
            <div>
                <a href="list.php" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white dark:bg-slate-800 text-gray-600 dark:text-slate-300 border border-gray-100 dark:border-slate-700 text-[12px] font-semibold hover:bg-gray-50 dark:hover:bg-slate-700/50 hover:border-gray-200 transition-all">
                    <i class="fas fa-arrow-left text-[10px]"></i> Back to List
                </a>
            </div>
        </div>

        <!-- Error Messages -->
        <?php if (!empty($errors)): ?>
        <div class="mb-6 p-4 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
            <?php foreach ($errors as $err): ?>
            <div class="flex items-center gap-2 text-red-700 dark:text-red-300 text-[13px] font-medium mb-1">
                <i class="fas fa-exclamation-circle text-red-500"></i>
                <?= htmlspecialchars($err) ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Form Form -->
        <form method="POST" action="" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <input type="hidden" name="remove_image" id="removeImageFlag" value="0">
            
            <!-- Left 2 Columns - Main Fields -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Basic Information Card -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-card border border-gray-100/50 dark:border-slate-700 p-6">
                    <div class="flex items-center gap-3 border-b border-gray-100 dark:border-slate-700 pb-4 mb-6">
                        <div class="w-8 h-8 rounded-lg bg-spice-green-600/10 dark:bg-spice-green-600/20 flex items-center justify-center text-spice-green-600">
                            <i class="fas fa-circle-info text-sm"></i>
                        </div>
                        <h2 class="text-[14px] font-bold text-spice-dark dark:text-white">Basic Information</h2>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div class="sm:col-span-2">
                            <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-2">Product Name <span class="text-spice-chili-500">*</span></label>
                            <input type="text" id="productName" name="name" value="<?= htmlspecialchars($old_name) ?>" required
                                   class="w-full px-4 py-3 rounded-xl border-2 border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-700/50 text-[12px] text-spice-dark dark:text-slate-200 placeholder-gray-300 dark:placeholder-slate-500 focus:border-spice-green-600 focus:bg-white dark:focus:bg-slate-700 focus:ring-4 focus:ring-spice-green-600/10 outline-none transition-all">
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-2">Product Slug (URL Path)</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-300 dark:text-slate-500 text-[11px] font-medium">visionexim.com/products/</span>
                                <input type="text" id="productSlug" name="slug" value="<?= htmlspecialchars($old_slug) ?>" readonly
                                       class="w-full pl-[168px] pr-4 py-3 rounded-xl border-2 border-gray-100 dark:border-slate-700 bg-gray-100 dark:bg-slate-700/30 text-[12px] text-gray-400 dark:text-slate-500 outline-none select-all">
                            </div>
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-2">Category <span class="text-spice-chili-500">*</span></label>
                            <select name="category_id" required
                                    class="w-full px-4 py-3 rounded-xl border-2 border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-700/50 text-[12px] text-gray-600 dark:text-slate-300 outline-none focus:border-spice-green-600 cursor-pointer">
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $c): ?>
                                <option value="<?= $c['id'] ?>" <?= $old_category == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-2">HS Code</label>
                            <input type="text" name="hs_code" value="<?= htmlspecialchars($old_hs) ?>" placeholder="e.g. 0908.1100"
                                   class="w-full px-4 py-3 rounded-xl border-2 border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-700/50 text-[12px] text-spice-dark dark:text-slate-200 placeholder-gray-300 dark:placeholder-slate-500 focus:border-spice-green-600 focus:bg-white dark:focus:bg-slate-700 focus:ring-4 focus:ring-spice-green-600/10 outline-none transition-all">
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-2">Short Description</label>
                            <textarea rows="2" name="short_description" placeholder="Brief summary of the product (appears in search results)"
                                      class="w-full px-4 py-3 rounded-xl border-2 border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-700/50 text-[12px] text-spice-dark dark:text-slate-200 placeholder-gray-300 dark:placeholder-slate-500 focus:border-spice-green-600 focus:bg-white dark:focus:bg-slate-700 focus:ring-4 focus:ring-spice-green-600/10 outline-none transition-all"><?= htmlspecialchars($old_short) ?></textarea>
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-2">Full Description</label>
                            <textarea rows="5" name="full_description" placeholder="Detailed description of the product qualities, benefits, cultivation etc."
                                      class="w-full px-4 py-3 rounded-xl border-2 border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-700/50 text-[12px] text-spice-dark dark:text-slate-200 placeholder-gray-300 dark:placeholder-slate-500 focus:border-spice-green-600 focus:bg-white dark:focus:bg-slate-700 focus:ring-4 focus:ring-spice-green-600/10 outline-none transition-all"><?= htmlspecialchars($old_full) ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Export Specifications Card -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-card border border-gray-100/50 dark:border-slate-700 p-6">
                    <div class="flex items-center gap-3 border-b border-gray-100 dark:border-slate-700 pb-4 mb-6">
                        <div class="w-8 h-8 rounded-lg bg-spice-turmeric-500/10 dark:bg-spice-turmeric-500/20 flex items-center justify-center text-spice-turmeric-500">
                            <i class="fas fa-ship text-sm"></i>
                        </div>
                        <h2 class="text-[14px] font-bold text-spice-dark dark:text-white">Export Specifications</h2>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-2">Minimum Order Quantity (MOQ)</label>
                            <input type="text" name="moq" value="<?= htmlspecialchars($old_moq) ?>" placeholder="e.g. 500 KG or 1 Ton"
                                   class="w-full px-4 py-3 rounded-xl border-2 border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-700/50 text-[12px] text-spice-dark dark:text-slate-200 placeholder-gray-300 dark:placeholder-slate-500 focus:border-spice-green-600 focus:bg-white dark:focus:bg-slate-700 focus:ring-4 focus:ring-spice-green-600/10 outline-none transition-all">
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-2">Packaging Size / Options</label>
                            <input type="text" name="packaging" value="<?= htmlspecialchars($old_packaging) ?>" placeholder="e.g. 25kg PP bags, Jute bags"
                                   class="w-full px-4 py-3 rounded-xl border-2 border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-700/50 text-[12px] text-spice-dark dark:text-slate-200 placeholder-gray-300 dark:placeholder-slate-500 focus:border-spice-green-600 focus:bg-white dark:focus:bg-slate-700 focus:ring-4 focus:ring-spice-green-600/10 outline-none transition-all">
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-2">Export Quality Standard</label>
                            <input type="text" name="quality_standard" value="<?= htmlspecialchars($old_quality) ?>" placeholder="e.g. Bold, Grade A, Organic"
                                   class="w-full px-4 py-3 rounded-xl border-2 border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-700/50 text-[12px] text-spice-dark dark:text-slate-200 placeholder-gray-300 dark:placeholder-slate-500 focus:border-spice-green-600 focus:bg-white dark:focus:bg-slate-700 focus:ring-4 focus:ring-spice-green-600/10 outline-none transition-all">
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-2">Origin State</label>
                            <input type="text" name="origin_state" value="<?= htmlspecialchars($old_origin_state) ?>" placeholder="e.g. Kerala, Gujarat"
                                   class="w-full px-4 py-3 rounded-xl border-2 border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-700/50 text-[12px] text-spice-dark dark:text-slate-200 placeholder-gray-300 dark:placeholder-slate-500 focus:border-spice-green-600 focus:bg-white dark:focus:bg-slate-700 focus:ring-4 focus:ring-spice-green-600/10 outline-none transition-all">
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-2">Origin Country</label>
                            <input type="text" value="India" readonly
                                   class="w-full px-4 py-3 rounded-xl border-2 border-gray-100 dark:border-slate-700 bg-gray-100 dark:bg-slate-700/30 text-[12px] text-gray-400 dark:text-slate-500 outline-none">
                        </div>
                    </div>
                </div>

                <!-- Product Images & Gallery -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-card border border-gray-100/50 dark:border-slate-700 p-6">
                    <div class="flex items-center gap-3 border-b border-gray-100 dark:border-slate-700 pb-4 mb-6">
                        <div class="w-8 h-8 rounded-lg bg-blue-500/10 dark:bg-blue-500/20 flex items-center justify-center text-blue-500">
                            <i class="fas fa-images text-sm"></i>
                        </div>
                        <h2 class="text-[14px] font-bold text-spice-dark dark:text-white">Product Images</h2>
                    </div>

                    <div class="space-y-6">
                        <!-- Featured Image Upload -->
                        <div>
                            <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-2">Featured Image</label>
                            
                            <!-- Thumbnail Preview -->
                            <div class="relative rounded-2xl overflow-hidden group border border-gray-200 dark:border-slate-700 mb-4 max-w-sm mx-auto" id="imagePreviewContainer">
                                <?php 
                                $has_image = !empty($old_image) && file_exists('../../../' . $old_image);
                                ?>
                                <img src="<?= $has_image ? '../../../' . htmlspecialchars($old_image) : '' ?>" alt="" id="imgPreview" class="w-full h-48 object-cover <?= $has_image ? '' : 'hidden' ?>">
                                <div class="w-full h-44 bg-gray-100 dark:bg-slate-700 flex flex-col items-center justify-center <?= $has_image ? 'hidden' : '' ?>" id="fallbackIcon">
                                    <i class="fas fa-image text-4xl text-gray-300 dark:text-slate-600 mb-2"></i>
                                    <span class="text-xs text-gray-400 dark:text-slate-500">No Image Uploaded</span>
                                </div>

                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                                    <label class="w-8 h-8 rounded-lg bg-white text-gray-700 hover:bg-gray-100 flex items-center justify-center shadow cursor-pointer" title="Upload New">
                                        <i class="fas fa-upload text-xs"></i>
                                        <input type="file" name="featured_image" class="hidden" accept="image/*" onchange="previewImage(this)" />
                                    </label>
                                    <button type="button" id="removeImgBtn" onclick="removeCurrentImage()" class="w-8 h-8 rounded-lg bg-spice-chili-500 text-white hover:bg-spice-chili-600 flex items-center justify-center shadow <?= $has_image ? '' : 'hidden' ?>" title="Remove">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Gallery Images Management -->
                        <div class="border-t border-gray-100 dark:border-slate-700 pt-6 mt-6">
                            <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-2">Gallery Images</label>
                            
                            <!-- Existing Gallery Images -->
                            <?php 
                            $gallery_images = [];
                            if (!empty($product['gallery_images'])) {
                                $decoded = json_decode($product['gallery_images'], true);
                                if (is_array($decoded)) {
                                    $gallery_images = $decoded;
                                }
                            }
                            ?>
                            <?php if (!empty($gallery_images)): ?>
                            <div class="grid grid-cols-4 gap-3 mb-4" id="existingGalleryContainer">
                                <?php foreach ($gallery_images as $index => $img): ?>
                                <div class="relative w-full h-16 rounded-xl overflow-hidden group border border-gray-200 dark:border-slate-700 shadow-sm" data-index="<?= $index ?>">
                                    <img src="../../../<?= htmlspecialchars($img) ?>" class="w-full h-full object-cover">
                                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                        <button type="button" onclick="removeGalleryImage(<?= $index ?>, this)" class="w-6 h-6 rounded-lg bg-spice-chili-500 text-white hover:bg-spice-chili-600 flex items-center justify-center shadow animate-fade-in" title="Remove image">
                                            <i class="fas fa-trash text-[10px]"></i>
                                        </button>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                            
                            <!-- Hidden inputs to track gallery images that are kept / removed -->
                            <input type="hidden" name="existing_gallery_json" id="existingGalleryJson" value="<?= htmlspecialchars(json_encode($gallery_images)) ?>">

                            <div class="flex items-center justify-center w-full">
                                <label class="flex flex-col items-center justify-center w-full h-28 border-2 border-dashed border-gray-200 dark:border-slate-700 rounded-2xl cursor-pointer bg-gray-50/50 dark:bg-slate-700/20 hover:bg-gray-100 dark:hover:bg-slate-700/30 transition-all group relative">
                                    <div class="flex flex-col items-center justify-center pt-3 pb-3">
                                        <div class="w-8 h-8 rounded-full bg-indigo-500/10 text-indigo-500 flex items-center justify-center mb-1 group-hover:scale-110 transition-transform">
                                            <i class="fas fa-images text-sm"></i>
                                        </div>
                                        <p class="text-[11px] font-semibold text-spice-dark dark:text-slate-200">Upload more gallery images</p>
                                        <p class="text-[9px] text-gray-400 dark:text-slate-500 mt-0.5">PNG, JPG, JPEG (Max. 3MB each)</p>
                                    </div>
                                    <input type="file" name="gallery_images[]" class="hidden" accept="image/*" multiple onchange="previewGalleryImages(this)" />
                                </label>
                            </div>
                            <!-- Gallery Preview Container -->
                            <div id="galleryPreview" class="grid grid-cols-4 gap-2 mt-4"></div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right 1 Column - Status & Meta -->
            <div class="space-y-6">
                
                <!-- Status & settings -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-card border border-gray-100/50 dark:border-slate-700 p-6">
                    <div class="flex items-center gap-3 border-b border-gray-100 dark:border-slate-700 pb-4 mb-6">
                        <div class="w-8 h-8 rounded-lg bg-purple-500/10 dark:bg-purple-500/20 flex items-center justify-center text-purple-500">
                            <i class="fas fa-sliders text-sm"></i>
                        </div>
                        <h2 class="text-[14px] font-bold text-spice-dark dark:text-white">Publish Status</h2>
                    </div>

                    <div class="space-y-5">
                        <div>
                            <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-2">Status</label>
                            <select name="status" class="w-full px-4 py-3 rounded-xl border-2 border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-700/50 text-[12px] text-gray-600 dark:text-slate-300 outline-none focus:border-spice-green-600 cursor-pointer">
                                <option value="draft" <?= $old_status === 'draft' ? 'selected' : '' ?>>Draft</option>
                                <option value="active" <?= $old_status === 'active' ? 'selected' : '' ?>>Active</option>
                                <option value="archived" <?= $old_status === 'archived' ? 'selected' : '' ?>>Archived</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-2">Display Sort Order</label>
                            <input type="number" name="sort_order" value="<?= $old_sort ?>" min="0"
                                   class="w-full px-4 py-3 rounded-xl border-2 border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-700/50 text-[12px] text-spice-dark dark:text-slate-200 focus:border-spice-green-600 focus:bg-white dark:focus:bg-slate-700 focus:ring-4 focus:ring-spice-green-600/10 outline-none transition-all">
                        </div>

                        <div class="pt-2">
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <input type="checkbox" name="is_featured" value="1" <?= $old_featured > 0 ? 'checked' : '' ?> class="w-4 h-4 rounded border-gray-200 text-spice-green-600 focus:ring-spice-green-600/30">
                                <div class="text-left">
                                    <span class="text-[12px] font-semibold text-spice-dark dark:text-slate-200 group-hover:text-spice-green-600 transition-colors">Featured Product</span>
                                    <p class="text-[10px] text-gray-400 dark:text-slate-500 leading-normal mt-0.5">Show this product on the home page featured slider</p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- SEO Meta Information -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-card border border-gray-100/50 dark:border-slate-700 p-6">
                    <div class="flex items-center gap-3 border-b border-gray-100 dark:border-slate-700 pb-4 mb-6">
                        <div class="w-8 h-8 rounded-lg bg-spice-chili-50/10 dark:bg-spice-chili-50/20 flex items-center justify-center text-spice-chili-500">
                            <i class="fas fa-search-plus text-sm"></i>
                        </div>
                        <h2 class="text-[14px] font-bold text-spice-dark dark:text-white">SEO Meta Info</h2>
                    </div>

                    <div class="space-y-5">
                        <div>
                            <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-2">Meta Title</label>
                            <input type="text" name="seo_title" placeholder="Default: Product Name" id="seoTitle"
                                   value="<?= htmlspecialchars($old_seo_title) ?>"
                                   class="w-full px-4 py-3 rounded-xl border-2 border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-700/50 text-[12px] text-spice-dark dark:text-slate-200 placeholder-gray-300 dark:placeholder-slate-500 focus:border-spice-green-600 focus:bg-white dark:focus:bg-slate-700 focus:ring-4 focus:ring-spice-green-600/10 outline-none transition-all">
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-2">Meta Description</label>
                            <textarea rows="3" name="seo_description" placeholder="SEO optimized description for search engines" id="seoDesc"
                                      class="w-full px-4 py-3 rounded-xl border-2 border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-700/50 text-[12px] text-spice-dark dark:text-slate-200 placeholder-gray-300 dark:placeholder-slate-500 focus:border-spice-green-600 focus:bg-white dark:focus:bg-slate-700 focus:ring-4 focus:ring-spice-green-600/10 outline-none transition-all"><?= htmlspecialchars($old_seo_desc) ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Action Button Card -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-card border border-gray-100/50 dark:border-slate-700 p-6">
                    <button type="submit" 
                            class="w-full py-3.5 rounded-xl bg-gradient-to-r from-spice-green-600 to-spice-green-700 text-white text-[13px] font-semibold hover:from-spice-green-700 hover:to-spice-green-800 transform hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200 shadow-lg shadow-spice-green-600/30 hover:shadow-xl hover:shadow-spice-green-600/40 mb-3">
                        <i class="fas fa-save mr-2"></i> Save Changes
                    </button>
                    <a href="list.php" 
                       class="block w-full py-3.5 rounded-xl bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-slate-300 text-[13px] font-semibold text-center hover:bg-gray-200 dark:hover:bg-slate-600 transition-colors">
                        Cancel
                    </a>
                </div>

            </div>

        </form>

    </div>
</main>

<!-- JS for auto-slug generation & preview -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const nameInput = document.getElementById('productName');
    const slugInput = document.getElementById('productSlug');
    const seoTitle = document.getElementById('seoTitle');

    nameInput.addEventListener('input', function() {
        const value = nameInput.value;
        const slug = value.toLowerCase()
                          .replace(/[^a-z0-9\s-]/g, '')
                          .replace(/\s+/g, '-')
                          .replace(/-+/g, '-');
        slugInput.value = slug;
        seoTitle.placeholder = value;
    });
});

function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('imgPreview');
            preview.src = e.target.result;
            preview.classList.remove('hidden');
            
            const fallback = document.getElementById('fallbackIcon');
            if (fallback) fallback.classList.add('hidden');
            
            const removeBtn = document.getElementById('removeImgBtn');
            if (removeBtn) removeBtn.classList.remove('hidden');
            
            document.getElementById('removeImageFlag').value = '0';
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function removeCurrentImage() {
    document.getElementById('removeImageFlag').value = '1';
    
    // Clear file input value
    const fileInput = document.querySelector('input[name="featured_image"]');
    if (fileInput) fileInput.value = '';
    
    const preview = document.getElementById('imgPreview');
    if (preview) {
        preview.classList.add('hidden');
        preview.src = '';
    }
    
    const fallback = document.getElementById('fallbackIcon');
    if (fallback) fallback.classList.remove('hidden');
    
    const removeBtn = document.getElementById('removeImgBtn');
    if (removeBtn) removeBtn.classList.add('hidden');
    
    showToast('Image flagged for removal. Click Save Changes to confirm.', 'info');
}

function previewGalleryImages(input) {
    const previewContainer = document.getElementById('galleryPreview');
    previewContainer.innerHTML = '';
    if (input.files) {
        Array.from(input.files).forEach(file => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'relative w-full h-16 rounded-xl overflow-hidden border border-gray-200 dark:border-slate-700 shadow-sm';
                div.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
                previewContainer.appendChild(div);
            }
            reader.readAsDataURL(file);
        });
    }
}

function removeGalleryImage(index, btnElement) {
    const existingInput = document.getElementById('existingGalleryJson');
    let currentGallery = JSON.parse(existingInput.value || '[]');
    
    // Remove the image path from our array
    if (index >= 0 && index < currentGallery.length) {
        currentGallery.splice(index, 1);
    }
    
    // Update the hidden input
    existingInput.value = JSON.stringify(currentGallery);
    
    // Remove the thumbnail element from UI
    const card = btnElement.closest('[data-index]');
    if (card) {
        card.remove();
    }
    
    // Re-index remaining existing items in DOM so correct index is passed if deleted
    const container = document.getElementById('existingGalleryContainer');
    if (container) {
        const remainingItems = container.querySelectorAll('[data-index]');
        remainingItems.forEach((item, newIndex) => {
            item.setAttribute('data-index', newIndex);
            const btn = item.querySelector('button');
            if (btn) {
                btn.setAttribute('onclick', `removeGalleryImage(${newIndex}, this)`);
            }
        });
    }
    
    showToast('Gallery image queued for removal.', 'info');
}
</script>

<?php include '../../includes/footer.php'; ?>
