<?php
session_start();
require_once '../includes/config.php';
if (isLoggedIn('customer')) redirect('dashboard.php');
$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = sanitize($conn, $_POST['email']);
    $pass  = $_POST['password'];
    $result = $conn->query("SELECT * FROM customers WHERE email='$email' AND status='active'");
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($pass, $user['password'])) {
            $_SESSION['customer_id']    = $user['id'];
            $_SESSION['customer_name']  = $user['full_name'];
            $_SESSION['customer_photo'] = $user['profile_photo'];
            redirect('dashboard.php');
        } else { $error = "Invalid password. Please try again."; }
    } else { $error = "No active account found with that email."; }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Customer Login – AutoCare</title>
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
                radial-gradient(ellipse 700px 500px at 70% -10%, rgba(34,211,238,0.06) 0%, transparent 60%),
                radial-gradient(ellipse 500px 600px at -10% 80%, rgba(139,92,246,0.05) 0%, transparent 60%);
        }
        .grid-bg {
            position: fixed; inset: 0; pointer-events: none; z-index: 0;
            background-image:
                linear-gradient(rgba(34,211,238,0.025) 1px, transparent 1px),
                linear-gradient(90deg, rgba(34,211,238,0.025) 1px, transparent 1px);
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
        @keyframes rise {
            from { opacity:0; transform:translateY(28px) scale(0.97); }
            to   { opacity:1; transform:translateY(0) scale(1); }
        }
        .brand { font-size:26px; font-weight:800; color:#fff; letter-spacing:-0.8px; margin-bottom:2px; display:flex; align-items:center; gap:8px; }
        .brand-dot { width:8px; height:8px; border-radius:50%; background:#22d3ee; box-shadow:0 0 10px #22d3ee; flex-shrink:0; }
        .brand span { color:#22d3ee; }
        .sub { font-size:12px; color:rgba(255,255,255,0.4); margin-bottom:28px; font-weight:500; }
        .welcome-banner {
            background: rgba(34,211,238,0.05);
            border: 1px solid rgba(34,211,238,0.12);
            border-radius: 12px;
            padding: 14px 16px; margin-bottom: 22px;
            display: flex; align-items: center; gap: 12px;
        }
        .welcome-icon { font-size:28px; flex-shrink:0; }
        .welcome-text h4 { font-size:13px; font-weight:700; color:#fff; margin-bottom:2px; }
        .welcome-text p  { font-size:11px; color:rgba(255,255,255,0.35); line-height:1.4; }
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
            border-color:#22d3ee;
            background:rgba(34,211,238,0.04);
            box-shadow:0 0 0 3px rgba(34,211,238,0.08);
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
        .pw-wrap { position:relative; }
        .pw-eye {
            position:absolute; right:11px; top:50%; transform:translateY(-50%);
            background:none; border:none; cursor:pointer;
            color:rgba(255,255,255,0.4); font-size:14px;
            transition:color 0.2s; padding:4px;
        }
        .pw-eye:hover { color:#22d3ee; }
        .pw-bar { height:2px; border-radius:2px; background:rgba(255,255,255,0.06); overflow:hidden; margin-top:6px; display:none; }
        .pw-bar.visible { display:block; }
        .pw-fill { height:100%; border-radius:2px; width:0; transition:width 0.3s, background 0.3s; }
        .pw-label { font-size:10px; text-align:right; margin-top:3px; font-weight:600; display:none; }
        .pw-label.visible { display:block; }
        .forgot-row { display:flex; justify-content:flex-end; margin-top:-6px; margin-bottom:22px; }
        .forgot-link { font-size:11px; font-weight:600; color:#22d3ee; text-decoration:none; transition:opacity 0.2s; }
        .forgot-link:hover { opacity:0.7; }
        .btn-primary {
            width:100%; padding:14px;
            background:linear-gradient(135deg,#22d3ee,#0891b2);
            border:none; border-radius:11px; color:#03111a;
            font-family:'Plus Jakarta Sans',sans-serif;
            font-size:14px; font-weight:800; cursor:pointer;
            letter-spacing:0.3px; transition:all 0.2s;
            box-shadow:0 4px 20px rgba(34,211,238,0.22);
            display:flex; align-items:center; justify-content:center; gap:8px;
        }
        .btn-primary:hover { transform:translateY(-1px); box-shadow:0 8px 30px rgba(34,211,238,0.32); }
        .btn-primary:active { transform:none; }
        .auth-footer { text-align:center; font-size:12px; font-weight:500; color:rgba(255,255,255,0.3); margin-top:16px; }
        .auth-footer a { color:#22d3ee; font-weight:700; text-decoration:none; transition:opacity 0.2s; }
        .auth-footer a:hover { opacity:0.75; }
        .divider { display:flex; align-items:center; gap:10px; margin:6px 0 0; }
        .divider-line { flex:1; height:1px; background:rgba(255,255,255,0.06); }
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
        <div class="brand"><div class="brand-dot"></div><span>Auto</span>Care</div>
        <div class="sub">Customer Portal — Sign in to your account</div>
        <div class="welcome-banner">
            <div class="welcome-icon">👋</div>
            <div class="welcome-text">
                <h4>Good to see you again!</h4>
                <p>Enter your credentials to continue to your dashboard.</p>
            </div>
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
                <input type="email" id="email" name="email"
                       placeholder="you@example.com"
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                       autocomplete="email"
                       oninput="validateEmail()" onblur="validateEmail(true)" />
                <div class="err-msg" id="err-email">
                    <i class="fas fa-circle-exclamation"></i>
                    <span id="err-email-text">Enter a valid email address</span>
                </div>
            </div>
            <div class="field" id="field-password">
                <label>Password <span class="lbl-ok">✔ Entered</span></label>
                <div class="pw-wrap">
                    <input type="password" id="password" name="password"
                           placeholder="Your password"
                           style="padding-right:42px"
                           autocomplete="current-password"
                           oninput="validatePassword(); showStrength()"
                           onblur="validatePassword(true)"
                           onfocus="showStrengthBar(true)" />
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
            <div class="forgot-row">
                <a class="forgot-link" href="forgot_password.php">Forgot password?</a>
            </div>
            <button type="submit" class="btn-primary" id="submit-btn">
                <i class="fas fa-sign-in-alt"></i> Sign In
            </button>
        </form>
        <p class="auth-footer" style="margin-top:20px;">
            Don't have an account? <a href="signup.php">Sign Up</a>
        </p>
        <div class="divider"><div class="divider-line"></div></div>
        <p class="auth-footer"><a href="../index.php">← Back to Home</a></p>
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
        fieldState('email', 'error', 'Enter a valid email (e.g. you@example.com)');
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
        { color:'#f87171', label:'Very weak' },
        { color:'#fb923c', label:'Weak' },
        { color:'#fbbf24', label:'Fair' },
        { color:'#4ade80', label:'Strong' },
        { color:'#22d3ee', label:'Very strong' },
    ];
    const fill  = document.getElementById('pw-fill');
    const label = document.getElementById('pw-label');
    if (!pw) { fill.style.width='0'; label.textContent=''; return; }
    const idx = Math.min(s, 5);
    fill.style.width      = `${(idx / 5) * 100}%`;
    fill.style.background = map[idx].color;
    label.textContent     = map[idx].label;
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
        void card.offsetWidth; // reflow
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