<?php 
/**
 * Vision Exim — Add Category
 */
include '../../includes/auth.php';
include '../../includes/db.php';

$page_title = 'Add Category';
$current_module = 'categories';

$errors = [];
$old_name = '';
$old_slug = '';
$old_parent = '';
$old_desc = '';
$old_sort = 0;
$old_status = 'active';

// Fetch potential parent categories
$parent_cats = [];
$res = $conn->query("SELECT id, name FROM categories WHERE parent_id IS NULL AND status = 'active' ORDER BY name ASC");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $parent_cats[] = $row;
    }
}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name       = trim($_POST['name'] ?? '');
    $slug       = trim($_POST['slug'] ?? '');
    $parent_id  = $_POST['parent_id'] ?? '';
    $description= trim($_POST['description'] ?? '');
    $status     = trim($_POST['status'] ?? 'active');
    $sort_order = (int)($_POST['sort_order'] ?? 0);

    $old_name   = $name;
    $old_slug   = $slug;
    $old_parent = $parent_id;
    $old_desc   = $description;
    $old_sort   = $sort_order;
    $old_status = $status;

    // Validation
    if (empty($name)) {
        $errors[] = 'Category name is required.';
    }
    if (empty($slug)) {
        $errors[] = 'Category slug is required.';
    }

    // Check unique slug
    if (empty($errors)) {
        $check = $conn->prepare("SELECT id FROM categories WHERE slug = ?");
        $check->bind_param('s', $slug);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            $errors[] = 'A category with this slug (URL Path) already exists. Please choose a different name or edit the slug.';
        }
        $check->close();
    }

    // Validate parent category selection
    if (empty($errors)) {
        $parent_val = ($parent_id === 'none' || empty($parent_id)) ? null : (int)$parent_id;
        if ($parent_val !== null) {
            $chk_p = $conn->prepare("SELECT parent_id FROM categories WHERE id = ?");
            $chk_p->bind_param('i', $parent_val);
            $chk_p->execute();
            $res_p = $chk_p->get_result()->fetch_assoc();
            if (!$res_p) {
                $errors[] = 'The selected parent category does not exist.';
            } elseif ($res_p['parent_id'] !== null) {
                $errors[] = 'The selected parent category is itself a subcategory. Multi-level nesting is not supported.';
            }
            $chk_p->close();
        }
    }

    // Process Image Upload
    $image_path = null;
    if (empty($errors) && isset($_FILES['category_image']) && $_FILES['category_image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['category_image'];
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
        $max_size = 2 * 1024 * 1024; // 2MB

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Upload failed with error code ' . $file['error'];
        } elseif (!in_array($file['type'], $allowed_types)) {
            $errors[] = 'Invalid file type. Only JPG, PNG, WEBP are allowed.';
        } elseif ($file['size'] > $max_size) {
            $errors[] = 'File size is too large. Max limit is 2MB.';
        } else {
            // Ensure upload directory exists
            $upload_dir = '../../../upload/categories/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $new_filename = uniqid('cat_') . '.' . $ext;
            $destination = $upload_dir . $new_filename;

            if (move_uploaded_file($file['tmp_name'], $destination)) {
                $image_path = 'upload/categories/' . $new_filename;
            } else {
                $errors[] = 'Failed to save uploaded file.';
            }
        }
    }

    // Insert into DB
    if (empty($errors)) {
        $parent_val = ($parent_id === 'none' || empty($parent_id)) ? null : (int)$parent_id;
        
        $stmt = $conn->prepare("INSERT INTO categories (name, slug, parent_id, description, status, sort_order, image) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('ssissis', $name, $slug, $parent_val, $description, $status, $sort_order, $image_path);
        
        if ($stmt->execute()) {
            header('Location: list.php?success=' . urlencode('Category "' . $name . '" created successfully!'));
            exit;
        } else {
            $errors[] = 'Database error: ' . $conn->error;
        }
        $stmt->close();
    }
}

// Get next sort order
$next_sort = 1;
$res = $conn->query("SELECT MAX(sort_order) as max_sort FROM categories");
if ($res && $row = $res->fetch_assoc()) {
    $next_sort = (int)$row['max_sort'] + 1;
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
                    <a href="list.php" class="hover:text-spice-green-600 transition-colors">Categories</a>
                    <span>/</span>
                    <span class="text-gray-600 dark:text-slate-400 font-medium">Add Category</span>
                </div>
                <h1 class="text-[22px] font-bold text-spice-dark dark:text-white">Add New Category</h1>
                <p class="text-[13px] text-gray-400 dark:text-slate-500 mt-1">Create a new product classification group</p>
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
            
            <!-- Left 2 Columns -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Core Details Card -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-card border border-gray-100/50 dark:border-slate-700 p-6">
                    <div class="flex items-center gap-3 border-b border-gray-100 dark:border-slate-700 pb-4 mb-6">
                        <div class="w-8 h-8 rounded-lg bg-spice-green-600/10 dark:bg-spice-green-600/20 flex items-center justify-center text-spice-green-600">
                            <i class="fas fa-layer-group text-sm"></i>
                        </div>
                        <h2 class="text-[14px] font-bold text-spice-dark dark:text-white">Category Details</h2>
                    </div>

                    <div class="grid grid-cols-1 gap-5">
                        <div>
                            <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-2">Category Name <span class="text-spice-chili-500">*</span></label>
                            <input type="text" id="categoryName" name="name" placeholder="e.g. Blended Spices" required
                                   value="<?= htmlspecialchars($old_name) ?>"
                                   class="w-full px-4 py-3 rounded-xl border-2 border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-700/50 text-[12px] text-spice-dark dark:text-slate-200 placeholder-gray-300 dark:placeholder-slate-500 focus:border-spice-green-600 focus:bg-white dark:focus:bg-slate-700 focus:ring-4 focus:ring-spice-green-600/10 outline-none transition-all">
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-2">Category Slug</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-300 dark:text-slate-500 text-[11px] font-medium">visionexim.com/category/</span>
                                <input type="text" id="categorySlug" name="slug" placeholder="blended-spices" readonly
                                       value="<?= htmlspecialchars($old_slug) ?>"
                                       class="w-full pl-[168px] pr-4 py-3 rounded-xl border-2 border-gray-100 dark:border-slate-700 bg-gray-100 dark:bg-slate-700/30 text-[12px] text-gray-400 dark:text-slate-500 outline-none select-all">
                            </div>
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-2">Parent Category</label>
                            <select name="parent_id" class="w-full px-4 py-3 rounded-xl border-2 border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-700/50 text-[12px] text-gray-600 dark:text-slate-300 outline-none focus:border-spice-green-600 cursor-pointer">
                                <option value="none">None (Parent Category)</option>
                                <?php foreach ($parent_cats as $pc): ?>
                                <option value="<?= $pc['id'] ?>" <?= $old_parent == $pc['id'] ? 'selected' : '' ?>><?= htmlspecialchars($pc['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-2">Description</label>
                            <textarea rows="4" name="description" placeholder="Describe the type of spices included in this category..."
                                      class="w-full px-4 py-3 rounded-xl border-2 border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-700/50 text-[12px] text-spice-dark dark:text-slate-200 placeholder-gray-300 dark:placeholder-slate-500 focus:border-spice-green-600 focus:bg-white dark:focus:bg-slate-700 focus:ring-4 focus:ring-spice-green-600/10 outline-none transition-all"><?= htmlspecialchars($old_desc) ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-6">
                <!-- Publish & Image Card -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-card border border-gray-100/50 dark:border-slate-700 p-6">
                    <div class="flex items-center gap-3 border-b border-gray-100 dark:border-slate-700 pb-4 mb-6">
                        <div class="w-8 h-8 rounded-lg bg-purple-500/10 dark:bg-purple-500/20 flex items-center justify-center text-purple-500">
                            <i class="fas fa-sliders text-sm"></i>
                        </div>
                        <h2 class="text-[14px] font-bold text-spice-dark dark:text-white">Settings</h2>
                    </div>

                    <div class="space-y-5">
                        <div>
                            <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-2">Status</label>
                            <select name="status" class="w-full px-4 py-3 rounded-xl border-2 border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-700/50 text-[12px] text-gray-600 dark:text-slate-300 outline-none focus:border-spice-green-600 cursor-pointer">
                                <option value="active" <?= $old_status === 'active' ? 'selected' : '' ?>>Active</option>
                                <option value="inactive" <?= $old_status === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-2">Display Sort Order</label>
                            <input type="number" name="sort_order" min="0"
                                   value="<?= $old_sort > 0 ? $old_sort : $next_sort ?>"
                                   class="w-full px-4 py-3 rounded-xl border-2 border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-700/50 text-[12px] text-spice-dark dark:text-slate-200 focus:border-spice-green-600 focus:bg-white dark:focus:bg-slate-700 focus:ring-4 focus:ring-spice-green-600/10 outline-none transition-all">
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-2">Category Image / Icon</label>
                            <div class="flex items-center justify-center w-full">
                                <label class="flex flex-col items-center justify-center w-full h-36 border-2 border-dashed border-gray-200 dark:border-slate-700 rounded-2xl cursor-pointer bg-gray-50/50 dark:bg-slate-700/20 hover:bg-gray-100 dark:hover:bg-slate-700/30 transition-all group relative">
                                    <div class="flex flex-col items-center justify-center pt-4 pb-4" id="uploadLabel">
                                        <i class="fas fa-image text-gray-400 dark:text-slate-500 text-lg mb-2"></i>
                                        <p class="text-[11px] font-semibold text-spice-dark dark:text-slate-200">Upload Image</p>
                                        <p class="text-[9px] text-gray-400 dark:text-slate-500 mt-1">PNG, JPG (Max. 2MB)</p>
                                    </div>
                                    <input type="file" name="category_image" class="hidden" accept="image/*" onchange="previewImage(this)" />
                                    <img id="imgPreview" class="absolute inset-0 w-full h-full object-cover rounded-2xl hidden">
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Button Card -->
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-card border border-gray-100/50 dark:border-slate-700 p-6">
                    <button type="submit" 
                            class="w-full py-3.5 rounded-xl bg-gradient-to-r from-spice-green-600 to-spice-green-700 text-white text-[13px] font-semibold hover:from-spice-green-700 hover:to-spice-green-800 transform hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200 shadow-lg shadow-spice-green-600/30 hover:shadow-xl hover:shadow-spice-green-600/40 mb-3">
                        <i class="fas fa-save mr-2"></i> Save Category
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
    const nameInput = document.getElementById('categoryName');
    const slugInput = document.getElementById('categorySlug');

    nameInput.addEventListener('input', function() {
        const value = nameInput.value;
        const slug = value.toLowerCase()
                          .replace(/[^a-z0-9\s-]/g, '')
                          .replace(/\s+/g, '-')
                          .replace(/-+/g, '-');
        slugInput.value = slug;
    });
});

function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('imgPreview').src = e.target.result;
            document.getElementById('imgPreview').classList.remove('hidden');
            document.getElementById('uploadLabel').classList.add('opacity-0');
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php include '../../includes/footer.php'; ?>
