<?php
/**
 * Vision Exim — Admin Password Reset Utility
 * 
 * SECURITY NOTICE: Use this utility only when locked out of the admin panel.
 * Once your password is reset, delete or disable this file for production safety.
 */

require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/db.php';

$message = '';
$error = '';
$default_email = 'admin@visionexim.com';
$new_pass_default = 'admin123';

// Require key or POST action for safety
$auth_key = 'vision_reset_2026';
$provided_key = $_GET['key'] ?? ($_POST['key'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['do_reset'])) {
    $target_email = trim($_POST['email'] ?? $default_email);
    $new_password = trim($_POST['new_password'] ?? $new_pass_default);

    if (empty($new_password)) {
        $error = 'Password cannot be empty.';
    } else {
        $hashed = password_hash($new_password, PASSWORD_DEFAULT);
        
        // Check if admin user exists
        $chk = $conn->prepare("SELECT id FROM admin_users WHERE email = ?");
        $chk->bind_param('s', $target_email);
        $chk->execute();
        $chk->store_result();

        if ($chk->num_rows > 0) {
            $stmt = $conn->prepare("UPDATE admin_users SET password = ? WHERE email = ?");
            $stmt->bind_param('ss', $hashed, $target_email);
            if ($stmt->execute()) {
                $message = "Password successfully reset for <strong>" . htmlspecialchars($target_email) . "</strong> to <strong>" . htmlspecialchars($new_password) . "</strong>.";
            } else {
                $error = "Failed to update password: " . $conn->error;
            }
            $stmt->close();
        } else {
            // Update all if no specific match, or insert default admin
            $stmt = $conn->prepare("UPDATE admin_users SET password = ?");
            $stmt->bind_param('s', $hashed);
            if ($stmt->execute() && $stmt->affected_rows > 0) {
                $message = "Password successfully reset for existing admin user(s) to <strong>" . htmlspecialchars($new_password) . "</strong>.";
            } else {
                // If table was empty, create default admin
                $stmt_ins = $conn->prepare("INSERT INTO admin_users (name, email, password, role) VALUES ('Admin', ?, ?, 'admin')");
                $stmt_ins->bind_param('ss', $target_email, $hashed);
                if ($stmt_ins->execute()) {
                    $message = "New admin account created: <strong>" . htmlspecialchars($target_email) . "</strong> with password <strong>" . htmlspecialchars($new_password) . "</strong>.";
                } else {
                    $error = "Failed to create admin: " . $conn->error;
                }
                $stmt_ins->close();
            }
            $stmt->close();
        }
        $chk->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Admin Password — Vision Exim</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #0E241B; color: #fff; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; }
        .card { background: #1F4D3A; border: 1px solid rgba(255,255,255,0.1); border-radius: 16px; padding: 32px; max-width: 440px; width: 100%; box-shadow: 0 20px 40px rgba(0,0,0,0.4); }
        h2 { margin-top: 0; color: #E8B94E; font-size: 22px; }
        p { color: #d1d5db; font-size: 14px; line-height: 1.5; }
        .alert-success { background: #14532d; border: 1px solid #22c55e; padding: 12px; border-radius: 8px; color: #86efac; margin-bottom: 20px; font-size: 13px; }
        .alert-error { background: #7f1d1d; border: 1px solid #ef4444; padding: 12px; border-radius: 8px; color: #fca5a5; margin-bottom: 20px; font-size: 13px; }
        label { display: block; font-size: 12px; text-transform: uppercase; font-weight: 600; color: #9ca3af; margin-bottom: 6px; }
        input[type="text"], input[type="password"] { width: 100%; box-sizing: border-box; padding: 10px 14px; border-radius: 8px; border: 1px solid #374151; background: #111827; color: #fff; font-size: 14px; margin-bottom: 16px; }
        button { width: 100%; padding: 12px; border: none; border-radius: 8px; background: #E8B94E; color: #1F4D3A; font-weight: bold; font-size: 14px; cursor: pointer; transition: background 0.2s; }
        button:hover { background: #d9a441; }
        .btn-link { display: inline-block; margin-top: 16px; color: #E8B94E; text-decoration: none; font-size: 13px; }
        .notice { font-size: 11px; color: #9ca3af; margin-top: 20px; line-height: 1.4; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 12px; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Vision Exim Password Reset</h2>
        <p>Use this form to safely reset or confirm your administrative login credentials.</p>

        <?php if (!empty($message)): ?>
            <div class="alert-success"><?= $message ?></div>
            <a href="index.php" class="btn-link">← Return to Admin Login</a>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="do_reset" value="1">
            <label>Admin Email</label>
            <input type="text" name="email" value="<?= htmlspecialchars($_POST['email'] ?? 'admin@visionexim.com') ?>" required>

            <label>New Password</label>
            <input type="text" name="new_password" value="<?= htmlspecialchars($_POST['new_password'] ?? 'admin123') ?>" required>

            <button type="submit">Reset Password Now</button>
        </form>

        <p class="notice">
            🔒 <strong>Production Security Reminder:</strong> After resetting your password, you can delete or rename <code>admin/reset_admin.php</code> to maintain strict access control.
        </p>
    </div>
</body>
</html>
