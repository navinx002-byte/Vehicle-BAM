<?php
session_start();
require_once '../includes/config.php';
if (!isLoggedIn('customer')) redirect('login.php');
$cid = $_SESSION['customer_id'];
$msg = $err = '';
if (isset($_GET['delete'])) {
    $did = (int)$_GET['delete'];
    $r = $conn->query("SELECT id FROM service_requests WHERE id=$did AND customer_id=$cid AND status='Pending'");
    if ($r->num_rows > 0) {
        $conn->query("DELETE FROM service_requests WHERE id=$did");
        $msg = "Request deleted successfully.";
    } else { $err = "Cannot delete this request."; }
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cat   = sanitize($conn, $_POST['vehicle_category']);
    $vname = sanitize($conn, $_POST['vehicle_name']);
    $brand = sanitize($conn, $_POST['vehicle_brand']);
    $model = sanitize($conn, $_POST['vehicle_model']);
    $prob  = sanitize($conn, $_POST['problem_description']);
    $addr  = sanitize($conn, $_POST['service_address']);   // ← new

    $vnum = preg_replace('/[^A-Z0-9]/', '', strtoupper(sanitize($conn, $_POST['vehicle_number'])));

    if (!preg_match('/^[A-Z]{2}[0-9]{2}[A-Z]{1,2}[0-9]{4}$/', $vnum)) {
        $err = "Invalid Vehicle Number. Please use format like KA25HX2020.";
    } elseif (empty($addr)) {
        $err = "Service address is required.";
    } else {
        $conn->query("INSERT INTO service_requests
            (customer_id,vehicle_category,vehicle_name,vehicle_number,vehicle_brand,vehicle_model,problem_description,service_address)
            VALUES ($cid,'$cat','$vname','$vnum','$brand','$model','$prob','$addr')");
        $msg = "Service request submitted successfully!";
    }
}
$pending  = $conn->query("SELECT * FROM service_requests WHERE customer_id=$cid AND status='Pending' ORDER BY enquiry_date DESC");
$approved = $conn->query("SELECT sr.*, m.full_name as mname FROM service_requests sr LEFT JOIN mechanics m ON sr.mechanic_id=m.id WHERE sr.customer_id=$cid AND sr.status NOT IN ('Pending') ORDER BY sr.enquiry_date DESC");
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>My Requests - AutoCare</title>
<link rel="stylesheet" href="../css/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
#newReqModal input:focus,
#newReqModal select:focus,
#newReqModal textarea:focus {
  border-color: #FF6B00 !important;
  background: rgba(255,107,0,0.06) !important;
  box-shadow: 0 0 0 3px rgba(255,107,0,0.15) !important;
}
#newReqModal input::placeholder,
#newReqModal textarea::placeholder { color: rgba(255,255,255,0.28); }
#newReqModal .modal-footer button[type="button"]:hover {
  border-color: rgba(255,255,255,0.3); color:#fff; background:rgba(255,255,255,0.10);
}
#newReqModal button[type="submit"]:hover {
  transform:translateY(-2px); box-shadow:0 10px 28px rgba(255,107,0,0.45);
}

/* address field styles */
.addr-wrap { position:relative; }
.addr-wrap textarea {
  width:100%; padding-right:44px !important;
}
.addr-gps-btn {
  position:absolute; top:10px; right:10px;
  background:rgba(255,107,0,0.15); border:1.5px solid rgba(255,107,0,0.35);
  color:#FF6B00; border-radius:8px; padding:6px 9px; cursor:pointer;
  font-size:13px; transition:all 0.2s; line-height:1;
}
.addr-gps-btn:hover { background:rgba(255,107,0,0.3); }
.addr-gps-btn.loading { opacity:0.6; cursor:not-allowed; }
.addr-status { font-size:11px; margin-top:5px; min-height:14px; }
.addr-status.ok  { color:#34d399; }
.addr-status.err { color:#f87171; }
</style>
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
    <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
    <div class="nav-section-title">Services</div>
    <a href="requests.php" class="active"><i class="fas fa-clipboard-list"></i> My Requests</a>
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
      <div class="topbar-left"><h1>Service Requests</h1><p>Manage your vehicle service requests</p></div>
    </div>
    <div class="topbar-right">
      <button class="btn btn-sm btn-primary" onclick="document.getElementById('newReqModal').classList.add('active')"><i class="fas fa-plus"></i> New Request</button>
      <button class="theme-toggle" onclick="toggleTheme()" id="themeBtn"><i class="fas fa-moon"></i></button>
    </div>
  </div>
  <div class="page-content">
    <?php if ($msg): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?=$msg?></div><?php endif; ?>
    <?php if ($err): ?><div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?=$err?></div><?php endif; ?>
    <div class="tabs">
      <button class="tab-btn active" onclick="switchTab(event,'pending')"><i class="fas fa-clock"></i> Pending Requests</button>
      <button class="tab-btn" onclick="switchTab(event,'approved')"><i class="fas fa-check"></i> All Active / History</button>
    </div>

    <!-- Pending table -->
    <div class="tab-pane active" id="pending">
      <div class="card">
        <div class="card-header"><h3><i class="fas fa-hourglass-half"></i> Pending Requests</h3></div>
        <div class="table-responsive">
          <table>
            <thead><tr><th>Vehicle Name</th><th>Category</th><th>Reg. Number</th><th>Problem</th><th>Service Address</th><th>Date</th><th>Status</th><th>Action</th></tr></thead>
            <tbody>
            <?php if ($pending->num_rows > 0): ?>
              <?php while ($r = $pending->fetch_assoc()): ?>
              <tr>
                <td><strong><?=$r['vehicle_name']?></strong><br><small style="color:var(--text-muted)"><?=$r['vehicle_brand']?> <?=$r['vehicle_model']?></small></td>
                <td><?=$r['vehicle_category']?></td>
                <td><?=$r['vehicle_number']?></td>
                <td style="max-width:180px"><?=substr($r['problem_description'],0,55)?>...</td>
                <td style="max-width:160px;font-size:0.82rem"><?=htmlspecialchars(substr($r['service_address']??'',0,50))?><?=strlen($r['service_address']??'')>50?'…':''?></td>
                <td><?=date('d M Y', strtotime($r['enquiry_date']))?></td>
                <td><?=getStatusBadge($r['status'])?></td>
                <td>
                  <a href="?delete=<?=$r['id']?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this request?')"><i class="fas fa-trash"></i> Delete</a>
                </td>
              </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr><td colspan="8"><div class="no-data"><div class="icon">📋</div><p>No pending requests</p></div></td></tr>
            <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- History table -->
    <div class="tab-pane" id="approved">
      <div class="card">
        <div class="card-header"><h3><i class="fas fa-list-alt"></i> All Requests</h3></div>
        <div class="table-responsive">
          <table>
            <thead><tr><th>Vehicle</th><th>Category</th><th>Number</th><th>Date</th><th>Mechanic</th><th>Status</th><th>Cost</th><th>Payment</th><th>Action</th></tr></thead>
            <tbody>
            <?php if ($approved->num_rows > 0): ?>
              <?php while ($r = $approved->fetch_assoc()): ?>
              <tr>
                <td><?=$r['vehicle_name']?></td>
                <td><?=$r['vehicle_category']?></td>
                <td><?=$r['vehicle_number']?></td>
                <td><?=date('d M Y', strtotime($r['enquiry_date']))?></td>
                <td><?=$r['mname']??'<em style="color:var(--text-muted)">Pending</em>'?></td>
                <td><?=getStatusBadge($r['status'])?></td>
                <td><?=$r['service_cost']>0 ? '&#8377;'.number_format($r['service_cost'],2) : '-'?></td>
                <td><?=getPaymentBadge($r['payment_status'])?></td>
                <td>
                  <?php if ($r['payment_status'] == 'Payment Sent'): ?>
                    <a href="pay.php?id=<?=$r['id']?>" class="btn btn-sm btn-success"><i class="fas fa-credit-card"></i> Pay Now</a>
                  <?php elseif ($r['payment_status'] == 'Paid'): ?>
                    <a href="invoice.php?id=<?=$r['id']?>" class="btn btn-sm btn-info"><i class="fas fa-file-invoice"></i> Invoice</a>
                  <?php else: ?>
                    <span style="color:var(--text-muted);font-size:0.8rem">-</span>
                  <?php endif; ?>
                </td>
              </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr><td colspan="9"><div class="no-data"><div class="icon">🚗</div><p>No active requests</p></div></td></tr>
            <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
</div>

<!-- New Request Modal -->
<div class="modal-overlay" id="newReqModal">
  <div class="modal" style="
    background: rgba(11,29,58,0.97);
    border: 1px solid rgba(255,255,255,0.10);
    border-radius: 20px;
    backdrop-filter: blur(16px);
    box-shadow: 0 30px 80px rgba(0,0,0,0.4);
    font-family: 'Manrope', sans-serif;
    max-width: 560px;
    width: 100%;
    overflow: hidden;
  ">
    <div class="modal-header" style="
      background: rgba(255,255,255,0.04);
      border-bottom: 1px solid rgba(255,255,255,0.08);
      padding: 22px 28px;
      display: flex; align-items: center; justify-content: space-between;
    ">
      <h3 style="
        font-family: 'Bebas Neue', sans-serif; font-size: 26px;
        letter-spacing: 2px; color: #fff;
        display: flex; align-items: center; gap: 10px; margin: 0;
      ">
        <span style="color:#FF6B00;">⚡</span> NEW SERVICE REQUEST
      </h3>
      <button class="modal-close" onclick="document.getElementById('newReqModal').classList.remove('active')" style="
        background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15);
        color: #fff; width: 32px; height: 32px; border-radius: 50%;
        font-size: 18px; cursor: pointer; display: grid; place-items: center; transition: background 0.2s;
      ">&times;</button>
    </div>

    <form method="POST">
      <div class="modal-body" style="padding:28px; max-height:72vh; overflow-y:auto;">

        <!-- Section: Vehicle -->
        <div style="font-size:10px;font-weight:700;letter-spacing:2.5px;text-transform:uppercase;color:#FF6B00;margin-bottom:18px;display:flex;align-items:center;gap:10px;">
          Vehicle Information
          <span style="flex:1;height:1px;background:rgba(255,255,255,0.08);display:block;"></span>
        </div>

        <div class="form-row" style="display:flex;gap:16px;">
          <div class="form-group" style="flex:1;margin-bottom:18px;">
            <label style="display:block;font-size:11px;font-weight:700;color:#A8B8CC;letter-spacing:0.5px;margin-bottom:7px;">
              Vehicle Category <span style="color:#FF6B00;">*</span>
            </label>
            <select name="vehicle_category" required style="width:100%;background:rgba(255,255,255,0.06);border:1.5px solid rgba(255,255,255,0.15);border-radius:10px;padding:11px 14px;font-family:'Manrope',sans-serif;font-size:14px;font-weight:600;color:#fff;outline:none;cursor:pointer;transition:border-color 0.25s,box-shadow 0.25s;">
              <option value="" style="background:#0B1D3A;">Select Category</option>
              <option style="background:#0B1D3A;">Car</option>
              <option style="background:#0B1D3A;">Bike</option>
              <option style="background:#0B1D3A;">Truck</option>
              <option style="background:#0B1D3A;">Bus</option>
              <option style="background:#0B1D3A;">Auto</option>
              <option style="background:#0B1D3A;">Other</option>
            </select>
          </div>
          <div class="form-group" style="flex:1;margin-bottom:18px;">
            <label style="display:block;font-size:11px;font-weight:700;color:#A8B8CC;letter-spacing:0.5px;margin-bottom:7px;">
              Vehicle Name <span style="color:#FF6B00;">*</span>
            </label>
            <input type="text" name="vehicle_name" placeholder="e.g. Swift Dzire" required style="width:100%;background:rgba(255,255,255,0.06);border:1.5px solid rgba(255,255,255,0.15);border-radius:10px;padding:11px 14px;font-family:'Manrope',sans-serif;font-size:14px;font-weight:600;color:#fff;outline:none;transition:border-color 0.25s,box-shadow 0.25s;">
          </div>
        </div>

        <div class="form-row" style="display:flex;gap:16px;">
          <div class="form-group" style="flex:1;margin-bottom:18px;">
            <label style="display:block;font-size:11px;font-weight:700;color:#A8B8CC;letter-spacing:0.5px;margin-bottom:7px;">
              Vehicle Number <span style="color:#FF6B00;">*</span>
            </label>
            <input type="text" name="vehicle_number" placeholder="KA25HN2025" required
              pattern="[A-Z]{2}[0-9]{2}[A-Z]{1,2}[0-9]{4}"
              title="Format: 2 Letters, 2 Numbers, 1-2 Letters, 4 Numbers (e.g., KA25HX2020)"
              oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '')" maxlength="10"
              style="width:100%;background:rgba(255,255,255,0.06);border:1.5px solid rgba(255,255,255,0.15);border-radius:10px;padding:11px 14px;font-family:'Manrope',sans-serif;font-size:14px;font-weight:600;color:#fff;outline:none;transition:border-color 0.25s,box-shadow 0.25s;">
          </div>
          <div class="form-group" style="flex:1;margin-bottom:18px;">
            <label style="display:block;font-size:11px;font-weight:700;color:#A8B8CC;letter-spacing:0.5px;margin-bottom:7px;">Brand</label>
            <input type="text" name="vehicle_brand" placeholder="e.g. Maruti" style="width:100%;background:rgba(255,255,255,0.06);border:1.5px solid rgba(255,255,255,0.15);border-radius:10px;padding:11px 14px;font-family:'Manrope',sans-serif;font-size:14px;font-weight:600;color:#fff;outline:none;transition:border-color 0.25s,box-shadow 0.25s;">
          </div>
        </div>

        <div class="form-group" style="margin-bottom:18px;">
          <label style="display:block;font-size:11px;font-weight:700;color:#A8B8CC;letter-spacing:0.5px;margin-bottom:7px;">Model</label>
          <input type="text" name="vehicle_model" placeholder="e.g. 2020" style="width:100%;background:rgba(255,255,255,0.06);border:1.5px solid rgba(255,255,255,0.15);border-radius:10px;padding:11px 14px;font-family:'Manrope',sans-serif;font-size:14px;font-weight:600;color:#fff;outline:none;transition:border-color 0.25s,box-shadow 0.25s;">
        </div>

        <!-- Section: Issue -->
        <div style="font-size:10px;font-weight:700;letter-spacing:2.5px;text-transform:uppercase;color:#FF6B00;margin-bottom:14px;margin-top:4px;display:flex;align-items:center;gap:10px;">
          Issue Details
          <span style="flex:1;height:1px;background:rgba(255,255,255,0.08);display:block;"></span>
        </div>

        <div class="form-group" style="margin-bottom:18px;">
          <label style="display:block;font-size:11px;font-weight:700;color:#A8B8CC;letter-spacing:0.5px;margin-bottom:7px;">
            Problem Description <span style="color:#FF6B00;">*</span>
          </label>
          <textarea name="problem_description" rows="3" placeholder="Describe the issue in detail..." required style="width:100%;background:rgba(255,255,255,0.06);border:1.5px solid rgba(255,255,255,0.15);border-radius:10px;padding:11px 14px;font-family:'Manrope',sans-serif;font-size:14px;font-weight:600;color:#fff;outline:none;resize:vertical;min-height:85px;transition:border-color 0.25s,box-shadow 0.25s;"></textarea>
        </div>

        <!-- Section: Address (NEW) -->
        <div style="font-size:10px;font-weight:700;letter-spacing:2.5px;text-transform:uppercase;color:#FF6B00;margin-bottom:14px;display:flex;align-items:center;gap:10px;">
          Service Location
          <span style="flex:1;height:1px;background:rgba(255,255,255,0.08);display:block;"></span>
        </div>

        <div class="form-group" style="margin-bottom:6px;">
          <label style="display:block;font-size:11px;font-weight:700;color:#A8B8CC;letter-spacing:0.5px;margin-bottom:7px;">
            Service Address <span style="color:#FF6B00;">*</span>
            <span style="font-weight:400;color:#5a7a99;margin-left:6px;">(where to pick up / visit your vehicle)</span>
          </label>
          <div class="addr-wrap">
            <textarea
              id="serviceAddress"
              name="service_address"
              rows="3"
              placeholder="Enter your full address, or click 📍 to fetch current location..."
              required
              style="width:100%;background:rgba(255,255,255,0.06);border:1.5px solid rgba(255,255,255,0.15);border-radius:10px;padding:11px 44px 11px 14px;font-family:'Manrope',sans-serif;font-size:14px;font-weight:600;color:#fff;outline:none;resize:vertical;min-height:80px;transition:border-color 0.25s,box-shadow 0.25s;box-sizing:border-box;"
            ></textarea>
            <button type="button" class="addr-gps-btn" id="gpsBtn" onclick="fetchCurrentLocation()" title="Use my current location">
              📍
            </button>
          </div>
          <div class="addr-status" id="addrStatus"></div>
        </div>

      </div><!-- /modal-body -->

      <div class="modal-footer" style="padding:18px 28px;border-top:1px solid rgba(255,255,255,0.08);display:flex;gap:12px;justify-content:flex-end;">
        <button type="button"
          onclick="document.getElementById('newReqModal').classList.remove('active')"
          style="padding:11px 22px;border-radius:10px;border:1.5px solid rgba(255,255,255,0.15);background:rgba(255,255,255,0.06);color:#A8B8CC;font-family:'Manrope',sans-serif;font-size:13px;font-weight:700;cursor:pointer;transition:all 0.2s;">
          Cancel
        </button>
        <button type="submit" style="padding:11px 28px;border-radius:10px;border:none;background:linear-gradient(135deg,#FF6B00 0%,#FF8C00 100%);color:#fff;font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:2px;cursor:pointer;display:flex;align-items:center;gap:8px;box-shadow:0 6px 20px rgba(255,107,0,0.35);transition:transform 0.2s,box-shadow 0.2s;">
          <i class="fas fa-paper-plane"></i> SUBMIT REQUEST
        </button>
      </div>
    </form>
  </div>
</div>

<script>
/* ── GPS location fetch ─────────────────────────────────── */
function fetchCurrentLocation() {
  var btn    = document.getElementById('gpsBtn');
  var status = document.getElementById('addrStatus');
  var field  = document.getElementById('serviceAddress');

  if (!navigator.geolocation) {
    status.textContent = '⚠ Geolocation not supported by your browser.';
    status.className   = 'addr-status err';
    return;
  }

  btn.classList.add('loading');
  btn.textContent   = '⏳';
  btn.disabled      = true;
  status.textContent = 'Fetching your location…';
  status.className   = 'addr-status';

  navigator.geolocation.getCurrentPosition(
    function(pos) {
      var lat = pos.coords.latitude;
      var lng = pos.coords.longitude;

      // Reverse geocode using OpenStreetMap Nominatim (free, no API key)
      fetch('https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=' + lat + '&lon=' + lng)
        .then(function(res) { return res.json(); })
        .then(function(data) {
          if (data && data.display_name) {
            field.value        = data.display_name;
            status.textContent = '✅ Location fetched! You can edit it if needed.';
            status.className   = 'addr-status ok';
          } else {
            // Fallback: just fill coordinates
            field.value        = 'Lat: ' + lat.toFixed(6) + ', Lng: ' + lng.toFixed(6);
            status.textContent = '📍 Coordinates fetched (address lookup unavailable).';
            status.className   = 'addr-status ok';
          }
        })
        .catch(function() {
          field.value        = 'Lat: ' + lat.toFixed(6) + ', Lng: ' + lng.toFixed(6);
          status.textContent = '📍 Coordinates filled. You may edit the address manually.';
          status.className   = 'addr-status ok';
        })
        .finally(function() {
          btn.classList.remove('loading');
          btn.textContent = '📍';
          btn.disabled    = false;
        });
    },
    function(error) {
      var msgs = {
        1: 'Location permission denied. Please allow location access and try again.',
        2: 'Location unavailable. Please enter address manually.',
        3: 'Location request timed out. Please enter address manually.'
      };
      status.textContent = '⚠ ' + (msgs[error.code] || 'Could not get location.');
      status.className   = 'addr-status err';
      btn.classList.remove('loading');
      btn.textContent    = '📍';
      btn.disabled       = false;
    },
    { timeout: 10000, enableHighAccuracy: true }
  );
}

/* ── Tabs / sidebar / theme ─────────────────────────────── */
function switchTab(e, id) {
  document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
  document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
  e.target.classList.add('active');
  document.getElementById(id).classList.add('active');
}
function toggleSidebar() {
  document.getElementById('sidebar').classList.toggle('open');
  document.getElementById('sidebarOverlay').classList.toggle('active');
}
function toggleTheme() {
  const html   = document.documentElement;
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