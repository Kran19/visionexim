<?php
/**
 * Vision Exim — Admin Profile Page
 */
include 'includes/auth.php';
include 'includes/db.php';

$page_title      = 'My Profile';
$current_module  = 'profile';

$admin_id = $_SESSION['admin_user_id'] ?? 0;

// Fetch current admin data
$stmt = $conn->prepare("SELECT * FROM admin_users WHERE id = ?");
$stmt->bind_param('i', $admin_id);
$stmt->execute();
$admin = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$admin) {
    header('Location: index.php');
    exit;
}

$success_msg  = '';
$error_msg    = '';
$active_tab   = $_POST['active_tab'] ?? 'profile';

// ── Handle Profile Update ──────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $active_tab = 'profile';
    $name  = trim($_POST['name']  ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    if (empty($name) || empty($email)) {
        $error_msg = 'Name and email are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_msg = 'Please enter a valid email address.';
    } else {
        // Check email not taken by another admin
        $chk = $conn->prepare("SELECT id FROM admin_users WHERE email = ? AND id != ?");
        $chk->bind_param('si', $email, $admin_id);
        $chk->execute();
        $chk->store_result();
        if ($chk->num_rows > 0) {
            $error_msg = 'This email is already used by another account.';
        } else {
            // Check if phone column exists
            $cols = [];
            $cr = $conn->query("DESCRIBE admin_users");
            while ($c = $cr->fetch_assoc()) $cols[] = $c['Field'];

            if (in_array('phone', $cols)) {
                $upd = $conn->prepare("UPDATE admin_users SET name=?, email=?, phone=? WHERE id=?");
                $upd->bind_param('sssi', $name, $email, $phone, $admin_id);
            } else {
                $upd = $conn->prepare("UPDATE admin_users SET name=?, email=? WHERE id=?");
                $upd->bind_param('ssi', $name, $email, $admin_id);
            }

            if ($upd->execute()) {
                $_SESSION['admin_user_name']  = $name;
                $_SESSION['admin_user_email'] = $email;
                $admin['name']  = $name;
                $admin['email'] = $email;
                $admin['phone'] = $phone;
                $success_msg = 'Profile updated successfully!';
            } else {
                $error_msg = 'Failed to update profile: ' . $conn->error;
            }
            $upd->close();
        }
        $chk->close();
    }
}

// ── Handle Password Change ─────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $active_tab      = 'password';
    $current_pass    = trim($_POST['current_password']  ?? '');
    $new_pass        = trim($_POST['new_password']       ?? '');
    $confirm_pass    = trim($_POST['confirm_password']   ?? '');

    if (empty($current_pass) || empty($new_pass) || empty($confirm_pass)) {
        $error_msg = 'All password fields are required.';
    } elseif (!password_verify($current_pass, $admin['password'])) {
        $error_msg = 'Current password is incorrect.';
    } elseif (strlen($new_pass) < 6) {
        $error_msg = 'New password must be at least 6 characters.';
    } elseif ($new_pass !== $confirm_pass) {
        $error_msg = 'New passwords do not match.';
    } else {
        $hashed = password_hash($new_pass, PASSWORD_DEFAULT);
        $upd = $conn->prepare("UPDATE admin_users SET password=? WHERE id=?");
        $upd->bind_param('si', $hashed, $admin_id);
        if ($upd->execute()) {
            $success_msg = 'Password changed successfully!';
        } else {
            $error_msg = 'Failed to change password.';
        }
        $upd->close();
    }
}

include 'includes/header.php';
include 'includes/sidebar.php';
include 'includes/navbar.php';
?>

<main class="lg:ml-[270px] pt-[70px] min-h-screen sidebar-transition">
    <div class="p-6 lg:p-8 page-content">

        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div>
                <div class="flex items-center gap-2 text-[12px] text-gray-400 dark:text-slate-500 mb-1">
                    <a href="dashboard.php" class="hover:text-spice-green-600 transition-colors">Dashboard</a>
                    <span>/</span>
                    <span class="text-gray-600 dark:text-slate-400 font-medium">My Profile</span>
                </div>
                <h1 class="text-[22px] font-bold text-spice-dark dark:text-white">My Profile</h1>
                <p class="text-[13px] text-gray-400 dark:text-slate-500 mt-1">Manage your account details and password</p>
            </div>
        </div>

        <!-- Flash Messages -->
        <?php if ($success_msg): ?>
        <div class="mb-6 flex items-center gap-3 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 text-[13px] font-medium">
            <i class="fas fa-check-circle text-emerald-500"></i> <?= htmlspecialchars($success_msg) ?>
            <button onclick="this.parentElement.remove()" class="ml-auto text-emerald-400 hover:text-emerald-600"><i class="fas fa-times"></i></button>
        </div>
        <?php endif; ?>
        <?php if ($error_msg): ?>
        <div class="mb-6 flex items-center gap-3 p-4 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 text-[13px] font-medium">
            <i class="fas fa-exclamation-circle text-red-500"></i> <?= htmlspecialchars($error_msg) ?>
            <button onclick="this.parentElement.remove()" class="ml-auto text-red-400 hover:text-red-600"><i class="fas fa-times"></i></button>
        </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- Left: Avatar Card -->
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-card border border-gray-100/50 dark:border-slate-700 p-6 text-center">
                    <!-- Avatar -->
                    <div class="w-24 h-24 rounded-full bg-gradient-to-br from-spice-green-600 to-spice-green-800 flex items-center justify-center mx-auto mb-4 shadow-lg">
                        <span class="text-white text-3xl font-bold">
                            <?= strtoupper(substr($admin['name'] ?? 'A', 0, 1)) ?>
                        </span>
                    </div>
                    <h3 class="text-[16px] font-bold text-spice-dark dark:text-white"><?= htmlspecialchars($admin['name']) ?></h3>
                    <p class="text-[12px] text-gray-400 dark:text-slate-500 mt-1"><?= htmlspecialchars($admin['email']) ?></p>
                    <span class="inline-flex items-center gap-1.5 mt-3 px-3 py-1 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 text-[11px] font-bold">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Super Admin
                    </span>

                    <div class="mt-6 pt-6 border-t border-gray-100 dark:border-slate-700 space-y-3 text-left">
                        <div class="flex items-center gap-3 text-[12px] text-gray-500 dark:text-slate-400">
                            <i class="fas fa-envelope w-4 text-center text-spice-green-600"></i>
                            <span class="truncate"><?= htmlspecialchars($admin['email']) ?></span>
                        </div>
                        <?php if (!empty($admin['phone'])): ?>
                        <div class="flex items-center gap-3 text-[12px] text-gray-500 dark:text-slate-400">
                            <i class="fas fa-phone w-4 text-center text-spice-green-600"></i>
                            <span><?= htmlspecialchars($admin['phone']) ?></span>
                        </div>
                        <?php endif; ?>
                        <div class="flex items-center gap-3 text-[12px] text-gray-500 dark:text-slate-400">
                            <i class="fas fa-calendar w-4 text-center text-spice-green-600"></i>
                            <span>Joined <?= !empty($admin['created_at']) ? date('M Y', strtotime($admin['created_at'])) : 'N/A' ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Tabs -->
            <div class="lg:col-span-2">

                <!-- Tab Buttons -->
                <div class="flex gap-2 mb-6">
                    <button onclick="switchTab('profile')" id="tab-profile"
                            class="tab-btn px-5 py-2.5 rounded-xl text-[12px] font-semibold transition-all <?= $active_tab === 'profile' ? 'bg-spice-green-600 text-white shadow-lg shadow-spice-green-600/25' : 'bg-white dark:bg-slate-800 text-gray-500 dark:text-slate-400 border border-gray-100 dark:border-slate-700 hover:border-spice-green-600/30' ?>">
                        <i class="fas fa-user mr-2"></i> Profile Info
                    </button>
                    <button onclick="switchTab('password')" id="tab-password"
                            class="tab-btn px-5 py-2.5 rounded-xl text-[12px] font-semibold transition-all <?= $active_tab === 'password' ? 'bg-spice-green-600 text-white shadow-lg shadow-spice-green-600/25' : 'bg-white dark:bg-slate-800 text-gray-500 dark:text-slate-400 border border-gray-100 dark:border-slate-700 hover:border-spice-green-600/30' ?>">
                        <i class="fas fa-lock mr-2"></i> Change Password
                    </button>
                </div>

                <!-- Profile Info Tab -->
                <div id="panel-profile" class="<?= $active_tab !== 'profile' ? 'hidden' : '' ?>">
                    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-card border border-gray-100/50 dark:border-slate-700 p-6">
                        <div class="flex items-center gap-3 border-b border-gray-100 dark:border-slate-700 pb-4 mb-6">
                            <div class="w-8 h-8 rounded-lg bg-spice-green-600/10 flex items-center justify-center text-spice-green-600">
                                <i class="fas fa-user text-sm"></i>
                            </div>
                            <h2 class="text-[14px] font-bold text-spice-dark dark:text-white">Profile Information</h2>
                        </div>
                        <form method="POST" action="" class="space-y-5">
                            <input type="hidden" name="active_tab" value="profile">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div class="sm:col-span-2">
                                    <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-2">Full Name <span class="text-red-500">*</span></label>
                                    <input type="text" name="name" value="<?= htmlspecialchars($admin['name']) ?>" required
                                           class="w-full px-4 py-3 rounded-xl border-2 border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-700/50 text-[12px] text-spice-dark dark:text-slate-200 focus:border-spice-green-600 focus:bg-white dark:focus:bg-slate-700 focus:ring-4 focus:ring-spice-green-600/10 outline-none transition-all">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-2">Email Address <span class="text-red-500">*</span></label>
                                    <input type="email" name="email" value="<?= htmlspecialchars($admin['email']) ?>" required
                                           class="w-full px-4 py-3 rounded-xl border-2 border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-700/50 text-[12px] text-spice-dark dark:text-slate-200 focus:border-spice-green-600 focus:bg-white dark:focus:bg-slate-700 focus:ring-4 focus:ring-spice-green-600/10 outline-none transition-all">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-2">Phone Number</label>
                                    <input type="text" name="phone" value="<?= htmlspecialchars($admin['phone'] ?? '') ?>" placeholder="+91 00000 00000"
                                           class="w-full px-4 py-3 rounded-xl border-2 border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-700/50 text-[12px] text-spice-dark dark:text-slate-200 focus:border-spice-green-600 focus:bg-white dark:focus:bg-slate-700 focus:ring-4 focus:ring-spice-green-600/10 outline-none transition-all">
                                </div>
                            </div>
                            <div class="pt-2">
                                <button type="submit" name="update_profile"
                                        class="px-6 py-3 rounded-xl bg-gradient-to-r from-spice-green-600 to-spice-green-700 text-white text-[12px] font-semibold hover:from-spice-green-700 hover:to-spice-green-800 transition-all shadow-lg shadow-spice-green-600/20">
                                    <i class="fas fa-save mr-1.5"></i> Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Change Password Tab -->
                <div id="panel-password" class="<?= $active_tab !== 'password' ? 'hidden' : '' ?>">
                    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-card border border-gray-100/50 dark:border-slate-700 p-6">
                        <div class="flex items-center gap-3 border-b border-gray-100 dark:border-slate-700 pb-4 mb-6">
                            <div class="w-8 h-8 rounded-lg bg-spice-green-600/10 flex items-center justify-center text-spice-green-600">
                                <i class="fas fa-lock text-sm"></i>
                            </div>
                            <h2 class="text-[14px] font-bold text-spice-dark dark:text-white">Change Password</h2>
                        </div>
                        <form method="POST" action="" class="space-y-5">
                            <input type="hidden" name="active_tab" value="password">
                            <div>
                                <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-2">Current Password <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input type="password" name="current_password" id="curPass" placeholder="Enter current password" required
                                           class="w-full px-4 pr-12 py-3 rounded-xl border-2 border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-700/50 text-[12px] text-spice-dark dark:text-slate-200 focus:border-spice-green-600 focus:bg-white dark:focus:bg-slate-700 outline-none transition-all">
                                    <button type="button" onclick="togglePass('curPass','eye0')" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-300 hover:text-gray-500">
                                        <i class="fas fa-eye text-sm" id="eye0"></i>
                                    </button>
                                </div>
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-2">New Password <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input type="password" name="new_password" id="newPass" placeholder="Min. 6 characters" required
                                           class="w-full px-4 pr-12 py-3 rounded-xl border-2 border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-700/50 text-[12px] text-spice-dark dark:text-slate-200 focus:border-spice-green-600 focus:bg-white dark:focus:bg-slate-700 outline-none transition-all">
                                    <button type="button" onclick="togglePass('newPass','eye1')" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-300 hover:text-gray-500">
                                        <i class="fas fa-eye text-sm" id="eye1"></i>
                                    </button>
                                </div>
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-2">Confirm New Password <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input type="password" name="confirm_password" id="conPass" placeholder="Re-enter new password" required
                                           class="w-full px-4 pr-12 py-3 rounded-xl border-2 border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-700/50 text-[12px] text-spice-dark dark:text-slate-200 focus:border-spice-green-600 focus:bg-white dark:focus:bg-slate-700 outline-none transition-all">
                                    <button type="button" onclick="togglePass('conPass','eye2')" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-300 hover:text-gray-500">
                                        <i class="fas fa-eye text-sm" id="eye2"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="pt-2">
                                <button type="submit" name="change_password"
                                        class="px-6 py-3 rounded-xl bg-gradient-to-r from-spice-green-600 to-spice-green-700 text-white text-[12px] font-semibold hover:from-spice-green-700 hover:to-spice-green-800 transition-all shadow-lg shadow-spice-green-600/20">
                                    <i class="fas fa-key mr-1.5"></i> Update Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>

    </div>
</main>

<script>
function switchTab(tab) {
    ['profile','password'].forEach(function(t) {
        document.getElementById('panel-' + t).classList.add('hidden');
        document.getElementById('tab-' + t).className = 'tab-btn px-5 py-2.5 rounded-xl text-[12px] font-semibold transition-all bg-white dark:bg-slate-800 text-gray-500 dark:text-slate-400 border border-gray-100 dark:border-slate-700 hover:border-spice-green-600/30';
    });
    document.getElementById('panel-' + tab).classList.remove('hidden');
    document.getElementById('tab-' + tab).className = 'tab-btn px-5 py-2.5 rounded-xl text-[12px] font-semibold transition-all bg-spice-green-600 text-white shadow-lg shadow-spice-green-600/25';
}

function togglePass(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon  = document.getElementById(iconId);
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}
</script>

<?php include 'includes/footer.php'; ?>
