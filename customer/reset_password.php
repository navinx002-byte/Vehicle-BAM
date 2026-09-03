<?php
session_start();
require_once '../includes/config.php';
$token = isset($_GET['token']) ? sanitize($conn, $_GET['token']) : '';
$msg = $err = '';
$valid = false;
if ($token) {
    $r = $conn->query("SELECT * FROM customers WHERE reset_token='$token' AND reset_expires > NOW()");
    if ($r->num_rows > 0) { $valid = true; $user = $r->fetch_assoc(); }
    else { $err = "Invalid or expired link."; }
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $valid) {
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $conn->query("UPDATE customers SET password='$pass', reset_token=NULL, reset_expires=NULL WHERE reset_token='$token'");
    $msg = "Password changed! <a href=\"login.php\">Login now</a>";
    $valid = false;
}
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Reset Password</title>
<link rel="stylesheet" href="../css/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="auth-wrapper">
<div class="auth-card">
<div class="auth-logo"><h2><span>Auto</span>Care</h2><p>Set New Password</p></div>
<?php if ($err): ?><div class="alert alert-danger"><?=$err?></div><?php endif; ?>
<?php if ($msg): ?><div class="alert alert-success"><?=$msg?></div><?php endif; ?>
<?php if ($valid): ?>
<form method="POST">
  <div class="form-group"><label>New Password</label><input type="password" name="password" class="form-control" required minlength="6"></div>
  <button type="submit" class="btn-primary btn-block">Update Password</button>
</form>
<?php endif; ?>
</div>
</div>
</body></html>
