<?php
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/../includes/config.php';

// If already logged in, redirect directly to dashboard
if (ve_is_admin_logged_in()) {
    header('Location: dashboard.php');
    exit;
}

$error_msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/includes/db.php';
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($email) || empty($password)) {
        $error_msg = 'Please enter both email and password.';
    } else {
        $stmt = $conn->prepare("SELECT * FROM admin_users WHERE email = ? LIMIT 1");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                // Prevent session fixation
                ve_regenerate_session();

                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_user_id']   = (int)$user['id'];
                $_SESSION['admin_user_name'] = $user['name'];
                $_SESSION['admin_user_email'] = $user['email'];

                // Handle Remember Me cookie
                $isHttps = (!empty($_SERVER['HTTPS']) && strtolower((string)$_SERVER['HTTPS']) !== 'off')
                    || (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443)
                    || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower((string)$_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https');

                if (isset($_POST['remember'])) {
                    if (PHP_VERSION_ID >= 70300) {
                        setcookie('remember_admin', $email, [
                            'expires'  => time() + (86400 * 30),
                            'path'     => '/',
                            'secure'   => $isHttps,
                            'httponly' => true,
                            'samesite' => 'Lax'
                        ]);
                    } else {
                        setcookie('remember_admin', $email, time() + (86400 * 30), '/; samesite=Lax', '', $isHttps, true);
                    }
                } else {
                    setcookie('remember_admin', '', time() - 3600, '/');
                }

                header('Location: dashboard.php');
                exit;
            }
        }
        $error_msg = 'Invalid email address or password.';
        if (isset($stmt)) {
            $stmt->close();
        }
    }
}

// Check for remember me cookie
$saved_email = isset($_COOKIE['remember_admin']) ? htmlspecialchars($_COOKIE['remember_admin'], ENT_QUOTES, 'UTF-8') : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Vision Exim Admin</title>
    <meta name="robots" content="noindex, nofollow">

    <link rel="icon" type="image/png" href="../images/favicons/favicon-96x96.png" sizes="96x96" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
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
                            cream: '#F7F3EB',
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
            position: relative;
            overflow: hidden;
        }
        .login-bg::before {
            content: '';
            position: absolute;
            width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(217,164,65,0.08) 0%, transparent 70%);
            top: -200px; right: -200px;
            border-radius: 50%;
        }
        .login-bg::after {
            content: '';
            position: absolute;
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(185,65,46,0.06) 0%, transparent 70%);
            bottom: -150px; left: -100px;
            border-radius: 50%;
        }

        .float-animation {
            animation: floatUpDown 6s ease-in-out infinite;
        }
        @keyframes floatUpDown {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
        }

        .spice-pattern {
            background-image: 
                radial-gradient(circle at 20% 80%, rgba(217,164,65,0.05) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(185,65,46,0.04) 0%, transparent 50%),
                radial-gradient(circle at 50% 50%, rgba(255,255,255,0.02) 0%, transparent 50%);
        }
    </style>
</head>

<body class="font-poppins">
    <div class="min-h-screen flex login-bg">
        
        <!-- Left Side - Branding -->
        <div class="hidden lg:flex lg:w-1/2 flex-col justify-center items-center px-16 relative spice-pattern">
            
            <!-- Decorative Elements -->
            <div class="absolute top-10 left-10 w-20 h-20 border border-white/5 rounded-3xl rotate-12"></div>
            <div class="absolute bottom-20 right-16 w-16 h-16 border border-spice-turmeric-500/10 rounded-2xl -rotate-12"></div>
            <div class="absolute top-1/4 right-20 w-3 h-3 bg-spice-turmeric-500/20 rounded-full"></div>
            <div class="absolute bottom-1/3 left-20 w-2 h-2 bg-spice-chili-500/20 rounded-full"></div>

            <div class="relative z-10 max-w-md text-center">
                <!-- Logo -->
                <div class="float-animation mb-10">
                    <div class="w-24 h-24 mx-auto rounded-3xl bg-white/10 backdrop-blur-md border border-white/10 flex items-center justify-center shadow-2xl">
                        <i class="fas fa-seedling text-spice-turmeric-400 text-4xl"></i>
                    </div>
                </div>

                <h1 class="text-white text-4xl font-bold mb-4 leading-tight">
                    Vision <span class="text-spice-turmeric-400">Exim</span>
                </h1>
                <p class="text-white/50 text-[15px] leading-relaxed mb-10">
                    Premium Spice Export Management System.<br>
                    Manage your global spice export business with elegance.
                </p>

                <!-- Feature Pills -->
                <!-- <div class="flex flex-wrap justify-center gap-3">
                    <div class="flex items-center gap-2 px-4 py-2 rounded-full bg-white/5 border border-white/10">
                        <i class="fas fa-globe-americas text-spice-turmeric-400 text-xs"></i>
                        <span class="text-white/60 text-[11px] font-medium">Global Export</span>
                    </div>
                    <div class="flex items-center gap-2 px-4 py-2 rounded-full bg-white/5 border border-white/10">
                        <i class="fas fa-chart-pie text-emerald-400 text-xs"></i>
                        <span class="text-white/60 text-[11px] font-medium">Analytics</span>
                    </div>
                    <div class="flex items-center gap-2 px-4 py-2 rounded-full bg-white/5 border border-white/10">
                        <i class="fas fa-shield-halved text-spice-chili-500 text-xs"></i>
                        <span class="text-white/60 text-[11px] font-medium">Secure</span>
                    </div>
                </div> -->

                <!-- Trust Indicators -->
                <!-- <div class="mt-14 flex items-center justify-center gap-8">
                    <div class="text-center">
                        <p class="text-white text-2xl font-bold">50+</p>
                        <p class="text-white/30 text-[10px] uppercase tracking-wider">Products</p>
                    </div>
                    <div class="w-px h-10 bg-white/10"></div>
                    <div class="text-center">
                        <p class="text-white text-2xl font-bold">20+</p>
                        <p class="text-white/30 text-[10px] uppercase tracking-wider">Countries</p>
                    </div>
                    <div class="w-px h-10 bg-white/10"></div>
                    <div class="text-center">
                        <p class="text-white text-2xl font-bold">100%</p>
                        <p class="text-white/30 text-[10px] uppercase tracking-wider">Organic</p>
                    </div>
                </div> -->
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-10 bg-white lg:rounded-l-[40px] relative z-10">
            <div class="w-full max-w-[420px]">
                
                <!-- Mobile Logo -->
                <div class="lg:hidden text-center mb-8">
                    <div class="w-16 h-16 mx-auto rounded-2xl bg-spice-green-600 flex items-center justify-center mb-4">
                        <i class="fas fa-seedling text-spice-turmeric-400 text-2xl"></i>
                    </div>
                    <h1 class="text-spice-dark text-2xl font-bold">Vision <span class="text-spice-green-600">Exim</span></h1>
                </div>

                <!-- Form Header -->
                <div class="mb-8">
                    <h2 class="text-[26px] font-bold text-spice-dark mb-2">Welcome Back</h2>
                    <p class="text-gray-400 text-[14px]">Sign in to your admin dashboard</p>
                </div>

                <!-- Error Message -->
                <?php if ($error_msg): ?>
                <div class="mb-5 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-[13px] font-medium flex items-center gap-2">
                    <i class="fas fa-exclamation-circle text-red-500"></i>
                    <?= htmlspecialchars($error_msg) ?>
                </div>
                <?php endif; ?>

                <!-- Login Form -->
                <form class="space-y-5" method="POST" action="">
                    
                    <!-- Email -->
                    <div>
                        <label class="block text-[12px] font-semibold text-gray-500 uppercase tracking-wider mb-2">Email Address</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-300">
                                <i class="fas fa-envelope text-sm"></i>
                            </span>
                            <input type="email" name="email" value="<?= $saved_email ?>" placeholder="Enter your email" required
                                   class="w-full pl-12 pr-4 py-3.5 rounded-xl border-2 border-gray-100 bg-gray-50/50 text-[13px] text-spice-dark placeholder-gray-300 focus:border-spice-green-600 focus:bg-white focus:ring-4 focus:ring-spice-green-600/10 outline-none transition-all">
                        </div>
                    </div>

                    <!-- Password -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-[12px] font-semibold text-gray-500 uppercase tracking-wider">Password</label>
                            <a href="forgot-password.php" class="text-[11px] font-semibold text-spice-green-600 hover:text-spice-green-700 transition-colors">Forgot Password?</a>
                        </div>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-300">
                                <i class="fas fa-lock text-sm"></i>
                            </span>
                            <input type="password" name="password" value="" placeholder="Enter your password" required
                                   class="w-full pl-12 pr-12 py-3.5 rounded-xl border-2 border-gray-100 bg-gray-50/50 text-[13px] text-spice-dark placeholder-gray-300 focus:border-spice-green-600 focus:bg-white focus:ring-4 focus:ring-spice-green-600/10 outline-none transition-all"
                                   id="passwordInput">
                            <button type="button" onclick="togglePassword()" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-300 hover:text-gray-500 transition-colors">
                                <i class="fas fa-eye text-sm" id="eyeIcon"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2.5 cursor-pointer group">
                            <input type="checkbox" name="remember" checked class="w-4 h-4 rounded border-gray-200 text-spice-green-600 focus:ring-spice-green-600/30">
                            <span class="text-[12px] text-gray-400 group-hover:text-gray-500 transition-colors">Remember me</span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" 
                            class="w-full py-3.5 rounded-xl bg-gradient-to-r from-spice-green-600 to-spice-green-700 text-white text-[14px] font-semibold hover:from-spice-green-700 hover:to-spice-green-800 transform hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200 shadow-lg shadow-spice-green-600/30 hover:shadow-xl hover:shadow-spice-green-600/40">
                        <i class="fas fa-arrow-right-to-bracket mr-2"></i>
                        Sign In
                    </button>
                </form>

                <!-- Footer -->
                <div class="mt-10 text-center">
                    <p class="text-gray-300 text-[11px]">
                        Secured by <strong class="text-gray-400">Vision Exim</strong> • © <?php echo date('Y'); ?> All Rights Reserved
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('passwordInput');
            const icon = document.getElementById('eyeIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
