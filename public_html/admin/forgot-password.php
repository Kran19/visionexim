<?php
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/../includes/config.php';

if (ve_is_admin_logged_in()) {
    header('Location: dashboard.php');
    exit;
}

require_once __DIR__ . '/includes/db.php';

$step       = 1; 
$error_msg  = '';
$success_msg = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['step']) && $_POST['step'] === '1') {
    $email = trim($_POST['email'] ?? '');
    if (empty($email)) {
        $error_msg = 'Please enter your email address.';
    } else {
        $stmt = $conn->prepare("SELECT id FROM admin_users WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows === 1) {
            $_SESSION['reset_email'] = $email;
            $step = 2;
        } else {
            $error_msg = 'No admin account found with that email address.';
        }
        $stmt->close();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['step']) && $_POST['step'] === '2') {
    $new_password  = trim($_POST['new_password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');
    $email = $_SESSION['reset_email'] ?? '';

    if (empty($new_password) || strlen($new_password) < 6) {
        $error_msg = 'Password must be at least 6 characters.';
        $step = 2;
    } elseif ($new_password !== $confirm_password) {
        $error_msg = 'Passwords do not match.';
        $step = 2;
    } elseif (empty($email)) {
        $error_msg = 'Session expired. Please start again.';
        $step = 1;
    } else {
        $hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE admin_users SET password = ? WHERE email = ?");
        $stmt->bind_param('ss', $hashed, $email);
        if ($stmt->execute()) {
            unset($_SESSION['reset_email']);
            $success_msg = 'Password updated successfully! You can now sign in.';
            $step = 1;
        } else {
            $error_msg = 'Failed to update password. Please try again.';
            $step = 2;
        }
        $stmt->close();
    }
}

// If session has reset_email, go to step 2
if (empty($_POST) && isset($_SESSION['reset_email'])) {
    $step = 2;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password — Vision Exim Admin</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        spice: {
                            green: { 600: '#1F4D3A', 700: '#1A3F30', 800: '#143226' },
                            turmeric: { 400: '#E8B94E', 500: '#D9A441' },
                            chili: { 500: '#B9412E' },
                            dark: '#1E1E1E',
                        }
                    },
                    fontFamily: { poppins: ['Poppins', 'sans-serif'] }
                }
            }
        }
    </script>
    <style>
        * { font-family: 'Poppins', sans-serif; }
        .login-bg {
            background: linear-gradient(135deg, #1F4D3A 0%, #143226 50%, #0E241B 100%);
        }
    </style>
</head>
<body>
    <div class="min-h-screen flex items-center justify-center login-bg p-6">
        <div class="w-full max-w-[420px] bg-white rounded-3xl shadow-2xl p-8">

            <!-- Header -->
            <div class="text-center mb-8">
                <div class="w-16 h-16 mx-auto rounded-2xl bg-spice-green-600 flex items-center justify-center mb-4">
                    <i class="fas fa-lock text-spice-turmeric-400 text-2xl"></i>
                </div>
                <h2 class="text-[24px] font-bold text-spice-dark">
                    <?= $step === 2 ? 'Set New Password' : 'Forgot Password' ?>
                </h2>
                <p class="text-gray-400 text-[13px] mt-1">
                    <?= $step === 2 ? 'Enter your new password below.' : 'Enter your admin email to reset your password.' ?>
                </p>
            </div>

            <!-- Success Message -->
            <?php if ($success_msg): ?>
            <div class="mb-5 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-[13px] font-medium flex items-center gap-2">
                <i class="fas fa-check-circle text-emerald-500"></i>
                <?= htmlspecialchars($success_msg) ?>
            </div>
            <?php endif; ?>

            <!-- Error Message -->
            <?php if ($error_msg): ?>
            <div class="mb-5 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-[13px] font-medium flex items-center gap-2">
                <i class="fas fa-exclamation-circle text-red-500"></i>
                <?= htmlspecialchars($error_msg) ?>
            </div>
            <?php endif; ?>

            <?php if ($step === 1): ?>
            <!-- Step 1: Email Form -->
            <form method="POST" action="" class="space-y-5">
                <input type="hidden" name="step" value="1">
                <div>
                    <label class="block text-[12px] font-semibold text-gray-500 uppercase tracking-wider mb-2">Admin Email Address</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-300">
                            <i class="fas fa-envelope text-sm"></i>
                        </span>
                        <input type="email" name="email" placeholder="Enter your admin email" required
                               class="w-full pl-12 pr-4 py-3.5 rounded-xl border-2 border-gray-100 bg-gray-50/50 text-[13px] text-spice-dark placeholder-gray-300 focus:border-spice-green-600 focus:bg-white focus:ring-4 focus:ring-spice-green-600/10 outline-none transition-all">
                    </div>
                </div>
                <button type="submit"
                        class="w-full py-3.5 rounded-xl bg-gradient-to-r from-spice-green-600 to-spice-green-700 text-white text-[14px] font-semibold hover:from-spice-green-700 hover:to-spice-green-800 transition-all shadow-lg shadow-spice-green-600/30">
                    <i class="fas fa-arrow-right mr-2"></i> Continue
                </button>
            </form>

            <?php else: ?>
            <!-- Step 2: New Password Form -->
            <form method="POST" action="" class="space-y-5">
                <input type="hidden" name="step" value="2">
                <div>
                    <label class="block text-[12px] font-semibold text-gray-500 uppercase tracking-wider mb-2">New Password</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-300">
                            <i class="fas fa-lock text-sm"></i>
                        </span>
                        <input type="password" name="new_password" id="newPass" placeholder="Min. 6 characters" required
                               class="w-full pl-12 pr-12 py-3.5 rounded-xl border-2 border-gray-100 bg-gray-50/50 text-[13px] text-spice-dark placeholder-gray-300 focus:border-spice-green-600 focus:bg-white focus:ring-4 focus:ring-spice-green-600/10 outline-none transition-all">
                        <button type="button" onclick="togglePass('newPass','eye1')" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-300 hover:text-gray-500">
                            <i class="fas fa-eye text-sm" id="eye1"></i>
                        </button>
                    </div>
                </div>
                <div>
                    <label class="block text-[12px] font-semibold text-gray-500 uppercase tracking-wider mb-2">Confirm New Password</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-300">
                            <i class="fas fa-lock text-sm"></i>
                        </span>
                        <input type="password" name="confirm_password" id="confirmPass" placeholder="Re-enter new password" required
                               class="w-full pl-12 pr-12 py-3.5 rounded-xl border-2 border-gray-100 bg-gray-50/50 text-[13px] text-spice-dark placeholder-gray-300 focus:border-spice-green-600 focus:bg-white focus:ring-4 focus:ring-spice-green-600/10 outline-none transition-all">
                        <button type="button" onclick="togglePass('confirmPass','eye2')" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-300 hover:text-gray-500">
                            <i class="fas fa-eye text-sm" id="eye2"></i>
                        </button>
                    </div>
                </div>
                <button type="submit"
                        class="w-full py-3.5 rounded-xl bg-gradient-to-r from-spice-green-600 to-spice-green-700 text-white text-[14px] font-semibold hover:from-spice-green-700 hover:to-spice-green-800 transition-all shadow-lg shadow-spice-green-600/30">
                    <i class="fas fa-save mr-2"></i> Update Password
                </button>
            </form>
            <?php endif; ?>

            <!-- Back to Login -->
            <div class="mt-6 text-center">
                <a href="index.php" class="text-[12px] font-semibold text-spice-green-600 hover:text-spice-green-700 transition-colors">
                    <i class="fas fa-arrow-left mr-1"></i> Back to Sign In
                </a>
            </div>

        </div>
    </div>

    <script>
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
</body>
</html>
