<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
redirectIfNotLoggedIn('mechanic', 'login.php');
$mid = $_SESSION['mechanic_id'];
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mech = $conn->query("SELECT * FROM mechanics WHERE id=$mid")->fetch_assoc();
    $old  = $_POST['old_password'];
    $new  = $_POST['new_password'];
    $cnf  = $_POST['confirm_password'];
    if (!password_verify($old, $mech['password'])) { $error = "Current password is incorrect!"; }
    elseif ($new !== $cnf) { $error = "New passwords do not match!"; }
    elseif (strlen($new) < 6) { $error = "Password must be at least 6 characters!"; }
    else {
        $h = password_hash($new, PASSWORD_DEFAULT);
        $conn->query("UPDATE mechanics SET password='$h' WHERE id=$mid");
        setFlash('success', 'Password changed successfully!');
        header("Location: change-password.php"); exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Change Password - <?= SITE_NAME ?></title>
<link rel="stylesheet" href="../assets/css/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script>const t=localStorage.getItem('vs_theme')||'light';document.documentElement.setAttribute('data-theme',t);</script>
</head>
<body class="dashboard-layout">
<?php include 'sidebar.php'; ?>
<div class="overlay" onclick="toggleSidebar()"></div>
<div class="main-content">
  <header class="topbar">
    <div class="topbar-left">
      <button class="mobile-menu-btn" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
      <span class="topbar-title">Change Password</span>
    </div>
    <div class="topbar-right"><button class="theme-toggle" onclick="toggleTheme()"><i class="fas fa-moon theme-icon"></i></button></div>
  </header>
  <div class="page-content">
    <?php showFlash(); ?>
    <div style="max-width:480px;">
      <div class="form-card">
        <div class="form-card-header">
          <div class="icon" style="background:rgba(16,185,129,0.1);color:#10b981;"><i class="fas fa-key"></i></div>
          <h3>Change Password</h3>
        </div>
        <?php if($error): ?>
          <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div>
        <?php endif; ?>
        <form method="POST">
          <div class="form-group">
            <label>Current Password</label>
            <div class="input-icon"><i class="fas fa-lock"></i>
              <input type="password" name="old_password" id="p1" class="form-control" required>
              <button type="button" class="toggle-password" onclick="togglePassword('p1',this)"><i class="fas fa-eye"></i></button>
            </div>
          </div>
          <div class="form-group">
            <label>New Password</label>
            <div class="input-icon"><i class="fas fa-lock"></i>
              <input type="password" name="new_password" id="p2" class="form-control" required>
              <button type="button" class="toggle-password" onclick="togglePassword('p2',this)"><i class="fas fa-eye"></i></button>
            </div>
          </div>
          <div class="form-group">
            <label>Confirm New Password</label>
            <div class="input-icon"><i class="fas fa-lock"></i>
              <input type="password" name="confirm_password" id="p3" class="form-control" required>
              <button type="button" class="toggle-password" onclick="togglePassword('p3',this)"><i class="fas fa-eye"></i></button>
            </div>
          </div>
          <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Update Password</button>
        </form>
      </div>
    </div>
  </div>
</div>
<script src="../assets/js/main.js"></script>
</body>
</html>
