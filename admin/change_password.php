<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
if (!isLoggedIn('admin')) redirect('login.php');
$aid = $_SESSION['admin_id'];
$msg = '';
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $admin = $conn->query("SELECT * FROM admin WHERE id=$aid")->fetch_assoc();
    $old = $_POST['old_password'];
    $new = $_POST['new_password'];
    $cnf = $_POST['confirm_password'];
    if (!password_verify($old, $admin['password']))  { $err = "Current password is incorrect!"; }
    elseif ($new !== $cnf)                           { $err = "New passwords do not match!"; }
    elseif (strlen($new) < 6)                        { $err = "Password must be at least 6 characters!"; }
    else {
        $h = password_hash($new, PASSWORD_DEFAULT);
        $conn->query("UPDATE admin SET password='$h' WHERE id=$aid");
        $msg = "Password changed successfully!";
    }
}
$total_feedback = $conn->query("SELECT COUNT(*) as c FROM feedback WHERE is_read=0")->fetch_assoc()['c'];
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Change Password - Admin</title>
<link rel="stylesheet" href="../css/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="dashboard-wrapper">
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>
<aside class="sidebar" id="sidebar">
  <div class="sidebar-brand"><i class="fas fa-car-crash"></i><div><div>AutoCare</div><span>Admin Panel</span></div></div>
  <div class="sidebar-user">
    <img src="../uploads/profiles/<?=$_SESSION['admin_photo']?>" class="sidebar-avatar" onerror="this.src='../uploads/profiles/default.png'">
    <div class="sidebar-user-info"><div class="name"><?=$_SESSION['admin_name']?></div><span class="role">Administrator</span></div>
  </div>
  <nav class="sidebar-nav">
    <div class="nav-section-title">Overview</div>
    <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
    <div class="nav-section-title">Management</div>
    <a href="customers.php"><i class="fas fa-users"></i> Customers</a>
    <a href="mechanics.php"><i class="fas fa-tools"></i> Mechanics</a>
    <a href="requests.php"><i class="fas fa-clipboard-list"></i> Service Requests</a>
    <a href="invoices.php"><i class="fas fa-file-invoice"></i> Invoices</a>
    <a href="feedback.php"><i class="fas fa-comments"></i> Feedback <?php if ($total_feedback>0): ?><span class="badge-count"><?=$total_feedback?></span><?php endif; ?></a>
    <div class="nav-section-title">Account</div>
    <a href="profile.php"><i class="fas fa-user-edit"></i> Profile</a>
    <a href="change_password.php" class="active"><i class="fas fa-lock"></i> Change Password</a>
  </nav>
  <div class="sidebar-footer"><a href="logout.php" class="btn btn-danger" style="width:100%;justify-content:center"><i class="fas fa-sign-out-alt"></i> Logout</a></div>
</aside>
<div class="main-content">
  <div class="topbar">
    <div style="display:flex;align-items:center;gap:12px">
      <button class="hamburger" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
      <div class="topbar-left"><h1>Change Password</h1><p>Update your account password</p></div>
    </div>
    <div class="topbar-right">
      <button class="theme-toggle" onclick="toggleTheme()" id="themeBtn"><i class="fas fa-moon"></i></button>
    </div>
  </div>
  <div class="page-content">
    <?php if (!empty($msg)): ?>
      <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $msg ?></div>
    <?php endif; ?>
    <?php if (!empty($err)): ?>
      <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?= $err ?></div>
    <?php endif; ?>
    <div class="card" style="max-width:480px;">
      <div class="card-header">
        <h3><i class="fas fa-key"></i> Change Admin Password</h3>
      </div>
      <div class="card-body" style="padding:24px;">
        <form method="POST">
          <div class="form-group" style="margin-bottom:16px;">
            <label style="display:block;margin-bottom:6px;font-weight:600;">Current Password</label>
            <input type="password" name="old_password" class="form-control" required
                   style="width:100%;padding:10px 14px;border-radius:8px;border:1px solid var(--border);background:var(--bg-dark);color:var(--text);">
          </div>
          <div class="form-group" style="margin-bottom:16px;">
            <label style="display:block;margin-bottom:6px;font-weight:600;">New Password</label>
            <input type="password" name="new_password" class="form-control" required
                   style="width:100%;padding:10px 14px;border-radius:8px;border:1px solid var(--border);background:var(--bg-dark);color:var(--text);">
          </div>
          <div class="form-group" style="margin-bottom:24px;">
            <label style="display:block;margin-bottom:6px;font-weight:600;">Confirm New Password</label>
            <input type="password" name="confirm_password" class="form-control" required
                   style="width:100%;padding:10px 14px;border-radius:8px;border:1px solid var(--border);background:var(--bg-dark);color:var(--text);">
          </div>
          <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-save"></i> Update Password</button>
        </form>
      </div>
    </div>
  </div>
</div>
</div>
<script>
function toggleSidebar() {
  document.getElementById('sidebar').classList.toggle('open');
  document.getElementById('sidebarOverlay').classList.toggle('active');
}
function toggleTheme() {
  const html = document.documentElement;
  const isDark = html.getAttribute('data-theme') === 'dark';
  html.setAttribute('data-theme', isDark ? 'light' : 'dark');
  document.getElementById('themeBtn').innerHTML = isDark ? '<i class="fas fa-sun"></i>' : '<i class="fas fa-moon"></i>';
  localStorage.setItem('theme', isDark ? 'light' : 'dark');
}
const savedTheme = localStorage.getItem('theme') || 'dark';
document.documentElement.setAttribute('data-theme', savedTheme);
document.getElementById('themeBtn').innerHTML = savedTheme === 'dark' ? '<i class="fas fa-moon"></i>' : '<i class="fas fa-sun"></i>';
</script>
</body>
</html>