<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
if (!isLoggedIn('customer')) redirect('login.php');
$cid     = $_SESSION['customer_id'];
$success = isset($_GET['success']);
$req_id  = isset($_GET['req']) ? (int)$_GET['req'] : 0;
$single_inv = null;
if ($req_id) {
    $single_inv = $conn->query(
        "SELECT i.*, sr.vehicle_name, sr.vehicle_number, sr.vehicle_category,
                sr.vehicle_brand, sr.vehicle_model, sr.problem_description,
                sr.service_cost, m.full_name as mname
         FROM invoices i
         JOIN service_requests sr ON i.request_id = sr.id
         LEFT JOIN mechanics m ON sr.mechanic_id = m.id
         WHERE i.customer_id=$cid AND i.request_id=$req_id
         LIMIT 1"
    )->fetch_assoc();
}
$invoices = $conn->query(
    "SELECT i.*, sr.vehicle_name, sr.vehicle_number
     FROM invoices i
     JOIN service_requests sr ON i.request_id = sr.id
     WHERE i.customer_id=$cid
     ORDER BY i.paid_at DESC"
);
$total_paid = $conn->query("SELECT COALESCE(SUM(total_amount),0) as tot FROM invoices WHERE customer_id=$cid")->fetch_assoc()['tot'];
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Invoices - AutoCare</title>
<link rel="stylesheet" href="../css/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script>const t=localStorage.getItem('vs_theme')||'dark';document.documentElement.setAttribute('data-theme',t);</script>
<style>
@media print {
  .sidebar,.topbar,.dashboard-wrapper>*:not(.main-content),.sidebar-overlay,
  .no-print{display:none!important}
  .main-content{margin:0!important}
  .invoice-paper{box-shadow:none!important;border:none!important}
}
.invoice-paper{background:#fff;color:#111;border-radius:12px;overflow:hidden;max-width:700px;box-shadow:0 4px 24px rgba(0,0,0,.15)}
.inv-header{background:linear-gradient(135deg,#FF6B35,#e55a24);color:white;padding:2rem 2.5rem;display:flex;justify-content:space-between;align-items:flex-start}
.inv-header h2{font-family:'Syne',sans-serif;font-size:1.8rem;margin:0}
.inv-header .inv-num{text-align:right}
.inv-header .inv-num h3{font-size:1.2rem;margin:0}
.inv-body{padding:2rem 2.5rem}
.inv-meta{display:grid;grid-template-columns:1fr 1fr;gap:2rem;margin-bottom:1.5rem}
.inv-meta-block h4{font-size:.75rem;color:#888;text-transform:uppercase;letter-spacing:1px;margin-bottom:6px}
.inv-meta-block p{font-size:.9rem;margin:2px 0;color:#333}
.inv-meta-block strong{color:#111}
.inv-table{width:100%;border-collapse:collapse;margin-bottom:1.5rem}
.inv-table thead tr{background:#f5f5f5}
.inv-table th{padding:10px 12px;text-align:left;font-size:.82rem;color:#666;font-weight:600}
.inv-table td{padding:10px 12px;border-bottom:1px solid #eee;font-size:.88rem}
.inv-table tfoot tr:last-child td{font-weight:700;font-size:1.05rem;border-top:2px solid #eee;padding-top:14px}
.inv-table tfoot tr:last-child td:last-child{color:#FF6B35;font-size:1.2rem}
.inv-footer-note{background:#f9f9f9;border-radius:8px;padding:1rem;font-size:.82rem;color:#666;line-height:1.6}
.inv-footer-note strong{color:#333}
.paid-stamp{display:inline-block;border:3px solid #10b981;color:#10b981;font-weight:800;font-size:.9rem;padding:4px 14px;border-radius:6px;letter-spacing:2px;transform:rotate(-3deg);margin-left:10px}
</style>
</head>
<body>
<div class="dashboard-wrapper">
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>
<aside class="sidebar" id="sidebar">
  <div class="sidebar-brand"><i class="fas fa-car-crash"></i><div><div>AutoCare</div><span>Service Center</span></div></div>
  <div class="sidebar-user">
    <img src="../uploads/profiles/<?=$_SESSION['customer_photo']?>" class="sidebar-avatar" onerror="this.src='../uploads/profiles/default.png'">
    <div class="sidebar-user-info"><div class="name"><?=$_SESSION['customer_name']?></div><span class="role">Customer</span></div>
  </div>
  <nav class="sidebar-nav">
    <div class="nav-section-title">Main</div>
    <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
    <div class="nav-section-title">Services</div>
    <a href="requests.php"><i class="fas fa-clipboard-list"></i> My Requests</a>
    <a href="invoice.php" class="active"><i class="fas fa-file-invoice"></i> Invoices</a>
    <a href="feedback.php"><i class="fas fa-comment-alt"></i> Feedback</a>
    <div class="nav-section-title">Account</div>
    <a href="profile.php"><i class="fas fa-user-edit"></i> My Profile</a>
    <a href="change_password.php"><i class="fas fa-lock"></i> Change Password</a>
  </nav>
  <div class="sidebar-footer"><a href="logout.php" class="btn btn-danger" style="width:100%;justify-content:center"><i class="fas fa-sign-out-alt"></i> Logout</a></div>
</aside>
<div class="main-content">
  <div class="topbar">
    <div style="display:flex;align-items:center;gap:12px">
      <button class="hamburger" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
      <div class="topbar-left"><h1>Invoices</h1><p>Your payment records</p></div>
    </div>
    <div class="topbar-right">
      <button class="theme-toggle" onclick="toggleTheme()" id="themeBtn"><i class="fas fa-moon"></i></button>
    </div>
  </div>
  <div class="page-content">
    <?php if ($success): ?>
    <div class="alert alert-success no-print" style="max-width:700px;margin:0 auto 1.5rem">
      <i class="fas fa-check-circle"></i> <strong>Payment Successful!</strong> Your vehicle has been released. Invoice is ready below.
    </div>
    <?php endif; ?>
    <?php if ($single_inv): ?>
    <div style="max-width:700px;margin:0 auto 2rem">
      <div class="no-print" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem">
        <a href="invoice.php" class="btn btn-sm btn-secondary"><i class="fas fa-arrow-left"></i> All Invoices</a>
        <button onclick="window.print()" class="btn btn-sm btn-primary"><i class="fas fa-print"></i> Print Invoice</button>
      </div>
      <div class="invoice-paper">
        <div class="inv-header">
          <div>
            <h2><i class="fas fa-car-crash"></i> AutoCare</h2>
            <p style="margin:4px 0 0;opacity:.85;font-size:.88rem">Vehicle Service Center</p>
            <p style="margin:4px 0 0;opacity:.75;font-size:.8rem">support@autocare.com | +91 98765 43210</p>
          </div>
          <div class="inv-num">
            <h3>INVOICE</h3>
            <p style="margin:4px 0;opacity:.9;font-size:.88rem"><?=$single_inv['invoice_number']?></p>
            <p style="margin:4px 0;opacity:.8;font-size:.8rem">
              <?=date('d M Y, h:i A', strtotime($single_inv['paid_at']))?>
            </p>
            <span class="paid-stamp">PAID</span>
          </div>
        </div>
        <div class="inv-body">
          <div class="inv-meta">
            <div class="inv-meta-block">
              <h4>Billed To</h4>
              <strong><?=$_SESSION['customer_name']?></strong>
              <p>Customer ID: #<?=$cid?></p>
            </div>
            <div class="inv-meta-block">
              <h4>Vehicle Details</h4>
              <strong><?=htmlspecialchars($single_inv['vehicle_name'])?></strong>
              <p><?=htmlspecialchars($single_inv['vehicle_number'])?> &bull; <?=htmlspecialchars($single_inv['vehicle_category'])?></p>
              <?php if ($single_inv['vehicle_brand']): ?>
              <p><?=htmlspecialchars($single_inv['vehicle_brand'].' '.$single_inv['vehicle_model'])?></p>
              <?php endif; ?>
            </div>
          </div>
          <table class="inv-table">
            <thead>
              <tr><th>Description</th><th style="text-align:right">Amount</th></tr>
            </thead>
            <tbody>
              <tr>
                <td>
                  Vehicle Service — <?=htmlspecialchars($single_inv['problem_description'])?>
                  <?php if ($single_inv['mname']): ?>
                  <br><small style="color:#888">Mechanic: <?=htmlspecialchars($single_inv['mname'])?></small>
                  <?php endif; ?>
                </td>
                <td style="text-align:right">&#8377;<?=number_format($single_inv['amount'],2)?></td>
              </tr>
            </tbody>
            <tfoot>
              <tr><td style="color:#888;font-size:.85rem">GST @ 18%</td><td style="text-align:right;color:#888">&#8377;<?=number_format($single_inv['tax'],2)?></td></tr>
              <tr><td>Total Amount</td><td style="text-align:right">&#8377;<?=number_format($single_inv['total_amount'],2)?></td></tr>
            </tfoot>
          </table>
          <div class="inv-footer-note">
            <strong>Payment Method:</strong> Razorpay &nbsp;&bull;&nbsp;
            <strong>Transaction ID:</strong> <?=htmlspecialchars($single_inv['payment_id'] ?? 'N/A')?> &nbsp;&bull;&nbsp;
            <strong>Status:</strong> <span style="color:#10b981;font-weight:700">PAID ✓</span>
            <br><br>
            Thank you for choosing AutoCare Service Center. For queries call <strong>+91 98765 43210</strong>.
          </div>
        </div>
      </div>
    </div>
    <?php endif; ?>
    <div class="card no-print">
      <div class="card-header" style="display:flex;justify-content:space-between;align-items:center">
        <h3><i class="fas fa-history"></i> All Invoices</h3>
        <span style="color:var(--primary);font-weight:700;font-size:.95rem">
          Total Paid: &#8377;<?=number_format($total_paid,2)?>
        </span>
      </div>
      <div class="table-responsive">
        <table>
          <thead>
            <tr><th>Invoice No.</th><th>Vehicle</th><th>Amount</th><th>GST</th><th>Total</th><th>Date</th><th>Action</th></tr>
          </thead>
          <tbody>
          <?php if ($invoices && $invoices->num_rows > 0):
            $invoices->data_seek(0);
            while ($r = $invoices->fetch_assoc()): ?>
          <tr>
            <td><code><?=$r['invoice_number']?></code></td>
            <td><strong><?=htmlspecialchars($r['vehicle_name'])?></strong><br><small style="color:var(--text-muted)"><?=htmlspecialchars($r['vehicle_number'])?></small></td>
            <td>&#8377;<?=number_format($r['amount'],2)?></td>
            <td>&#8377;<?=number_format($r['tax'],2)?></td>
            <td><strong style="color:var(--primary)">&#8377;<?=number_format($r['total_amount'],2)?></strong></td>
            <td><?=date('d M Y', strtotime($r['paid_at']))?></td>
            <td>
              <a href="invoice.php?req=<?=$r['request_id']?>" class="btn btn-sm btn-info">
                <i class="fas fa-eye"></i> View
              </a>
            </td>
          </tr>
          <?php endwhile; else: ?>
          <tr><td colspan="7">
            <div class="empty-state"><i class="fas fa-file-invoice"></i><p>No invoices yet. Pay a service request to see your invoice here.</p></div>
          </td></tr>
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
  localStorage.setItem('vs_theme', isDark ? 'light' : 'dark');
}
const savedTheme = localStorage.getItem('vs_theme') || 'dark';
document.documentElement.setAttribute('data-theme', savedTheme);
document.getElementById('themeBtn').innerHTML = savedTheme === 'dark' ? '<i class="fas fa-moon"></i>' : '<i class="fas fa-sun"></i>';
</script>
</body>
</html>