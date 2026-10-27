<?php 
include 'includes/auth.php';
include 'includes/db.php';

$page_title = 'Dashboard';

// Fetch dynamic stats
$total_products = 0;
if ($res = $conn->query("SELECT COUNT(*) as cnt FROM products WHERE status = 'active'")) {
    $row = $res->fetch_assoc();
    $total_products = (int)$row['cnt'];
}

$total_categories = 0;
if ($res = $conn->query("SELECT COUNT(*) as cnt FROM categories WHERE status = 'active'")) {
    $row = $res->fetch_assoc();
    $total_categories = (int)$row['cnt'];
}

$total_inquiries = 0;
if ($res = $conn->query("SELECT COUNT(*) as cnt FROM inquiries")) {
    $row = $res->fetch_assoc();
    $total_inquiries = (int)$row['cnt'];
}

$pending_inquiries = 0;
if ($res = $conn->query("SELECT COUNT(*) as cnt FROM inquiries WHERE status = 'new'")) {
    $row = $res->fetch_assoc();
    $pending_inquiries = (int)$row['cnt'];
}

// Fetch status distribution for Donut Chart
$new_count = 0;
$progress_count = 0;
$replied_count = 0;
$closed_count = 0;

if ($res = $conn->query("SELECT status, COUNT(*) as cnt FROM inquiries GROUP BY status")) {
    while ($row = $res->fetch_assoc()) {
        if ($row['status'] === 'new') $new_count = (int)$row['cnt'];
        elseif ($row['status'] === 'progress') $progress_count = (int)$row['cnt'];
        elseif ($row['status'] === 'replied') $replied_count = (int)$row['cnt'];
        elseif ($row['status'] === 'closed') $closed_count = (int)$row['cnt'];
    }
}
$grand_total_inquiries = $new_count + $progress_count + $replied_count + $closed_count;

$new_pct = $grand_total_inquiries > 0 ? round(($new_count / $grand_total_inquiries) * 100) : 0;
$progress_pct = $grand_total_inquiries > 0 ? round(($progress_count / $grand_total_inquiries) * 100) : 0;
$replied_pct = $grand_total_inquiries > 0 ? round(($replied_count / $grand_total_inquiries) * 100) : 0;
$closed_pct = $grand_total_inquiries > 0 ? round(($closed_count / $grand_total_inquiries) * 100) : 0;

// Fetch Monthly Inquiry Trends (last 12 months)
$months_list = [];
$inquiry_counts = [];
for ($i = 11; $i >= 0; $i--) {
    $time = strtotime("-$i months");
    $month_num = date('n', $time);
    $month_name = date('M', $time);
    $year_num = date('Y', $time);
    
    $months_list[] = $month_name;
    
    $cnt = 0;
    $stmt = $conn->prepare("SELECT COUNT(*) as cnt FROM inquiries WHERE MONTH(created_at) = ? AND YEAR(created_at) = ?");
    $stmt->bind_param('ii', $month_num, $year_num);
    $stmt->execute();
    if ($res = $stmt->get_result()) {
        $cnt = (int)$res->fetch_assoc()['cnt'];
    }
    $stmt->close();
    
    $inquiry_counts[] = $cnt;
}
$js_months = json_encode($months_list);
$js_counts = json_encode($inquiry_counts);

// Fetch 3 latest inquiries
$latest_inquiries = [];
if ($res = $conn->query("SELECT * FROM inquiries ORDER BY created_at DESC LIMIT 3")) {
    while ($row = $res->fetch_assoc()) {
        $latest_inquiries[] = $row;
    }
}

// Fetch 3 recently added products
$recent_products = [];
if ($res = $conn->query("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC LIMIT 3")) {
    while ($row = $res->fetch_assoc()) {
        $recent_products[] = $row;
    }
}

include 'includes/header.php'; 
include 'includes/sidebar.php'; 
include 'includes/navbar.php'; 
?>

<!-- Main Content Area -->
<main class="lg:ml-[270px] pt-[70px] min-h-screen sidebar-transition">
    <div class="p-6 lg:p-8 page-content">

        <!-- Welcome Banner -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-spice-green-600 via-spice-green-700 to-spice-green-800 p-7 mb-8">
            <div class="relative z-10">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-white text-xl font-bold mb-1">Welcome back, Vision Exim! 👋</h2>
                        <p class="text-white/60 text-[13px]">Manage your spice products and client inquiries with elegance.</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="<?= htmlspecialchars(ve_url('admin/modules/products/add.php')) ?>" class="px-5 py-2.5 rounded-xl bg-white text-spice-green-600 text-[12px] font-semibold hover:bg-white/90 transition-colors shadow-lg">
                            <i class="fas fa-plus mr-2"></i>Add Product
                        </a>
                    </div>
                </div>
            </div>
            <!-- Decorative -->
            <div class="absolute -top-10 -right-10 w-44 h-44 bg-white/5 rounded-full"></div>
            <div class="absolute -bottom-8 -right-4 w-28 h-28 bg-spice-turmeric-500/10 rounded-full"></div>
            <div class="absolute top-4 right-32 w-3 h-3 bg-spice-turmeric-400/30 rounded-full"></div>
        </div>

        <!-- Stats Cards Row -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
            
            <!-- Total Products -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 shadow-card hover:shadow-card-hover transition-all duration-300 group border border-gray-100/50 dark:border-slate-700">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-11 h-11 rounded-xl bg-spice-green-600/10 dark:bg-spice-green-600/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i class="fas fa-boxes-stacked text-spice-green-600 text-[15px]"></i>
                    </div>
                    <span class="flex items-center gap-1 text-emerald-500 text-[11px] font-semibold bg-emerald-50 dark:bg-emerald-900/20 px-2 py-0.5 rounded-full">
                        Active
                    </span>
                </div>
                <p class="text-[26px] font-bold text-spice-dark dark:text-white leading-tight"><?= $total_products ?></p>
                <p class="text-gray-400 dark:text-slate-500 text-[12px] mt-0.5">Total Products</p>
            </div>

            <!-- Categories -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 shadow-card hover:shadow-card-hover transition-all duration-300 group border border-gray-100/50 dark:border-slate-700">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-11 h-11 rounded-xl bg-spice-turmeric-500/10 dark:bg-spice-turmeric-500/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i class="fas fa-layer-group text-spice-turmeric-500 text-[15px]"></i>
                    </div>
                    <span class="flex items-center gap-1 text-spice-turmeric-500 text-[11px] font-semibold bg-spice-turmeric-50 dark:bg-spice-turmeric-900/20 px-2 py-0.5 rounded-full">
                        <?= $total_categories ?> active
                    </span>
                </div>
                <p class="text-[26px] font-bold text-spice-dark dark:text-white leading-tight"><?= $total_categories ?></p>
                <p class="text-gray-400 dark:text-slate-500 text-[12px] mt-0.5">Product Categories</p>
            </div>

            <!-- Total Inquiries -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 shadow-card hover:shadow-card-hover transition-all duration-300 group border border-gray-100/50 dark:border-slate-700">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-11 h-11 rounded-xl bg-blue-500/10 dark:bg-blue-500/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i class="fas fa-envelope text-blue-500 text-[15px]"></i>
                    </div>
                    <span class="flex items-center gap-1 text-blue-500 text-[11px] font-semibold bg-blue-50 dark:bg-blue-900/20 px-2 py-0.5 rounded-full">
                        All time
                    </span>
                </div>
                <p class="text-[26px] font-bold text-spice-dark dark:text-white leading-tight"><?= $total_inquiries ?></p>
                <p class="text-gray-400 dark:text-slate-500 text-[12px] mt-0.5">Total Inquiries</p>
            </div>

            <!-- Pending Inquiries -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 shadow-card hover:shadow-card-hover transition-all duration-300 group border border-gray-100/50 dark:border-slate-700">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-11 h-11 rounded-xl bg-spice-chili-500/10 dark:bg-spice-chili-500/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i class="fas fa-envelope-open-text text-spice-chili-500 text-[15px]"></i>
                    </div>
                    <span class="flex items-center gap-1 text-spice-chili-500 text-[11px] font-semibold bg-spice-chili-50 dark:bg-spice-chili-900/20 px-2 py-0.5 rounded-full">
                        <?= $pending_inquiries ?> new
                    </span>
                </div>
                <p class="text-[26px] font-bold text-spice-dark dark:text-white leading-tight"><?= $pending_inquiries ?></p>
                <p class="text-gray-400 dark:text-slate-500 text-[12px] mt-0.5">Pending Inquiries</p>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-8">
            
            <!-- Monthly Inquiry Trends (2/3 width) -->
            <div class="xl:col-span-2 bg-white dark:bg-slate-800 rounded-2xl shadow-card border border-gray-100/50 dark:border-slate-700">
                <div class="flex items-center justify-between p-6 pb-2">
                    <div>
                        <h3 class="text-[15px] font-semibold text-spice-dark dark:text-white">Inquiry Analytics Trend</h3>
                        <p class="text-[12px] text-gray-400 dark:text-slate-500 mt-0.5">Monthly inquiries volume received</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-spice-green-600"></span>
                        <span class="text-[11px] font-medium text-gray-500 dark:text-slate-400">Inquiries Count</span>
                    </div>
                </div>
                <div id="inquiryAnalyticsChart" class="px-4 pb-2"></div>
            </div>

            <!-- Inquiries by Status (1/3 width) -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-card border border-gray-100/50 dark:border-slate-700">
                <div class="p-6 pb-2">
                    <h3 class="text-[15px] font-semibold text-spice-dark dark:text-white">Inquiries Status</h3>
                    <p class="text-[12px] text-gray-400 dark:text-slate-500 mt-0.5">Conversion distribution</p>
                </div>
                <div id="inquiryStatusChart" class="px-4"></div>
                <!-- Status Legend -->
                <div class="px-6 pb-5 space-y-3 mt-2">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                            <span class="text-[12px] text-gray-600 dark:text-slate-400">New / Unreplied</span>
                        </div>
                        <span class="text-[12px] font-semibold text-spice-dark dark:text-white"><?= $new_count ?> (<?= $new_pct ?>%)</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-spice-turmeric-500"></span>
                            <span class="text-[12px] text-gray-600 dark:text-slate-400">In Progress</span>
                        </div>
                        <span class="text-[12px] font-semibold text-spice-dark dark:text-white"><?= $progress_count ?> (<?= $progress_pct ?>%)</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-spice-green-600"></span>
                            <span class="text-[12px] text-gray-600 dark:text-slate-400">Replied / Resolved</span>
                        </div>
                        <span class="text-[12px] font-semibold text-spice-dark dark:text-white"><?= $replied_count ?> (<?= $replied_pct ?>%)</span>
                    </div>
                    <?php if ($closed_count > 0): ?>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-gray-500"></span>
                            <span class="text-[12px] text-gray-600 dark:text-slate-400">Closed</span>
                        </div>
                        <span class="text-[12px] font-semibold text-spice-dark dark:text-white"><?= $closed_count ?> (<?= $closed_pct ?>%)</span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Recent Activity Section -->
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-8">
            
            <!-- Latest Inquiries -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-card border border-gray-100/50 dark:border-slate-700">
                <div class="flex items-center justify-between p-6 pb-4">
                    <div>
                        <h3 class="text-[15px] font-semibold text-spice-dark dark:text-white">Latest Inquiries</h3>
                        <p class="text-[12px] text-gray-400 dark:text-slate-500 mt-0.5">Recent client messages</p>
                    </div>
                    <a href="<?= htmlspecialchars(ve_url('admin/modules/inquiries/list.php')) ?>" class="text-[12px] font-semibold text-spice-green-600 hover:text-spice-green-700 transition-colors">View All →</a>
                </div>
                <div class="px-6 pb-5 space-y-3">
                    <?php if (empty($latest_inquiries)): ?>
                    <p class="text-[12px] text-gray-400 dark:text-slate-500 text-center py-6">No inquiries yet.</p>
                    <?php else: ?>
                    <?php foreach ($latest_inquiries as $inq): 
                        // Generate initials
                        $comp_name = $inq['company_name'];
                        $words = explode(' ', $comp_name);
                        $init = strtoupper(substr($words[0] ?? '', 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
                        
                        // Status styling
                        $status_class = 'bg-gray-100 text-gray-700 dark:bg-slate-700 dark:text-slate-300';
                        if ($inq['status'] === 'new') {
                            $status_class = 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400';
                        } elseif ($inq['status'] === 'progress') {
                            $status_class = 'bg-spice-turmeric-50 text-spice-turmeric-700 dark:bg-spice-turmeric-950 dark:text-spice-turmeric-400';
                        } elseif ($inq['status'] === 'replied') {
                            $status_class = 'bg-blue-50 text-blue-700 dark:bg-blue-950 dark:text-blue-400';
                        }
                        
                        // Time formatting
                        $time_str = date('M d, H:i', strtotime($inq['created_at']));
                    ?>
                    <div onclick="window.location.href='<?= htmlspecialchars(ve_url('admin/modules/inquiries/view.php?id=' . (int)$inq['id'])) ?>'" class="flex items-center gap-4 p-3.5 rounded-xl bg-gray-50/80 dark:bg-slate-700/40 hover:bg-gray-100 dark:hover:bg-slate-700/70 transition-colors cursor-pointer">
                        <div class="w-10 h-10 rounded-xl bg-spice-green-600/10 text-spice-green-600 dark:bg-spice-green-600/20 dark:text-emerald-400 flex items-center justify-center flex-shrink-0">
                            <span class="font-bold text-[12px]"><?= htmlspecialchars($init) ?></span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-[13px] font-semibold text-spice-dark dark:text-white truncate"><?= htmlspecialchars($inq['company_name']) ?></p>
                            <p class="text-[11px] text-gray-400 dark:text-slate-500"><?= htmlspecialchars($inq['requested_product']) ?> • <?= htmlspecialchars($inq['quantity']) ?></p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <span class="inline-flex px-2.5 py-1 rounded-full text-[10px] font-bold <?= $status_class ?>"><?= ucfirst(htmlspecialchars($inq['status'] === 'progress' ? 'in progress' : $inq['status'])) ?></span>
                            <p class="text-[10px] text-gray-400 dark:text-slate-500 mt-1"><?= $time_str ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Recently Added Products -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-card border border-gray-100/50 dark:border-slate-700">
                <div class="flex items-center justify-between p-6 pb-4">
                    <div>
                        <h3 class="text-[15px] font-semibold text-spice-dark dark:text-white">Recently Added Products</h3>
                        <p class="text-[12px] text-gray-400 dark:text-slate-500 mt-0.5">Fresh catalog arrivals</p>
                    </div>
                    <a href="<?= htmlspecialchars(ve_url('admin/modules/products/list.php')) ?>" class="text-[12px] font-semibold text-spice-green-600 hover:text-spice-green-700 transition-colors">View All →</a>
                </div>
                <div class="px-6 pb-5">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="text-[11px] font-semibold text-gray-400 dark:text-slate-500 uppercase tracking-wider">
                                    <th class="text-left pb-3">Product Name</th>
                                    <th class="text-left pb-3">Category</th>
                                    <th class="text-left pb-3">MOQ</th>
                                    <th class="text-left pb-3">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                                <?php if (empty($recent_products)): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-6 text-[12px] text-gray-400 dark:text-slate-500">No products added yet.</td>
                                </tr>
                                <?php else: ?>
                                <?php foreach ($recent_products as $p): 
                                    $status_style = 'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-400';
                                    if ($p['status'] === 'active') {
                                        $status_style = 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400';
                                    } elseif ($p['status'] === 'archived') {
                                        $status_style = 'bg-gray-100 text-gray-700 dark:bg-slate-700 dark:text-slate-300';
                                    }
                                ?>
                                <tr onclick="window.location.href='<?= htmlspecialchars(ve_url('admin/modules/products/edit.php?id=' . (int)$p['id'])) ?>'" class="hover:bg-gray-50 dark:hover:bg-slate-700/30 transition-colors cursor-pointer">
                                    <td class="py-3">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-lg bg-spice-green-600/5 flex items-center justify-center text-spice-green-600">
                                                <i class="fas fa-leaf text-xs"></i>
                                            </div>
                                            <span class="text-[12px] font-semibold text-spice-dark dark:text-white"><?= htmlspecialchars($p['name']) ?></span>
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <span class="text-[12px] text-gray-500 dark:text-slate-400"><?= htmlspecialchars($p['category_name']) ?></span>
                                    </td>
                                    <td class="py-3 text-[12px] text-gray-600 dark:text-slate-400"><?= htmlspecialchars($p['moq']) ?></td>
                                    <td class="py-3">
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-[9px] font-bold <?= $status_style ?>"><?= ucfirst(htmlspecialchars($p['status'])) ?></span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center py-4 text-[11px] text-gray-400 dark:text-slate-600">
            © <?php echo date('Y'); ?> Vision Exim. Spice Export Management System. All Rights Reserved.
        </div>

    </div>
</main>

<!-- ApexCharts Initialization -->
<script>
document.addEventListener('DOMContentLoaded', function() {

    // ==========================================
    // 1. Inquiry Analytics Trend (Area Chart)
    // ==========================================
    var inquiryOptions = {
        series: [{
            name: 'Inquiries Received',
            data: <?= $js_counts ?>
        }],
        chart: {
            type: 'area',
            height: 320,
            fontFamily: 'Poppins, sans-serif',
            toolbar: { show: false },
            dropShadow: {
                enabled: true,
                top: 3, left: 0, blur: 6,
                color: '#1F4D3A',
                opacity: 0.15
            }
        },
        colors: ['#1F4D3A'],
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.4,
                opacityTo: 0.05,
                stops: [0, 90, 100]
            }
        },
        stroke: { curve: 'smooth', width: 2.5 },
        dataLabels: { enabled: false },
        xaxis: {
            categories: <?= $js_months ?>,
            labels: { style: { fontSize: '11px', colors: '#9ca3af' } },
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: {
            labels: { 
                style: { fontSize: '11px', colors: '#9ca3af' },
                formatter: function(val) { return val }
            }
        },
        grid: {
            borderColor: '#f3f4f6',
            strokeDashArray: 4,
            xaxis: { lines: { show: false } }
        },
        tooltip: {
            theme: 'light',
            y: {
                formatter: function(val) {
                    return val + ' inquiries';
                }
            }
        }
    };
    new ApexCharts(document.querySelector("#inquiryAnalyticsChart"), inquiryOptions).render();

    // ==========================================
    // 2. Inquiry Status (Donut Chart)
    // ==========================================
    var statusOptions = {
        series: [<?= $new_count ?>, <?= $progress_count ?>, <?= $replied_count ?><?= $closed_count > 0 ? ", $closed_count" : "" ?>],
        chart: {
            type: 'donut',
            height: 220,
            fontFamily: 'Poppins, sans-serif',
        },
        colors: ['#10b981', '#D9A441', '#1F4D3A'<?= $closed_count > 0 ? ", '#6b7280'" : "" ?>],
        labels: ['New', 'In Progress', 'Replied'<?= $closed_count > 0 ? ", 'Closed'" : "" ?>],
        plotOptions: {
            pie: {
                donut: {
                    size: '72%',
                    labels: {
                        show: true,
                        total: {
                            show: true,
                            label: 'Total Inquiries',
                            fontSize: '11px',
                            color: '#9ca3af',
                            formatter: function() { return '<?= $grand_total_inquiries ?>' }
                        },
                        value: {
                            fontSize: '20px',
                            fontWeight: 700,
                            color: '#1E1E1E'
                        }
                    }
                }
            }
        },
        dataLabels: { enabled: false },
        legend: { show: false },
        stroke: { width: 3, colors: ['#fff'] },
        tooltip: {
            y: { formatter: function(val) { return val + '%' } }
        }
    };
    new ApexCharts(document.querySelector("#inquiryStatusChart"), statusOptions).render();

});
</script>

<?php include 'includes/footer.php'; ?>
