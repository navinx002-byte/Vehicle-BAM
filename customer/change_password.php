<?php
session_start();
require_once '../includes/config.php';
if (!isLoggedIn('customer')) redirect('login.php');
$cid = $_SESSION['customer_id'];
$msg = $err = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cur = $_POST['current_password'];
    $new = $_POST['new_password'];
    $user = $conn->query("SELECT password FROM customers WHERE id=$cid")->fetch_assoc();
    if (password_verify($cur, $user['password'])) {
        $hash = password_hash($new, PASSWORD_DEFAULT);
        $conn->query("UPDATE customers SET password='$hash' WHERE id=$cid");
        $msg = "Password changed successfully!";
    } else { $err = "Current password is incorrect."; }
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head><meta charset="UTF-8"><title>Change Password</title>
<link rel="stylesheet" href="../css/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="dashboard-wrapper">
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>
<aside class="sidebar" id="sidebar">
  <div class="sidebar-brand"><i class="fas fa-car-crash"></i><div><div>AutoCare</div><span>Service Center</span></div></div>
  <div class="sidebar-user">
    <img src="../uploads/profiles/<?=$_SESSION['customer_photo']?>" class="sidebar-avatar" onerror="this.src='../uploads/profiles/default.png'">
    <div class="sidebar-user-info">
      <div class="name"><?=$_SESSION['customer_name']?></div>
      <span class="role">Customer</span>
    </div>
  </div>
  <nav class="sidebar-nav">
    <div class="nav-section-title">Main</div>
    <a href="dashboard.php" class="active"><i class="fas fa-home"></i> Dashboard</a>
    <div class="nav-section-title">Services</div>
    <a href="requests.php"><i class="fas fa-clipboard-list"></i> My Requests</a>
    <a href="invoice.php"><i class="fas fa-file-invoice"></i> Invoices</a>
    <a href="feedback.php"><i class="fas fa-comment-alt"></i> Send Feedback</a>
    <div class="nav-section-title">Account</div>
    <a href="profile.php"><i class="fas fa-user-edit"></i> My Profile</a>
    <a href="change_password.php"><i class="fas fa-lock"></i> Change Password</a>
  </nav>
  <div class="sidebar-footer">
    <a href="logout.php" class="btn btn-danger" style="width:100%;justify-content:center"><i class="fas fa-sign-out-alt"></i> Logout</a>
  </div>
</aside>
<div class="main-content">
  <div class="topbar"><div class="topbar-left"><h1>Change Password</h1></div></div>
  <div class="page-content">
    <?php if ($msg): ?><div class="alert alert-success"><?=$msg?></div><?php endif; ?>
    <?php if ($err): ?><div class="alert alert-danger"><?=$err?></div><?php endif; ?>
    <div class="card" style="max-width:500px">
      <div class="card-body">
        <form method="POST">
          <div class="form-group"><label>Current Password</label><input type="password" name="current_password" class="form-control" required></div>
          <div class="form-group"><label>New Password</label><input type="password" name="new_password" class="form-control" required minlength="6"></div>
          <button type="submit" class="btn-primary"><i class="fas fa-key"></i> Update Password</button>
        </form>
      </div>
    </div>
  </div>
</div>
</div>
<script>
const savedTheme = localStorage.getItem('theme') || 'dark';
document.documentElement.setAttribute('data-theme', savedTheme);
</script>
</body></html>
