<?php
session_start();
require_once '../includes/config.php';
if (!isLoggedIn('admin')) redirect('login.php');
$msg = $err = '';
if (isset($_POST['action']) && $_POST['action'] == 'add') {
    $name = sanitize($conn, $_POST['full_name']);
    $email = sanitize($conn, $_POST['email']);
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $mobile = sanitize($conn, $_POST['mobile']);
    $address = sanitize($conn, $_POST['address']);
    $check = $conn->query("SELECT id FROM customers WHERE email='$email'");
    if ($check->num_rows > 0) { $err = "Email already exists."; }
    else {
        $conn->query("INSERT INTO customers (full_name,email,password,mobile,address) VALUES ('$name','$email','$pass','$mobile','$address')");
        $msg = "Customer added!";
    }
}
if (isset($_POST['action']) && $_POST['action'] == 'update') {
    $id = (int)$_POST['id'];
    $name = sanitize($conn, $_POST['full_name']);
    $mobile = sanitize($conn, $_POST['mobile']);
    $address = sanitize($conn, $_POST['address']);
    $status = sanitize($conn, $_POST['status']);
    $conn->query("UPDATE customers SET full_name='$name',mobile='$mobile',address='$address',status='$status' WHERE id=$id");
    $msg = "Customer updated!";
}
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM customers WHERE id=$id");
    $msg = "Customer deleted.";
}
if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    $conn->query("UPDATE customers SET status = CASE WHEN status='active' THEN 'inactive' ELSE 'active' END WHERE id=$id");
    $msg = "Status updated.";
}
$customers = $conn->query("SELECT c.*, (SELECT COUNT(*) FROM service_requests WHERE customer_id=c.id) as req_count, (SELECT COALESCE(SUM(total_amount),0) FROM invoices WHERE customer_id=c.id) as total_paid FROM customers c ORDER BY c.created_at DESC");
$edit_cust = null;
if (isset($_GET['edit'])) {
    $edit_cust = $conn->query("SELECT * FROM customers WHERE id=".(int)$_GET['edit'])->fetch_assoc();
}
$total_feedback = $conn->query("SELECT COUNT(*) as c FROM feedback WHERE is_read=0")->fetch_assoc()['c'];
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Customers - Admin</title>
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
      <div class="topbar-left"><h1>Customers</h1><p>Manage customer accounts</p></div>
    </div>
    <div class="topbar-right">
      <button class="btn btn-sm btn-primary" onclick="document.getElementById('addModal').classList.add('active')"><i class="fas fa-plus"></i> Add Customer</button>
      <button class="theme-toggle" onclick="toggleTheme()" id="themeBtn"><i class="fas fa-moon"></i></button>
    </div>
  </div>
  <div class="page-content">
    <?php if ($msg): ?><div class="alert alert-success"><?=$msg?></div><?php endif; ?>
    <?php if ($err): ?><div class="alert alert-danger"><?=$err?></div><?php endif; ?>
    <div class="card">
      <div class="table-responsive">
        <table>
          <thead><tr><th>Name</th><th>Email</th><th>Mobile</th><th>Requests</th><th>Total Paid</th><th>Status</th><th>Joined</th><th>Actions</th></tr></thead>
          <tbody>
          <?php while ($r = $customers->fetch_assoc()): ?>
          <tr>
            <td><img src="../uploads/profiles/<?=$r['profile_photo']?>" style="width:32px;height:32px;border-radius:50%;object-fit:cover;margin-right:8px;border:1px solid var(--border)" onerror="this.style.display='none'"><?=$r['full_name']?></td>
            <td><?=$r['email']?></td>
            <td><?=$r['mobile']?></td>
            <td><span class="badge badge-approved"><?=$r['req_count']?> requests</span></td>
            <td>&#8377;<?=number_format($r['total_paid'],2)?></td>
            <td><span class="badge badge-<?=$r['status']?>"><?=ucfirst($r['status'])?></span></td>
            <td><?=date('d M Y', strtotime($r['created_at']))?></td>
            <td>
              <div class="action-btns">
                <a href="?edit=<?=$r['id']?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                <a href="?toggle=<?=$r['id']?>" class="btn btn-sm btn-info" title="Toggle Status"><i class="fas fa-toggle-on"></i></a>
                <a href="?delete=<?=$r['id']?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete customer?')"><i class="fas fa-trash"></i></a>
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
<div class="modal-overlay" id="addModal">
  <div class="modal">
    <div class="modal-header"><h3>Add Customer</h3><button class="modal-close" onclick="document.getElementById('addModal').classList.remove('active')">&times;</button></div>
    <form method="POST">
      <input type="hidden" name="action" value="add">
      <div class="modal-body">
        <div class="form-row">
          <div class="form-group"><label>Full Name</label><input type="text" name="full_name" class="form-control" required></div>
          <div class="form-group"><label>Mobile</label><input type="tel" name="mobile" class="form-control" required></div>
        </div>
        <div class="form-group"><label>Email</label><input type="email" name="email" class="form-control" required></div>
        <div class="form-group"><label>Password</label><input type="password" name="password" class="form-control" required></div>
        <div class="form-group"><label>Address</label><textarea name="address" class="form-control" rows="2"></textarea></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="document.getElementById('addModal').classList.remove('active')">Cancel</button>
        <button type="submit" class="btn-primary">Add Customer</button>
      </div>
    </form>
  </div>
</div>
<?php if ($edit_cust): ?>
<div class="modal-overlay active" id="editModal">
  <div class="modal">
    <div class="modal-header"><h3>Edit Customer</h3><button class="modal-close" onclick="window.location='customers.php'">&times;</button></div>
    <form method="POST">
      <input type="hidden" name="action" value="update">
      <input type="hidden" name="id" value="<?=$edit_cust['id']?>">
      <div class="modal-body">
        <div class="form-row">
          <div class="form-group"><label>Full Name</label><input type="text" name="full_name" class="form-control" value="<?=$edit_cust['full_name']?>" required></div>
          <div class="form-group"><label>Mobile</label><input type="tel" name="mobile" class="form-control" value="<?=$edit_cust['mobile']?>" required></div>
        </div>
        <div class="form-group"><label>Address</label><textarea name="address" class="form-control" rows="2"><?=$edit_cust['address']?></textarea></div>
        <div class="form-group"><label>Status</label>
          <select name="status" class="form-control">
            <option value="active" <?=$edit_cust['status']=='active'?'selected':''?>>Active</option>
            <option value="inactive" <?=$edit_cust['status']=='inactive'?'selected':''?>>Inactive</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <a href="customers.php" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn-primary">Save Changes</button>
      </div>
    </form>
  </div>
</div>
<?php endif; ?>
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
