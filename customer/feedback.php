<?php
session_start();
require_once '../includes/config.php';
if (!isLoggedIn('customer')) redirect('login.php');
$cid = $_SESSION['customer_id'];
$msg = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $subject = sanitize($conn, $_POST['subject']);
    $message = sanitize($conn, $_POST['message']);
    $name = $_SESSION['customer_name'];
    $conn->query("INSERT INTO feedback (sender_id,sender_type,sender_name,subject,message) VALUES ($cid,'customer','$name','$subject','$message')");
    $msg = "Feedback sent! Thank you.";
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Feedback - AutoCare</title>
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
  <div class="topbar">
    <div style="display:flex;align-items:center;gap:12px">
      <button class="hamburger" onclick="document.getElementById('sidebar').classList.toggle('open')"><i class="fas fa-bars"></i></button>
      <div class="topbar-left"><h1>Send Feedback</h1><p>Share your experience</p></div>
    </div>
    <div class="topbar-right"><button class="theme-toggle" onclick="toggleTheme()" id="themeBtn"><i class="fas fa-moon"></i></button></div>
  </div>
  <div class="page-content">
    <?php if ($msg): ?><div class="alert alert-success"><?=$msg?></div><?php endif; ?>
    <div class="card" style="max-width:600px">
      <div class="card-header"><h3><i class="fas fa-comment-dots"></i> Send Feedback to Admin</h3></div>
      <div class="card-body">
        <form method="POST">
          <div class="form-group"><label>Subject</label><input type="text" name="subject" class="form-control" placeholder="What is this about?" required></div>
          <div class="form-group"><label>Message</label><textarea name="message" class="form-control" rows="5" placeholder="Your feedback..." required></textarea></div>
          <button type="submit" class="btn-primary"><i class="fas fa-paper-plane"></i> Send Feedback</button>
        </form>
      </div>
    </div>
  </div>
</div>
</div>
<script>
function toggleTheme() {
  const html = document.documentElement;
  const isDark = html.getAttribute('data-theme') === 'dark';
  html.setAttribute('data-theme', isDark ? 'light' : 'dark');
  document.getElementById('themeBtn').innerHTML = isDark ? '<i class="fas fa-sun"></i>' : '<i class="fas fa-moon"></i>';
  localStorage.setItem('theme', isDark ? 'light' : 'dark');
}
const savedTheme = localStorage.getItem('theme') || 'dark';
document.documentElement.setAttribute('data-theme', savedTheme);
</script>
</body></html>
