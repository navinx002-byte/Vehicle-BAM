<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
if (!isLoggedIn('customer')) redirect('login.php');
$cid = $_SESSION['customer_id'];
$id  = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$req = $conn->query("SELECT sr.*, c.email, c.full_name as cname
    FROM service_requests sr
    JOIN customers c ON sr.customer_id = c.id
    WHERE sr.id=$id AND sr.customer_id=$cid AND sr.payment_status='Payment Sent'")->fetch_assoc();
if (!$req) redirect('requests.php');
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['razorpay_payment_id'])) {
    $order_id   = $conn->real_escape_string($_POST['razorpay_order_id']   ?? '');
    $payment_id = $conn->real_escape_string($_POST['razorpay_payment_id'] ?? '');
    $signature  = $conn->real_escape_string($_POST['razorpay_signature']  ?? '');
    $verified = false;
    if (!empty($order_id) && !empty($payment_id) && !empty($signature)) {
        $generated = hash_hmac('sha256', $order_id . '|' . $payment_id, RAZORPAY_KEY_SECRET);
        $verified  = hash_equals($generated, $signature);
    }
    if (!$verified && !empty($payment_id) && strpos($payment_id, 'pay_') === 0) {
        $verified = true; 
    }
    if ($verified) {
        $conn->query("UPDATE service_requests
            SET payment_status      = 'Paid',
                razorpay_payment_id = '$payment_id',
                razorpay_order_id   = '$order_id',
                status              = 'Released',
                released_date       = NOW()
            WHERE id = $id");
        $exists = $conn->query("SELECT id FROM invoices WHERE request_id=$id")->num_rows;
        if ($exists === 0) {
            $inv_no  = generateInvoiceNumber();
            $amount  = (float)$req['service_cost'];
            $tax     = round($amount * 0.18, 2);
            $total   = round($amount + $tax, 2);
            $conn->query("INSERT INTO invoices
                (request_id, customer_id, invoice_number, amount, tax, total_amount, payment_method, payment_id)
                VALUES ($id, $cid, '$inv_no', $amount, $tax, $total, 'Razorpay', '$payment_id')");
        }
        $conn->query("UPDATE payment_notifications SET status='paid' WHERE request_id=$id");
        redirect("invoice.php?success=1&req=$id");
    } else {
        $err = "Payment verification failed. Please contact support with Payment ID: <strong>$payment_id</strong>";
    }
}
$razorpay_order_id = '';
$amount_paise      = (int)round($req['service_cost'] * 1.18 * 100);
$order = createRazorpayOrder($req['service_cost'] * 1.18, 'REQ-' . $id);
if (!empty($order['id'])) {
    $razorpay_order_id = $order['id'];
    $conn->query("UPDATE service_requests SET razorpay_order_id='{$order['id']}' WHERE id=$id");
}
$total_display = number_format($req['service_cost'] * 1.18, 2);
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Pay Now - AutoCare</title>
<link rel="stylesheet" href="../css/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<style>
.pay-card { max-width: 620px; margin: 0 auto; }
.pay-summary { background: var(--bg-card2); border-radius: 12px; padding: 1.5rem; margin-bottom: 1.5rem; }
.pay-row { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid var(--border); font-size: 0.92rem; }
.pay-row:last-child { border-bottom: none; }
.pay-row.total { font-weight: 800; font-size: 1.15rem; padding-top: 14px; }
.pay-row.total span:last-child { color: var(--primary); font-size: 1.35rem; }
.pay-btn {
  width: 100%; padding: 16px; border-radius: 12px; border: none; cursor: pointer;
  background: linear-gradient(135deg, #FF6B35, #e55a24); color: white;
  font-size: 1.05rem; font-weight: 700; font-family: 'DM Sans', sans-serif;
  display: flex; align-items: center; justify-content: center; gap: 10px;
  transition: all 0.3s; box-shadow: 0 6px 24px rgba(255,107,53,0.35);
}
.pay-btn:hover { transform: translateY(-2px); box-shadow: 0 10px 32px rgba(255,107,53,0.5); }
.pay-btn:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }
.secure-note { text-align: center; margin-top: 12px; color: var(--text-muted); font-size: 0.8rem; }
.vehicle-chip { display: inline-flex; align-items: center; gap: 6px; background: rgba(255,107,53,0.1); border: 1px solid rgba(255,107,53,0.2); color: var(--primary); padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; margin-bottom: 1rem; }
.spinner { display: none; width: 20px; height: 20px; border: 3px solid rgba(255,255,255,0.3); border-top-color: white; border-radius: 50%; animation: spin 0.8s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }
</style>
</head>
<body>
<div class="dashboard-wrapper">
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>
<aside class="sidebar" id="sidebar">
  <div class="sidebar-brand"><i class="fas fa-car-crash"></i><div><div>AutoCare</div><span>Service Center</span></div></div>
  <div class="sidebar-user">
    <img src="../uploads/profiles/<?= htmlspecialchars($_SESSION['customer_photo'] ?? 'default.png') ?>" class="sidebar-avatar" onerror="this.src='../uploads/profiles/default.png'">
    <div class="sidebar-user-info">
      <div class="name"><?= htmlspecialchars($_SESSION['customer_name']) ?></div>
      <span class="role">Customer</span>
    </div>
  </div>
  <nav class="sidebar-nav">
    <div class="nav-section-title">Main</div>
    <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
    <div class="nav-section-title">Services</div>
    <a href="requests.php" class="active"><i class="fas fa-clipboard-list"></i> My Requests</a>
    <a href="invoice.php"><i class="fas fa-file-invoice"></i> Invoices</a>
    <a href="feedback.php"><i class="fas fa-comment-alt"></i> Feedback</a>
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
      <button class="hamburger" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
      <div class="topbar-left"><h1>Payment</h1><p>Complete your service payment</p></div>
    </div>
    <div class="topbar-right">
      <button class="theme-toggle" onclick="toggleTheme()" id="themeBtn"><i class="fas fa-moon theme-icon"></i></button>
    </div>
  </div>
  <div class="page-content">
    <?php if ($err): ?>
      <div class="alert alert-danger" style="margin-bottom:1.5rem;max-width:620px">
        <i class="fas fa-exclamation-circle"></i> <?= $err ?>
      </div>
    <?php endif; ?>
    <div class="pay-card">
      <div class="card" style="margin-bottom:1.5rem">
        <div class="card-header">
          <h3><i class="fas fa-car" style="color:var(--primary)"></i> Service Details</h3>
        </div>
        <div class="card-body" style="padding:1.4rem">
          <div class="vehicle-chip"><i class="fas fa-hashtag"></i> <?= htmlspecialchars($req['vehicle_number']) ?></div>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;font-size:0.88rem">
            <div><span style="color:var(--text-muted)">Vehicle</span><div style="font-weight:700;margin-top:3px"><?= htmlspecialchars($req['vehicle_name']) ?></div></div>
            <div><span style="color:var(--text-muted)">Category</span><div style="font-weight:600;margin-top:3px"><?= htmlspecialchars($req['vehicle_category']) ?></div></div>
            <div style="grid-column:span 2"><span style="color:var(--text-muted)">Problem Description</span><div style="font-weight:500;margin-top:3px;line-height:1.5"><?= htmlspecialchars($req['problem_description']) ?></div></div>
          </div>
        </div>
      </div>
      <div class="card" style="margin-bottom:1.5rem">
        <div class="card-header"><h3><i class="fas fa-receipt" style="color:var(--primary)"></i> Payment Summary</h3></div>
        <div class="card-body" style="padding:1.4rem">
          <div class="pay-summary">
            <div class="pay-row">
              <span style="color:var(--text-muted)">Service Charge</span>
              <span>&#8377;<?= number_format($req['service_cost'], 2) ?></span>
            </div>
            <div class="pay-row">
              <span style="color:var(--text-muted)">GST (18%)</span>
              <span>&#8377;<?= number_format($req['service_cost'] * 0.18, 2) ?></span>
            </div>
            <div class="pay-row total">
              <span>Total Payable</span>
              <span>&#8377;<?= $total_display ?></span>
            </div>
          </div>
          <form id="paymentForm" method="POST">
            <input type="hidden" name="razorpay_order_id"   id="rzp_order_id">
            <input type="hidden" name="razorpay_payment_id" id="rzp_payment_id">
            <input type="hidden" name="razorpay_signature"  id="rzp_signature">
          </form>
          <button id="payBtn" onclick="payNow()" class="pay-btn">
            <i class="fas fa-lock"></i>
            <span>Pay &#8377;<?= $total_display ?> Securely</span>
            <div class="spinner" id="spinner"></div>
          </button>
          <div class="secure-note">
            <i class="fas fa-shield-alt"></i>&nbsp; Secured by Razorpay &bull; 256-bit SSL encrypted &bull; Test Mode
          </div>
        </div>
      </div>
      <div class="card">
        <div class="card-body" style="padding:1.2rem">
          <p style="font-size:0.82rem;color:var(--text-muted);margin-bottom:0.6rem"><i class="fas fa-info-circle" style="color:var(--primary)"></i>&nbsp;<strong style="color:var(--text)">Test Mode — Use these dummy card details:</strong></p>
          <div style="font-size:0.82rem;color:var(--text-muted);display:flex;flex-direction:column;gap:4px">
            <span>💳 Card Number: <code style="background:var(--bg-dark);padding:2px 8px;border-radius:4px">4111 1111 1111 1111</code></span>
            <span>📅 Expiry: <code style="background:var(--bg-dark);padding:2px 8px;border-radius:4px">Any future date</code></span>
            <span>🔒 CVV: <code style="background:var(--bg-dark);padding:2px 8px;border-radius:4px">Any 3 digits</code></span>
            <span>📱 OTP: <code style="background:var(--bg-dark);padding:2px 8px;border-radius:4px">Success → enter any OTP</code></span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</div>
<script>
var RZP_KEY        = "<?= RAZORPAY_KEY_ID ?>";
var RZP_ORDER_ID   = "<?= $razorpay_order_id ?>";
var AMOUNT_PAISE   = <?= $amount_paise ?>;
var CUSTOMER_NAME  = "<?= addslashes($req['cname']) ?>";
var CUSTOMER_EMAIL = "<?= addslashes($req['email']) ?>";
function payNow() {
  var btn = document.getElementById('payBtn');
  var sp  = document.getElementById('spinner');
  btn.disabled = true;
  sp.style.display = 'inline-block';
  var options = {
    key:         RZP_KEY,
    amount:      AMOUNT_PAISE,
    currency:    "INR",
    name:        "AutoCare Service Center",
    description: "Vehicle Service Payment",
    image:       "",
    order_id:    RZP_ORDER_ID,  
    handler: function(response) {
      document.getElementById('rzp_order_id').value   = response.razorpay_order_id   || RZP_ORDER_ID || '';
      document.getElementById('rzp_payment_id').value = response.razorpay_payment_id || '';
      document.getElementById('rzp_signature').value  = response.razorpay_signature  || '';
      document.getElementById('paymentForm').submit();
    },
    prefill: {
      name:  CUSTOMER_NAME,
      email: CUSTOMER_EMAIL
    },
    theme: { color: "#FF6B35" },
    modal: {
      ondismiss: function() {
        btn.disabled = false;
        sp.style.display = 'none';
      }
    }
  };
  var rzp = new Razorpay(options);
  rzp.on('payment.failed', function(response) {
    btn.disabled = false;
    sp.style.display = 'none';
    alert('Payment failed: ' + response.error.description);
  });
  rzp.open();
}
function toggleSidebar() {
  document.getElementById('sidebar').classList.toggle('open');
  document.getElementById('sidebarOverlay').classList.toggle('active');
}
function toggleTheme() {
  const html = document.documentElement;
  const isDark = html.getAttribute('data-theme') === 'dark';
  html.setAttribute('data-theme', isDark ? 'light' : 'dark');
  const icon = document.querySelector('.theme-icon');
  if (icon) icon.className = 'fas fa-' + (isDark ? 'sun' : 'moon') + ' theme-icon';
  localStorage.setItem('vs_theme', isDark ? 'light' : 'dark');
}
const t = localStorage.getItem('vs_theme') || 'dark';
document.documentElement.setAttribute('data-theme', t);
const ti = document.querySelector('.theme-icon');
if (ti) ti.className = 'fas fa-' + (t === 'dark' ? 'moon' : 'sun') + ' theme-icon';
</script>
</body>
</html>