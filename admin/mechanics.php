<?php
session_start();
require_once '../includes/config.php';
if (!isLoggedIn('admin')) redirect('login.php');
$msg = $err = '';

/* ── SERVER-SIDE VALIDATION HELPER ─────────────────────── */
function valErr($msg) { return '<span class="field-err"><i class="fas fa-exclamation-circle"></i> '.$msg.'</span>'; }

if (isset($_POST['action']) && $_POST['action'] == 'add') {
    $name    = trim($_POST['full_name']    ?? '');
    $email   = trim($_POST['email']        ?? '');
    $pass    =      $_POST['password']     ?? '';
    $mobile  = trim($_POST['mobile']       ?? '');
    $spec    = trim($_POST['specialization']?? '');
    $address = trim($_POST['address']      ?? '');
    $licence = strtoupper(trim($_POST['licence_number'] ?? ''));

    $errors = [];

    // Full Name
    if ($name === '')                            $errors['full_name']      = 'Full name is required.';
    elseif (strlen($name) < 3)                   $errors['full_name']      = 'Name must be at least 3 characters.';
    elseif (!preg_match('/^[A-Za-z\s]+$/', $name)) $errors['full_name']   = 'Name must contain letters only.';

    // Email
    if ($email === '')                            $errors['email']          = 'Email is required.';
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email']   = 'Enter a valid email address.';

    // Password
    if ($pass === '')                             $errors['password']       = 'Password is required.';
    elseif (strlen($pass) < 6)                   $errors['password']       = 'Password must be at least 6 characters.';

    // Mobile
    if ($mobile === '')                           $errors['mobile']         = 'Mobile number is required.';
    elseif (!preg_match('/^[6-9]\d{9}$/', $mobile)) $errors['mobile']     = 'Enter valid 10-digit Indian mobile number.';

    // Specialization
    if ($spec === '')                             $errors['specialization'] = 'Specialization is required.';

    // Licence Number (optional but must match format if provided)
    if ($licence !== '' && !preg_match('/^[A-Z0-9\-]{5,20}$/', $licence))
                                                  $errors['licence_number'] = 'Invalid licence format (e.g. KA-2020-0012345).';

    // Address
    if ($address === '')                          $errors['address']        = 'Address is required.';
    elseif (strlen($address) < 10)               $errors['address']        = 'Please enter a complete address (min 10 chars).';

    if (empty($errors)) {
        $emailSafe = sanitize($conn, $email);
        $check = $conn->query("SELECT id FROM mechanics WHERE email='$emailSafe'");
        if ($check->num_rows > 0) {
            $errors['email'] = 'This email is already registered.';
        } else {
            $n = sanitize($conn, $name);
            $p = password_hash($pass, PASSWORD_DEFAULT);
            $m = sanitize($conn, $mobile);
            $s = sanitize($conn, $spec);
            $a = sanitize($conn, $address);
            $l = sanitize($conn, $licence);
            $conn->query("INSERT INTO mechanics (full_name,email,password,mobile,specialization,address,licence_number)
                          VALUES ('$n','$emailSafe','$p','$m','$s','$a','$l')");
            $msg = "Mechanic added successfully!";
        }
    }
    // keep modal open on error
    $openAdd = !empty($errors);
}

if (isset($_POST['action']) && $_POST['action'] == 'update') {
    $id      = (int)$_POST['id'];
    $name    = trim($_POST['full_name']     ?? '');
    $mobile  = trim($_POST['mobile']        ?? '');
    $spec    = trim($_POST['specialization']?? '');
    $status  = trim($_POST['status']        ?? '');
    $licence = strtoupper(trim($_POST['licence_number'] ?? ''));

    $errors = [];

    if ($name === '')                               $errors['full_name']      = 'Full name is required.';
    elseif (strlen($name) < 3)                     $errors['full_name']      = 'Name must be at least 3 characters.';
    elseif (!preg_match('/^[A-Za-z\s]+$/', $name)) $errors['full_name']      = 'Name must contain letters only.';

    if ($mobile === '')                             $errors['mobile']         = 'Mobile number is required.';
    elseif (!preg_match('/^[6-9]\d{9}$/', $mobile))$errors['mobile']         = 'Enter valid 10-digit Indian mobile number.';

    if ($spec === '')                               $errors['specialization'] = 'Specialization is required.';

    if ($licence !== '' && !preg_match('/^[A-Z0-9\-]{5,20}$/', $licence))
                                                    $errors['licence_number'] = 'Invalid licence format (e.g. KA-2020-0012345).';

    if (!in_array($status, ['active','inactive']))  $errors['status']         = 'Invalid status selected.';

    if (empty($errors)) {
        $n = sanitize($conn, $name);
        $m = sanitize($conn, $mobile);
        $s = sanitize($conn, $spec);
        $st= sanitize($conn, $status);
        $l = sanitize($conn, $licence);
        $conn->query("UPDATE mechanics
                      SET full_name='$n', mobile='$m', specialization='$s',
                          status='$st', licence_number='$l'
                      WHERE id=$id");
        $msg = "Mechanic updated successfully!";
        $edit_m = null;
    } else {
        // Re-load record to keep modal populated
        $edit_m = $conn->query("SELECT * FROM mechanics WHERE id=$id")->fetch_assoc();
        // Override with posted values so user sees what they typed
        $edit_m['full_name']     = $name;
        $edit_m['mobile']        = $mobile;
        $edit_m['specialization']= $spec;
        $edit_m['status']        = $status;
        $edit_m['licence_number']= $licence;
        $openEdit = true;
    }
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM mechanics WHERE id=$id");
    $msg = "Mechanic deleted.";
}

$mechanics = $conn->query("
    SELECT m.*,
      (SELECT COUNT(*) FROM service_requests WHERE mechanic_id=m.id AND status IN ('Repairing','Approved'))        as active_jobs,
      (SELECT COUNT(*) FROM service_requests WHERE mechanic_id=m.id AND status IN ('Repairing Done','Released'))   as completed
    FROM mechanics m ORDER BY m.created_at DESC");

if (!isset($edit_m)) {
    $edit_m = isset($_GET['edit'])
        ? $conn->query("SELECT * FROM mechanics WHERE id=".(int)$_GET['edit'])->fetch_assoc()
        : null;
}
$openEdit = $openEdit ?? ($edit_m !== null);

$total_feedback = $conn->query("SELECT COUNT(*) as c FROM feedback WHERE is_read=0")->fetch_assoc()['c'];
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Mechanics - Admin</title>
<link rel="stylesheet" href="../css/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
/* ── Licence badge ── */
.licence-badge {
  display:inline-flex;align-items:center;gap:5px;
  background:rgba(99,179,237,0.12);border:1px solid rgba(99,179,237,0.25);
  color:#63b3ed;padding:3px 10px;border-radius:20px;
  font-size:0.78rem;font-weight:600;font-family:monospace;
}

/* ── Validation styles ── */
.form-group { position:relative; margin-bottom:16px; }
.form-control { transition:border-color 0.2s, box-shadow 0.2s; }
.form-control.is-invalid {
  border-color:#f87171 !important;
  box-shadow:0 0 0 3px rgba(248,113,113,0.18) !important;
}
.form-control.is-valid {
  border-color:#34d399 !important;
  box-shadow:0 0 0 3px rgba(52,211,153,0.15) !important;
}
.field-err {
  display:block; color:#f87171; font-size:0.76rem;
  margin-top:5px; animation:fadeIn 0.2s;
}
.field-err i { margin-right:3px; }
.field-ok {
  display:block; color:#34d399; font-size:0.76rem;
  margin-top:5px; animation:fadeIn 0.2s;
}
@keyframes fadeIn { from{opacity:0;transform:translateY(-4px)} to{opacity:1;transform:translateY(0)} }

/* strength bar */
.strength-bar { height:4px; border-radius:4px; margin-top:6px; background:var(--border); overflow:hidden; }
.strength-fill { height:100%; width:0%; border-radius:4px; transition:width 0.3s,background 0.3s; }
.strength-label { font-size:0.72rem; margin-top:3px; }
</style>
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
    <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
    <div class="nav-section-title">Management</div>
    <a href="customers.php"><i class="fas fa-users"></i> Customers</a>
    <a href="mechanics.php" class="active"><i class="fas fa-tools"></i> Mechanics</a>
    <a href="requests.php"><i class="fas fa-clipboard-list"></i> Service Requests</a>
    <a href="invoices.php"><i class="fas fa-clipboard-list"></i> Invoices</a>
    <a href="feedback.php"><i class="fas fa-comments"></i> Feedback
      <?php if ($total_feedback > 0): ?><span class="badge-count"><?=$total_feedback?></span><?php endif; ?>
    </a>
    <div class="nav-section-title">Account</div>
    <a href="profile.php"><i class="fas fa-user-edit"></i> Profile</a>
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
      <div class="topbar-left"><h1>Mechanics</h1><p>Manage mechanic staff</p></div>
    </div>
    <div class="topbar-right">
      <button class="btn btn-sm btn-primary" onclick="document.getElementById('addModal').classList.add('active')">
        <i class="fas fa-plus"></i> Add Mechanic
      </button>
      <button class="theme-toggle" onclick="toggleTheme()" id="themeBtn"><i class="fas fa-moon"></i></button>
    </div>
  </div>

  <div class="page-content">
    <?php if ($msg): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?=$msg?></div><?php endif; ?>

    <div class="card">
      <div class="table-responsive">
        <table>
          <thead>
            <tr><th>Name</th><th>Email</th><th>Mobile</th><th>Licence No.</th><th>Specialization</th><th>Active Jobs</th><th>Completed</th><th>Status</th><th>Actions</th></tr>
          </thead>
          <tbody>
          <?php while ($r = $mechanics->fetch_assoc()): ?>
          <tr>
            <td><?=htmlspecialchars($r['full_name'])?></td>
            <td><?=htmlspecialchars($r['email'])?></td>
            <td><?=htmlspecialchars($r['mobile'])?></td>
            <td>
              <?php if (!empty($r['licence_number'])): ?>
                <span class="licence-badge"><i class="fas fa-id-card"></i> <?=htmlspecialchars($r['licence_number'])?></span>
              <?php else: ?><span style="color:var(--text-muted);font-size:0.8rem">—</span><?php endif; ?>
            </td>
            <td><?=!empty($r['specialization'])?htmlspecialchars($r['specialization']):'-'?></td>
            <td><span class="badge badge-repairing"><?=$r['active_jobs']?></span></td>
            <td><span class="badge badge-released"><?=$r['completed']?></span></td>
            <td><span class="badge badge-<?=$r['status']?>"><?=ucfirst($r['status'])?></span></td>
            <td>
              <div class="action-btns">
                <a href="?edit=<?=$r['id']?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                <a href="?delete=<?=$r['id']?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this mechanic?')"><i class="fas fa-trash"></i></a>
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

<!-- ══════════════════════════════════════════════════════
     ADD MECHANIC MODAL
══════════════════════════════════════════════════════ -->
<div class="modal-overlay <?=!empty($openAdd)?'active':''?>" id="addModal">
  <div class="modal" style="max-width:560px;width:100%">
    <div class="modal-header">
      <h3><i class="fas fa-user-plus" style="color:var(--primary)"></i> Add Mechanic</h3>
      <button class="modal-close" onclick="document.getElementById('addModal').classList.remove('active')">&times;</button>
    </div>
    <form method="POST" id="addForm" novalidate>
      <input type="hidden" name="action" value="add">
      <div class="modal-body" style="max-height:70vh;overflow-y:auto;padding:20px 24px">

        <!-- Full Name -->
        <div class="form-row">
          <div class="form-group">
            <label>Full Name <span style="color:var(--primary)">*</span></label>
            <input type="text" name="full_name" id="add_name" class="form-control <?=isset($errors['full_name'])?'is-invalid':''?>"
                   value="<?=htmlspecialchars($_POST['full_name']??'')?>"
                   placeholder="e.g. Rajesh Kumar" autocomplete="off">
            <?php if (isset($errors['full_name'])): ?>
              <span class="field-err"><i class="fas fa-exclamation-circle"></i> <?=$errors['full_name']?></span>
            <?php endif; ?>
          </div>

          <!-- Mobile -->
          <div class="form-group">
            <label>Mobile <span style="color:var(--primary)">*</span></label>
            <input type="tel" name="mobile" id="add_mobile" class="form-control <?=isset($errors['mobile'])?'is-invalid':''?>"
                   value="<?=htmlspecialchars($_POST['mobile']??'')?>"
                   placeholder="10-digit number" maxlength="10">
            <?php if (isset($errors['mobile'])): ?>
              <span class="field-err"><i class="fas fa-exclamation-circle"></i> <?=$errors['mobile']?></span>
            <?php endif; ?>
          </div>
        </div>

        <!-- Email -->
        <div class="form-group">
          <label>Email <span style="color:var(--primary)">*</span></label>
          <input type="email" name="email" id="add_email" class="form-control <?=isset($errors['email'])?'is-invalid':''?>"
                 value="<?=htmlspecialchars($_POST['email']??'')?>"
                 placeholder="mechanic@example.com">
          <?php if (isset($errors['email'])): ?>
            <span class="field-err"><i class="fas fa-exclamation-circle"></i> <?=$errors['email']?></span>
          <?php endif; ?>
        </div>

        <!-- Password -->
        <div class="form-group">
          <label>Password <span style="color:var(--primary)">*</span></label>
          <div style="position:relative">
            <input type="password" name="password" id="add_pass" class="form-control <?=isset($errors['password'])?'is-invalid':''?>"
                   placeholder="Min 6 characters" style="padding-right:40px">
            <button type="button" onclick="togglePwd('add_pass',this)"
                    style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--text-muted);cursor:pointer;font-size:14px">
              <i class="fas fa-eye"></i>
            </button>
          </div>
          <div class="strength-bar"><div class="strength-fill" id="add_strength_fill"></div></div>
          <span class="strength-label" id="add_strength_label" style="color:var(--text-muted)"></span>
          <?php if (isset($errors['password'])): ?>
            <span class="field-err"><i class="fas fa-exclamation-circle"></i> <?=$errors['password']?></span>
          <?php endif; ?>
        </div>

        <div class="form-row">
          <!-- Specialization -->
          <div class="form-group">
            <label>Specialization <span style="color:var(--primary)">*</span></label>
            <input type="text" name="specialization" id="add_spec" class="form-control <?=isset($errors['specialization'])?'is-invalid':''?>"
                   value="<?=htmlspecialchars($_POST['specialization']??'')?>"
                   placeholder="e.g. Engine Specialist">
            <?php if (isset($errors['specialization'])): ?>
              <span class="field-err"><i class="fas fa-exclamation-circle"></i> <?=$errors['specialization']?></span>
            <?php endif; ?>
          </div>

          <!-- Licence Number -->
          <div class="form-group">
            <label><i class="fas fa-id-card" style="color:var(--primary);margin-right:4px"></i> Licence Number</label>
            <input type="text" name="licence_number" id="add_licence" class="form-control <?=isset($errors['licence_number'])?'is-invalid':''?>"
                   value="<?=htmlspecialchars($_POST['licence_number']??'')?>"
                   placeholder="e.g. KA-2020-0012345"
                   oninput="this.value=this.value.toUpperCase().replace(/[^A-Z0-9\-]/g,'')">
            <?php if (isset($errors['licence_number'])): ?>
              <span class="field-err"><i class="fas fa-exclamation-circle"></i> <?=$errors['licence_number']?></span>
            <?php else: ?>
              <span style="font-size:0.72rem;color:var(--text-muted);margin-top:4px;display:block">Optional — letters, numbers and hyphens only</span>
            <?php endif; ?>
          </div>
        </div>

        <!-- Address -->
        <div class="form-group">
          <label>Address <span style="color:var(--primary)">*</span></label>
          <textarea name="address" id="add_address" rows="2" class="form-control <?=isset($errors['address'])?'is-invalid':''?>"
                    placeholder="Full address (min 10 characters)"><?=htmlspecialchars($_POST['address']??'')?></textarea>
          <?php if (isset($errors['address'])): ?>
            <span class="field-err"><i class="fas fa-exclamation-circle"></i> <?=$errors['address']?></span>
          <?php endif; ?>
        </div>

      </div><!-- /modal-body -->
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="document.getElementById('addModal').classList.remove('active')">Cancel</button>
        <button type="submit" class="btn btn-primary" onclick="return validateAdd()">
          <i class="fas fa-user-plus"></i> Add Mechanic
        </button>
      </div>
    </form>
  </div>
</div>

<!-- ══════════════════════════════════════════════════════
     EDIT MECHANIC MODAL
══════════════════════════════════════════════════════ -->
<?php if ($edit_m): ?>
<div class="modal-overlay <?=!empty($openEdit)?'active':''?>">
  <div class="modal" style="max-width:540px;width:100%">
    <div class="modal-header">
      <h3><i class="fas fa-user-edit" style="color:var(--primary)"></i> Edit Mechanic</h3>
      <button class="modal-close" onclick="window.location='mechanics.php'">&times;</button>
    </div>
    <form method="POST" novalidate>
      <input type="hidden" name="action" value="update">
      <input type="hidden" name="id" value="<?=$edit_m['id']?>">
      <div class="modal-body" style="padding:20px 24px">

        <div class="form-row">
          <!-- Full Name -->
          <div class="form-group">
            <label>Full Name <span style="color:var(--primary)">*</span></label>
            <input type="text" name="full_name" id="edit_name" class="form-control <?=isset($errors['full_name'])?'is-invalid':''?>"
                   value="<?=htmlspecialchars($edit_m['full_name'])?>" required>
            <?php if (isset($errors['full_name'])): ?>
              <span class="field-err"><i class="fas fa-exclamation-circle"></i> <?=$errors['full_name']?></span>
            <?php endif; ?>
          </div>

          <!-- Mobile -->
          <div class="form-group">
            <label>Mobile <span style="color:var(--primary)">*</span></label>
            <input type="tel" name="mobile" id="edit_mobile" class="form-control <?=isset($errors['mobile'])?'is-invalid':''?>"
                   value="<?=htmlspecialchars($edit_m['mobile'])?>" maxlength="10" required>
            <?php if (isset($errors['mobile'])): ?>
              <span class="field-err"><i class="fas fa-exclamation-circle"></i> <?=$errors['mobile']?></span>
            <?php endif; ?>
          </div>
        </div>

        <div class="form-row">
          <!-- Specialization -->
          <div class="form-group">
            <label>Specialization <span style="color:var(--primary)">*</span></label>
            <input type="text" name="specialization" id="edit_spec" class="form-control <?=isset($errors['specialization'])?'is-invalid':''?>"
                   value="<?=htmlspecialchars($edit_m['specialization'])?>" required>
            <?php if (isset($errors['specialization'])): ?>
              <span class="field-err"><i class="fas fa-exclamation-circle"></i> <?=$errors['specialization']?></span>
            <?php endif; ?>
          </div>

          <!-- Licence Number -->
          <div class="form-group">
            <label><i class="fas fa-id-card" style="color:var(--primary);margin-right:4px"></i> Licence Number</label>
            <input type="text" name="licence_number" id="edit_licence" class="form-control <?=isset($errors['licence_number'])?'is-invalid':''?>"
                   value="<?=htmlspecialchars($edit_m['licence_number']??'')?>"
                   oninput="this.value=this.value.toUpperCase().replace(/[^A-Z0-9\-]/g,'')"
                   placeholder="e.g. KA-2020-0012345">
            <?php if (isset($errors['licence_number'])): ?>
              <span class="field-err"><i class="fas fa-exclamation-circle"></i> <?=$errors['licence_number']?></span>
            <?php else: ?>
              <span style="font-size:0.72rem;color:var(--text-muted);margin-top:4px;display:block">Optional — letters, numbers and hyphens only</span>
            <?php endif; ?>
          </div>
        </div>

        <!-- Status -->
        <div class="form-group">
          <label>Status <span style="color:var(--primary)">*</span></label>
          <select name="status" class="form-control <?=isset($errors['status'])?'is-invalid':''?>">
            <option value="active"   <?=$edit_m['status']=='active'  ?'selected':''?>>Active</option>
            <option value="inactive" <?=$edit_m['status']=='inactive'?'selected':''?>>Inactive</option>
          </select>
          <?php if (isset($errors['status'])): ?>
            <span class="field-err"><i class="fas fa-exclamation-circle"></i> <?=$errors['status']?></span>
          <?php endif; ?>
        </div>

      </div>
      <div class="modal-footer">
        <a href="mechanics.php" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary" onclick="return validateEdit()">
          <i class="fas fa-save"></i> Save Changes
        </button>
      </div>
    </form>
  </div>
</div>
<?php endif; ?>

<script>
/* ── Utility ──────────────────────────────────────── */
function showErr(id, msg) {
  var el = document.getElementById(id);
  if (!el) return;
  el.classList.add('is-invalid'); el.classList.remove('is-valid');
  var next = el.nextElementSibling;
  // Remove old inline error if any
  if (next && next.classList.contains('js-err')) next.remove();
  var span = document.createElement('span');
  span.className = 'field-err js-err';
  span.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + msg;
  el.insertAdjacentElement('afterend', span);
}
function clearErr(id) {
  var el = document.getElementById(id);
  if (!el) return;
  el.classList.remove('is-invalid'); el.classList.add('is-valid');
  var next = el.nextElementSibling;
  if (next && next.classList.contains('js-err')) next.remove();
}
function getVal(id) { return (document.getElementById(id)||{}).value?.trim()||''; }

/* ── Live validation helpers ──────────────────────── */
function liveValidateName(id) {
  var v = getVal(id);
  if (!v)                       { showErr(id,'Full name is required.'); return false; }
  if (v.length < 3)             { showErr(id,'Name must be at least 3 characters.'); return false; }
  if (!/^[A-Za-z\s]+$/.test(v)){ showErr(id,'Name must contain letters only.'); return false; }
  clearErr(id); return true;
}
function liveValidateMobile(id) {
  var v = getVal(id);
  if (!v)                          { showErr(id,'Mobile number is required.'); return false; }
  if (!/^[6-9]\d{9}$/.test(v))    { showErr(id,'Enter valid 10-digit Indian number (starts 6-9).'); return false; }
  clearErr(id); return true;
}
function liveValidateEmail(id) {
  var v = getVal(id);
  if (!v)                          { showErr(id,'Email is required.'); return false; }
  if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v)) { showErr(id,'Enter a valid email address.'); return false; }
  clearErr(id); return true;
}
function liveValidateSpec(id) {
  var v = getVal(id);
  if (!v) { showErr(id,'Specialization is required.'); return false; }
  clearErr(id); return true;
}
function liveValidateLicence(id) {
  var v = getVal(id);
  if (v && !/^[A-Z0-9\-]{5,20}$/.test(v)) {
    showErr(id,'5–20 chars: letters, numbers, hyphens only.'); return false;
  }
  clearErr(id); return true;
}
function liveValidateAddress(id) {
  var v = getVal(id);
  if (!v)         { showErr(id,'Address is required.'); return false; }
  if (v.length<10){ showErr(id,'Please enter a complete address (min 10 chars).'); return false; }
  clearErr(id); return true;
}
function liveValidatePassword(id) {
  var v = document.getElementById(id)?.value || '';
  if (!v)         { showErr(id,'Password is required.'); return false; }
  if (v.length<6) { showErr(id,'Password must be at least 6 characters.'); return false; }
  clearErr(id); return true;
}

/* ── Password strength meter ──────────────────────── */
function passwordStrength(pwd) {
  var score = 0;
  if (pwd.length >= 8)              score++;
  if (/[A-Z]/.test(pwd))           score++;
  if (/[0-9]/.test(pwd))           score++;
  if (/[^A-Za-z0-9]/.test(pwd))    score++;
  return score; // 0-4
}
document.getElementById('add_pass')?.addEventListener('input', function() {
  var s = passwordStrength(this.value);
  var fill  = document.getElementById('add_strength_fill');
  var label = document.getElementById('add_strength_label');
  var colors = ['#ef4444','#f97316','#eab308','#22c55e'];
  var labels = ['Weak','Fair','Good','Strong'];
  var pct    = [25,50,75,100];
  if (this.value === '') { fill.style.width='0%'; label.textContent=''; return; }
  var idx = Math.max(0, s-1);
  fill.style.width      = pct[idx] + '%';
  fill.style.background = colors[idx];
  label.style.color     = colors[idx];
  label.textContent     = labels[idx];
  liveValidatePassword('add_pass');
});

/* ── Toggle password visibility ───────────────────── */
function togglePwd(fieldId, btn) {
  var f = document.getElementById(fieldId);
  if (!f) return;
  var isPass = f.type === 'password';
  f.type = isPass ? 'text' : 'password';
  btn.innerHTML = '<i class="fas fa-' + (isPass ? 'eye-slash' : 'eye') + '"></i>';
}

/* ── Live listeners — Add form ────────────────────── */
['add_name','add_mobile','add_email','add_pass','add_spec','add_licence','add_address'].forEach(function(id) {
  var el = document.getElementById(id);
  if (!el) return;
  el.addEventListener('blur', function() {
    if (id==='add_name')    liveValidateName('add_name');
    if (id==='add_mobile')  liveValidateMobile('add_mobile');
    if (id==='add_email')   liveValidateEmail('add_email');
    if (id==='add_pass')    liveValidatePassword('add_pass');
    if (id==='add_spec')    liveValidateSpec('add_spec');
    if (id==='add_licence') liveValidateLicence('add_licence');
    if (id==='add_address') liveValidateAddress('add_address');
  });
  // Remove red border on type
  el.addEventListener('input', function() {
    if (this.classList.contains('is-invalid')) this.classList.remove('is-invalid');
  });
});

/* ── Live listeners — Edit form ───────────────────── */
['edit_name','edit_mobile','edit_spec','edit_licence'].forEach(function(id) {
  var el = document.getElementById(id);
  if (!el) return;
  el.addEventListener('blur', function() {
    if (id==='edit_name')    liveValidateName('edit_name');
    if (id==='edit_mobile')  liveValidateMobile('edit_mobile');
    if (id==='edit_spec')    liveValidateSpec('edit_spec');
    if (id==='edit_licence') liveValidateLicence('edit_licence');
  });
  el.addEventListener('input', function() {
    if (this.classList.contains('is-invalid')) this.classList.remove('is-invalid');
  });
});

/* ── Mobile: only allow digits ────────────────────── */
['add_mobile','edit_mobile'].forEach(function(id){
  var el = document.getElementById(id);
  if (el) el.addEventListener('input', function(){ this.value = this.value.replace(/\D/g,''); });
});

/* ── Submit-time validation — Add ─────────────────── */
function validateAdd() {
  var ok = true;
  ok = liveValidateName('add_name')    && ok;
  ok = liveValidateMobile('add_mobile')&& ok;
  ok = liveValidateEmail('add_email')  && ok;
  ok = liveValidatePassword('add_pass')&& ok;
  ok = liveValidateSpec('add_spec')    && ok;
  ok = liveValidateLicence('add_licence')&&ok;
  ok = liveValidateAddress('add_address')&&ok;
  if (!ok) {
    // Scroll to first error
    var first = document.querySelector('#addModal .is-invalid');
    if (first) first.scrollIntoView({behavior:'smooth', block:'center'});
  }
  return ok;
}

/* ── Submit-time validation — Edit ────────────────── */
function validateEdit() {
  var ok = true;
  ok = liveValidateName('edit_name')    && ok;
  ok = liveValidateMobile('edit_mobile')&& ok;
  ok = liveValidateSpec('edit_spec')    && ok;
  ok = liveValidateLicence('edit_licence')&&ok;
  if (!ok) {
    var first = document.querySelector('.modal-overlay.active .is-invalid');
    if (first) first.scrollIntoView({behavior:'smooth', block:'center'});
  }
  return ok;
}

/* ── Sidebar / theme ──────────────────────────────── */
function toggleSidebar() {
  document.getElementById('sidebar').classList.toggle('open');
  document.getElementById('sidebarOverlay').classList.toggle('active');
}
function toggleTheme() {
  var html  = document.documentElement;
  var isDark= html.getAttribute('data-theme') === 'dark';
  html.setAttribute('data-theme', isDark ? 'light' : 'dark');
  document.getElementById('themeBtn').innerHTML = isDark ? '<i class="fas fa-sun"></i>' : '<i class="fas fa-moon"></i>';
  localStorage.setItem('theme', isDark ? 'light' : 'dark');
}
var savedTheme = localStorage.getItem('theme') || 'dark';
document.documentElement.setAttribute('data-theme', savedTheme);
</script>
</body>
</html>