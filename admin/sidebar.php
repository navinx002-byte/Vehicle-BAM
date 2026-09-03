<?php
$admin_photo  = $_SESSION['admin_photo'] ?? '';
$admin_name   = $_SESSION['admin_name']  ?? 'Admin';
$current_page = basename($_SERVER['PHP_SELF']);
$photo_url = !empty($admin_photo)
    ? '../uploads/profiles/' . htmlspecialchars($admin_photo)
    : '../assets/images/default.png';
?>
<aside class="sidebar" id="sidebar">
  <div class="sidebar-brand">
    <div class="brand-icon"><i class="fas fa-car-crash"></i></div>
    <div class="brand-name">Auto<span>Care</span>
      <span style="font-size:11px;background:rgba(230,57,70,0.2);color:#ff6b6b;padding:2px 8px;border-radius:4px;margin-left:5px;">Admin</span>
    </div>
  </div>
  <div class="sidebar-user">
    <img src="<?= $photo_url ?>" alt="Admin" onerror="this.src='../assets/images/default.png'">
    <div class="user-info">
      <div class="user-name"><?= htmlspecialchars($admin_name) ?></div>
      <div class="user-role">Administrator</div>
    </div>
  </div>
  <nav class="sidebar-nav">
    <div class="nav-label">Overview</div>
    <a href="dashboard.php" class="<?= $current_page==='dashboard.php' ? 'active' : '' ?>">
      <span class="nav-icon"><i class="fas fa-tachometer-alt"></i></span> Dashboard
    </a>
    <div class="nav-label">Management</div>
    <a href="customers.php" class="<?= $current_page==='customers.php' ? 'active' : '' ?>">
      <span class="nav-icon"><i class="fas fa-users"></i></span> Customers
    </a>
    <a href="mechanics.php" class="<?= $current_page==='mechanics.php' ? 'active' : '' ?>">
      <span class="nav-icon"><i class="fas fa-hard-hat"></i></span> Mechanics
    </a>
    <a href="requests.php" class="<?= $current_page==='requests.php' ? 'active' : '' ?>">
      <span class="nav-icon"><i class="fas fa-clipboard-list"></i></span> Service Requests
    </a>
    <a href="invoices.php" class="<?= $current_page==='invoices.php' ? 'active' : '' ?>">
      <span class="nav-icon"><i class="fas fa-file-invoice"></i></span> Invoices
    </a>
    <a href="feedback.php" class="<?= $current_page==='feedback.php' ? 'active' : '' ?>">
      <span class="nav-icon"><i class="fas fa-comment-alt"></i></span> Feedbacks
    </a>
    <div class="nav-label">Account</div>
    <a href="profile.php" class="<?= $current_page==='profile.php' ? 'active' : '' ?>">
      <span class="nav-icon"><i class="fas fa-user"></i></span> Profile
    </a>
    <a href="change_password.php" class="<?= $current_page==='change_password.php' ? 'active' : '' ?>">
      <span class="nav-icon"><i class="fas fa-key"></i></span> Change Password
    </a>
  </nav>
  <div class="sidebar-footer">
    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
  </div>
</aside>