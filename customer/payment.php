<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
redirectIfNotLoggedIn('customer', 'login.php');
$cid = $_SESSION['customer_id'];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['razorpay_payment_id'])) {
    $payment_id  = sanitize($_POST['razorpay_payment_id']);
    $order_id    = sanitize($_POST['razorpay_order_id']);
    $signature   = sanitize($_POST['razorpay_signature']);
    $request_id  = (int)$_POST['request_id_pay'];
    if (verifyRazorpaySignature($order_id, $payment_id, $signature)) {
        $conn->query("UPDATE service_requests SET payment_status='Paid', razorpay_order_id='$order_id', razorpay_payment_id='$payment_id', status='Released', released_date=NOW() WHERE id=$request_id AND customer_id=$cid");
        $req = $conn->query("SELECT * FROM service_requests WHERE id=$request_id")->fetch_assoc();
        $inv_no = generateInvoiceNumber();
        $tax = round($req['cost'] * 0.18, 2);
        $total = $req['cost'] + $tax;
        $conn->query("INSERT INTO invoices (request_id,customer_id,invoice_number,amount,tax,total_amount,razorpay_payment_id,payment_date) VALUES ($request_id,$cid,'$inv_no',{$req['cost']},$tax,$total,'$payment_id',NOW())");
        setFlash('success', 'Payment successful! Your vehicle is ready for pickup. Invoice generated.');
        header("Location: invoice.php?id=$request_id"); exit;
    } else {
        setFlash('error', 'Payment verification failed! Please contact support.');
        header("Location: dashboard.php"); exit;
    }
}
$rid = (int)($_GET['id'] ?? 0);
$request = $conn->query("SELECT * FROM service_requests WHERE id=$rid AND customer_id=$cid AND status='Repairing Done' AND payment_status='Unpaid'")->fetch_assoc();
if (!$request) { setFlash('error', 'Invalid payment request!'); header("Location: dashboard.php"); exit; }
$customer = $conn->query("SELECT * FROM customers WHERE id=$cid")->fetch_assoc();
$razorpay_order = createRazorpayOrder($request['cost'], 'REQ-'.$rid.'-'.time());
$razorpay_order_id = $razorpay_order['id'] ?? '';
$conn->query("UPDATE service_requests SET razorpay_order_id='$razorpay_order_id' WHERE id=$rid");
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Payment - <?= SITE_NAME ?></title>
<link rel="stylesheet" href="../assets/css/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>const t=localStorage.getItem('vs_theme')||'light';document.documentElement.setAttribute('data-theme',t);</script>
</head>
<body class="dashboard-layout">
<aside class="sidebar" id="sidebar">
  <div class="sidebar-brand"><div class="brand-icon"><i class="fas fa-car-crash"></i></div><div class="brand-name">Auto<span>Care</span></div></div>
  <div class="sidebar-user">
    <img src="<?= UPLOAD_URL . htmlspecialchars($customer['profile_photo']) ?>" alt="" onerror="this.src='../assets/images/default.png'">
    <div class="user-info"><div class="user-name"><?= htmlspecialchars($customer['full_name']) ?></div><div class="user-role">Customer</div></div>
  </div>
  <nav class="sidebar-nav">
    <a href="dashboard.php"><span class="nav-icon"><i class="fas fa-tachometer-alt"></i></span> Dashboard</a>
    <a href="requests.php"><span class="nav-icon"><i class="fas fa-clipboard-list"></i></span> My Requests</a>
    <a href="invoice.php"><span class="nav-icon"><i class="fas fa-file-invoice"></i></span> Invoices</a>
    <a href="feedback.php"><span class="nav-icon"><i class="fas fa-comment-alt"></i></span> Feedback</a>
    <a href="profile.php"><span class="nav-icon"><i class="fas fa-user"></i></span> My Profile</a>
  </nav>
  <div class="sidebar-footer"><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></div>
</aside>
<div class="overlay" onclick="toggleSidebar()"></div>
<div class="main-content">
  <header class="topbar">
    <div class="topbar-left">
      <button class="mobile-menu-btn" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
      <span class="topbar-title">Make Payment</span>
    </div>
    <div class="topbar-right"><button class="theme-toggle" onclick="toggleTheme()"><i class="fas fa-moon theme-icon"></i></button></div>
  </header>
  <div class="page-content">
    <?php showFlash(); ?>
    <div style="max-width:600px;margin:0 auto;">
      <div class="payment-card">
        <h3><i class="fas fa-wrench"></i> Vehicle Service Payment</h3>
        <div class="amount">₹<?= number_format($request['cost'], 2) ?></div>
        <p>Plus 18% GST: ₹<?= number_format($request['cost']*0.18, 2) ?></p>
        <p><strong>Total: ₹<?= number_format($request['cost']*1.18, 2) ?></strong></p>
      </div>
      <div class="form-card">
        <div class="form-card-header"><div class="icon"><i class="fas fa-info"></i></div><h3>Service Details</h3></div>
        <table class="invoice-table">
          <tr><th>Vehicle Name</th><td><?= htmlspecialchars($request['vehicle_name']) ?></td></tr>
          <tr><th>Vehicle Number</th><td><?= htmlspecialchars($request['vehicle_number']) ?></td></tr>
          <tr><th>Category</th><td><?= htmlspecialchars($request['vehicle_category']) ?></td></tr>
          <tr><th>Brand / Model</th><td><?= htmlspecialchars($request['vehicle_brand'].' '.$request['vehicle_model']) ?></td></tr>
          <tr><th>Problem</th><td><?= htmlspecialchars($request['problem_description']) ?></td></tr>
          <tr><th>Service Charge</th><td>₹<?= number_format($request['cost'], 2) ?></td></tr>
          <tr><th>GST (18%)</th><td>₹<?= number_format($request['cost']*0.18, 2) ?></td></tr>
          <tr style="font-weight:bold;"><th>Total Amount</th><td style="color:var(--primary);">₹<?= number_format($request['cost']*1.18, 2) ?></td></tr>
        </table>
      </div>
      <form id="paymentForm" method="POST" action="payment.php?id=<?= $rid ?>">
        <input type="hidden" id="razorpay_payment_id" name="razorpay_payment_id">
        <input type="hidden" id="razorpay_order_id" name="razorpay_order_id">
        <input type="hidden" id="razorpay_signature" name="razorpay_signature">
        <input type="hidden" id="request_id_pay" name="request_id_pay" value="<?= $rid ?>">
      </form>
      <div style="text-align:center;">
        <button class="payment-btn" onclick="initiatePayment(
          '<?= $razorpay_order_id ?>',
          <?= $request['cost'] * 1.18 ?>,
          <?= $rid ?>,
          '<?= htmlspecialchars(addslashes($customer['full_name'])) ?>',
          '<?= $customer['email'] ?>',
          '<?= $customer['mobile'] ?>'
        )">
          <i class="fas fa-lock"></i> Pay Securely with Razorpay
        </button>
        <p style="margin-top:15px;color:var(--text-muted);font-size:13px;"><i class="fas fa-shield-alt"></i> Secured by Razorpay • 256-bit SSL Encryption</p>
      </div>
    </div>
  </div>
</div>
<script>
  const razorpayKeyId = '<?= RAZORPAY_KEY_ID ?>';
  const siteUrl = '<?= SITE_URL ?>';
</script>
<script src="../assets/js/main.js"></script>
</body>
</html>
