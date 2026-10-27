<?php 
/**
 * Vision Exim — Categories Management List
 */
include '../../includes/auth.php';
include '../../includes/db.php';

$page_title = 'Categories';
$current_module = 'categories';

// Handle success/error messages from redirect parameters
$success_msg = isset($_GET['success']) ? htmlspecialchars($_GET['success']) : '';
$error_msg   = isset($_GET['error'])   ? htmlspecialchars($_GET['error'])   : '';

// Read filter variables
$search = trim($_GET['search'] ?? '');
$status_filter = trim($_GET['status'] ?? '');

// Build query
$sql = "SELECT c1.*, c2.name as parent_name, 
        (SELECT COUNT(*) FROM products WHERE category_id = c1.id) as product_count 
        FROM categories c1 
        LEFT JOIN categories c2 ON c1.parent_id = c2.id 
        WHERE 1=1";

$types = "";
$params = [];

if (!empty($search)) {
    $sql .= " AND (c1.name LIKE ? OR c1.description LIKE ?)";
    $search_param = "%$search%";
    $params[] = &$search_param;
    $params[] = &$search_param;
    $types .= "ss";
}

if (!empty($status_filter) && $status_filter !== 'All Status') {
    $sql .= " AND c1.status = ?";
    $status_val = strtolower($status_filter);
    $params[] = &$status_val;
    $types .= "s";
}

$sql .= " ORDER BY c1.sort_order ASC, c1.name ASC";

$stmt = $conn->prepare($sql);
if (!empty($types)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$categories_data = [];
while ($row = $result->fetch_assoc()) {
    $categories_data[] = $row;
}
$stmt->close();

// Process into hierarchical order
$all_cats = [];
foreach ($categories_data as $cat) {
    $all_cats[$cat['id']] = $cat;
}

$hierarchical_categories = [];
foreach ($all_cats as $cat) {
    if ($cat['parent_id'] === null) {
        $cat['level'] = 0;
        $hierarchical_categories[] = $cat;
        
        // Find subcategories of this parent
        foreach ($all_cats as $sub) {
            if ($sub['parent_id'] == $cat['id']) {
                $sub['level'] = 1;
                $hierarchical_categories[] = $sub;
            }
        }
    }
}

// Add orphan subcategories whose parent doesn't exist in the filtered set
foreach ($all_cats as $cat) {
    if ($cat['parent_id'] !== null && !isset($all_cats[$cat['parent_id']])) {
        $cat['level'] = 0; // Display as root
        $hierarchical_categories[] = $cat;
    }
}

$categories_data = $hierarchical_categories;

include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 
include '../../includes/navbar.php'; 
?>

<main class="lg:ml-[270px] pt-[70px] min-h-screen sidebar-transition">
    <div class="p-6 lg:p-8 page-content">

        <!-- Flash Messages -->
        <?php if ($success_msg): ?>
        <div id="flashSuccess" class="mb-6 flex items-center gap-3 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 text-[13px] font-medium">
            <i class="fas fa-check-circle text-emerald-500"></i>
            <?= $success_msg ?>
            <button onclick="this.parentElement.remove()" class="ml-auto text-emerald-400 hover:text-emerald-600"><i class="fas fa-times"></i></button>
        </div>
        <?php endif; ?>
        <?php if ($error_msg): ?>
        <div id="flashError" class="mb-6 flex items-center gap-3 p-4 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 text-[13px] font-medium">
            <i class="fas fa-exclamation-circle text-red-500"></i>
            <?= $error_msg ?>
            <button onclick="this.parentElement.remove()" class="ml-auto text-red-400 hover:text-red-600"><i class="fas fa-times"></i></button>
        </div>
        <?php endif; ?>

        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div>
                <div class="flex items-center gap-2 text-[12px] text-gray-400 dark:text-slate-500 mb-1">
                    <a href="../../dashboard.php" class="hover:text-spice-green-600 transition-colors">Dashboard</a>
                    <span>/</span>
                    <span class="text-gray-600 dark:text-slate-400 font-medium">Categories</span>
                </div>
                <h1 class="text-[22px] font-bold text-spice-dark dark:text-white">Category Management</h1>
                <p class="text-[13px] text-gray-400 dark:text-slate-500 mt-1">Organize spice products under hierarchical categories</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="add.php" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-spice-green-600 to-spice-green-700 text-white text-[12px] font-semibold hover:from-spice-green-700 hover:to-spice-green-800 transition-all shadow-lg shadow-spice-green-600/25 hover:shadow-xl hover:shadow-spice-green-600/35 transform hover:-translate-y-0.5">
                    <i class="fas fa-plus mr-2"></i>Add Category
                </a>
            </div>
        </div>

        <!-- Filters Bar -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-card border border-gray-100/50 dark:border-slate-700 p-5 mb-6">
            <form method="GET" action="" class="flex flex-col sm:flex-row sm:items-center gap-4">
                <!-- Search -->
                <div class="flex-1">
                    <div class="relative">
                        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-300 dark:text-slate-500 text-xs"></i>
                        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search categories by name, description..." 
                               class="w-full pl-11 pr-4 py-3 rounded-xl border-2 border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-700/50 text-[12px] text-spice-dark dark:text-slate-200 placeholder-gray-300 dark:placeholder-slate-500 focus:border-spice-green-600 focus:bg-white dark:focus:bg-slate-700 focus:ring-4 focus:ring-spice-green-600/10 outline-none transition-all">
                    </div>
                </div>
                <!-- Status Filter -->
                <select name="status" class="px-4 py-3 rounded-xl border-2 border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-700/50 text-[12px] text-gray-600 dark:text-slate-300 outline-none focus:border-spice-green-600 cursor-pointer min-w-[140px]">
                    <option value="">All Status</option>
                    <option value="active" <?= $status_filter === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= $status_filter === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                </select>
                
                <button type="submit" class="px-5 py-3 rounded-xl bg-spice-green-600 hover:bg-spice-green-700 text-white text-[12px] font-semibold transition-colors">
                    Filter
                </button>
                <?php if (!empty($search) || !empty($status_filter)): ?>
                <a href="list.php" class="px-4 py-3 rounded-xl bg-gray-100 dark:bg-slate-700 text-gray-500 dark:text-slate-400 text-[12px] font-semibold hover:bg-gray-200 dark:hover:bg-slate-600 transition-colors text-center">
                    Clear
                </a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Categories Table -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-card border border-gray-100/50 dark:border-slate-700 overflow-hidden">
            <?php if (empty($categories_data)): ?>
            <!-- Empty State -->
            <div class="text-center py-16">
                <div class="w-20 h-20 rounded-2xl bg-spice-green-600/10 flex items-center justify-center mx-auto mb-5">
                    <i class="fas fa-layer-group text-3xl text-spice-green-600"></i>
                </div>
                <h3 class="text-[16px] font-bold text-spice-dark dark:text-white mb-2">No Categories Found</h3>
                <p class="text-[13px] text-gray-400 dark:text-slate-500 mb-6 max-w-md mx-auto">Create spice categories to begin organizing your premium export catalog products.</p>
                <a href="add.php" class="inline-flex items-center gap-2 bg-gradient-to-r from-spice-green-600 to-spice-green-700 text-white px-6 py-3 rounded-xl text-[13px] font-semibold hover:from-spice-green-700 hover:to-spice-green-800 transition-all shadow-lg shadow-spice-green-600/20">
                    <i class="fas fa-plus"></i> Add Your First Category
                </a>
            </div>
            <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50/80 dark:bg-slate-700/50 border-b border-gray-100 dark:border-slate-700">
                            <th class="px-5 py-4 text-left text-[11px] font-semibold text-gray-400 dark:text-slate-500 uppercase tracking-wider ps-6">Category</th>
                            <th class="px-5 py-4 text-left text-[11px] font-semibold text-gray-400 dark:text-slate-500 uppercase tracking-wider">Slug</th>
                            <th class="px-5 py-4 text-left text-[11px] font-semibold text-gray-400 dark:text-slate-500 uppercase tracking-wider">Parent Category</th>
                            <th class="px-5 py-4 text-left text-[11px] font-semibold text-gray-400 dark:text-slate-500 uppercase tracking-wider">Products Count</th>
                            <th class="px-5 py-4 text-left text-[11px] font-semibold text-gray-400 dark:text-slate-500 uppercase tracking-wider">Sort Order</th>
                            <th class="px-5 py-4 text-left text-[11px] font-semibold text-gray-400 dark:text-slate-500 uppercase tracking-wider">Status</th>
                            <th class="px-5 py-4 text-center text-[11px] font-semibold text-gray-400 dark:text-slate-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                        <?php foreach ($categories_data as $row): 
                            // Determine the icon based on name
                            $icon = 'fa-pepper-hot';
                            $icon_color = 'text-spice-turmeric-500';
                            $bg_color = 'bg-spice-turmeric-100 dark:bg-spice-turmeric-900/30';
                            
                            $name_lower = strtolower($row['name']);
                            if (strpos($name_lower, 'whole') !== false) {
                                $icon = 'fa-seedling';
                                $icon_color = 'text-emerald-600';
                                $bg_color = 'bg-emerald-100 dark:bg-emerald-900/30';
                            } elseif (strpos($name_lower, 'blend') !== false) {
                                $icon = 'fa-fire-burner';
                                $icon_color = 'text-spice-chili-500';
                                $bg_color = 'bg-spice-chili-100 dark:bg-spice-chili-900/30';
                            } elseif (strpos($name_lower, 'seed') !== false) {
                                $icon = 'fa-wheat-awn';
                                $icon_color = 'text-yellow-600';
                                $bg_color = 'bg-yellow-100 dark:bg-yellow-900/30';
                            }
                        ?>
                        <tr class="hover:bg-gray-50/60 dark:hover:bg-slate-700/30 transition-colors" id="row-<?= $row['id'] ?>">
                            <td class="px-5 py-4 ps-6">
                                <div class="flex items-center gap-3 <?= isset($row['level']) && $row['level'] > 0 ? 'pl-8' : '' ?>">
                                    <?php if (isset($row['level']) && $row['level'] > 0): ?>
                                    <span class="text-gray-400 dark:text-slate-500 font-mono text-[13px] flex-shrink-0">├──</span>
                                    <?php endif; ?>
                                    <?php if (!empty($row['image']) && file_exists('../../../' . $row['image'])): ?>
                                    <img src="../../../<?= htmlspecialchars($row['image']) ?>" alt="" class="w-10 h-10 rounded-lg object-cover flex-shrink-0">
                                    <?php else: ?>
                                    <div class="w-10 h-10 rounded-lg <?= $bg_color ?> flex items-center justify-center flex-shrink-0">
                                        <i class="fas <?= $icon ?> <?= $icon_color ?> text-sm"></i>
                                    </div>
                                    <?php endif; ?>
                                    <div>
                                        <p class="text-[13px] font-semibold text-spice-dark dark:text-white"><?= htmlspecialchars($row['name']) ?></p>
                                        <p class="text-[10px] text-gray-400 dark:text-slate-500 truncate max-w-[200px]" title="<?= htmlspecialchars($row['description']) ?>">
                                            <?= htmlspecialchars($row['description']) ?>
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-[12px] text-gray-600 dark:text-slate-400 font-mono"><?= htmlspecialchars($row['slug']) ?></td>
                            <td class="px-5 py-4 text-[12px] text-gray-500 dark:text-slate-400">
                                <?= !empty($row['parent_name']) ? '<span class="inline-flex px-2 py-0.5 rounded bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-slate-300 text-[11px] font-medium">' . htmlspecialchars($row['parent_name']) . '</span>' : '— None' ?>
                            </td>
                            <td class="px-5 py-4 text-[13px] text-gray-600 dark:text-slate-400 font-semibold ps-10">
                                <?= $row['product_count'] ?>
                            </td>
                            <td class="px-5 py-4 text-[12px] text-gray-600 dark:text-slate-400 font-medium"><?= $row['sort_order'] ?></td>
                            <td class="px-5 py-4">
                                <?php if ($row['status'] === 'active'): ?>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Active
                                </span>
                                <?php else: ?>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-gray-100 dark:bg-slate-700 text-gray-500 dark:text-slate-400">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>Inactive
                                </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="edit.php?id=<?= $row['id'] ?>" class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-blue-500 hover:bg-blue-100 dark:hover:bg-blue-900/40 transition-colors" title="Edit">
                                        <i class="fas fa-pen text-[10px]"></i>
                                    </a>
                                    <button onclick="deleteCategory(<?= $row['id'] ?>, '<?= htmlspecialchars($row['name'], ENT_QUOTES) ?>')" class="w-8 h-8 rounded-lg bg-spice-chili-50 dark:bg-spice-chili-900/20 flex items-center justify-center text-spice-chili-500 hover:bg-spice-chili-100 dark:hover:bg-spice-chili-900/40 transition-colors" title="Delete">
                                        <i class="fas fa-trash text-[10px]"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>

    </div>
</main>

<script>
function deleteCategory(id, name) {
    confirmDelete(name).then((result) => {
        if (result.isConfirmed) {
            fetch('delete.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'id=' + encodeURIComponent(id)
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const row = document.getElementById('row-' + id);
                    if (row) {
                        row.style.transition = 'opacity 0.3s, transform 0.3s';
                        row.style.opacity = '0';
                        row.style.transform = 'translateX(20px)';
                        setTimeout(() => row.remove(), 300);
                    }
                    showToast(name + ' category deleted successfully!', 'success');
                } else {
                    showToast(data.message || 'Failed to delete category.', 'error');
                }
            })
            .catch(() => showToast('Server error. Please try again.', 'error'));
        }
    });
}
</script>

<?php include '../../includes/footer.php'; ?>
