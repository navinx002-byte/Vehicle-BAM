<?php
session_start();
require_once '../includes/config.php';
if (!isLoggedIn('admin')) redirect('login.php');
$msg = $err = '';
$aid = $_SESSION['admin_id'];
if (isset($_POST['action']) && $_POST['action'] == 'add_walkin') {
    $cid = (int)$_POST['customer_id'];
    $cat = sanitize($conn, $_POST['vehicle_category']);
    $vname = sanitize($conn, $_POST['vehicle_name']);
    $vnum = sanitize($conn, $_POST['vehicle_number']);
    $brand = sanitize($conn, $_POST['vehicle_brand']);
    $model = sanitize($conn, $_POST['vehicle_model']);
    $prob = sanitize($conn, $_POST['problem_description']);
    $conn->query("INSERT INTO service_requests (customer_id,vehicle_category,vehicle_name,vehicle_number,vehicle_brand,vehicle_model,problem_description,added_by) VALUES ($cid,'$cat','$vname','$vnum','$brand','$model','$prob','admin')");
    $msg = "Walk-in request added!";
}
if (isset($_POST['action']) && $_POST['action'] == 'approve') {
    $id = (int)$_POST['req_id'];
    $mid = (int)$_POST['mechanic_id'];
    $cost = (float)$_POST['service_cost'];
    $conn->query("UPDATE service_requests SET status='Approved', mechanic_id=$mid, service_cost=$cost, approved_date=NOW() WHERE id=$id");
    $msg = "Request approved and mechanic assigned!";
}
if (isset($_POST['action']) && $_POST['action'] == 'send_payment') {
    $id = (int)$_POST['req_id'];
    $req = $conn->query("SELECT * FROM service_requests WHERE id=$id AND status='Repairing Done'")->fetch_assoc();
    if ($req) {
        $order = createRazorpayOrder($req['service_cost'], 'REQ-'.$id);
        $rzp_order_id = $order['id'] ?? '';
        $conn->query("UPDATE service_requests SET payment_status='Payment Sent', razorpay_order_id='$rzp_order_id' WHERE id=$id");
        $conn->query("INSERT INTO payment_notifications (request_id,customer_id,amount,razorpay_order_id) VALUES ($id,{$req['customer_id']},{$req['service_cost']},'$rzp_order_id')");
        $msg = "Payment notification sent to customer!";
    } else { $err = "Request must be in Repairing Done status."; }
}
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM service_requests WHERE id=$id");
    $msg = "Request deleted.";
}
$requests = $conn->query("SELECT sr.*, c.full_name as cname, c.email as cemail, m.full_name as mname FROM service_requests sr LEFT JOIN customers c ON sr.customer_id=c.id LEFT JOIN mechanics m ON sr.mechanic_id=m.id ORDER BY sr.enquiry_date DESC");
$mechanics_list = $conn->query("SELECT id, full_name, specialization FROM mechanics WHERE status='active'");
$customers_list = $conn->query("SELECT id, full_name FROM customers WHERE status='active'");
$view_req = null;
if (isset($_GET['view'])) {
    $view_req = $conn->query("SELECT sr.*, c.full_name as cname, c.email as cemail, c.mobile as cmobile, m.full_name as mname FROM service_requests sr LEFT JOIN customers c ON sr.customer_id=c.id LEFT JOIN mechanics m ON sr.mechanic_id=m.id WHERE sr.id=".(int)$_GET['view'])->fetch_assoc();
}
$total_feedback = $conn->query("SELECT COUNT(*) as c FROM feedback WHERE is_read=0")->fetch_assoc()['c'];
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Requests - Admin</title>
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
      <div class="topbar-left"><h1>Service Requests</h1><p>Manage all service requests</p></div>
    </div>
    <div class="topbar-right">
      <button class="btn btn-sm btn-primary" onclick="document.getElementById('walkInModal').classList.add('active')"><i class="fas fa-plus"></i> Walk-in Request</button>
      <button class="theme-toggle" onclick="toggleTheme()" id="themeBtn"><i class="fas fa-moon"></i></button>
    </div>
  </div>
  <div class="page-content">
    <?php if ($msg): ?><div class="alert alert-success"><?=$msg?></div><?php endif; ?>
    <?php if ($err): ?><div class="alert alert-danger"><?=$err?></div><?php endif; ?>
    <?php if ($view_req): ?>
    <div class="card" style="margin-bottom:1.5rem">
      <div class="card-header">
        <h3><i class="fas fa-file-alt"></i> Request Details #<?=$view_req['id']?></h3>
        <a href="requests.php" class="btn btn-sm btn-secondary">Back to List</a>
      </div>
      <div class="card-body">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:2rem;margin-bottom:1.5rem">
          <div>
            <h4 style="margin-bottom:0.8rem;color:var(--primary)">Customer Info</h4>
            <p><strong>Name:</strong> <?=$view_req['cname']??'Walk-in'?></p>
            <p><strong>Email:</strong> <?=$view_req['cemail']??'-'?></p>
            <p><strong>Mobile:</strong> <?=$view_req['cmobile']??'-'?></p>
          </div>
          <div>
            <h4 style="margin-bottom:0.8rem;color:var(--primary)">Vehicle Info</h4>
            <p><strong>Name:</strong> <?=$view_req['vehicle_name']?></p>
            <p><strong>Number:</strong> <?=$view_req['vehicle_number']?></p>
            <p><strong>Category:</strong> <?=$view_req['vehicle_category']?></p>
            <p><strong>Brand/Model:</strong> <?=$view_req['vehicle_brand']?> <?=$view_req['vehicle_model']?></p>
          </div>
        </div>
        <div style="background:var(--bg-dark);padding:1rem;border-radius:10px;margin-bottom:1.5rem">
          <strong>Problem Description:</strong><br>
          <p style="margin-top:0.5rem;color:var(--text-muted)"><?=$view_req['problem_description']?></p>
        </div>
        <div style="display:flex;gap:1rem;flex-wrap:wrap;align-items:center">
          <div><?=getStatusBadge($view_req['status'])?></div>
          <div><?=getPaymentBadge($view_req['payment_status'])?></div>
          <?php if ($view_req['service_cost']>0): ?>
          <div style="font-weight:700">Cost: &#8377;<?=number_format($view_req['service_cost'],2)?></div>
          <?php endif; ?>
          <?php if ($view_req['mname']): ?>
          <div style="color:var(--text-muted)">Mechanic: <strong><?=$view_req['mname']?></strong></div>
          <?php endif; ?>
        </div>
        <?php if ($view_req['status'] == 'Pending'): ?>
        <hr style="border-color:var(--border);margin:1.5rem 0">
        <h4 style="margin-bottom:1rem">Approve & Assign Mechanic</h4>
        <form method="POST" style="display:flex;gap:1rem;flex-wrap:wrap;align-items:flex-end">
          <input type="hidden" name="action" value="approve">
          <input type="hidden" name="req_id" value="<?=$view_req['id']?>">
          <div class="form-group" style="flex:1;min-width:150px">
            <label>Assign Mechanic</label>
            <select name="mechanic_id" class="form-control" required>
              <option value="">Select Mechanic</option>
              <?php $mechanics_list->data_seek(0); while($m = $mechanics_list->fetch_assoc()): ?>
              <option value="<?=$m['id']?>"><?=$m['full_name']?> (<?=$m['specialization']?>)</option>
              <?php endwhile; ?>
            </select>
          </div>
          <div class="form-group" style="flex:1;min-width:120px">
            <label>Service Cost (₹)</label>
            <input type="number" name="service_cost" class="form-control" placeholder="0.00" step="0.01" required>
          </div>
          <button type="submit" class="btn-primary"><i class="fas fa-check"></i> Approve Request</button>
        </form>
        <?php endif; ?>
        <?php if ($view_req['status'] == 'Repairing Done' && $view_req['payment_status'] == 'Unpaid'): ?>
        <hr style="border-color:var(--border);margin:1.5rem 0">
        <h4>Send Payment Request to Customer</h4>
        <p style="color:var(--text-muted);margin-bottom:1rem">This will send a payment notification to the customer for &#8377;<?=number_format($view_req['service_cost'],2)?>.</p>
        <form method="POST">
          <input type="hidden" name="action" value="send_payment">
          <input type="hidden" name="req_id" value="<?=$view_req['id']?>">
          <button type="submit" class="btn-primary"><i class="fas fa-paper-plane"></i> Send Payment Notification</button>
        </form>
        <?php endif; ?>
      </div>
    </div>
    <?php endif; ?>
    <div class="card">
      <div class="table-responsive">
        <table>
          <thead><tr><th>ID</th><th>Customer</th><th>Vehicle</th><th>Number</th><th>Date</th><th>Mechanic</th><th>Status</th><th>Cost</th><th>Payment</th><th>Actions</th></tr></thead>
          <tbody>
          <?php $requests->data_seek(0); while ($r = $requests->fetch_assoc()): ?>
          <tr>
            <td>#<?=$r['id']?></td>
            <td><?=$r['cname']??'<em>Walk-in</em>'?></td>
            <td><?=$r['vehicle_name']?><br><small style="color:var(--text-muted)"><?=$r['vehicle_category']?></small></td>
            <td><?=$r['vehicle_number']?></td>
            <td><?=date('d M Y', strtotime($r['enquiry_date']))?></td>
            <td><?=$r['mname']??'<span style="color:var(--text-muted)">Unassigned</span>'?></td>
            <td><?=getStatusBadge($r['status'])?></td>
            <td><?=$r['service_cost']>0?'&#8377;'.number_format($r['service_cost'],2):'-'?></td>
            <td><?=getPaymentBadge($r['payment_status'])?></td>
            <td>
              <div class="action-btns">
                <a href="?view=<?=$r['id']?>" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                <a href="?delete=<?=$r['id']?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete request?')"><i class="fas fa-trash"></i></a>
              </div>
            </td>
          </tr>
          <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
</div>
<div class="modal-overlay" id="walkInModal">
  <div class="modal">
    <div class="modal-header"><h3>Add Walk-in Request</h3><button class="modal-close" onclick="document.getElementById('walkInModal').classList.remove('active')">&times;</button></div>
    <form method="POST">
      <input type="hidden" name="action" value="add_walkin">
      <div class="modal-body">
        <div class="form-group">
          <label>Customer</label>
          <select name="customer_id" class="form-control" required>
            <option value="">Select Customer</option>
            <?php $customers_list->data_seek(0); while($c = $customers_list->fetch_assoc()): ?>
            <option value="<?=$c['id']?>"><?=$c['full_name']?></option>
            <?php endwhile; ?>
          </select>
        </div>
        <div class="form-row">
          <div class="form-group"><label>Vehicle Category</label>
            <select name="vehicle_category" class="form-control" required>
              <option value="">Select</option>
              <option>Car</option><option>Bike</option><option>Truck</option><option>Bus</option><option>Auto</option><option>Other</option>
            </select>
          </div>
          <div class="form-group"><label>Vehicle Name</label><input type="text" name="vehicle_name" class="form-control" required></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label>Vehicle Number</label><input type="text" name="vehicle_number" class="form-control" required></div>
          <div class="form-group"><label>Brand</label><input type="text" name="vehicle_brand" class="form-control"></div>
        </div>
        <div class="form-group"><label>Model</label><input type="text" name="vehicle_model" class="form-control"></div>
        <div class="form-group"><label>Problem Description</label><textarea name="problem_description" class="form-control" rows="3" required></textarea></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="document.getElementById('walkInModal').classList.remove('active')">Cancel</button>
        <button type="submit" class="btn-primary">Add Request</button>
      </div>
    </form>
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
</script>
</body></html>
