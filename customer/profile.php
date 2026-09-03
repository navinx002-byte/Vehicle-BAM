<?php
session_start();
require_once '../includes/config.php';
if (!isLoggedIn('customer')) redirect('login.php');
$cid = $_SESSION['customer_id'];
$user = $conn->query("SELECT * FROM customers WHERE id=$cid")->fetch_assoc();
$msg = $err = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize($conn, $_POST['full_name']);
    $mobile = sanitize($conn, $_POST['mobile']);
    $address = sanitize($conn, $_POST['address']);
    $photo = $user['profile_photo'];
    if (!empty($_FILES['photo']['name'])) {
        $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $photo = 'cust_' . time() . '.' . $ext;
        move_uploaded_file($_FILES['photo']['tmp_name'], UPLOAD_PATH . 'profiles/' . $photo);
    }
    $conn->query("UPDATE customers SET full_name='$name',mobile='$mobile',address='$address',profile_photo='$photo' WHERE id=$cid");
    $_SESSION['customer_name'] = $name;
    $_SESSION['customer_photo'] = $photo;
    $msg = "Profile updated!";
    $user = $conn->query("SELECT * FROM customers WHERE id=$cid")->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Profile - AutoCare</title>
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
      <button class="hamburger" onclick="document.getElementById('sidebar').classList.toggle('open')"><i class="fas fa-bars"></i></button>
      <div class="topbar-left"><h1>My Profile</h1><p>Manage your account</p></div>
    </div>
    <div class="topbar-right"><button class="theme-toggle" onclick="toggleTheme()" id="themeBtn"><i class="fas fa-moon"></i></button></div>
  </div>
  <div class="page-content">
    <?php if ($msg): ?><div class="alert alert-success"><?=$msg?></div><?php endif; ?>
    <div class="card" style="max-width:600px">
      <div class="card-body">
        <form method="POST" enctype="multipart/form-data">
          <div class="profile-header">
            <div class="profile-avatar-wrap">
              <img src="../uploads/profiles/<?=$user['profile_photo']?>" class="profile-avatar" id="avatarPreview" onerror="this.src='../uploads/profiles/default.png'">
              <label for="photoInput" class="avatar-edit-btn"><i class="fas fa-camera"></i></label>
              <input type="file" id="photoInput" name="photo" accept="image/*" style="display:none" onchange="previewImg(this)">
            </div>
            <div><h3><?=$user['full_name']?></h3><p style="color:var(--text-muted)"><?=$user['email']?></p></div>
          </div>
          <div class="form-row">
            <div class="form-group"><label>Full Name</label><input type="text" name="full_name" class="form-control" value="<?=$user['full_name']?>" required></div>
            <div class="form-group"><label>Mobile</label><input type="tel" name="mobile" class="form-control" value="<?=$user['mobile']?>" required></div>
          </div>
          <div class="form-group"><label>Email (read-only)</label><input type="email" class="form-control" value="<?=$user['email']?>" disabled></div>
          <div class="form-group"><label>Address</label><textarea name="address" class="form-control" rows="3"><?=$user['address']?></textarea></div>
          <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Save Changes</button>
        </form>
      </div>
    </div>
  </div>
</div>
</div>
<script>
function previewImg(input) {
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = e => document.getElementById('avatarPreview').src = e.target.result;
    reader.readAsDataURL(input.files[0]);
  }
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
