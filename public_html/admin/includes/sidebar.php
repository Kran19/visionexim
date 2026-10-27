<?php
require_once __DIR__ . '/../../includes/config.php';
$current_page = basename($_SERVER['PHP_SELF']);
?>
<?php $current_module = isset($current_module) ? $current_module : ''; ?>
<?php
// Dynamic counts for sidebar badges
if (!isset($conn)) { include __DIR__ . '/db.php'; }
$_sidebar_products = 0;
$_sidebar_inquiries = 0;
$r = $conn->query("SELECT COUNT(*) as c FROM products WHERE status='active'");
if ($r) $_sidebar_products = (int)$r->fetch_assoc()['c'];
$r = $conn->query("SELECT COUNT(*) as c FROM inquiries WHERE status='new'");
if ($r) $_sidebar_inquiries = (int)$r->fetch_assoc()['c'];
?>

<!-- Sidebar Overlay (Mobile) -->
<div id="sidebarOverlay" class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden" onclick="toggleSidebar()"></div>

<!-- Sidebar -->
<aside id="sidebar" class="fixed top-0 left-0 h-screen w-[270px] bg-gradient-to-b from-spice-green-600 to-spice-green-800 z-50 sidebar-transition -translate-x-full lg:translate-x-0 flex flex-col shadow-sidebar">
    
    <!-- Logo Section -->
    <div class="flex items-center justify-between px-6 py-5 border-b border-white/10">
        <a href="<?= htmlspecialchars(ve_url('admin/dashboard.php')) ?>" class="flex items-center gap-3 group">
            <div class="w-10 h-10 rounded-xl bg-white/15 flex items-center justify-center group-hover:bg-white/25 transition-colors">
                <i class="fas fa-seedling text-spice-turmeric-400 text-lg"></i>
            </div>
            <div>
                <h1 class="text-white font-bold text-[15px] leading-tight tracking-wide">Vision Exim</h1>
                <span class="text-white/50 text-[10px] uppercase tracking-[0.15em] font-medium">Admin Panel</span>
            </div>
        </a>
        <button onclick="toggleSidebar()" class="lg:hidden text-white/60 hover:text-white transition-colors">
            <i class="fas fa-times text-lg"></i>
        </button>
    </div>

    <!-- Navigation -->
    <nav class="sidebar-nav flex-1 overflow-y-auto px-4 py-4 space-y-1">

        <!-- Dashboard -->
        <a href="<?= htmlspecialchars(ve_url('admin/dashboard.php')) ?>" 
           class="flex items-center gap-3 px-4 py-3 rounded-xl text-[13px] font-medium transition-all duration-200
           <?php echo ($current_page == 'dashboard.php') 
                ? 'bg-white/15 text-white shadow-lg shadow-black/10' 
                : 'text-white/70 hover:bg-white/8 hover:text-white'; ?>">
            <div class="w-8 h-8 rounded-lg <?php echo ($current_page == 'dashboard.php') ? 'bg-spice-turmeric-500' : 'bg-white/10'; ?> flex items-center justify-center transition-colors">
                <i class="fas fa-th-large text-xs <?php echo ($current_page == 'dashboard.php') ? 'text-white' : 'text-white/70'; ?>"></i>
            </div>
            <span>Dashboard</span>
        </a>

        <!-- Section Label -->
        <div class="px-4 pt-5 pb-2">
            <span class="text-[10px] font-semibold uppercase tracking-[0.15em] text-white/30">Management</span>
        </div>

        <!-- Products -->
        <a href="<?= htmlspecialchars(ve_url('admin/modules/products/list.php')) ?>" 
           class="flex items-center gap-3 px-4 py-3 rounded-xl text-[13px] font-medium transition-all duration-200
           <?php echo ($current_module == 'products') 
                ? 'bg-white/15 text-white shadow-lg shadow-black/10' 
                : 'text-white/70 hover:bg-white/8 hover:text-white'; ?>">
            <div class="w-8 h-8 rounded-lg <?php echo ($current_module == 'products') ? 'bg-spice-turmeric-500' : 'bg-white/10'; ?> flex items-center justify-center">
                <i class="fas fa-boxes-stacked text-xs <?php echo ($current_module == 'products') ? 'text-white' : 'text-white/70'; ?>"></i>
            </div>
            <span>Products</span>
            <span class="ml-auto bg-spice-turmeric-500/20 text-spice-turmeric-300 text-[10px] font-bold px-2 py-0.5 rounded-full"><?= $_sidebar_products ?></span>
        </a>

        <!-- Categories -->
        <a href="<?= htmlspecialchars(ve_url('admin/modules/categories/list.php')) ?>" 
           class="flex items-center gap-3 px-4 py-3 rounded-xl text-[13px] font-medium transition-all duration-200
           <?php echo ($current_module == 'categories') 
                ? 'bg-white/15 text-white shadow-lg shadow-black/10' 
                : 'text-white/70 hover:bg-white/8 hover:text-white'; ?>">
            <div class="w-8 h-8 rounded-lg <?php echo ($current_module == 'categories') ? 'bg-spice-turmeric-500' : 'bg-white/10'; ?> flex items-center justify-center">
                <i class="fas fa-layer-group text-xs <?php echo ($current_module == 'categories') ? 'text-white' : 'text-white/70'; ?>"></i>
            </div>
            <span>Categories</span>
        </a>

        <!-- Export Inquiries -->
        <a href="<?= htmlspecialchars(ve_url('admin/modules/inquiries/list.php')) ?>" 
           class="flex items-center gap-3 px-4 py-3 rounded-xl text-[13px] font-medium transition-all duration-200
           <?php echo ($current_module == 'inquiries') 
                ? 'bg-white/15 text-white shadow-lg shadow-black/10' 
                : 'text-white/70 hover:bg-white/8 hover:text-white'; ?>">
            <div class="w-8 h-8 rounded-lg <?php echo ($current_module == 'inquiries') ? 'bg-spice-turmeric-500' : 'bg-white/10'; ?> flex items-center justify-center">
                <i class="fas fa-envelope-open-text text-xs <?php echo ($current_module == 'inquiries') ? 'text-white' : 'text-white/70'; ?>"></i>
            </div>
            <span>Inquiries</span>
            <?php if ($_sidebar_inquiries > 0): ?>
            <span class="ml-auto bg-emerald-500/20 text-emerald-300 text-[10px] font-bold px-2 py-0.5 rounded-full"><?= $_sidebar_inquiries ?></span>
            <?php endif; ?>
        </a>

        <!-- Harvest Chart -->
        <a href="<?= htmlspecialchars(ve_url('admin/modules/harvest/list.php')) ?>" 
           class="flex items-center gap-3 px-4 py-3 rounded-xl text-[13px] font-medium transition-all duration-200
           <?php echo ($current_module == 'harvest') 
                ? 'bg-white/15 text-white shadow-lg shadow-black/10' 
                : 'text-white/70 hover:bg-white/8 hover:text-white'; ?>">
            <div class="w-8 h-8 rounded-lg <?php echo ($current_module == 'harvest') ? 'bg-spice-turmeric-500' : 'bg-white/10'; ?> flex items-center justify-center">
                <i class="fas fa-calendar-days text-xs <?php echo ($current_module == 'harvest') ? 'text-white' : 'text-white/70'; ?>"></i>
            </div>
            <span>Harvest Chart</span>
        </a>

    </nav>

    <!-- Sidebar Footer - Admin Profile -->
    <div class="border-t border-white/10 px-4 py-4">
        <?php
        $sb_admin_name = !empty($_SESSION['admin_user_name']) ? $_SESSION['admin_user_name'] : 'Vision Exim';
        $sb_initials = '';
        foreach (explode(' ', $sb_admin_name) as $w) {
            $sb_initials .= strtoupper(substr($w, 0, 1));
        }
        $sb_initials = substr($sb_initials, 0, 2) ?: 'VE';
        ?>
        <a href="<?= htmlspecialchars(ve_url('admin/profile.php')) ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-xl bg-white/8 hover:bg-white/12 transition-colors mb-2">
            <div class="w-9 h-9 rounded-full bg-spice-turmeric-500 flex items-center justify-center text-white font-bold text-sm">
                <?= htmlspecialchars($sb_initials) ?>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-white text-[12px] font-semibold truncate"><?= htmlspecialchars($sb_admin_name) ?></p>
                <p class="text-white/40 text-[10px] truncate">Super Admin</p>
            </div>
            <i class="fas fa-user-pen text-white/40 text-xs"></i>
        </a>
        <a href="<?= htmlspecialchars(ve_url('admin/logout.php')) ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-xl bg-white/8 hover:bg-spice-chili-500/30 transition-colors group w-full">
            <div class="w-9 h-9 rounded-full bg-white/10 flex items-center justify-center">
                <i class="fas fa-right-from-bracket text-white/60 group-hover:text-spice-chili-400 text-sm transition-colors"></i>
            </div>
            <span class="text-white/60 group-hover:text-white text-[12px] font-semibold transition-colors">Sign Out</span>
        </a>
    </div>
</aside>
