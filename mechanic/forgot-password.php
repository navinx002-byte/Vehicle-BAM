<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
$message = ''; $type = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($conn, $_POST['email']);
    $res = $conn->query("SELECT * FROM mechanics WHERE email='$email'");
    if ($res && $res->num_rows === 1) {
        $user = $res->fetch_assoc();
        $newPass = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 10);
        $hash    = password_hash($newPass, PASSWORD_DEFAULT);
        $conn->query("UPDATE mechanics SET password='$hash' WHERE id={$user['id']}");
        $name    = htmlspecialchars($user['full_name']);
        $body    = "
        <div style='font-family:sans-serif;max-width:500px;margin:auto;padding:30px;border:1px solid #eee;border-radius:10px;'>
          <h2 style='color:#10b981;'>AutoCare – Your Password</h2>
          <p>Hi <strong>{$name}</strong>,</p>
          <p>Your password has been reset. Use the password below to log in:</p>
          <div style='background:#f5f5f5;padding:16px;border-radius:8px;text-align:center;margin:20px 0;'>
            <span style='font-size:1.4rem;font-weight:bold;letter-spacing:2px;color:#333;'>{$newPass}</span>
          </div>
          <p style='color:#888;font-size:13px;'>Please change your password after logging in.</p>
          <a href='" . SITE_URL . "/mechanic/login.php' style='display:inline-block;background:#10b981;color:white;padding:10px 24px;border-radius:6px;text-decoration:none;'>Go to Login</a>
        </div>";
        if (sendMail($email, 'Your AutoCare Password', $body)) {
            $message = "Your password has been sent to your email!"; $type = 'success';
        } else {
            $message = "Failed to send email. Please contact admin."; $type = 'error';
        }
    } else {
        $message = "No mechanic account found with that email."; $type = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Forgot Password - <?= SITE_NAME ?></title>
<link rel="stylesheet" href="../assets/css/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script>const t=localStorage.getItem('vs_theme')||'light';document.documentElement.setAttribute('data-theme',t);</script>
</head>
<body>
<div class="auth-page">
  <div class="auth-card">
    <div class="auth-logo">
      <div class="logo-circle" style="background:linear-gradient(135deg,#10b981,#059669);"><i class="fas fa-key"></i></div>
      <h2>Forgot Password</h2>
      <p>Enter your email — we'll send your password</p>
    </div>
    <?php if ($message): ?>
      <div class="alert alert-<?= $type ?>" style="margin-bottom:16px;">
        <i class="fas fa-<?= $type==='success'?'check':'exclamation' ?>-circle"></i> <?= $message ?>
      </div>
    <?php endif; ?>
    <form method="POST">
      <div class="form-group">
        <label>Email Address</label>
        <div class="input-icon">
          <i class="fas fa-envelope"></i>
          <input type="email" name="email" class="form-control" placeholder="mechanic@example.com" required>
        </div>
      </div>
      <button type="submit" class="btn btn-success" style="width:100%;justify-content:center;border-radius:10px;padding:14px;">
        <i class="fas fa-paper-plane"></i> Send My Password
      </button>
    </form>
    <div class="auth-footer" style="margin-top:15px;">
      <a href="login.php"><i class="fas fa-arrow-left"></i> Back to Login</a>
    </div>
  </div>
</div>
<script src="../assets/js/main.js"></script>
</body>
</html>