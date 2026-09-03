<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
redirectIfNotLoggedIn('mechanic', 'login.php');
$mid = $_SESSION['mechanic_id'];
$mechanic = $conn->query("SELECT * FROM mechanics WHERE id=$mid")->fetch_assoc();
$assigned      = $conn->query("SELECT COUNT(*) c FROM service_requests WHERE mechanic_id=$mid AND status IN ('Approved','Repairing')")->fetch_assoc()['c'];
$completed     = $conn->query("SELECT COUNT(*) c FROM service_requests WHERE mechanic_id=$mid AND status IN ('Repairing Done','Released')")->fetch_assoc()['c'];
$total_assigned= $conn->query("SELECT COUNT(*) c FROM service_requests WHERE mechanic_id=$mid")->fetch_assoc()['c'];
$repairing_now = $conn->query("SELECT COUNT(*) c FROM service_requests WHERE mechanic_id=$mid AND status='Repairing'")->fetch_assoc()['c'];
$recent = $conn->query("SELECT sr.*, c.full_name as cname FROM service_requests sr LEFT JOIN customers c ON sr.customer_id=c.id WHERE sr.mechanic_id=$mid ORDER BY sr.enquiry_date DESC LIMIT 6");
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Mechanic Dashboard - <?= SITE_NAME ?></title>
<link rel="stylesheet" href="../assets/css/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
  :root { --bg-sidebar: #064e3b; }
  .sidebar-nav a.active { background: rgba(16,185,129,0.2); border-left-color: #10b981; }
  .sidebar-nav a:hover { color: white; background: rgba(255,255,255,0.08); }
</style>
<script>const t=localStorage.getItem('vs_theme')||'light';document.documentElement.setAttribute('data-theme',t);</script>
</head>
<body class="dashboard-layout">
<?php include 'sidebar.php'; ?>
<div class="overlay" onclick="toggleSidebar()"></div>
<div class="main-content">
  <header class="topbar">
    <div class="topbar-left">
      <button class="mobile-menu-btn" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
      <span class="topbar-title">Mechanic Dashboard</span>
    </div>
    <div class="topbar-right">
      <button class="theme-toggle" onclick="toggleTheme()"><i class="fas fa-moon theme-icon"></i></button>
      <div class="user-menu">
        <img src="<?= '../uploads/profiles/' . htmlspecialchars($mechanic['profile_photo'] ?? 'default.png') ?>" alt="" onerror="this.src='../assets/images/default.png'">
        <span class="user-name"><?= htmlspecialchars(explode(' ',$mechanic['full_name'])[0]) ?></span>
      </div>
    </div>
  </header>
  <div class="page-content">
    <?php showFlash(); ?>
    <div style="margin-bottom:24px;">
      <h2 style="font-size:1.5rem;font-weight:800;">Welcome, <?= htmlspecialchars(explode(' ',$mechanic['full_name'])[0]) ?>! 🔧</h2>
      <p style="color:var(--text-muted);margin-top:5px;"><?= htmlspecialchars($mechanic['specialization'] ?? 'Vehicle Mechanic') ?> &bull; Here's your work overview.</p>
    </div>
    <div class="stat-cards">
      <div class="stat-card">
        <div class="stat-card-icon blue"><i class="fas fa-clipboard-list"></i></div>
        <div class="stat-card-info">
          <div class="number counter" data-target="<?= $total_assigned ?>"><?= $total_assigned ?></div>
          <div class="label">Total Assigned</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-card-icon orange"><i class="fas fa-tools"></i></div>
        <div class="stat-card-info">
          <div class="number counter" data-target="<?= $assigned ?>"><?= $assigned ?></div>
          <div class="label">Active / Pending</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-card-icon red"><i class="fas fa-spinner"></i></div>
        <div class="stat-card-info">
          <div class="number counter" data-target="<?= $repairing_now ?>"><?= $repairing_now ?></div>
          <div class="label">Currently Repairing</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-card-icon green"><i class="fas fa-check-circle"></i></div>
        <div class="stat-card-info">
          <div class="number counter" data-target="<?= $completed ?>"><?= $completed ?></div>
          <div class="label">Vehicles Repaired</div>
        </div>
      </div>
    </div>
    <div class="table-card">
      <div class="table-card-header">
        <h3><i class="fas fa-wrench" style="color:#10b981"></i> Recent Assignments</h3>
        <a href="assignments.php" class="btn btn-sm" style="background:#10b981;color:white;border-radius:8px;"><i class="fas fa-eye"></i> View All</a>
      </div>
      <div class="table-responsive">
        <table>
          <thead>
            <tr><th>#</th><th>Customer</th><th>Vehicle</th><th>Vehicle No.</th><th>Category</th><th>Problem</th><th>Status</th><th>Action</th></tr>
          </thead>
          <tbody>
          <?php if($recent->num_rows === 0): ?>
            <tr><td colspan="8"><div class="empty-state"><i class="fas fa-clipboard"></i><p>No assignments yet. Admin will assign work to you.</p></div></td></tr>
          <?php else: $i=1; while($r=$recent->fetch_assoc()): ?>
            <tr>
              <td><?= $i++ ?></td>
              <td><?= htmlspecialchars($r['cname'] ?? 'Walk-in') ?></td>
              <td><strong><?= htmlspecialchars($r['vehicle_name']) ?></strong></td>
              <td><code><?= htmlspecialchars($r['vehicle_number']) ?></code></td>
              <td><?= htmlspecialchars($r['vehicle_category']) ?></td>
              <td style="max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="<?= htmlspecialchars($r['problem_description']) ?>"><?= htmlspecialchars($r['problem_description']) ?></td>
              <td><?= statusBadge($r['status']) ?></td>
              <td>
                <?php if(in_array($r['status'], ['Approved','Repairing'])): ?>
                  <button class="btn btn-sm" style="background:#10b981;color:white;" onclick="updateStatus(<?= $r['id'] ?>, '<?= $r['status'] ?>')">
                    <i class="fas fa-edit"></i> Update
                  </button>
                <?php else: ?>
                  <span class="badge badge-success"><i class="fas fa-check"></i> Done</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endwhile; endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<div class="modal-overlay" id="statusModal">
  <div class="modal">
    <div class="modal-header">
      <h4><i class="fas fa-edit"></i> Update Repair Status</h4>
      <button class="modal-close" onclick="closeModal('statusModal')">×</button>
    </div>
    <div class="modal-body">
      <form method="POST" action="assignments.php">
        <input type="hidden" name="action" value="update_status">
        <input type="hidden" name="req_id" id="statusReqId">
        <div class="form-group">
          <label>Select New Status</label>
          <select name="status" id="statusSelect" class="form-control">
            <option value="Repairing">🔧 Repairing (In Progress)</option>
            <option value="Repairing Done">✅ Repairing Done</option>
          </select>
        </div>
        <div class="modal-footer" style="padding:0;border:none;margin-top:15px;">
          <button type="button" class="btn btn-secondary" onclick="closeModal('statusModal')">Cancel</button>
          <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Update Status</button>
        </div>
      </form>
    </div>
  </div>
</div>
<script src="../assets/js/main.js"></script>
<script>
function updateStatus(id, currentStatus) {
  document.getElementById('statusReqId').value = id;
  const sel = document.getElementById('statusSelect');
  sel.value = currentStatus === 'Approved' ? 'Repairing' : 'Repairing Done';
  openModal('statusModal');
}
</script>
</body>
</html>
