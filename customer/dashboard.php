<?php
session_start();
require_once '../includes/config.php';
if (!isLoggedIn('customer')) redirect('login.php');
$cid = $_SESSION['customer_id'];
$stats = $conn->query("SELECT 
  SUM(CASE WHEN status='Pending' THEN 1 ELSE 0 END) as pending,
  SUM(CASE WHEN status IN ('Approved','Repairing') THEN 1 ELSE 0 END) as inprogress,
  SUM(CASE WHEN status IN ('Repairing Done','Released') THEN 1 ELSE 0 END) as done,
  SUM(CASE WHEN payment_status='Paid' THEN service_cost ELSE 0 END) as total_paid
  FROM service_requests WHERE customer_id=$cid")->fetch_assoc();
$recent = $conn->query("SELECT sr.*, m.full_name as mechanic_name FROM service_requests sr LEFT JOIN mechanics m ON sr.mechanic_id=m.id WHERE sr.customer_id=$cid ORDER BY sr.enquiry_date DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Customer Dashboard - AutoCare</title>
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
      <button class="hamburger" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
      <div class="topbar-left"><h1>Dashboard</h1><p>Welcome back, <?=$_SESSION['customer_name']?>!</p></div>
    </div>
    <div class="topbar-right">
      <button class="theme-toggle" onclick="toggleTheme()" id="themeBtn"><i class="fas fa-moon"></i></button>
    </div>
  </div>
  <div class="page-content">
    <div class="stats-row">
      <div class="stat-card">
        <div class="stat-icon yellow"><i class="fas fa-clock"></i></div>
        <div class="stat-info"><div class="value"><?=$stats['pending']??0?></div><div class="label">Pending Requests</div></div>
      </div>
      <div class="stat-card">
        <div class="stat-icon orange"><i class="fas fa-tools"></i></div>
        <div class="stat-info"><div class="value"><?=$stats['inprogress']??0?></div><div class="label">In Progress</div></div>
      </div>
      <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
        <div class="stat-info"><div class="value"><?=$stats['done']??0?></div><div class="label">Completed</div></div>
      </div>
      <div class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-rupee-sign"></i></div>
        <div class="stat-info"><div class="value">&#8377;<?=number_format($stats['total_paid']??0,0)?></div><div class="label">Total Paid</div></div>
      </div>
    </div>
    <div class="card">
      <div class="card-header">
        <h3><i class="fas fa-history"></i> Recent Service Requests</h3>
        <a href="requests.php" class="btn btn-sm btn-secondary">View All</a>
      </div>
      <div class="table-responsive">
        <table>
          <thead><tr><th>Vehicle</th><th>Number</th><th>Problem</th><th>Date</th><th>Mechanic</th><th>Status</th><th>Payment</th><th>Action</th></tr></thead>
          <tbody>
          <?php if ($recent->num_rows > 0): ?>
            <?php while ($row = $recent->fetch_assoc()): ?>
            <tr>
              <td><?=$row['vehicle_name']?><br><small style="color:var(--text-muted)"><?=$row['vehicle_category']?></small></td>
              <td><?=$row['vehicle_number']?></td>
              <td style="max-width:150px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?=$row['problem_description']?></td>
              <td><?=date('d M Y', strtotime($row['enquiry_date']))?></td>
              <td><?=$row['mechanic_name']??'<span style="color:var(--text-muted)">Not Assigned</span>'?></td>
              <td><?=getStatusBadge($row['status'])?></td>
              <td><?=getPaymentBadge($row['payment_status'])?></td>
              <td>
                <?php if ($row['payment_status'] == 'Payment Sent'): ?>
                  <a href="pay.php?id=<?=$row['id']?>" class="btn btn-sm btn-success"><i class="fas fa-credit-card"></i> Pay</a>
                <?php elseif ($row['status'] == 'Pending'): ?>
                  <a href="requests.php?delete=<?=$row['id']?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete?')"><i class="fas fa-trash"></i></a>
                <?php endif; ?>
              </td>
            </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="8"><div class="no-data"><div class="icon">🚗</div><p>No service requests yet. <a href="requests.php" style="color:var(--primary)">Make one!</a></p></div></td></tr>
          <?php endif; ?>
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
