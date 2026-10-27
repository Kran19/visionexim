<?php 
/**
 * Vision Exim — Edit Spice Harvest Calendar
 */
include '../../includes/auth.php';
include '../../includes/db.php';

$page_title = 'Edit Harvest Crop';
$current_module = 'harvest';

$month_names = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
$month_cols  = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec_month'];

// Ensure image column exists (MySQL 5.7+ compatible)
$check_col = $conn->query("SHOW COLUMNS FROM harvest_calendar LIKE 'image'");
if ($check_col && $check_col->num_rows === 0) {
    $conn->query("ALTER TABLE harvest_calendar ADD COLUMN image VARCHAR(255) DEFAULT NULL");
}

$crop_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$crop = null;
if ($crop_id > 0) {
    $stmt = $conn->prepare("SELECT * FROM harvest_calendar WHERE id = ?");
    $stmt->bind_param('i', $crop_id);
    $stmt->execute();
    $crop = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

if (!$crop) {
    header('Location: list.php?error=' . urlencode('Crop not found.'));
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $spice_name = trim($_POST['spice_name'] ?? '');
    $months     = $_POST['months'] ?? [];
    $sort_order = (int)($_POST['sort_order'] ?? 0);
    $image_path = $crop['image'] ?? null;

    if (empty($spice_name)) {
        $errors[] = 'Spice / Crop name is required.';
    }

    if (empty($errors)) {
        $check = $conn->prepare("SELECT id FROM harvest_calendar WHERE spice_name = ? AND id != ?");
        $check->bind_param('si', $spice_name, $crop_id);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            $errors[] = 'Another crop with this name already exists.';
        }
        $check->close();
    }

    // Handle image upload
    if (empty($errors) && isset($_FILES['crop_image']) && $_FILES['crop_image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['crop_image'];
        $allowed = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Image upload failed.';
        } elseif (!in_array($file['type'], $allowed)) {
            $errors[] = 'Only JPG, PNG, WEBP allowed.';
        } elseif ($file['size'] > 3 * 1024 * 1024) {
            $errors[] = 'Image must be under 3MB.';
        } else {
            $upload_dir = '../../../upload/products/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'harvest_' . uniqid() . '.' . $ext;
            if (move_uploaded_file($file['tmp_name'], $upload_dir . $filename)) {
                // Delete old image
                if (!empty($crop['image']) && file_exists('../../../' . $crop['image'])) {
                    @unlink('../../../' . $crop['image']);
                }
                $image_path = 'upload/products/' . $filename;
            } else {
                $errors[] = 'Failed to save image.';
            }
        }
    }

    // Remove image
    if (isset($_POST['remove_image']) && $_POST['remove_image'] === '1') {
        if (!empty($crop['image']) && file_exists('../../../' . $crop['image'])) {
            @unlink('../../../' . $crop['image']);
        }
        $image_path = null;
    }

    if (empty($errors)) {
        $month_values = [];
        for ($i = 0; $i < 12; $i++) {
            $month_values[] = in_array((string)($i + 1), $months) ? 1 : 0;
        }

        $stmt = $conn->prepare("UPDATE harvest_calendar SET spice_name=?, image=?, jan=?, feb=?, mar=?, apr=?, may=?, jun=?, jul=?, aug=?, sep=?, oct=?, nov=?, dec_month=?, sort_order=? WHERE id=?");
        $stmt->bind_param('ssiiiiiiiiiiiiii',
            $spice_name, $image_path,
            $month_values[0], $month_values[1], $month_values[2],
            $month_values[3], $month_values[4], $month_values[5],
            $month_values[6], $month_values[7], $month_values[8],
            $month_values[9], $month_values[10], $month_values[11],
            $sort_order, $crop_id
        );

        if ($stmt->execute()) {
            header('Location: list.php?success=' . urlencode($spice_name . ' updated successfully!'));
            exit;
        } else {
            $errors[] = 'Database error: ' . $conn->error;
        }
        $stmt->close();
    } else {
        $crop['spice_name'] = $spice_name;
        $crop['sort_order'] = $sort_order;
        for ($i = 0; $i < 12; $i++) {
            $crop[$month_cols[$i]] = in_array((string)($i + 1), $months) ? 1 : 0;
        }
    }
}

$active_months = [];
foreach ($month_cols as $i => $col) {
    if ((int)$crop[$col] > 0) $active_months[] = $i + 1;
}

include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 
include '../../includes/navbar.php'; 
?>

<main class="lg:ml-[270px] pt-[70px] min-h-screen sidebar-transition">
    <div class="p-6 lg:p-8 page-content">

        <div class="mb-8">
            <div class="flex items-center gap-2 text-[12px] text-gray-400 dark:text-slate-500 mb-1">
                <a href="../../dashboard.php" class="hover:text-spice-green-600 transition-colors">Dashboard</a>
                <span>/</span>
                <a href="list.php" class="hover:text-spice-green-600 transition-colors">Harvest Chart</a>
                <span>/</span>
                <span class="text-gray-600 dark:text-slate-400 font-medium">Edit</span>
            </div>
            <h1 class="text-[22px] font-bold text-spice-dark dark:text-white">Edit Harvest: <?= htmlspecialchars($crop['spice_name']) ?></h1>
        </div>

        <?php if (!empty($errors)): ?>
        <div class="mb-6 p-4 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
            <?php foreach ($errors as $err): ?>
            <div class="flex items-center gap-2 text-red-700 dark:text-red-300 text-[13px] font-medium">
                <i class="fas fa-exclamation-circle text-red-500"></i> <?= htmlspecialchars($err) ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="" enctype="multipart/form-data"
              class="max-w-3xl bg-white dark:bg-slate-800 rounded-2xl shadow-card border border-gray-100/50 dark:border-slate-700 p-6 space-y-6">

            <input type="hidden" name="remove_image" id="removeImageFlag" value="0">

            <!-- Name & Sort Order -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-2">Spice / Crop Name <span class="text-spice-chili-500">*</span></label>
                    <input type="text" name="spice_name" value="<?= htmlspecialchars($crop['spice_name']) ?>" required
                           class="w-full px-4 py-3 rounded-xl border-2 border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-700/50 text-[12px] text-spice-dark dark:text-slate-200 focus:border-spice-green-600 focus:bg-white dark:focus:bg-slate-700 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-2">Sort Order</label>
                    <input type="number" name="sort_order" min="0" value="<?= (int)$crop['sort_order'] ?>"
                           class="w-full px-4 py-3 rounded-xl border-2 border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-700/50 text-[12px] text-spice-dark dark:text-slate-200 focus:border-spice-green-600 focus:bg-white dark:focus:bg-slate-700 outline-none transition-all">
                </div>
            </div>

            <!-- Image Upload -->
            <div>
                <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-3">Product Image</label>
                <div class="flex flex-col sm:flex-row items-center sm:items-start gap-4 sm:gap-5">
                    <!-- Current Image Preview -->
                    <div class="relative flex-shrink-0" id="imgPreviewWrap">
                        <?php 
                        $has_image = !empty($crop['image']) && file_exists('../../../' . $crop['image']);
                        ?>
                        
                        <!-- Placeholder -->
                        <div id="imgPlaceholder" class="w-24 h-24 rounded-xl bg-gray-100 dark:bg-slate-700 flex flex-col items-center justify-center border-2 border-dashed border-gray-200 dark:border-slate-600 <?= $has_image ? 'hidden' : '' ?>">
                            <i class="fas fa-image text-2xl text-gray-300 dark:text-slate-500"></i>
                            <span class="text-[9px] text-gray-400 mt-1">No Image</span>
                        </div>

                        <!-- Image Preview -->
                        <img id="imgPreview" 
                             src="<?= $has_image ? '../../../' . htmlspecialchars($crop['image']) : '' ?>" 
                             class="w-24 h-24 rounded-xl object-cover border-2 border-gray-200 dark:border-slate-600 <?= $has_image ? '' : 'hidden' ?>">

                        <!-- Remove Action Button -->
                        <button type="button" 
                                id="removeImgBtn" 
                                onclick="removeImage()" 
                                class="absolute -top-2 -right-2 w-6 h-6 rounded-full bg-red-500 text-white flex items-center justify-center shadow hover:bg-red-600 transition-all <?= $has_image ? '' : 'hidden' ?>" 
                                title="Remove">
                            <i class="fas fa-times text-[10px]"></i>
                        </button>
                    </div>

                    <!-- Upload Area -->
                    <label class="w-full sm:flex-1 flex flex-col items-center justify-center h-24 border-2 border-dashed border-gray-200 dark:border-slate-700 rounded-xl cursor-pointer bg-gray-50/50 dark:bg-slate-700/20 hover:bg-gray-100 dark:hover:bg-slate-700/40 transition-all">
                        <span class="text-[11px] font-semibold text-gray-500 dark:text-slate-400">Click to upload new image</span>
                        <span class="text-[10px] text-gray-400 dark:text-slate-500 mt-0.5">JPG, PNG, WEBP — Max 3MB</span>
                        <input type="file" name="crop_image" class="hidden" accept="image/*" onchange="previewNewImage(this)">
                    </label>
                </div>
            </div>

            <!-- Month Selectors -->
            <div>
                <div class="flex items-center justify-between mb-3">
                    <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Active Harvesting Months</label>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="toggleAllMonths(true)" class="text-[10px] font-semibold text-spice-green-600 px-2 py-1 rounded-lg hover:bg-spice-green-600/5">Select All</button>
                        <span class="text-gray-300">|</span>
                        <button type="button" onclick="toggleAllMonths(false)" class="text-[10px] font-semibold text-gray-400 px-2 py-1 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700">Clear All</button>
                    </div>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                    <?php foreach ($month_names as $i => $month):
                        $month_num = $i + 1;
                        $is_checked = in_array($month_num, $active_months) ? 'checked' : '';
                    ?>
                    <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 dark:border-slate-700 hover:bg-gray-50 dark:hover:bg-slate-700/50 cursor-pointer transition-all has-[:checked]:bg-spice-green-600/5 has-[:checked]:border-spice-green-600/30">
                        <input type="checkbox" name="months[]" value="<?= $month_num ?>" <?= $is_checked ?>
                               class="month-checkbox w-4 h-4 rounded border-gray-300 text-spice-green-600 focus:ring-spice-green-600/30">
                        <span class="text-[12px] font-medium text-spice-dark dark:text-slate-300"><?= $month ?></span>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-3 border-t border-gray-100 dark:border-slate-700 pt-5">
                <button type="submit"
                        class="px-6 py-3 rounded-xl bg-gradient-to-r from-spice-green-600 to-spice-green-700 text-white text-[12px] font-semibold hover:from-spice-green-700 hover:to-spice-green-800 transition-all shadow-lg shadow-spice-green-600/20">
                    <i class="fas fa-save mr-1.5"></i> Save Changes
                </button>
                <a href="list.php" class="px-6 py-3 rounded-xl border-2 border-gray-100 dark:border-slate-700 text-gray-500 dark:text-slate-400 text-[12px] font-semibold transition-all">
                    Cancel
                </a>
            </div>

        </form>
    </div>
</main>

<script>
function toggleAllMonths(checked) {
    document.querySelectorAll('.month-checkbox').forEach(cb => cb.checked = checked);
}

function previewNewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var preview = document.getElementById('imgPreview');
            var placeholder = document.getElementById('imgPlaceholder');
            var removeBtn = document.getElementById('removeImgBtn');
            
            preview.src = e.target.result;
            preview.classList.remove('hidden');
            if (placeholder) placeholder.classList.add('hidden');
            if (removeBtn) removeBtn.classList.remove('hidden');
            document.getElementById('removeImageFlag').value = '0';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function removeImage() {
    document.getElementById('removeImageFlag').value = '1';
    
    // Clear file input
    var fileInput = document.querySelector('input[name="crop_image"]');
    if (fileInput) fileInput.value = '';
    
    // Hide preview
    var preview = document.getElementById('imgPreview');
    if (preview) {
        preview.classList.add('hidden');
        preview.src = '';
    }
    
    // Show placeholder
    var placeholder = document.getElementById('imgPlaceholder');
    if (placeholder) placeholder.classList.remove('hidden');
    
    // Hide remove button
    var removeBtn = document.getElementById('removeImgBtn');
    if (removeBtn) removeBtn.classList.add('hidden');
}
</script>

<?php include '../../includes/footer.php'; ?>
