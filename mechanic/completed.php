<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
if (!isLoggedIn('mechanic')) redirect('login.php');
$mid = $_SESSION['mechanic_id'];
$mechanic = $conn->query("SELECT * FROM mechanics WHERE id=$mid")->fetch_assoc();
$completed = $conn->query("SELECT sr.*, c.full_name as cname FROM service_requests sr LEFT JOIN customers c ON sr.customer_id=c.id WHERE sr.mechanic_id=$mid AND sr.status IN ('Repairing Done','Released') ORDER BY sr.enquiry_date DESC");
$total_earnings = $conn->query("SELECT COALESCE(SUM(service_cost),0) s FROM service_requests WHERE mechanic_id=$mid AND payment_status='Paid'")->fetch_assoc()['s'];
$total_done = $conn->query("SELECT COUNT(*) c FROM service_requests WHERE mechanic_id=$mid AND status IN ('Repairing Done','Released')")->fetch_assoc()['c'];
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Completed Work - <?= SITE_NAME ?></title>
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
      <span class="topbar-title">Completed Work</span>
    </div>
    <div class="topbar-right"><button class="theme-toggle" onclick="toggleTheme()"><i class="fas fa-moon theme-icon"></i></button></div>
  </header>
  <div class="page-content">
    <?php showFlash(); ?>
    <div class="stat-cards" style="margin-bottom:24px;">
      <div class="stat-card">
        <div class="stat-card-icon green"><i class="fas fa-car-side"></i></div>
        <div class="stat-card-info">
          <div class="number counter" data-target="<?= $total_done ?>"><?= $total_done ?></div>
          <div class="label">Vehicles Repaired</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-card-icon orange"><i class="fas fa-rupee-sign"></i></div>
        <div class="stat-card-info">
          <div class="number">₹<?= number_format($total_earnings,2) ?></div>
          <div class="label">Total Service Value</div>
        </div>
      </div>
    </div>
    <div class="table-card">
      <div class="table-card-header">
        <h3><i class="fas fa-check-double" style="color:#10b981"></i> Vehicles I Have Repaired</h3>
        <span class="badge badge-success"><?= $completed->num_rows ?> completed</span>
      </div>
      <div class="table-responsive">
        <table>
          <thead>
            <tr><th>#</th><th>Customer</th><th>Vehicle</th><th>Vehicle No.</th><th>Category</th><th>Brand / Model</th><th>Problem</th><th>Cost</th><th>Status</th><th>Payment</th></tr>
          </thead>
          <tbody>
          <?php if($completed->num_rows === 0): ?>
            <tr><td colspan="10"><div class="empty-state"><i class="fas fa-tools"></i><p>No completed repairs yet. Your completed work will appear here.</p></div></td></tr>
          <?php else: $i=1; while($r=$completed->fetch_assoc()): ?>
            <tr>
              <td><?= $i++ ?></td>
              <td><?= htmlspecialchars($r['cname'] ?? 'Walk-in') ?></td>
              <td><strong><?= htmlspecialchars($r['vehicle_name']) ?></strong></td>
              <td><code><?= htmlspecialchars($r['vehicle_number']) ?></code></td>
              <td><?= htmlspecialchars($r['vehicle_category']) ?></td>
              <td style="font-size:13px;"><?= htmlspecialchars(trim($r['vehicle_brand'].' '.$r['vehicle_model'])) ?: '-' ?></td>
              <td style="max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="<?= htmlspecialchars($r['problem_description']) ?>">
                <?= htmlspecialchars($r['problem_description']) ?>
              </td>
              <td><strong style="color:var(--primary);">₹<?= number_format($r['service_cost'],2) ?></strong></td>
              <td><?= statusBadge($r['status']) ?></td>
              <td><?= $r['payment_status']==='Paid'?'<span class="badge badge-success"><i class="fas fa-check"></i> Paid</span>':'<span class="badge badge-warning">Pending</span>' ?></td>
            </tr>
          <?php endwhile; endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<script src="../assets/js/main.js"></script>
</body>
</html>