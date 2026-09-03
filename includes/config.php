<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'vehicle_service_db');
define('RAZORPAY_KEY_ID', 'rzp_test_fDmVzWIk43LrqG');
define('RAZORPAY_KEY_SECRET', '2vqsxsdMHHdMpVrmvQoP5x5h');
define('SITE_NAME', 'AutoCare Service Center');
define('SITE_URL', 'http://localhost/vehicle_service');
define('UPLOAD_PATH', __DIR__ . '/../uploads/');

// ── Gmail SMTP config ──────────────────────────────────────────
define('MAIL_FROM_NAME',  'AutoCare Service Center');
define('MAIL_FROM_EMAIL', 'zarinasab03@gmail.com');   // ← your Gmail
define('MAIL_PASSWORD',   'coqdqiebpuytedif');       // ← Gmail App Password (not your login password)
// ──────────────────────────────────────────────────────────────

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }
$conn->set_charset("utf8mb4");

function sanitize($conn, $data) { return $conn->real_escape_string(htmlspecialchars(trim($data))); }
function redirect($url) { header("Location: $url"); exit(); }
function isLoggedIn($role) { return isset($_SESSION[$role . '_id']); }
function generateInvoiceNumber() { return 'INV-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT); }

function getStatusBadge($status) {
    $map = ['Pending'=>'pending','Approved'=>'approved','Repairing'=>'repairing','Repairing Done'=>'done','Released'=>'released'];
    $cls = $map[$status] ?? 'pending';
    return '<span class="badge badge-'.$cls.'">'.$status.'</span>';
}
function getPaymentBadge($status) {
    $map = ['Unpaid'=>'unpaid','Payment Sent'=>'sent','Paid'=>'paid'];
    $cls = $map[$status] ?? 'unpaid';
    return '<span class="badge badge-'.$cls.'">'.$status.'</span>';
}

function sendMail($to, $subject, $body) {
    // Use PHPMailer via Gmail SMTP (works on localhost XAMPP)
    $host     = 'smtp.gmail.com';
    $port     = 587;
    $username = MAIL_FROM_EMAIL;
    $password = MAIL_PASSWORD;
    $from     = MAIL_FROM_EMAIL;
    $fromName = MAIL_FROM_NAME;

    // Open socket to Gmail SMTP
    $socket = fsockopen($host, $port, $errno, $errstr, 10);
    if (!$socket) return false;

    $read = function() use ($socket) { return fgets($socket, 1024); };
    $send = function($cmd) use ($socket, $read) {
        fputs($socket, $cmd . "\r\n");
        return $read();
    };

    $read(); // greeting
    $send("EHLO localhost");
    // Read all EHLO lines
    $line = '';
    while (true) {
        $line = $read();
        if ($line[3] === ' ') break;
    }
    $send("STARTTLS");
    stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
    $send("EHLO localhost");
    while (true) {
        $line = $read();
        if ($line[3] === ' ') break;
    }
    $send("AUTH LOGIN");
    $send(base64_encode($username));
    $send(base64_encode($password));
    $send("MAIL FROM:<$from>");
    $send("RCPT TO:<$to>");
    $send("DATA");

    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: $fromName <$from>\r\n";
    $headers .= "To: $to\r\n";
    $headers .= "Subject: $subject\r\n";

    fputs($socket, $headers . "\r\n" . $body . "\r\n.\r\n");
    $read();
    $send("QUIT");
    fclose($socket);
    return true;
}

function createRazorpayOrder($amount, $receipt) {
    $key_id     = RAZORPAY_KEY_ID;
    $key_secret = RAZORPAY_KEY_SECRET;
    $data = json_encode(['amount' => $amount * 100, 'currency' => 'INR', 'receipt' => $receipt]);
    $ch = curl_init('https://api.razorpay.com/v1/orders');
    curl_setopt($ch, CURLOPT_USERPWD,       "$key_id:$key_secret");
    curl_setopt($ch, CURLOPT_POST,          true);
    curl_setopt($ch, CURLOPT_POSTFIELDS,    $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER,    ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);
    return json_decode($result, true);
}

function verifyRazorpaySignature($order_id, $payment_id, $signature) {
    $generated = hash_hmac('sha256', $order_id . '|' . $payment_id, RAZORPAY_KEY_SECRET);
    return hash_equals($generated, $signature);
}