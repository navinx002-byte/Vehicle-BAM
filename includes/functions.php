<?php
if (!function_exists('isLoggedIn')) {
function isLoggedIn($role = null) {
    if ($role === 'customer') return isset($_SESSION['customer_id']);
    if ($role === 'admin') return isset($_SESSION['admin_id']);
    if ($role === 'mechanic') return isset($_SESSION['mechanic_id']);
    return isset($_SESSION['customer_id']) || isset($_SESSION['admin_id']) || isset($_SESSION['mechanic_id']);
}
}
function redirectIfNotLoggedIn($role, $redirect_url) {
    if (!isLoggedIn($role)) {
        header("Location: $redirect_url");
        exit;
    }
}
if (!function_exists('sanitize')) {
function sanitize($data) {
    global $conn;
    return htmlspecialchars(strip_tags(trim($conn->real_escape_string($data))));
}
}
function setFlash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}
function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}
function showFlash() {
    $flash = getFlash();
    if ($flash) {
        $icon = $flash['type'] === 'success' ? 'check-circle' : ($flash['type'] === 'error' ? 'x-circle' : 'info');
        echo "<div class='alert alert-{$flash['type']}'>
                <i class='fas fa-{$icon}'></i> {$flash['message']}
              </div>";
    }
}
if (!function_exists('generateInvoiceNumber')) {
function generateInvoiceNumber() {
    return 'INV-' . strtoupper(uniqid()) . '-' . date('Y');
}
}
function uploadProfilePhoto($file, $old_photo = null) {
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed)) return false;
    if ($file['size'] > 2 * 1024 * 1024) return false;
    $filename = uniqid() . '_' . time() . '.' . $ext;
    $destination = UPLOAD_PATH . $filename;
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        if ($old_photo && $old_photo !== 'default.png' && file_exists(UPLOAD_PATH . $old_photo)) {
            unlink(UPLOAD_PATH . $old_photo);
        }
        return $filename;
    }
    return false;
}
function sendResetEmail($to_email, $to_name, $reset_link) {
    $subject = SITE_NAME . " - Password Reset";
    $message = "
    <html>
    <body style='font-family:Arial,sans-serif;background:#f5f5f5;padding:30px;'>
    <div style='max-width:500px;margin:auto;background:#fff;border-radius:10px;padding:30px;'>
        <h2 style='color:#e74c3c;'>Password Reset Request</h2>
        <p>Hello <strong>$to_name</strong>,</p>
        <p>We received a request to reset your password. Click the button below:</p>
        <a href='$reset_link' style='display:inline-block;background:#e74c3c;color:#fff;padding:12px 25px;border-radius:5px;text-decoration:none;margin:20px 0;'>Reset Password</a>
        <p style='color:#888;font-size:12px;'>This link expires in 1 hour. If you didn't request this, ignore this email.</p>
        <hr style='border:none;border-top:1px solid #eee;margin:20px 0;'>
        <p style='color:#888;font-size:12px;'>" . SITE_NAME . "</p>
    </div>
    </body>
    </html>";
    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: " . MAIL_FROM_NAME . " <" . MAIL_FROM_EMAIL . ">\r\n";
    return mail($to_email, $subject, $message, $headers);
}
function statusBadge($status) {
    $classes = [
        'Pending'        => 'badge-warning',
        'Approved'       => 'badge-info',
        'Repairing'      => 'badge-primary',
        'Repairing Done' => 'badge-success',
        'Released'       => 'badge-dark',
    ];
    $class = $classes[$status] ?? 'badge-secondary';
    return "<span class='badge $class'>$status</span>";
}
if (!function_exists('createRazorpayOrder')) {
function createRazorpayOrder($amount, $receipt) {
    $url = "https://api.razorpay.com/v1/orders";
    $data = json_encode([
        'amount'   => $amount * 100,
        'currency' => 'INR',
        'receipt'  => $receipt,
    ]);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_USERPWD, RAZORPAY_KEY_ID . ':' . RAZORPAY_KEY_SECRET);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}
}
if (!function_exists('verifyRazorpaySignature')) {
function verifyRazorpaySignature($order_id, $payment_id, $signature) {
    $generated = hash_hmac('sha256', $order_id . '|' . $payment_id, RAZORPAY_KEY_SECRET);
    return hash_equals($generated, $signature);
}
}