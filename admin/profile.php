<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
if (!isLoggedIn('admin')) redirect('login.php');
$aid = $_SESSION['admin_id'];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action']==='profile') {
    $name = sanitize($conn, $_POST['full_name']);
    $admin = $conn->query("SELECT * FROM admin WHERE id=$aid")->fetch_assoc();
    $photo = $admin['profile_photo']; 
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $ext = strtolower(pathinfo($_FILES['profile_photo']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            $new_filename = 'admin_' . $aid . '_' . time() . '.' . $ext;
            $upload_dir   = __DIR__ . '/../uploads/profiles/';
            if (!empty($photo) && $photo !== 'default.png' && file_exists($upload_dir . $photo)) {
                unlink($upload_dir . $photo);
            }
            if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $upload_dir . $new_filename)) {
                $photo = $new_filename;
                $_SESSION['admin_photo'] = $photo;
            }
        }
    }
    $_SESSION['admin_name'] = $name;
    $conn->query("UPDATE admin SET full_name='$name', profile_photo='$photo' WHERE id=$aid");
    $msg = "Profile updated successfully!";
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action']==='password') {
    $admin = $conn->query("SELECT * FROM admin WHERE id=$aid")->fetch_assoc();
    $old = $_POST['old_password']; $new = $_POST['new_password']; $cnf = $_POST['confirm_password'];
    if (!password_verify($old, $admin['password']))  { $err = "Current password is incorrect!"; }
    elseif ($new !== $cnf)                           { $err = "New passwords do not match!"; }
    elseif (strlen($new) < 6)                        { $err = "Password must be at least 6 characters!"; }
    else {
        $h = password_hash($new, PASSWORD_DEFAULT);
        $conn->query("UPDATE admin SET password='$h' WHERE id=$aid");
        $msg = "Password changed successfully!";
    }
}
$admin = $conn->query("SELECT * FROM admin WHERE id=$aid")->fetch_assoc();
$total_feedback = $conn->query("SELECT COUNT(*) as c FROM feedback WHERE is_read=0")->fetch_assoc()['c'];
$default_photo = '../uploads/profiles/default.png';
$sidebar_photo = !empty($_SESSION['admin_photo'])
    ? '../uploads/profiles/' . htmlspecialchars($_SESSION['admin_photo'])
    : $default_photo;
$photo_src = !empty($admin['profile_photo'])
    ? '../uploads/profiles/' . htmlspecialchars($admin['profile_photo'])
    : $default_photo;
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Profile - Admin</title>
<link rel="stylesheet" href="../css/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
  .sidebar-avatar { background: var(--bg-card, #1e293b); }
  #profilePreview  { background: var(--bg-card, #1e293b); }
</style>
</head>
<body>
<div class="dashboard-wrapper">
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>
<aside class="sidebar" id="sidebar">
  <div class="sidebar-brand"><i class="fas fa-car-crash"></i><div><div>AutoCare</div><span>Admin Panel</span></div></div>
  <div class="sidebar-user">
    <img id="sidebarAvatar"
         src="<?= $sidebar_photo ?>"
         class="sidebar-avatar"
         onerror="this.onerror=null;this.src='<?= $default_photo ?>'">
    <div class="sidebar-user-info">
      <div class="name"><?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?></div>
      <span class="role">Administrator</span>
    </div>
  </div>
  <nav class="sidebar-nav">
    <div class="nav-section-title">Overview</div>
    <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
    <div class="nav-section-title">Management</div>
    <a href="customers.php"><i class="fas fa-users"></i> Customers</a>
    <a href="mechanics.php"><i class="fas fa-tools"></i> Mechanics</a>
    <a href="requests.php"><i class="fas fa-clipboard-list"></i> Service Requests</a>
    <a href="invoices.php"><i class="fas fa-file-invoice"></i> Invoices</a>
    <a href="feedback.php"><i class="fas fa-comments"></i> Feedback
      <?php if ($total_feedback > 0): ?>
        <span class="badge-count"><?= $total_feedback ?></span>
      <?php endif; ?>
    </a>
    <div class="nav-section-title">Account</div>
    <a href="profile.php" class="active"><i class="fas fa-user-edit"></i> Profile</a>
    <a href="change_password.php"><i class="fas fa-lock"></i> Change Password</a>
  </nav>
  <div class="sidebar-footer">
    <a href="logout.php" class="btn btn-danger" style="width:100%;justify-content:center">
      <i class="fas fa-sign-out-alt"></i> Logout
    </a>
  </div>
</aside>
<div class="main-content">
  <div class="topbar">
    <div style="display:flex;align-items:center;gap:12px">
      <button class="hamburger" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
      <div class="topbar-left"><h1>My Profile</h1><p>Manage your account settings</p></div>
    </div>
    <div class="topbar-right">
      <button class="theme-toggle" onclick="toggleTheme()" id="themeBtn"><i class="fas fa-moon"></i></button>
    </div>
  </div>
  <div class="page-content">
    <?php if (!empty($msg)): ?>
      <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $msg ?></div>
    <?php endif; ?>
    <?php if (!empty($err)): ?>
      <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?= $err ?></div>
    <?php endif; ?>
    <div class="card" style="margin-bottom:24px;">
      <div class="card-body" style="display:flex;align-items:center;gap:24px;padding:24px;">
        <div style="position:relative;flex-shrink:0;">
          <img id="profilePreview"
               src="<?= $photo_src ?>"
               alt="Profile"
               onerror="this.onerror=null;this.src='<?= $default_photo ?>'"
               style="width:90px;height:90px;border-radius:50%;object-fit:cover;border:3px solid var(--primary);">
          <label for="photoInputHeader"
                 style="position:absolute;bottom:0;right:0;background:var(--primary);color:#fff;width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:12px;">
            <i class="fas fa-camera"></i>
          </label>
          <input type="file" id="photoInputHeader" accept="image/*" style="display:none;"
                 onchange="previewImage(this)">
        </div>
        <div>
          <h2 style="margin:0 0 4px;"><?= htmlspecialchars($admin['full_name']) ?></h2>
          <p style="color:var(--text-muted);margin:0 0 8px;"><?= htmlspecialchars($admin['email']) ?></p>
          <span class="badge badge-approved"><i class="fas fa-user-shield"></i> Administrator</span>
        </div>
      </div>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;align-items:start;">
      <div class="card">
        <div class="card-header">
          <h3><i class="fas fa-edit"></i> Edit Profile</h3>
        </div>
        <div class="card-body" style="padding:24px;">
          <form method="POST" enctype="multipart/form-data" id="profileForm">
            <input type="hidden" name="action" value="profile">
            <input type="file" id="photoInput" name="profile_photo" accept="image/*" style="display:none;">
            <div class="form-group" style="margin-bottom:16px;">
              <label style="display:block;margin-bottom:6px;font-weight:600;">Full Name</label>
              <input type="text" name="full_name" class="form-control"
                     value="<?= htmlspecialchars($admin['full_name']) ?>" required
                     style="width:100%;padding:10px 14px;border-radius:8px;border:1px solid var(--border);background:var(--bg-dark);color:var(--text);">
            </div>
            <div class="form-group" style="margin-bottom:20px;">
              <label style="display:block;margin-bottom:6px;font-weight:600;">Email (Read Only)</label>
              <input type="email" class="form-control"
                     value="<?= htmlspecialchars($admin['email']) ?>" disabled
                     style="width:100%;padding:10px 14px;border-radius:8px;border:1px solid var(--border);background:var(--bg-dark);color:var(--text-muted);cursor:not-allowed;">
            </div>
            <button type="submit" class="btn btn-sm btn-primary">
              <i class="fas fa-save"></i> Update Profile
            </button>
          </form>
        </div>
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
(function() {
  const t = localStorage.getItem('theme') || 'dark';
  document.documentElement.setAttribute('data-theme', t);
  document.getElementById('themeBtn').innerHTML = t === 'dark' ? '<i class="fas fa-moon"></i>' : '<i class="fas fa-sun"></i>';
})();
function previewImage(input) {
  if (!input.files || !input.files[0]) return;
  const file = input.files[0];
  const reader = new FileReader();
  reader.onload = function(e) {
    const src = e.target.result;
    document.getElementById('profilePreview').src = src;
    document.getElementById('sidebarAvatar').src = src;
  };
  reader.readAsDataURL(file);
  try {
    const dt = new DataTransfer();
    dt.items.add(file);
    document.getElementById('photoInput').files = dt.files;
  } catch(e) {
    console.warn('DataTransfer not supported, photo sync skipped.');
  }
}
</script>
</body>
</html>