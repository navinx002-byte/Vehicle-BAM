<?php
$mech_photo   = $_SESSION['mechanic_photo'] ?? '';
$mech_name    = $_SESSION['mechanic_name']  ?? 'Mechanic';
$current_page = basename($_SERVER['PHP_SELF']);
$photo_url = !empty($mech_photo)
    ? '../uploads/profiles/' . htmlspecialchars($mech_photo)
    : '../assets/images/default.png';
?>
<aside class="sidebar" id="sidebar" style="--bg-sidebar:#064e3b;">
  <div class="sidebar-brand">
    <div class="brand-icon" style="background:#10b981;"><i class="fas fa-tools"></i></div>
    <div class="brand-name">Auto<span style="color:#6ee7b7;">Care</span>
      <span style="font-size:11px;background:rgba(16,185,129,0.2);color:#6ee7b7;padding:2px 8px;border-radius:4px;margin-left:5px;">Mechanic</span>
    </div>
  </div>
  <div class="sidebar-user">
    <img src="<?= $photo_url ?>" alt="" onerror="this.src='../assets/images/default.png'">
    <div class="user-info">
      <div class="user-name"><?= htmlspecialchars($mech_name) ?></div>
      <div class="user-role">Mechanic</div>
    </div>
  </div>
  <nav class="sidebar-nav">
    <div class="nav-label">Overview</div>
    <a href="dashboard.php" class="<?= $current_page==='dashboard.php'?'active':'' ?>"><span class="nav-icon"><i class="fas fa-tachometer-alt"></i></span> Dashboard</a>
    <div class="nav-label">Work</div>
    <a href="assignments.php" class="<?= $current_page==='assignments.php'?'active':'' ?>"><span class="nav-icon"><i class="fas fa-clipboard-list"></i></span> My Assignments</a>
    <a href="completed.php" class="<?= $current_page==='completed.php'?'active':'' ?>"><span class="nav-icon"><i class="fas fa-check-circle"></i></span> Completed Work</a>
    <a href="feedback.php" class="<?= $current_page==='feedback.php'?'active':'' ?>"><span class="nav-icon"><i class="fas fa-comment-alt"></i></span> Send Feedback</a>
    <div class="nav-label">Account</div>
    <a href="profile.php" class="<?= $current_page==='profile.php'?'active':'' ?>"><span class="nav-icon"><i class="fas fa-user"></i></span> My Profile</a>
    <a href="change-password.php" class="<?= $current_page==='change-password.php'?'active':'' ?>"><span class="nav-icon"><i class="fas fa-key"></i></span> Change Password</a>
  </nav>
  <div class="sidebar-footer">
    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
  </div>
</aside>
