<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
redirectIfNotLoggedIn('mechanic', 'login.php');
$mid = $_SESSION['mechanic_id'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = sanitize($conn, $_POST['full_name']);
    $mobile= sanitize($conn, $_POST['mobile']);
    $addr  = sanitize($conn, $_POST['address']);
    $spec  = sanitize($conn, $_POST['specialization']);
    $mech  = $conn->query("SELECT * FROM mechanics WHERE id=$mid")->fetch_assoc();
    $photo = $mech['profile_photo']; 
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $ext = strtolower(pathinfo($_FILES['profile_photo']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            $new_filename = 'mech_' . $mid . '_' . time() . '.' . $ext;
            $upload_dir   = __DIR__ . '/../uploads/profiles/';
            if (!empty($photo) && $photo !== 'default.png' && file_exists($upload_dir . $photo)) {
                unlink($upload_dir . $photo);
            }
            if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $upload_dir . $new_filename)) {
                $photo = $new_filename;
                $_SESSION['mechanic_photo'] = $photo;
            }
        }
    }
    $_SESSION['mechanic_name'] = $name;
    $conn->query("UPDATE mechanics SET full_name='$name', mobile='$mobile', address='$addr', specialization='$spec', profile_photo='$photo' WHERE id=$mid");
    setFlash('success', 'Profile updated successfully!');
    header("Location: profile.php"); exit;
}
$mechanic = $conn->query("SELECT * FROM mechanics WHERE id=$mid")->fetch_assoc();
$total_done     = $conn->query("SELECT COUNT(*) c FROM service_requests WHERE mechanic_id=$mid AND status IN ('Repairing Done','Released')")->fetch_assoc()['c'];
$total_assigned = $conn->query("SELECT COUNT(*) c FROM service_requests WHERE mechanic_id=$mid")->fetch_assoc()['c'];
$default_photo = '../assets/images/default.png';
$photo_src = !empty($mechanic['profile_photo'])
    ? '../uploads/profiles/' . htmlspecialchars($mechanic['profile_photo'])
    : $default_photo;
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Profile - <?= SITE_NAME ?></title>
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
      <span class="topbar-title">My Profile</span>
    </div>
    <div class="topbar-right">
      <button class="theme-toggle" onclick="toggleTheme()"><i class="fas fa-moon theme-icon"></i></button>
    </div>
  </header>
  <div class="page-content">
    <?php showFlash(); ?>
    <div class="profile-header">
      <div class="profile-avatar">
        <img id="profilePreview"
             src="<?= $photo_src ?>"
             alt="Profile"
             onerror="this.onerror=null;this.src='<?= $default_photo ?>'">
        <label class="avatar-edit" for="photoInput" style="background:#10b981;">
          <i class="fas fa-camera"></i>
        </label>
      </div>
      <div class="profile-info">
        <h3><?= htmlspecialchars($mechanic['full_name']) ?></h3>
        <p><?= htmlspecialchars($mechanic['email']) ?></p>
        <div class="profile-meta">
          <div class="profile-meta-item"><i class="fas fa-tools" style="color:#10b981;"></i> <?= htmlspecialchars($mechanic['specialization'] ?? 'General Mechanic') ?></div>
          <div class="profile-meta-item"><i class="fas fa-phone" style="color:#10b981;"></i> <?= htmlspecialchars($mechanic['mobile']) ?></div>
          <div class="profile-meta-item"><i class="fas fa-check-circle" style="color:#10b981;"></i> <?= $total_done ?> vehicles repaired</div>
          <div class="profile-meta-item"><i class="fas fa-clipboard" style="color:#10b981;"></i> <?= $total_assigned ?> total assigned</div>
        </div>
      </div>
    </div>
    <div class="form-card">
      <div class="form-card-header">
        <div class="icon" style="background:rgba(16,185,129,0.1);color:#10b981;"><i class="fas fa-edit"></i></div>
        <h3>Edit Profile</h3>
      </div>
      <form method="POST" enctype="multipart/form-data">
        <input type="file" id="photoInput" name="profile_photo" accept="image/*" style="display:none;"
               onchange="previewPhoto(this)">
        <div class="form-row">
          <div class="form-group">
            <label>Full Name <span class="required">*</span></label>
            <div class="input-icon"><i class="fas fa-user"></i>
              <input type="text" name="full_name" class="form-control"
                     value="<?= htmlspecialchars($mechanic['full_name']) ?>" required>
            </div>
          </div>
          <div class="form-group">
            <label>Email (Read Only)</label>
            <div class="input-icon"><i class="fas fa-envelope"></i>
              <input type="email" class="form-control"
                     value="<?= htmlspecialchars($mechanic['email']) ?>" disabled>
            </div>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Mobile Number</label>
            <div class="input-icon"><i class="fas fa-phone"></i>
              <input type="text" name="mobile" class="form-control"
                     value="<?= htmlspecialchars($mechanic['mobile']) ?>">
            </div>
          </div>
          <div class="form-group">
            <label>Specialization</label>
            <div class="input-icon"><i class="fas fa-tools"></i>
              <input type="text" name="specialization" class="form-control"
                     value="<?= htmlspecialchars($mechanic['specialization'] ?? '') ?>"
                     placeholder="e.g. Engine Specialist">
            </div>
          </div>
        </div>
        <div class="form-group">
          <label>Address</label>
          <textarea name="address" class="form-control"
                    placeholder="Your address..."><?= htmlspecialchars($mechanic['address'] ?? '') ?></textarea>
        </div>
        <button type="submit" class="btn btn-success">
          <i class="fas fa-save"></i> Update Profile
        </button>
      </form>
    </div>
  </div>
</div>
<script src="../assets/js/main.js"></script>
<script>
function previewPhoto(input) {
  if (!input.files || !input.files[0]) return;
  const reader = new FileReader();
  reader.onload = function(e) {
    document.getElementById('profilePreview').src = e.target.result;
  };
  reader.readAsDataURL(input.files[0]);
}
</script>
</body>
</html>