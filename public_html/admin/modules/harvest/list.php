<?php 
$page_title = 'Harvesting Calendar';
$current_module = 'harvest';
include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 
include '../../includes/navbar.php'; 
include '../../includes/db.php';

// Handle success/error messages from redirects
$success_msg = isset($_GET['success']) ? htmlspecialchars($_GET['success']) : '';
$error_msg   = isset($_GET['error'])   ? htmlspecialchars($_GET['error'])   : '';

// Ensure image column exists (MySQL 5.7+ compatible)
$check_col = $conn->query("SHOW COLUMNS FROM harvest_calendar LIKE 'image'");
if ($check_col && $check_col->num_rows === 0) {
    $conn->query("ALTER TABLE harvest_calendar ADD COLUMN image VARCHAR(255) DEFAULT NULL");
}

$harvest_data = [];
$result = $conn->query("SELECT * FROM harvest_calendar ORDER BY sort_order ASC, spice_name ASC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $harvest_data[] = $row;
    }
}

$month_cols = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec_month'];
$month_labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
?>

<main class="lg:ml-[270px] pt-[70px] min-h-screen sidebar-transition">
    <div class="p-6 lg:p-8 page-content">

        <!-- Flash Messages -->
        <?php if ($success_msg): ?>
        <div id="flashSuccess" class="mb-6 flex items-center gap-3 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 text-[13px] font-medium animate-pulse">
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
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div>
                <div class="flex items-center gap-2 text-[12px] text-gray-400 dark:text-slate-500 mb-1">
                    <a href="../../dashboard.php" class="hover:text-spice-green-600 transition-colors">Dashboard</a>
                    <span>/</span>
                    <span class="text-gray-600 dark:text-slate-400 font-medium">Harvest Chart</span>
                </div>
                <h1 class="text-[22px] font-bold text-spice-dark dark:text-white">Harvesting Chart Management</h1>
                <p class="text-[13px] text-gray-400 dark:text-slate-500 mt-1">Configure harvesting calendars, crop seasons, and procurement schedules</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="px-3 py-1.5 rounded-lg bg-spice-green-600/10 text-spice-green-600 dark:text-emerald-400 text-[11px] font-bold">
                    <i class="fas fa-leaf mr-1"></i> <?= count($harvest_data) ?> Crops
                </span>
                <a href="add.php" class="flex items-center gap-2 bg-gradient-to-r from-spice-green-600 to-spice-green-700 text-white px-5 py-2.5 rounded-xl text-[12px] font-semibold hover:from-spice-green-700 hover:to-spice-green-800 transition-all shadow-lg shadow-spice-green-600/20">
                    <i class="fas fa-plus"></i> Add Spice Harvest Row
                </a>
            </div>
        </div>

        <!-- Harvest Table Card -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-card border border-gray-100/50 dark:border-slate-700 p-6">
            <?php if (count($harvest_data) === 0): ?>
            <!-- Empty State -->
            <div class="text-center py-16">
                <div class="w-20 h-20 rounded-2xl bg-spice-green-600/10 flex items-center justify-center mx-auto mb-5">
                    <i class="fas fa-calendar-plus text-3xl text-spice-green-600"></i>
                </div>
                <h3 class="text-[16px] font-bold text-spice-dark dark:text-white mb-2">No Harvest Data Yet</h3>
                <p class="text-[13px] text-gray-400 dark:text-slate-500 mb-6 max-w-md mx-auto">Start building your harvesting calendar by adding spice crops and their active harvest months.</p>
                <a href="add.php" class="inline-flex items-center gap-2 bg-gradient-to-r from-spice-green-600 to-spice-green-700 text-white px-6 py-3 rounded-xl text-[13px] font-semibold hover:from-spice-green-700 hover:to-spice-green-800 transition-all shadow-lg shadow-spice-green-600/20">
                    <i class="fas fa-plus"></i> Add Your First Crop
                </a>
            </div>
            <?php else: ?>
            <!-- Desktop View (Horizontal Scrollable Table) -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full min-w-[900px] border-collapse">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-slate-700 pb-3">
                            <th class="text-left font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider text-[11px] pb-4 ps-4">Product</th>
                            <?php foreach ($month_labels as $m): ?>
                            <th class="text-center font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider text-[11px] pb-4 px-1"><?= $m ?></th>
                            <?php endforeach; ?>
                            <th class="text-right font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider text-[11px] pb-4 pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                        <?php foreach ($harvest_data as $row): ?>
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-slate-700/30 transition-all group" id="row-<?= $row['id'] ?>">
                            <!-- Spice Name with Image -->
                            <td class="py-4 ps-4 align-middle">
                                <div class="flex items-center gap-3">
                                    <?php if (!empty($row['image'])): ?>
                                    <img src="../../../<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['spice_name']) ?>" class="w-10 h-10 rounded-lg object-cover flex-shrink-0 border border-gray-100 dark:border-slate-700">
                                    <?php else: ?>
                                    <div class="w-10 h-10 rounded-lg bg-spice-green-600/10 flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-seedling text-spice-green-600 text-sm"></i>
                                    </div>
                                    <?php endif; ?>
                                    <span class="text-[13px] font-semibold text-spice-dark dark:text-white"><?= htmlspecialchars($row['spice_name']) ?></span>
                                </div>
                            </td>
                            <!-- 12 Months Indicators -->
                            <?php 
                            $prod_img = !empty($row['image']) ? '../../../' . htmlspecialchars($row['image']) : null;
                            foreach ($month_cols as $col): ?>
                            <td class="py-4 align-middle text-center px-1">
                                <?php if ((int)$row[$col] > 0 && $prod_img): ?>
                                <img src="<?= $prod_img ?>" alt="" class="mx-auto rounded-md object-cover" style="width:36px;height:36px;" title="Harvesting Period">
                                <?php elseif ((int)$row[$col] > 0): ?>
                                <span class="block mx-auto w-9 h-3 bg-spice-green-600/90 rounded-full" title="Harvesting Period"></span>
                                <?php else: ?>
                                <span class="block mx-auto" style="width:36px;height:36px;"></span>
                                <?php endif; ?>
                            </td>
                            <?php endforeach; ?>
                            <!-- Action Buttons -->
                            <td class="py-4 pe-4 align-middle text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="edit.php?id=<?= $row['id'] ?>" 
                                       class="w-7 h-7 rounded-lg bg-spice-green-600/5 hover:bg-spice-green-600/10 text-spice-green-600 dark:text-emerald-400 flex items-center justify-center transition-colors" 
                                       title="Edit Crop Season">
                                        <i class="fas fa-edit text-xs"></i>
                                    </a>
                                    <button onclick="deleteRow(<?= $row['id'] ?>, '<?= htmlspecialchars($row['spice_name'], ENT_QUOTES) ?>')" 
                                            class="w-7 h-7 rounded-lg bg-spice-chili-500/5 hover:bg-spice-chili-500/10 text-spice-chili-500 flex items-center justify-center transition-colors" 
                                            title="Delete Spice">
                                        <i class="fas fa-trash-alt text-xs"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Mobile View (Card List) -->
            <div class="block md:hidden space-y-4">
                <?php foreach ($harvest_data as $row): ?>
                <div class="bg-gray-50/50 dark:bg-slate-700/20 rounded-xl p-4 border border-gray-100 dark:border-slate-700/50" id="mobile-row-<?= $row['id'] ?>">
                    <div class="flex items-center justify-between gap-3 mb-3">
                        <div class="flex items-center gap-3">
                            <?php if (!empty($row['image'])): ?>
                            <img src="../../../<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['spice_name']) ?>" class="w-10 h-10 rounded-lg object-cover flex-shrink-0 border border-gray-100 dark:border-slate-700">
                            <?php else: ?>
                            <div class="w-10 h-10 rounded-lg bg-spice-green-600/10 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-seedling text-spice-green-600 text-sm"></i>
                            </div>
                            <?php endif; ?>
                            <span class="text-[14px] font-bold text-spice-dark dark:text-white"><?= htmlspecialchars($row['spice_name']) ?></span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <a href="edit.php?id=<?= $row['id'] ?>" 
                               class="w-8 h-8 rounded-lg bg-spice-green-600/5 hover:bg-spice-green-600/10 text-spice-green-600 dark:text-emerald-400 flex items-center justify-center transition-colors" 
                               title="Edit Crop Season">
                                <i class="fas fa-edit text-xs"></i>
                            </a>
                            <button onclick="deleteRow(<?= $row['id'] ?>, '<?= htmlspecialchars($row['spice_name'], ENT_QUOTES) ?>')" 
                                    class="w-8 h-8 rounded-lg bg-spice-chili-500/5 hover:bg-spice-chili-500/10 text-spice-chili-500 flex items-center justify-center transition-colors" 
                                    title="Delete Spice">
                                <i class="fas fa-trash-alt text-xs"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- 12 Months Grid -->
                    <div class="grid grid-cols-4 gap-2">
                        <?php foreach ($month_cols as $index => $col):
                            $is_active = (int)$row[$col] > 0;
                            $m_label = $month_labels[$index];
                        ?>
                        <div class="text-center py-1.5 rounded-lg border text-[10px] font-medium transition-all <?= $is_active ? 'bg-spice-green-600/10 text-spice-green-600 border-spice-green-600/20 dark:bg-emerald-900/20 dark:text-emerald-400 dark:border-emerald-800 font-semibold' : 'bg-gray-50/55 text-gray-400 border-gray-100 dark:bg-slate-800/50 dark:text-slate-500 dark:border-slate-800' ?>">
                            <?= $m_label ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Legend Section -->
            <div class="flex flex-wrap items-center justify-center gap-x-6 gap-y-3 mt-6 border-t border-gray-100 dark:border-slate-700 pt-5 text-[12px]">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-3 rounded bg-spice-green-600 dark:bg-emerald-500 block"></span>
                    <span class="text-gray-500 dark:text-slate-400 font-medium">Harvesting Period</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-6 h-3 rounded bg-gray-100 dark:bg-slate-700 block border border-gray-200 dark:border-slate-600"></span>
                    <span class="text-gray-500 dark:text-slate-400 font-medium">Off Season</span>
                </div>
            </div>
        </div>

        <!-- Info Cards -->
        <!-- <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-card border border-gray-100/50 dark:border-slate-700 p-6 flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-spice-green-600/10 flex items-center justify-center text-spice-green-600 flex-shrink-0">
                    <i class="fas fa-lightbulb text-sm"></i>
                </div>
                <div>
                    <h3 class="text-[13px] font-bold text-spice-dark dark:text-white mb-1.5">Harvest Synchronization</h3>
                    <p class="text-[12px] text-gray-400 dark:text-slate-500 leading-relaxed">This chart directly updates the calendar grid on the public website, allowing international buyers to time their orders to match fresh availability.</p>
                </div>
            </div>
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-card border border-gray-100/50 dark:border-slate-700 p-6 flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-spice-turmeric-500/10 flex items-center justify-center text-spice-turmeric-500 flex-shrink-0">
                    <i class="fas fa-database text-sm"></i>
                </div>
                <div>
                    <h3 class="text-[13px] font-bold text-spice-dark dark:text-white mb-1.5">Database Connected</h3>
                    <p class="text-[12px] text-gray-400 dark:text-slate-500 leading-relaxed">All harvest data is stored in the <code class="bg-gray-100 dark:bg-slate-700 px-1.5 py-0.5 rounded text-[11px]">harvest_calendar</code> table. Changes here are reflected on the public website instantly.</p>
                </div>
            </div>
        </div> -->

    </div>
</main>

<script>
function deleteRow(id, name) {
    confirmDelete(name).then((result) => {
        if (result.isConfirmed) {
            // Send delete request
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
                    const mobileRow = document.getElementById('mobile-row-' + id);
                    if (mobileRow) {
                        mobileRow.style.transition = 'opacity 0.3s, transform 0.3s';
                        mobileRow.style.opacity = '0';
                        mobileRow.style.transform = 'translateX(20px)';
                        setTimeout(() => mobileRow.remove(), 300);
                    }
                    showToast(name + ' removed from harvest calendar', 'success');
                } else {
                    showToast(data.message || 'Failed to delete', 'error');
                }
            })
            .catch(() => showToast('Server error. Please try again.', 'error'));
        }
    });
}

// Auto-dismiss flash messages after 5s
setTimeout(() => {
    const flash = document.getElementById('flashSuccess') || document.getElementById('flashError');
    if (flash) {
        flash.style.transition = 'opacity 0.3s';
        flash.style.opacity = '0';
        setTimeout(() => flash.remove(), 300);
    }
}, 5000);
</script>

<?php include '../../includes/footer.php'; ?>
