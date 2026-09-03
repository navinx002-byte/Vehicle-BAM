<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
if (!isLoggedIn('admin')) redirect('login.php');
$invoices = $conn->query("
    SELECT i.*, 
           COALESCE(c.full_name, 'Unknown') as cname, 
           COALESCE(sr.vehicle_name, '-') as vehicle_name, 
           COALESCE(sr.vehicle_number, '-') as vehicle_number 
    FROM invoices i 
    LEFT JOIN customers c ON i.customer_id = c.id 
    LEFT JOIN service_requests sr ON i.request_id = sr.id 
    ORDER BY i.id DESC
");
$total_rev = $conn->query("SELECT COALESCE(SUM(total_amount),0) as s FROM invoices")->fetch_assoc()['s'];
$total_feedback = $conn->query("SELECT COUNT(*) as c FROM feedback WHERE is_read=0")->fetch_assoc()['c'];
if (!$invoices) die("Query error: " . $conn->error);
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Invoices - Admin</title>
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
    <a href="invoices.php" class="active"><i class="fas fa-file-invoice"></i> Invoices</a>
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
      <div class="topbar-left"><h1>All Invoices</h1><p>View all service invoices</p></div>
    </div>
    <div class="topbar-right">
      <button class="theme-toggle" onclick="toggleTheme()" id="themeBtn"><i class="fas fa-moon"></i></button>
    </div>
  </div>
  <div class="page-content">
    <div class="stats-row">
      <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-rupee-sign"></i></div>
        <div class="stat-info">
          <div class="value">&#8377;<?= number_format($total_rev, 2) ?></div>
          <div class="label">Total Revenue</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-file-invoice"></i></div>
        <div class="stat-info">
          <div class="value"><?= $invoices->num_rows ?></div>
          <div class="label">Total Invoices</div>
        </div>
      </div>
    </div>
    <div class="card">
      <div class="card-header">
        <h3><i class="fas fa-file-invoice-dollar"></i> All Service Invoices</h3>
      </div>
      <div class="table-responsive">
        <table>
          <thead>
            <tr>
              <th>#</th>
              <th>Invoice No.</th>
              <th>Customer</th>
              <th>Vehicle</th>
              <th>Vehicle No.</th>
              <th>Amount</th>
              <th>GST</th>
              <th>Total</th>
              <th>Payment ID</th>
              <th>Paid At</th>
            </tr>
          </thead>
          <tbody>
          <?php if ($invoices->num_rows === 0): ?>
            <tr>
              <td colspan="10" style="text-align:center;padding:40px;color:var(--text-muted);">
                <i class="fas fa-file-invoice" style="font-size:2rem;display:block;margin-bottom:10px;"></i>
                No invoices found yet.
              </td>
            </tr>
          <?php else: $i = 1; while ($inv = $invoices->fetch_assoc()): ?>
            <tr>
              <td><?= $i++ ?></td>
              <td><strong><?= htmlspecialchars($inv['invoice_number'] ?? '-') ?></strong></td>
              <td><?= htmlspecialchars($inv['cname']) ?></td>
              <td><?= htmlspecialchars($inv['vehicle_name']) ?></td>
              <td><?= htmlspecialchars($inv['vehicle_number']) ?></td>
              <td>&#8377;<?= number_format($inv['amount'] ?? 0, 2) ?></td>
              <td>&#8377;<?= number_format($inv['tax'] ?? 0, 2) ?></td>
              <td><strong style="color:var(--primary);">&#8377;<?= number_format($inv['total_amount'] ?? 0, 2) ?></strong></td>
              <td><small style="color:var(--text-muted);"><?= htmlspecialchars($inv['payment_id'] ?? '-') ?></small></td>
              <td><?= !empty($inv['paid_at']) ? date('d M Y', strtotime($inv['paid_at'])) : '-' ?></td>
            </tr>
          <?php endwhile; endif; ?>
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
</body>
</html>