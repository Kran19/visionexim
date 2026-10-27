<?php 
/**
 * Vision Exim — Add Spice Harvest Calendar
 */
include '../../includes/auth.php';
include '../../includes/db.php';

$page_title = 'Add Harvest Crop';
$current_module = 'harvest';

$month_names = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
$month_cols  = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec_month'];

// Handle form submission
$errors = [];
$old_name = '';
$old_months = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $spice_name = trim($_POST['spice_name'] ?? '');
    $months     = $_POST['months'] ?? [];
    $sort_order = (int)($_POST['sort_order'] ?? 0);

    $old_name   = $spice_name;
    $old_months = $months;

    // Validation
    if (empty($spice_name)) {
        $errors[] = 'Spice / Crop name is required.';
    }

    // Check for duplicate name
    if (empty($errors)) {
        $check = $conn->prepare("SELECT id FROM harvest_calendar WHERE spice_name = ?");
        $check->bind_param('s', $spice_name);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            $errors[] = 'A crop with this name already exists.';
        }
        $check->close();
    }

    if (empty($errors)) {
        // Build month values
        $month_values = [];
        for ($i = 0; $i < 12; $i++) {
            $month_values[] = in_array((string)($i + 1), $months) ? 1 : 0;
        }

        $stmt = $conn->prepare("INSERT INTO harvest_calendar (spice_name, jan, feb, mar, apr, may, jun, jul, aug, sep, oct, nov, dec_month, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('siiiiiiiiiiiii',
            $spice_name,
            $month_values[0], $month_values[1], $month_values[2],
            $month_values[3], $month_values[4], $month_values[5],
            $month_values[6], $month_values[7], $month_values[8],
            $month_values[9], $month_values[10], $month_values[11],
            $sort_order
        );

        if ($stmt->execute()) {
            header('Location: list.php?success=' . urlencode($spice_name . ' added to harvest calendar successfully!'));
            exit;
        } else {
            $errors[] = 'Database error: ' . $conn->error;
        }
        $stmt->close();
    }
}

// Get next sort order
$next_sort = 1;
$sort_result = $conn->query("SELECT MAX(sort_order) as max_sort FROM harvest_calendar");
if ($sort_result && $r = $sort_result->fetch_assoc()) {
    $next_sort = (int)$r['max_sort'] + 1;
}

include '../../includes/header.php'; 
include '../../includes/sidebar.php'; 
include '../../includes/navbar.php'; 
?>

<main class="lg:ml-[270px] pt-[70px] min-h-screen sidebar-transition">
    <div class="p-6 lg:p-8 page-content">

        <!-- Page Header -->
        <div class="mb-8">
            <div class="flex items-center gap-2 text-[12px] text-gray-400 dark:text-slate-500 mb-1">
                <a href="../../dashboard.php" class="hover:text-spice-green-600 transition-colors">Dashboard</a>
                <span>/</span>
                <a href="list.php" class="hover:text-spice-green-600 transition-colors">Harvest Chart</a>
                <span>/</span>
                <span class="text-gray-600 dark:text-slate-400 font-medium">Add Spice Crop</span>
            </div>
            <h1 class="text-[22px] font-bold text-spice-dark dark:text-white">Add Spice Harvest Calendar</h1>
            <p class="text-[13px] text-gray-400 dark:text-slate-500 mt-1">Configure active harvesting months for a new spice or agricultural crop</p>
        </div>

        <!-- Error Messages -->
        <?php if (!empty($errors)): ?>
        <div class="mb-6 p-4 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
            <?php foreach ($errors as $err): ?>
            <div class="flex items-center gap-2 text-red-700 dark:text-red-300 text-[13px] font-medium">
                <i class="fas fa-exclamation-circle text-red-500"></i>
                <?= htmlspecialchars($err) ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Form Card -->
        <form method="POST" action=""
              class="max-w-3xl bg-white dark:bg-slate-800 rounded-2xl shadow-card border border-gray-100/50 dark:border-slate-700 p-6 space-y-6">
            
            <!-- Crop details -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-2">Spice / Crop Name <span class="text-spice-chili-500">*</span></label>
                    <input type="text" name="spice_name" placeholder="e.g. Cloves, Nutmeg, Sesame Seeds" required
                           value="<?= htmlspecialchars($old_name) ?>"
                           class="w-full px-4 py-3 rounded-xl border-2 border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-700/50 text-[12px] text-spice-dark dark:text-slate-200 focus:border-spice-green-600 focus:bg-white dark:focus:bg-slate-700 focus:ring-4 focus:ring-spice-green-600/10 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-2">Sort Order</label>
                    <input type="number" name="sort_order" placeholder="Display order" min="0"
                           value="<?= $next_sort ?>"
                           class="w-full px-4 py-3 rounded-xl border-2 border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-700/50 text-[12px] text-spice-dark dark:text-slate-200 focus:border-spice-green-600 focus:bg-white dark:focus:bg-slate-700 focus:ring-4 focus:ring-spice-green-600/10 outline-none transition-all">
                </div>
            </div>

            <!-- Month selectors -->
            <div>
                <div class="flex items-center justify-between mb-3">
                    <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Select Active Harvesting Months</label>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="toggleAllMonths(true)" class="text-[10px] font-semibold text-spice-green-600 hover:text-spice-green-700 transition-colors px-2 py-1 rounded-lg hover:bg-spice-green-600/5">Select All</button>
                        <span class="text-gray-300 dark:text-slate-600">|</span>
                        <button type="button" onclick="toggleAllMonths(false)" class="text-[10px] font-semibold text-gray-400 hover:text-gray-600 transition-colors px-2 py-1 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-700">Clear All</button>
                    </div>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                    <?php foreach ($month_names as $i => $month): ?>
                    <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 dark:border-slate-700 hover:bg-gray-50 dark:hover:bg-slate-700/50 cursor-pointer group transition-all has-[:checked]:bg-spice-green-600/5 has-[:checked]:border-spice-green-600/30">
                        <input type="checkbox" name="months[]" value="<?= $i + 1 ?>" class="month-checkbox w-4 h-4 rounded border-gray-300 text-spice-green-600 focus:ring-spice-green-600/30"
                               <?= in_array((string)($i + 1), $old_months) ? 'checked' : '' ?>>
                        <span class="text-[12px] font-medium text-spice-dark dark:text-slate-300 group-hover:text-spice-green-600 transition-colors"><?= $month ?></span>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-3 border-t border-gray-100 dark:border-slate-700 pt-5">
                <button type="submit" 
                        class="px-6 py-3 rounded-xl bg-gradient-to-r from-spice-green-600 to-spice-green-700 text-white text-[12px] font-semibold hover:from-spice-green-700 hover:to-spice-green-800 transition-all shadow-lg shadow-spice-green-600/20">
                    <i class="fas fa-save mr-1.5"></i> Save Crop Season
                </button>
                <a href="list.php" 
                   class="px-6 py-3 rounded-xl border-2 border-gray-100 dark:border-slate-700 hover:border-gray-200 dark:hover:border-slate-600 text-gray-500 dark:text-slate-400 text-[12px] font-semibold transition-all">
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
</script>

<?php include '../../includes/footer.php'; ?>
