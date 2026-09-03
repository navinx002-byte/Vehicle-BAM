<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
redirectIfNotLoggedIn('admin', 'login.php');
$cid = (int)($_GET['cid'] ?? 0);
if (!$cid) { header("Location: customers.php"); exit; }
$customer = $conn->query("SELECT * FROM customers WHERE id=$cid")->fetch_assoc();
if (!$customer) { header("Location: customers.php"); exit; }
$requests = $conn->query("SELECT sr.*, i.invoice_number, i.total_amount FROM service_requests sr LEFT JOIN invoices i ON i.request_id=sr.id WHERE sr.customer_id=$cid ORDER BY sr.enquiry_date DESC");
$total_sum = $conn->query("SELECT COALESCE(SUM(cost),0) s FROM service_requests WHERE customer_id=$cid AND payment_status='Paid'")->fetch_assoc()['s'];
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Customer Invoice - <?= SITE_NAME ?></title>
<link rel="stylesheet" href="../assets/css/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script>const t=localStorage.getItem('vs_theme')||'light';document.documentElement.setAttribute('data-theme',t);</script>
</head>
<body class="dashboard-layout">
<?php include 'sidebar.php'; ?>
<div class="overlay" onclick="toggleSidebar()"></div>
<div class="main-content">
  <header class="topbar">
    <div class="topbar-left"><button class="mobile-menu-btn" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button><span class="topbar-title">Customer Invoice History</span></div>
    <div class="topbar-right"><button class="theme-toggle" onclick="toggleTheme()"><i class="fas fa-moon theme-icon"></i></button></div>
  </header>
  <div class="page-content">
    <div class="profile-header" style="margin-bottom:24px;">
      <div class="profile-avatar"><img src="<?= UPLOAD_URL.htmlspecialchars($customer['profile_photo']) ?>" alt="" onerror="this.src='../assets/images/default.png'"></div>
      <div class="profile-info">
        <h3><?= htmlspecialchars($customer['full_name']) ?></h3>
        <p><?= htmlspecialchars($customer['email']) ?> | <?= htmlspecialchars($customer['mobile']) ?></p>
        <div class="profile-meta">
          <div class="profile-meta-item"><i class="fas fa-rupee-sign"></i> Total Paid: <strong>₹<?= number_format($total_sum,2) ?></strong></div>
        </div>
      </div>
      <a href="customers.php" class="btn btn-secondary btn-sm" style="margin-left:auto;"><i class="fas fa-arrow-left"></i> Back</a>
    </div>
    <div class="table-card">
      <div class="table-card-header"><h3><i class="fas fa-file-invoice-dollar" style="color:var(--primary)"></i> All Requests & Invoices</h3>
        <div style="padding:10px 15px;background:rgba(230,57,70,0.1);border-radius:8px;"><strong style="color:var(--primary);">Total Paid Sum: ₹<?= number_format($total_sum,2) ?></strong></div>
      </div>
      <div class="table-responsive">
        <table>
          <thead><tr><th>#</th><th>Vehicle</th><th>Vehicle No.</th><th>Date</th><th>Status</th><th>Cost</th><th>Payment</th><th>Invoice No.</th></tr></thead>
          <tbody>
          <?php if($requests->num_rows===0): ?>
            <tr><td colspan="8"><div class="empty-state"><i class="fas fa-inbox"></i><p>No requests found.</p></div></td></tr>
          <?php else: $i=1; while($r=$requests->fetch_assoc()): ?>
            <tr>
              <td><?= $i++ ?></td>
              <td><?= htmlspecialchars($r['vehicle_name']) ?></td>
              <td><code><?= htmlspecialchars($r['vehicle_number']) ?></code></td>
              <td><?= date('d M Y', strtotime($r['enquiry_date'])) ?></td>
              <td><?= statusBadge($r['status']) ?></td>
              <td><?= $r['cost']>0 ? '₹'.number_format($r['cost'],2) : '-' ?></td>
              <td><?= $r['payment_status']==='Paid' ? '<span class="badge badge-success">Paid</span>' : '<span class="badge badge-warning">Unpaid</span>' ?></td>
              <td><?= $r['invoice_number'] ? '<code>'.htmlspecialchars($r['invoice_number']).'</code>' : '-' ?></td>
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
