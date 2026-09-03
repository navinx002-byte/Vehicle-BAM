<?php
session_start();
require_once '../includes/config.php';
if (!isLoggedIn('admin')) redirect('login.php');
$total_customers = $conn->query("SELECT COUNT(*) as c FROM customers")->fetch_assoc()['c'];
$total_mechanics = $conn->query("SELECT COUNT(*) as c FROM mechanics")->fetch_assoc()['c'];
$total_requests = $conn->query("SELECT COUNT(*) as c FROM service_requests")->fetch_assoc()['c'];
$total_feedback = $conn->query("SELECT COUNT(*) as c FROM feedback WHERE is_read=0")->fetch_assoc()['c'];
$pending = $conn->query("SELECT COUNT(*) as c FROM service_requests WHERE status='Pending'")->fetch_assoc()['c'];
$repairing = $conn->query("SELECT COUNT(*) as c FROM service_requests WHERE status IN ('Approved','Repairing')")->fetch_assoc()['c'];
$done = $conn->query("SELECT COUNT(*) as c FROM service_requests WHERE status IN ('Repairing Done','Released')")->fetch_assoc()['c'];
$revenue = $conn->query("SELECT COALESCE(SUM(total_amount),0) as tot FROM invoices")->fetch_assoc()['tot'];
$recent = $conn->query("SELECT sr.*, c.full_name as cname FROM service_requests sr LEFT JOIN customers c ON sr.customer_id=c.id ORDER BY sr.enquiry_date DESC LIMIT 8");
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Admin Dashboard - AutoCare</title>
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
    <a href="dashboard.php" class="active"><i class="fas fa-home"></i> Dashboard</a>
    <div class="nav-section-title">Management</div>
    <a href="customers.php"><i class="fas fa-users"></i> Customers</a>
    <a href="mechanics.php"><i class="fas fa-tools"></i> Mechanics</a>
    <a href="requests.php"><i class="fas fa-clipboard-list"></i> Service Requests</a>
    <a href="invoices.php"><i class="fas fa-clipboard-list"></i> Invoices</a>
    <a href="feedback.php"><i class="fas fa-comments"></i> Feedback <?php if ($total_feedback>0): ?><span class="badge-count"><?=$total_feedback?></span><?php endif; ?></a>
    <div class="nav-section-title">Account</div>
    <a href="profile.php"><i class="fas fa-user-edit"></i> Profile</a>
    <a href="change_password.php"><i class="fas fa-lock"></i> Change Password</a>
  </nav>
  <div class="sidebar-footer"><a href="logout.php" class="btn btn-danger" style="width:100%;justify-content:center"><i class="fas fa-sign-out-alt"></i> Logout</a></div>
</aside>
<div class="main-content">
  <div class="topbar">
    <div style="display:flex;align-items:center;gap:12px">
      <button class="hamburger" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
      <div class="topbar-left"><h1>Dashboard</h1><p>Welcome, <?=$_SESSION['admin_name']?></p></div>
    </div>
    <div class="topbar-right">
      <button class="theme-toggle" onclick="toggleTheme()" id="themeBtn"><i class="fas fa-moon"></i></button>
    </div>
  </div>
  <div class="page-content">
    <div class="stats-row">
      <div class="stat-card"><div class="stat-icon blue"><i class="fas fa-users"></i></div><div class="stat-info"><div class="value"><?=$total_customers?></div><div class="label">Total Customers</div></div></div>
      <div class="stat-card"><div class="stat-icon orange"><i class="fas fa-tools"></i></div><div class="stat-info"><div class="value"><?=$total_mechanics?></div><div class="label">Total Mechanics</div></div></div>
      <div class="stat-card"><div class="stat-icon yellow"><i class="fas fa-clipboard-list"></i></div><div class="stat-info"><div class="value"><?=$total_requests?></div><div class="label">Total Requests</div></div></div>
      <div class="stat-card"><div class="stat-icon green"><i class="fas fa-rupee-sign"></i></div><div class="stat-info"><div class="value">&#8377;<?=number_format($revenue,0)?></div><div class="label">Total Revenue</div></div></div>
    </div>
    <div class="stats-row">
      <div class="stat-card"><div class="stat-icon yellow"><i class="fas fa-hourglass-half"></i></div><div class="stat-info"><div class="value"><?=$pending?></div><div class="label">Pending</div></div></div>
      <div class="stat-card"><div class="stat-icon orange"><i class="fas fa-wrench"></i></div><div class="stat-info"><div class="value"><?=$repairing?></div><div class="label">In Progress</div></div></div>
      <div class="stat-card"><div class="stat-icon green"><i class="fas fa-check-double"></i></div><div class="stat-info"><div class="value"><?=$done?></div><div class="label">Completed</div></div></div>
      <div class="stat-card"><div class="stat-icon purple"><i class="fas fa-comment-dots"></i></div><div class="stat-info"><div class="value"><?=$total_feedback?></div><div class="label">Unread Feedback</div></div></div>
    </div>
    <div class="card">
      <div class="card-header">
        <h3><i class="fas fa-history"></i> Recent Service Requests</h3>
        <a href="requests.php" class="btn btn-sm btn-secondary">View All</a>
      </div>
      <div class="table-responsive">
        <table>
          <thead><tr><th>Customer</th><th>Vehicle</th><th>Number</th><th>Date</th><th>Status</th><th>Cost</th><th>Payment</th><th>Action</th></tr></thead>
          <tbody>
          <?php while ($r = $recent->fetch_assoc()): ?>
          <tr>
            <td><?=$r['cname']??'Walk-in'?></td>
            <td><?=$r['vehicle_name']?></td>
            <td><?=$r['vehicle_number']?></td>
            <td><?=date('d M Y', strtotime($r['enquiry_date']))?></td>
            <td><?=getStatusBadge($r['status'])?></td>
            <td><?=$r['service_cost']>0?'&#8377;'.number_format($r['service_cost'],2):'-'?></td>
            <td><?=getPaymentBadge($r['payment_status'])?></td>
            <td><a href="requests.php?view=<?=$r['id']?>" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a></td>
          </tr>
          <?php endwhile; ?>
          </tbody>
        </table>
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
</body></html>