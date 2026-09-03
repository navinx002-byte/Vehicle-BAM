<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
if (!isLoggedIn('mechanic')) redirect('login.php');
$mid = $_SESSION['mechanic_id'];
$mechanic = $conn->query("SELECT * FROM mechanics WHERE id=$mid")->fetch_assoc();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject = sanitize($conn, $_POST['subject']);
    $message = sanitize($conn, $_POST['message']);
    $conn->query("INSERT INTO feedback (sender_type, sender_id, subject, message) VALUES ('mechanic', $mid, '$subject', '$message')");
    $msg = "Feedback sent to admin successfully!";
}
$my_feedbacks = $conn->query("SELECT * FROM feedback WHERE sender_type='mechanic' AND sender_id=$mid ORDER BY created_at DESC");
if (!$my_feedbacks) die("Query error: " . $conn->error);
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Send Feedback - <?= SITE_NAME ?></title>
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
      <span class="topbar-title">Send Feedback</span>
    </div>
    <div class="topbar-right"><button class="theme-toggle" onclick="toggleTheme()"><i class="fas fa-moon theme-icon"></i></button></div>
  </header>
  <div class="page-content">
    <?php if (!empty($msg)): ?>
      <div class="alert alert-success" style="margin-bottom:20px;">
        <i class="fas fa-check-circle"></i> <?= $msg ?>
      </div>
    <?php endif; ?>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;align-items:start;">
      <div class="form-card">
        <div class="form-card-header">
          <div class="icon" style="background:rgba(16,185,129,0.1);color:#10b981;"><i class="fas fa-comment-alt"></i></div>
          <h3>Send Feedback to Admin</h3>
        </div>
        <form method="POST">
          <div class="form-group">
            <label>Subject <span class="required">*</span></label>
            <input type="text" name="subject" class="form-control" placeholder="e.g. Tool request, Issue report..." required>
          </div>
          <div class="form-group">
            <label>Message <span class="required">*</span></label>
            <textarea name="message" class="form-control" placeholder="Write your feedback, suggestion, or report here..." style="min-height:160px;" required></textarea>
          </div>
          <button type="submit" class="btn btn-success"><i class="fas fa-paper-plane"></i> Send Feedback</button>
        </form>
      </div>
      <div class="table-card">
        <div class="table-card-header">
          <h3><i class="fas fa-history" style="color:#10b981"></i> My Feedbacks</h3>
          <span class="badge badge-info"><?= $my_feedbacks->num_rows ?></span>
        </div>
        <?php if ($my_feedbacks->num_rows === 0): ?>
          <div class="empty-state" style="padding:40px 20px;">
            <i class="fas fa-comment-slash"></i>
            <p>No feedback sent yet. Use the form to contact admin.</p>
          </div>
        <?php else: while ($fb = $my_feedbacks->fetch_assoc()): ?>
          <div style="padding:16px 20px;border-bottom:1px solid var(--border);">
            <div style="display:flex;justify-content:space-between;margin-bottom:6px;">
              <strong style="font-size:14px;"><?= htmlspecialchars($fb['subject']) ?></strong>
              <div style="display:flex;align-items:center;gap:8px;">
                <span style="font-size:12px;color:var(--text-muted);"><?= date('d M Y', strtotime($fb['created_at'])) ?></span>
                <?= $fb['is_read'] ? '<span class="badge badge-success" style="font-size:10px;">Read</span>' : '<span class="badge badge-warning" style="font-size:10px;">Unread</span>' ?>
              </div>
            </div>
            <p style="font-size:13px;color:var(--text-muted);line-height:1.5;"><?= htmlspecialchars($fb['message']) ?></p>
          </div>
        <?php endwhile; endif; ?>
      </div>
    </div>
  </div>
</div>
<script src="../assets/js/main.js"></script>
</body>
</html>