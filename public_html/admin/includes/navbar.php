<!-- Top Navbar -->
<?php require_once __DIR__ . '/../../includes/config.php'; ?>
<header class="fixed top-0 right-0 left-0 lg:left-[270px] h-[70px] bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl border-b border-gray-200/60 dark:border-slate-700/60 z-30 sidebar-transition">
    <div class="flex items-center justify-between h-full px-6">
        
        <!-- Left Side -->
        <div class="flex items-center gap-4">
            <!-- Mobile Menu Toggle -->
            <button onclick="toggleSidebar()" class="lg:hidden w-10 h-10 rounded-xl bg-spice-green-600/10 dark:bg-spice-green-600/20 hover:bg-spice-green-600/20 flex items-center justify-center text-spice-green-600 dark:text-white transition-colors">
                <i class="fas fa-bars text-sm"></i>
            </button>

            <!-- Breadcrumb -->
            <div class="hidden sm:block">
                <div class="flex items-center gap-2 text-[12px] text-gray-400 dark:text-slate-500">
                    <i class="fas fa-home text-[10px]"></i>
                    <span>/</span>
                    <span class="text-gray-600 dark:text-slate-400 font-medium"><?php echo isset($page_title) ? $page_title : 'Dashboard'; ?></span>
                </div>
                <h2 class="text-[16px] font-semibold text-spice-dark dark:text-white -mt-0.5"><?php echo isset($page_title) ? $page_title : 'Dashboard'; ?></h2>
            </div>
        </div>

        <!-- Right Side -->
        <div class="flex items-center gap-2">
            
            <!-- Search -->
            <div class="hidden md:flex items-center bg-gray-100 dark:bg-slate-700 rounded-xl px-4 py-2.5 gap-2 w-[260px] focus-within:ring-2 focus-within:ring-spice-green-600/30 focus-within:bg-white dark:focus-within:bg-slate-600 transition-all">
                <i class="fas fa-search text-gray-400 dark:text-slate-400 text-xs"></i>
                <input type="text" placeholder="Search anything..." class="bg-transparent border-none outline-none text-[13px] text-gray-700 dark:text-slate-200 placeholder-gray-400 dark:placeholder-slate-400 w-full">
                <kbd class="hidden lg:inline-flex items-center gap-1 px-1.5 py-0.5 text-[10px] font-medium text-gray-400 bg-white dark:bg-slate-600 dark:text-slate-400 rounded border border-gray-200 dark:border-slate-500">⌘K</kbd>
            </div>

            <!-- Mobile Search Toggle -->
            <button class="md:hidden w-10 h-10 rounded-xl bg-gray-100 dark:bg-slate-700 flex items-center justify-center text-gray-500 dark:text-slate-400 hover:bg-gray-200 dark:hover:bg-slate-600 transition-colors">
                <i class="fas fa-search text-sm"></i>
            </button>

            <!-- Divider -->
            <div class="hidden md:block w-px h-8 bg-gray-200 dark:bg-slate-600 mx-1"></div>

            <!-- Dark Mode Toggle -->
            <button onclick="toggleDarkMode()" class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-slate-700 flex items-center justify-center text-gray-500 dark:text-slate-400 hover:bg-gray-200 dark:hover:bg-slate-600 transition-colors" title="Toggle Dark Mode">
                <i class="fas fa-moon text-sm dark:hidden"></i>
                <i class="fas fa-sun text-sm hidden dark:inline-block text-spice-turmeric-400"></i>
            </button>

            <!-- Notifications -->
            <div class="relative">
                <!-- <button onclick="toggleNotifications()" class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-slate-700 flex items-center justify-center text-gray-500 dark:text-slate-400 hover:bg-gray-200 dark:hover:bg-slate-600 transition-colors relative">
                    <i class="fas fa-bell text-sm"></i>
                    <span class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-spice-chili-500 text-white text-[9px] font-bold rounded-full flex items-center justify-center">3</span>
                </button> -->

                <!-- Notification Dropdown -->
                <div id="notificationDropdown" class="absolute right-0 top-[calc(100%+8px)] w-[360px] bg-white dark:bg-slate-800 rounded-2xl shadow-dropdown border border-gray-100 dark:border-slate-700 hidden z-50">
                    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-slate-700">
                        <h4 class="font-semibold text-sm text-spice-dark dark:text-white">Notifications</h4>
                        <span class="bg-spice-chili-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">3 New</span>
                    </div>
                    <div class="max-h-[320px] overflow-y-auto">
                        <!-- Notification Item -->
                        <div class="flex gap-3 px-5 py-3.5 hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors border-b border-gray-50 dark:border-slate-700/50 cursor-pointer">
                            <div class="w-9 h-9 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-envelope text-emerald-600 dark:text-emerald-400 text-xs"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-[12px] font-medium text-spice-dark dark:text-white">New inquiry from <strong>Ahmed Trading Co.</strong></p>
                                <p class="text-[11px] text-gray-400 dark:text-slate-500 mt-0.5">2 minutes ago</p>
                            </div>
                            <span class="w-2 h-2 rounded-full bg-spice-green-500 mt-2 flex-shrink-0"></span>
                        </div>
                        <div class="flex gap-3 px-5 py-3.5 hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors border-b border-gray-50 dark:border-slate-700/50 cursor-pointer">
                            <div class="w-9 h-9 rounded-xl bg-spice-turmeric-100 dark:bg-spice-turmeric-900/30 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-ship text-spice-turmeric-600 dark:text-spice-turmeric-400 text-xs"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-[12px] font-medium text-spice-dark dark:text-white">Shipment <strong>SHP-2024-089</strong> delivered</p>
                                <p class="text-[11px] text-gray-400 dark:text-slate-500 mt-0.5">1 hour ago</p>
                            </div>
                            <span class="w-2 h-2 rounded-full bg-spice-green-500 mt-2 flex-shrink-0"></span>
                        </div>
                        <div class="flex gap-3 px-5 py-3.5 hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors cursor-pointer">
                            <div class="w-9 h-9 rounded-xl bg-spice-chili-100 dark:bg-spice-chili-900/30 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-certificate text-spice-chili-600 dark:text-spice-chili-400 text-xs"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-[12px] font-medium text-spice-dark dark:text-white"><strong>FSSAI</strong> certificate expiring in 15 days</p>
                                <p class="text-[11px] text-gray-400 dark:text-slate-500 mt-0.5">3 hours ago</p>
                            </div>
                        </div>
                    </div>
                    <div class="px-5 py-3 border-t border-gray-100 dark:border-slate-700 text-center">
                        <a href="#" class="text-[12px] font-semibold text-spice-green-600 hover:text-spice-green-700 transition-colors">View All Notifications</a>
                    </div>
                </div>
            </div>

            <!-- Divider -->
            <div class="hidden sm:block w-px h-8 bg-gray-200 dark:bg-slate-600 mx-1"></div>            <!-- Admin Profile -->
            <div class="relative">
                <button onclick="toggleProfile()" class="hidden sm:flex items-center gap-3 px-3 py-2 rounded-xl hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-spice-green-600 to-spice-green-700 flex items-center justify-center text-white font-bold text-[12px]">
                        <?php 
                        $admin_name = isset($_SESSION['admin_user_name']) ? $_SESSION['admin_user_name'] : 'Vision Exim';
                        $initials = '';
                        $words = explode(' ', $admin_name);
                        foreach ($words as $w) {
                            $initials .= strtoupper(substr($w, 0, 1));
                        }
                        echo htmlspecialchars(substr($initials, 0, 2));
                        ?>
                    </div>
                    <div class="hidden lg:block text-left">
                        <p class="text-[12px] font-semibold text-spice-dark dark:text-white leading-tight"><?= htmlspecialchars($admin_name) ?></p>
                        <p class="text-[10px] text-gray-400 dark:text-slate-500">Super Admin</p>
                    </div>
                    <i class="fas fa-chevron-down text-[9px] text-gray-400 dark:text-slate-500 hidden lg:block"></i>
                </button>

                <!-- Profile Dropdown -->
                <div id="profileDropdown" class="absolute right-0 top-[calc(100%+8px)] w-[220px] bg-white dark:bg-slate-800 rounded-2xl shadow-dropdown border border-gray-100 dark:border-slate-700 hidden z-50">
                    <div class="px-4 py-3 border-b border-gray-100 dark:border-slate-700">
                        <p class="text-[13px] font-semibold text-spice-dark dark:text-white"><?= htmlspecialchars($admin_name) ?></p>
                        <p class="text-[11px] text-gray-400 dark:text-slate-500"><?= htmlspecialchars(isset($_SESSION['admin_user_email']) ? $_SESSION['admin_user_email'] : 'admin@visionexim.com') ?></p>
                    </div>
                    <div class="py-2">
                        <a href="<?= htmlspecialchars(ve_url('admin/profile.php')) ?>" class="flex items-center gap-3 px-4 py-2.5 text-[12px] text-gray-600 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                            <i class="fas fa-user-circle text-gray-400 dark:text-slate-500 w-4"></i> My Profile
                        </a>
                        <!-- <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-[12px] text-gray-600 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                            <i class="fas fa-question-circle text-gray-400 dark:text-slate-500 w-4"></i> Help Center
                        </a> -->
                    </div>
                    <div class="border-t border-gray-100 dark:border-slate-700 py-2">
                        <a href="<?= htmlspecialchars(ve_url('admin/logout.php')) ?>" class="flex items-center gap-3 px-4 py-2.5 text-[12px] text-spice-chili-500 hover:bg-spice-chili-50 dark:hover:bg-spice-chili-900/20 transition-colors">
                            <i class="fas fa-right-from-bracket w-4"></i> Sign Out
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</header>
