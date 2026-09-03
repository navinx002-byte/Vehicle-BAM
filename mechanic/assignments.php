<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
if (!isLoggedIn('mechanic')) redirect('login.php');
$mid = $_SESSION['mechanic_id'];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $rid    = (int)$_POST['req_id'];
    $status = sanitize($conn,$_POST['status']);
    if (in_array($status, ['Repairing', 'Repairing Done'])) {
        $check = $conn->query("SELECT id FROM service_requests WHERE id=$rid AND mechanic_id=$mid AND status IN ('Approved','Repairing')");
        if ($check->num_rows === 1) {
            $conn->query("UPDATE service_requests SET status='$status' WHERE id=$rid");
            setFlash('success', "Status updated to <strong>$status</strong> successfully!");
        } else {
            setFlash('error', 'You cannot update this request!');
        }
    }
    header("Location: assignments.php"); exit;
}
$mechanic   = $conn->query("SELECT * FROM mechanics WHERE id=$mid")->fetch_assoc();
$active     = $conn->query("SELECT sr.*, c.full_name as cname, c.mobile as cmobile FROM service_requests sr LEFT JOIN customers c ON sr.customer_id=c.id WHERE sr.mechanic_id=$mid AND sr.status IN ('Approved','Repairing') ORDER BY sr.enquiry_date DESC");
$all        = $conn->query("SELECT sr.*, c.full_name as cname FROM service_requests sr LEFT JOIN customers c ON sr.customer_id=c.id WHERE sr.mechanic_id=$mid ORDER BY sr.enquiry_date DESC");
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Assignments - <?= SITE_NAME ?></title>
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
      <span class="topbar-title">My Assignments</span>
    </div>
    <div class="topbar-right"><button class="theme-toggle" onclick="toggleTheme()"><i class="fas fa-moon theme-icon"></i></button></div>
  </header>
  <div class="page-content">
    <?php showFlash(); ?>
    <div class="tab-group">
      <div class="tab-nav">
        <button class="tab-btn active" data-tab="tab-active">
          <i class="fas fa-tools"></i> Active Assignments
          <?php if($active->num_rows > 0): ?>
            <span style="background:#ef4444;color:white;border-radius:50%;width:20px;height:20px;display:inline-flex;align-items:center;justify-content:center;font-size:11px;margin-left:6px;"><?= $active->num_rows ?></span>
          <?php endif; ?>
        </button>
        <button class="tab-btn" data-tab="tab-all"><i class="fas fa-list"></i> All Assignments</button>
      </div>
      <div class="tab-content active" id="tab-active">
        <?php if($active->num_rows === 0): ?>
          <div class="table-card">
            <div class="empty-state" style="padding:60px 20px;">
              <i class="fas fa-clipboard-check" style="color:#10b981;"></i>
              <p>No active assignments right now. Great job staying on top of work!</p>
            </div>
          </div>
        <?php else: while($r = $active->fetch_assoc()): ?>
          <div class="form-card" style="margin-bottom:20px;border-left:4px solid <?= $r['status']==='Repairing'?'#f59e0b':'#3b82f6' ?>;">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:15px;">
              <div style="flex:1;min-width:250px;">
                <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px;">
                  <div style="width:50px;height:50px;background:rgba(16,185,129,0.1);border-radius:12px;display:flex;align-items:center;justify-content:center;color:#10b981;font-size:22px;">
                    <i class="fas fa-<?= $r['vehicle_category']==='Two Wheeler'?'motorcycle':($r['vehicle_category']==='Heavy Vehicle'?'truck':'car') ?>"></i>
                  </div>
                  <div>
                    <h4 style="font-weight:800;font-size:1.1rem;"><?= htmlspecialchars($r['vehicle_name']) ?></h4>
                    <div style="color:var(--text-muted);font-size:13px;">
                      <code><?= htmlspecialchars($r['vehicle_number']) ?></code> &bull; <?= htmlspecialchars($r['vehicle_category']) ?>
                    </div>
                  </div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;font-size:14px;">
                  <?php if($r['vehicle_brand']): ?>
                    <div><span style="color:var(--text-muted);">Brand:</span> <strong><?= htmlspecialchars($r['vehicle_brand']) ?></strong></div>
                  <?php endif; ?>
                  <?php if($r['vehicle_model']): ?>
                    <div><span style="color:var(--text-muted);">Model:</span> <strong><?= htmlspecialchars($r['vehicle_model']) ?></strong></div>
                  <?php endif; ?>
                  <div><span style="color:var(--text-muted);">Customer:</span> <strong><?= htmlspecialchars($r['cname'] ?? 'Walk-in') ?></strong></div>
                  <?php if($r['cmobile']): ?>
                    <div><span style="color:var(--text-muted);">Contact:</span> <strong><?= htmlspecialchars($r['cmobile']) ?></strong></div>
                  <?php endif; ?>
                  <div><span style="color:var(--text-muted);">Date:</span> <strong><?= date('d M Y', strtotime($r['enquiry_date'])) ?></strong></div>
                  <div><span style="color:var(--text-muted);">Cost:</span> <strong style="color:var(--primary);">₹<?= number_format($r['service_cost'],2) ?></strong></div>
                </div>
                <div style="margin-top:12px;padding:10px;background:var(--bg);border-radius:8px;font-size:14px;">
                  <strong>🔍 Problem:</strong> <?= htmlspecialchars($r['problem_description']) ?>
                </div>
               <?php if(!empty($r['admin_notes'])): ?>
                  <div style="margin-top:8px;padding:10px;background:rgba(59,130,246,0.05);border-radius:8px;font-size:14px;border-left:3px solid #3b82f6;">
                    <strong>📋 Admin Notes:</strong> <?= htmlspecialchars($r['admin_notes']) ?>
                  </div>
                <?php endif; ?>
              </div>
              <div style="display:flex;flex-direction:column;align-items:flex-end;gap:12px;min-width:180px;">
                <div><?= statusBadge($r['status']) ?></div>
                <form method="POST">
                  <input type="hidden" name="action" value="update_status">
                  <input type="hidden" name="req_id" value="<?= $r['id'] ?>">
                  <div class="form-group" style="margin:0;">
                    <select name="status" class="form-control" style="margin-bottom:10px;">
                      <?php if($r['status'] === 'Approved'): ?>
                        <option value="Repairing">🔧 Start Repairing</option>
                        <option value="Repairing Done">✅ Mark as Done</option>
                      <?php else: ?>
                        <option value="Repairing" <?= $r['status']==='Repairing'?'selected':'' ?>>🔧 Repairing</option>
                        <option value="Repairing Done">✅ Repairing Done</option>
                      <?php endif; ?>
                    </select>
                    <button type="submit" class="btn btn-success" style="width:100%;justify-content:center;">
                      <i class="fas fa-save"></i> Update Status
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        <?php endwhile; endif; ?>
      </div>
      <div class="tab-content" id="tab-all">
        <div class="table-card">
          <div class="table-card-header">
            <h3><i class="fas fa-list" style="color:#10b981"></i> All My Assignments</h3>
            <span class="badge badge-info"><?= $all->num_rows ?> total</span>
          </div>
          <div class="table-responsive">
            <table>
              <thead>
                <tr><th>#</th><th>Customer</th><th>Vehicle</th><th>Vehicle No.</th><th>Category</th><th>Date</th><th>Cost</th><th>Status</th><th>Payment</th></tr>
              </thead>
              <tbody>
              <?php
              $all->data_seek(0);
              if($all->num_rows === 0): ?>
                <tr><td colspan="9"><div class="empty-state"><i class="fas fa-clipboard"></i><p>No assignments found.</p></div></td></tr>
              <?php else: $i=1; while($r=$all->fetch_assoc()): ?>
                <tr>
                  <td><?= $i++ ?></td>
                  <td><?= htmlspecialchars($r['cname'] ?? 'Walk-in') ?></td>
                  <td><strong><?= htmlspecialchars($r['vehicle_name']) ?></strong><br><small style="color:var(--text-muted);"><?= htmlspecialchars(substr($r['problem_description'],0,40)).'...' ?></small></td>
                  <td><code><?= htmlspecialchars($r['vehicle_number']) ?></code></td>
                  <td><?= htmlspecialchars($r['vehicle_category']) ?></td>
                  <td><?= date('d M Y', strtotime($r['enquiry_date'])) ?></td>
                  <td>₹<?= number_format($r['service_cost'],2) ?></td>
                  <td><?= statusBadge($r['status']) ?></td>
                  <td><?= $r['payment_status']==='Paid'?'<span class="badge badge-success">Paid</span>':'<span class="badge badge-warning">Unpaid</span>' ?></td>
                </tr>
              <?php endwhile; endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="../assets/js/main.js"></script>
</body>
</html>