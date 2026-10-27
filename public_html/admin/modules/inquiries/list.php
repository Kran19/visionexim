<?php 
/**
 * Vision Exim — Inquiries Management List
 */
include '../../includes/auth.php';
include '../../includes/db.php';

$page_title = 'Export Inquiries';
$current_module = 'inquiries';

// Handle flash redirect notifications
$success_msg = isset($_GET['success']) ? htmlspecialchars($_GET['success']) : '';
$error_msg   = isset($_GET['error'])   ? htmlspecialchars($_GET['error'])   : '';

// Read filter parameters
$search = trim($_GET['search'] ?? '');
$status_filter = trim($_GET['status'] ?? '');
$source_filter = trim($_GET['source'] ?? '');

// Compute dynamic count metrics
$new_count = 0;
if ($res = $conn->query("SELECT COUNT(*) as cnt FROM inquiries WHERE status = 'new'")) {
    $new_count = (int)$res->fetch_assoc()['cnt'];
}

$progress_count = 0;
if ($res = $conn->query("SELECT COUNT(*) as cnt FROM inquiries WHERE status = 'progress'")) {
    $progress_count = (int)$res->fetch_assoc()['cnt'];
}

$replied_count = 0;
if ($res = $conn->query("SELECT COUNT(*) as cnt FROM inquiries WHERE status = 'replied'")) {
    $replied_count = (int)$res->fetch_assoc()['cnt'];
}

$total_count = 0;
if ($res = $conn->query("SELECT COUNT(*) as cnt FROM inquiries")) {
    $total_count = (int)$res->fetch_assoc()['cnt'];
}

// Build query
$sql = "SELECT * FROM inquiries WHERE 1=1";
$types = "";
$params = [];

if (!empty($search)) {
    $sql .= " AND (company_name LIKE ? OR contact_name LIKE ? OR email LIKE ? OR country_name LIKE ? OR requested_product LIKE ?)";
    $search_param = "%$search%";
    for ($i = 0; $i < 5; $i++) {
        $params[] = &$search_param;
        $types .= "s";
    }
}

if (!empty($status_filter)) {
    $sql .= " AND status = ?";
    $params[] = &$status_filter;
    $types .= "s";
}

if (!empty($source_filter)) {
    $sql .= " AND source = ?";
    $params[] = &$source_filter;
    $types .= "s";
}

$sql .= " ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);
if (!empty($types)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$inquiries_data = [];
while ($row = $result->fetch_assoc()) {
    $inquiries_data[] = $row;
}
$stmt->close();

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
                    <span class="text-gray-600 dark:text-slate-400 font-medium">Inquiries</span>
                </div>
                <h1 class="text-[22px] font-bold text-spice-dark dark:text-white">Export Inquiry Management</h1>
                <p class="text-[13px] text-gray-400 dark:text-slate-500 mt-1">Review and reply to incoming spice export inquiries</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="px-3.5 py-1.5 rounded-xl bg-spice-green-600/10 text-spice-green-600 dark:text-emerald-400 text-[11px] font-bold">
                    <i class="fas fa-envelope mr-1.5"></i> <?= $total_count ?> Total
                </span>
            </div>
        </div>

        <!-- Inquiry Status Summary Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-5 mb-6">
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 border border-gray-100/50 dark:border-slate-700 shadow-card">
                <p class="text-gray-400 dark:text-slate-500 text-[11px] font-semibold uppercase tracking-wider">New Inquiries</p>
                <div class="flex items-baseline justify-between mt-1">
                    <span class="text-[22px] font-bold text-spice-green-600"><?= $new_count ?></span>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-600 dark:bg-emerald-900/20 dark:text-emerald-400">Pending</span>
                </div>
            </div>
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 border border-gray-100/50 dark:border-slate-700 shadow-card">
                <p class="text-gray-400 dark:text-slate-500 text-[11px] font-semibold uppercase tracking-wider">In Progress</p>
                <div class="flex items-baseline justify-between mt-1">
                    <span class="text-[22px] font-bold text-spice-turmeric-500"><?= $progress_count ?></span>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-spice-turmeric-50 text-spice-turmeric-600 dark:bg-spice-turmeric-900/20 dark:text-spice-turmeric-400">Discussion</span>
                </div>
            </div>
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 border border-gray-100/50 dark:border-slate-700 shadow-card">
                <p class="text-gray-400 dark:text-slate-500 text-[11px] font-semibold uppercase tracking-wider">Replied</p>
                <div class="flex items-baseline justify-between mt-1">
                    <span class="text-[22px] font-bold text-blue-500"><?= $replied_count ?></span>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-blue-50 text-blue-600 dark:bg-blue-900/20 dark:text-blue-400">Quoted</span>
                </div>
            </div>
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 border border-gray-100/50 dark:border-slate-700 shadow-card">
                <p class="text-gray-400 dark:text-slate-500 text-[11px] font-semibold uppercase tracking-wider">Total Received</p>
                <div class="flex items-baseline justify-between mt-1">
                    <span class="text-[22px] font-bold text-purple-500"><?= $total_count ?></span>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-purple-50 text-purple-600 dark:bg-purple-900/20 dark:text-purple-400">CRM Logs</span>
                </div>
            </div>
        </div>

        <!-- Filters Bar -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-card border border-gray-100/50 dark:border-slate-700 p-5 mb-6">
            <form method="GET" action="" class="flex flex-col lg:flex-row lg:items-center gap-4">
                <!-- Search -->
                <div class="flex-1">
                    <div class="relative">
                        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-300 dark:text-slate-500 text-xs"></i>
                        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search inquiries by buyer name, email, country, product..." 
                               class="w-full pl-11 pr-4 py-3 rounded-xl border-2 border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-700/50 text-[12px] text-spice-dark dark:text-slate-200 placeholder-gray-300 dark:placeholder-slate-500 focus:border-spice-green-600 focus:bg-white dark:focus:bg-slate-700 focus:ring-4 focus:ring-spice-green-600/10 outline-none transition-all">
                    </div>
                </div>
                <!-- Status Filter -->
                <select name="status" class="px-4 py-3 rounded-xl border-2 border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-700/50 text-[12px] text-gray-600 dark:text-slate-300 outline-none focus:border-spice-green-600 cursor-pointer min-w-[140px]">
                    <option value="">All Status</option>
                    <option value="new" <?= $status_filter === 'new' ? 'selected' : '' ?>>New</option>
                    <option value="progress" <?= $status_filter === 'progress' ? 'selected' : '' ?>>In Progress</option>
                    <option value="replied" <?= $status_filter === 'replied' ? 'selected' : '' ?>>Replied</option>
                    <option value="closed" <?= $status_filter === 'closed' ? 'selected' : '' ?>>Closed</option>
                </select>
                
                <button type="submit" class="px-5 py-3 rounded-xl bg-spice-green-600 hover:bg-spice-green-700 text-white text-[12px] font-semibold transition-colors">
                    Filter
                </button>
                <?php if (!empty($search) || !empty($status_filter) || !empty($source_filter)): ?>
                <a href="list.php" class="px-4 py-3 rounded-xl bg-gray-100 dark:bg-slate-700 text-gray-500 dark:text-slate-400 text-[12px] font-semibold hover:bg-gray-200 dark:hover:bg-slate-600 transition-colors text-center">
                    Clear
                </a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Inquiries Table -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-card border border-gray-100/50 dark:border-slate-700 overflow-hidden">
            <?php if (empty($inquiries_data)): ?>
            <!-- Empty State -->
            <div class="text-center py-16">
                <div class="w-20 h-20 rounded-2xl bg-spice-green-600/10 flex items-center justify-center mx-auto mb-5">
                    <i class="fas fa-envelope-open-text text-3xl text-spice-green-600"></i>
                </div>
                <h3 class="text-[16px] font-bold text-spice-dark dark:text-white mb-2">No Inquiries Found</h3>
                <p class="text-[13px] text-gray-400 dark:text-slate-500 mb-4 max-w-md mx-auto">Export queries sent by global buyers will show up here. You can track response status and draft quotations.</p>
            </div>
            <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50/80 dark:bg-slate-700/50 border-b border-gray-100 dark:border-slate-700">
                            <th class="px-5 py-4 text-left text-[11px] font-semibold text-gray-400 dark:text-slate-500 uppercase tracking-wider ps-6">Buyer / Company</th>
                            <th class="px-5 py-4 text-left text-[11px] font-semibold text-gray-400 dark:text-slate-500 uppercase tracking-wider">Date</th>
                            <th class="px-5 py-4 text-left text-[11px] font-semibold text-gray-400 dark:text-slate-500 uppercase tracking-wider">Status</th>
                            <th class="px-5 py-4 text-center text-[11px] font-semibold text-gray-400 dark:text-slate-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                        <?php foreach ($inquiries_data as $row): 
                            $status_class = 'bg-gray-100 text-gray-700 dark:bg-slate-700 dark:text-slate-300';
                            if ($row['status'] === 'new') {
                                $status_class = 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400';
                            } elseif ($row['status'] === 'progress') {
                                $status_class = 'bg-spice-turmeric-100 text-spice-turmeric-600 dark:bg-spice-turmeric-900/30 dark:text-spice-turmeric-400';
                            } elseif ($row['status'] === 'replied') {
                                $status_class = 'bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400';
                            }
                            
                            // Source icons
                            $source_icon = 'fa-globe text-spice-green-600';
                            if ($row['source'] === 'whatsapp') {
                                $source_icon = 'fa-whatsapp text-emerald-500 fab';
                            } elseif ($row['source'] === 'email') {
                                $source_icon = 'fa-envelope text-blue-500';
                            } elseif ($row['source'] === 'direct') {
                                $source_icon = 'fa-phone text-purple-500';
                            }
                        ?>
                        <tr class="hover:bg-gray-50/60 dark:hover:bg-slate-700/30 transition-colors" id="row-<?= $row['id'] ?>">
                            <td class="px-5 py-4 ps-6">
                                <div>
                                    <p class="text-[13px] font-semibold text-spice-dark dark:text-white">
                                        <?= htmlspecialchars(!empty($row['contact_name']) ? $row['contact_name'] : $row['company_name']) ?>
                                    </p>
                                    <?php if (!empty($row['company_name'])): ?>
                                    <p class="text-[11px] text-gray-500 dark:text-slate-400 font-medium"><?= htmlspecialchars($row['company_name']) ?></p>
                                    <?php endif; ?>
                                    <p class="text-[10px] text-gray-400 dark:text-slate-500"><?= htmlspecialchars($row['email']) ?><?= !empty($row['phone']) ? ' • ' . htmlspecialchars($row['phone']) : '' ?></p>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-[12px] text-gray-600 dark:text-slate-400"><?= date('M d, Y', strtotime($row['created_at'])) ?></td>
                            <td class="px-5 py-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold <?= $status_class ?>">
                                    <span class="w-1.5 h-1.5 rounded-full <?= $row['status'] === 'new' ? 'bg-emerald-500 animate-pulse' : ($row['status'] === 'progress' ? 'bg-spice-turmeric-500' : ($row['status'] === 'replied' ? 'bg-blue-500' : 'bg-gray-400')) ?>"></span>
                                    <?= ucfirst(htmlspecialchars($row['status'] === 'progress' ? 'In Progress' : $row['status'])) ?>
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="view.php?id=<?= $row['id'] ?>" class="w-8 h-8 rounded-lg bg-spice-green-600/10 hover:bg-spice-green-600/20 flex items-center justify-center text-spice-green-600 dark:text-emerald-400 transition-colors" title="View & Reply">
                                        <i class="fas fa-envelope-open text-[10px]"></i>
                                    </a>
                                    <?php if (!empty($row['phone'])): ?>
                                    <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $row['phone']) ?>" target="_blank" class="w-8 h-8 rounded-lg bg-green-500/10 hover:bg-green-500/20 flex items-center justify-center text-green-500 transition-colors" title="WhatsApp Chat">
                                        <i class="fab fa-whatsapp text-xs"></i>
                                    </a>
                                    <?php endif; ?>
                                    <button onclick="deleteInquiry(<?= $row['id'] ?>, '<?= htmlspecialchars(!empty($row['company_name']) ? $row['company_name'] : $row['contact_name'], ENT_QUOTES) ?>')" class="w-8 h-8 rounded-lg bg-spice-chili-50 dark:bg-spice-chili-900/20 flex items-center justify-center text-spice-chili-500 hover:bg-spice-chili-100 dark:hover:bg-spice-chili-900/40 transition-colors" title="Delete">
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
function deleteInquiry(id, name) {
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
                    showToast('Inquiry from ' + name + ' deleted.', 'success');
                } else {
                    showToast(data.message || 'Failed to delete inquiry.', 'error');
                }
            })
            .catch(() => showToast('Server error. Please try again.', 'error'));
        }
    });
}
</script>

<?php include '../../includes/footer.php'; ?>
