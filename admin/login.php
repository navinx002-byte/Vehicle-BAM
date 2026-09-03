<?php
session_start();
require_once '../includes/config.php';
if (isLoggedIn('admin')) redirect('dashboard.php');
$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = sanitize($conn, $_POST['email']);
    $pass = $_POST['password'];
    $result = $conn->query("SELECT * FROM admin WHERE email='$email'");
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($pass, $user['password'])) {
            $_SESSION['admin_id']    = $user['id'];
            $_SESSION['admin_name']  = $user['full_name'];
            $_SESSION['admin_photo'] = $user['profile_photo'];
            redirect('dashboard.php');
        } else { $error = "Invalid password. Please try again."; }
    } else { $error = "Admin account not found."; }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Login – AutoCare</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Fira+Code:wght@400;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #060b14;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            position: relative;
            overflow-x: hidden;
        }
        body::before {
            content: '';
            position: fixed; inset: 0; pointer-events: none;
            background:
                radial-gradient(ellipse 700px 500px at 70% -10%, rgba(168,85,247,0.07) 0%, transparent 60%),
                radial-gradient(ellipse 500px 600px at -10% 80%, rgba(124,58,237,0.05) 0%, transparent 60%);
        }
        .grid-bg {
            position: fixed; inset: 0; pointer-events: none; z-index: 0;
            background-image:
                linear-gradient(rgba(168,85,247,0.025) 1px, transparent 1px),
                linear-gradient(90deg, rgba(168,85,247,0.025) 1px, transparent 1px);
            background-size: 48px 48px;
        }
        .card {
            background: rgba(255,255,255,0.025);
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: 22px;
            padding: 44px 40px;
            width: 100%; max-width: 440px;
            position: relative; z-index: 10;
            backdrop-filter: blur(24px);
            box-shadow: 0 30px 80px rgba(0,0,0,0.6), inset 0 1px 0 rgba(255,255,255,0.05);
            animation: rise 0.55s cubic-bezier(0.22,1,0.36,1) both;
        }
        .card-accent {
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            border-radius: 22px 22px 0 0;
            background: linear-gradient(90deg, #7c3aed, #a855f7, #7c3aed);
        }
        @keyframes rise {
            from { opacity:0; transform:translateY(28px) scale(0.97); }
            to   { opacity:1; transform:translateY(0) scale(1); }
        }
        .admin-icon {
            width: 60px; height: 60px;
            background: linear-gradient(135deg, #7c3aed, #6d28d9);
            border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            font-size: 26px; margin: 0 auto 16px;
            box-shadow: 0 8px 24px rgba(124,58,237,0.35);
        }
        .brand { font-size:24px; font-weight:800; color:#fff; letter-spacing:-0.8px; margin-bottom:4px; text-align:center; }
        .brand span { color:#a855f7; }
        .sub { font-size:12px; color:rgba(255,255,255,0.4); margin-bottom:20px; text-align:center; font-weight:500; }
        .admin-badge {
            display: flex; width: fit-content;
            align-items: center; gap:7px;
            font-size:11px; font-weight:700; letter-spacing:1px; text-transform:uppercase;
            color:#a855f7;
            background:rgba(168,85,247,0.08); border:1px solid rgba(168,85,247,0.2);
            padding:5px 14px; border-radius:20px; margin:0 auto 22px;
        }
        .notice {
            background:rgba(168,85,247,0.05); border:1px solid rgba(168,85,247,0.15);
            border-radius:12px; padding:13px 15px; margin-bottom:22px;
            display:flex; align-items:flex-start; gap:10px;
        }
        .notice i { color:#a855f7; font-size:14px; flex-shrink:0; margin-top:1px; }
        .notice p { font-size:11px; color:rgba(255,255,255,0.35); line-height:1.55; }
        .notice p strong { color:rgba(255,255,255,0.6); font-weight:700; }
        .alert {
            display:flex; align-items:center; gap:10px;
            background:rgba(248,113,113,0.07); border:1px solid rgba(248,113,113,0.25);
            border-radius:10px; padding:12px 14px;
            font-size:12px; font-weight:600; color:#f87171;
            margin-bottom:18px; animation:rise 0.3s ease both;
        }
        .field { margin-bottom:16px; }
        .field label {
            display:flex; align-items:center; justify-content:space-between;
            font-size:10px; font-weight:700; color:rgba(255,255,255,0.5);
            letter-spacing:1.3px; text-transform:uppercase; margin-bottom:6px;
        }
        .lbl-ok { font-size:10px; color:#4ade80; letter-spacing:0; text-transform:none; display:none; font-weight:600; }
        .field.valid .lbl-ok { display:inline; }
        .field input {
            width:100%; padding:13px 14px;
            background:rgba(255,255,255,0.04);
            border:1px solid rgba(255,255,255,0.09);
            border-radius:10px; color:#fff;
            font-family:'Plus Jakarta Sans',sans-serif;
            font-size:14px; font-weight:500; outline:none;
            transition:border-color 0.2s, background 0.2s, box-shadow 0.2s;
        }
        .field input:focus {
            border-color:#a855f7;
            background:rgba(168,85,247,0.04);
            box-shadow:0 0 0 3px rgba(168,85,247,0.1);
        }
        .field input::placeholder { color:rgba(255,255,255,0.18); }
        .field.valid input { border-color:#4ade80 !important; background:rgba(74,222,128,0.04) !important; }
        .field.error input { border-color:#f87171 !important; background:rgba(248,113,113,0.04) !important; }
        .err-msg {
            font-size:11px; color:#f87171; margin-top:5px;
            display:none; align-items:center; gap:5px; font-weight:600;
        }
        .err-msg.show { display:flex; }
        .err-msg i { font-size:10px; flex-shrink:0; }
        .input-wrap { position:relative; }
        .input-wrap .field-icon {
            position:absolute; left:13px; top:50%; transform:translateY(-50%);
            color:rgba(255,255,255,0.25); font-size:13px; pointer-events:none;
            transition:color 0.2s;
        }
        .field.valid .field-icon { color:#4ade80; }
        .field.error .field-icon { color:#f87171; }
        .input-wrap:focus-within .field-icon { color:#a855f7; }
        .input-wrap input { padding-left:38px; }
        .pw-wrap input { padding-left:38px; padding-right:42px; }
        .pw-eye {
            position:absolute; right:11px; top:50%; transform:translateY(-50%);
            background:none; border:none; cursor:pointer;
            color:rgba(255,255,255,0.4); font-size:14px;
            transition:color 0.2s; padding:4px;
        }
        .pw-eye:hover { color:#a855f7; }
        .pw-bar { height:2px; border-radius:2px; background:rgba(255,255,255,0.06); overflow:hidden; margin-top:6px; display:none; }
        .pw-bar.visible { display:block; }
        .pw-fill { height:100%; border-radius:2px; width:0; transition:width 0.3s, background 0.3s; }
        .pw-label { font-size:10px; text-align:right; margin-top:3px; font-weight:600; display:none; }
        .pw-label.visible { display:block; }
        .forgot-row { display:flex; justify-content:flex-end; margin-top:-6px; margin-bottom:22px; }
        .forgot-link { font-size:11px; font-weight:600; color:#a855f7; text-decoration:none; transition:opacity 0.2s; }
        .forgot-link:hover { opacity:0.7; }
        .btn-primary {
            width:100%; padding:14px;
            background:linear-gradient(135deg,#7c3aed,#6d28d9);
            border:none; border-radius:11px; color:#fff;
            font-family:'Plus Jakarta Sans',sans-serif;
            font-size:14px; font-weight:800; cursor:pointer;
            letter-spacing:0.3px; transition:all 0.2s;
            box-shadow:0 4px 20px rgba(124,58,237,0.3);
            display:flex; align-items:center; justify-content:center; gap:8px;
            margin-top:8px;
        }
        .btn-primary:hover { transform:translateY(-1px); box-shadow:0 8px 30px rgba(124,58,237,0.45); }
        .btn-primary:active { transform:none; }
        .auth-footer { text-align:center; font-size:12px; font-weight:500; color:rgba(255,255,255,0.3); margin-top:16px; }
        .auth-footer a { color:#a855f7; font-weight:700; text-decoration:none; transition:opacity 0.2s; }
        .auth-footer a:hover { opacity:0.75; }
        .divider { display:flex; align-items:center; gap:10px; margin:6px 0 0; }
        .divider-line { flex:1; height:1px; background:rgba(255,255,255,0.06); }
        .footer-note {
            text-align:center; margin-top:18px;
            font-size:11px; color:rgba(255,255,255,0.15); font-weight:500;
        }
        @keyframes shake {
            0%,100% { transform:translateX(0); }
            20%,60% { transform:translateX(-6px); }
            40%,80% { transform:translateX(6px); }
        }
        .shake { animation:shake 0.4s ease; }
    </style>
</head>
<body>
    <div class="grid-bg"></div>
    <div class="card" id="card">
        <div class="card-accent"></div>
        <div class="admin-icon"><i class="fas fa-shield-alt" style="color:#fff;font-size:24px;"></i></div>
        <div class="brand"><span>Auto</span>Care</div>
        <div class="sub">Admin Control Panel — Restricted Access</div>
        <div class="admin-badge">
            <i class="fas fa-lock" style="font-size:10px;"></i> Authorized Personnel Only
        </div>
        <div class="notice">
            <i class="fas fa-triangle-exclamation"></i>
            <p><strong>Restricted area.</strong> Unauthorized access attempts are monitored and logged by the system.</p>
        </div>
        <?php if ($error): ?>
        <div class="alert">
            <i class="fas fa-exclamation-circle"></i>
            <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>
        <form method="POST" action="" onsubmit="return validateForm()" novalidate>
            <div class="field" id="field-email">
                <label>Email Address <span class="lbl-ok">✔ Looks good</span></label>
                <div class="input-wrap">
                    <input type="email" id="email" name="email"
                           placeholder="admin@autocare.com"
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                           autocomplete="email"
                           oninput="validateEmail()" onblur="validateEmail(true)" />
                    <i class="fas fa-envelope field-icon"></i>
                </div>
                <div class="err-msg" id="err-email">
                    <i class="fas fa-circle-exclamation"></i>
                    <span id="err-email-text">Enter a valid email address</span>
                </div>
            </div>
            <div class="field" id="field-password">
                <label>Password <span class="lbl-ok">✔ Entered</span></label>
                <div class="input-wrap pw-wrap">
                    <input type="password" id="password" name="password"
                           placeholder="Admin password"
                           autocomplete="current-password"
                           oninput="validatePassword(); showStrength()"
                           onblur="validatePassword(true)"
                           onfocus="showStrengthBar(true)" />
                    <i class="fas fa-lock field-icon"></i>
                    <button class="pw-eye" type="button" onclick="togglePw()" title="Toggle password">
                        <i class="fas fa-eye" id="eye-icon"></i>
                    </button>
                </div>
                <div class="pw-bar" id="pw-bar"><div class="pw-fill" id="pw-fill"></div></div>
                <div class="pw-label" id="pw-label"></div>
                <div class="err-msg" id="err-password">
                    <i class="fas fa-circle-exclamation"></i>
                    <span id="err-password-text">Password is required</span>
                </div>
            </div>
            <!--<div class="forgot-row">
                <a class="forgot-link" href="forgot_password.php"><i class="fas fa-key" style="font-size:10px;margin-right:4px;"></i>Forgot password?</a>
            </div>-->
            <button type="submit" class="btn-primary">
                <i class="fas fa-shield-alt"></i> Access Dashboard
            </button>
        </form>
        <div class="divider" style="margin-top:18px;"><div class="divider-line"></div></div>
        <p class="auth-footer"><a href="../index.php">← Back to Home</a></p>
        <div class="footer-note">© AutoCare Admin Panel — All rights reserved</div>
    </div>
<script>
function fieldState(id, state, msg) {
    const wrap  = document.getElementById('field-' + id);
    const errEl = document.getElementById('err-' + id);
    const txtEl = document.getElementById('err-' + id + '-text');
    if (!wrap) return;
    wrap.classList.remove('valid', 'error');
    if (errEl) errEl.classList.remove('show');
    if (state === 'valid') {
        wrap.classList.add('valid');
    } else if (state === 'error') {
        wrap.classList.add('error');
        if (errEl) errEl.classList.add('show');
        if (txtEl && msg) txtEl.textContent = msg;
    }
}
function validateEmail(blur) {
    const val = document.getElementById('email').value.trim();
    if (!val) {
        if (blur) fieldState('email', 'error', 'Email address is required');
        else      fieldState('email', 'idle');
        return false;
    }
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(val)) {
        fieldState('email', 'error', 'Enter a valid email (e.g. admin@autocare.com)');
        return false;
    }
    fieldState('email', 'valid');
    return true;
}
function validatePassword(blur) {
    const pw = document.getElementById('password').value;
    if (!pw) {
        if (blur) fieldState('password', 'error', 'Password is required');
        else      fieldState('password', 'idle');
        return false;
    }
    if (pw.length < 6) {
        fieldState('password', 'error', 'Password must be at least 6 characters');
        return false;
    }
    fieldState('password', 'valid');
    return true;
}
function showStrengthBar(show) {
    document.getElementById('pw-bar').classList.toggle('visible', show);
    document.getElementById('pw-label').classList.toggle('visible', show);
}

function showStrength() {
    const pw = document.getElementById('password').value;
    let s = 0;
    if (pw.length >= 6)          s++;
    if (pw.length >= 10)         s++;
    if (/[A-Z]/.test(pw))        s++;
    if (/[0-9]/.test(pw))        s++;
    if (/[^A-Za-z0-9]/.test(pw)) s++;
    const map = [
        { color:'', label:'' },
    ];
    const fill  = document.getElementById('pw-fill');
    const label = document.getElementById('pw-label');
    if (!pw) { fill.style.width='0'; label.textContent=''; return; }
    const idx = Math.min(s, 5);
    fill.style.width      = `${(idx / 5) * 100}%`;
    fill.style.background = map[idx].color;
    label.textContent     = map[idx].color ? map[idx].label : '';
    label.style.color     = map[idx].color;
}
function togglePw() {
    const inp  = document.getElementById('password');
    const icon = document.getElementById('eye-icon');
    inp.type === 'password'
        ? (inp.type = 'text',     icon.classList.replace('fa-eye', 'fa-eye-slash'))
        : (inp.type = 'password', icon.classList.replace('fa-eye-slash', 'fa-eye'));
}
function validateForm() {
    const emailOk = validateEmail(true);
    const passOk  = validatePassword(true);
    if (!emailOk || !passOk) {
        const card = document.getElementById('card');
        card.classList.remove('shake');
        void card.offsetWidth;
        card.classList.add('shake');
        card.addEventListener('animationend', () => card.classList.remove('shake'), { once: true });
        const first = document.querySelector('.field.error');
        if (first) first.scrollIntoView({ behavior: 'smooth', block: 'center' });
        return false;
    }
    return true;
}
document.addEventListener('keydown', e => {
    if (e.key === 'Enter') {
        const active = document.activeElement;
        if (active && (active.id === 'email' || active.id === 'password')) {
            e.preventDefault();
            if (validateForm()) active.closest('form').submit();
        }
    }
});
window.addEventListener('DOMContentLoaded', () => {
    const emailVal = document.getElementById('email').value.trim();
    if (emailVal) validateEmail(true);
});
</script>
</body>
</html>