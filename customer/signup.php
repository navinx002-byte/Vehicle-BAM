<?php
session_start();
require_once '../includes/config.php';
if (isLoggedIn('customer')) redirect('dashboard.php');
$error = $success = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name    = sanitize($conn, $_POST['full_name']);
    $email   = sanitize($conn, $_POST['email']);
    $pass    = $_POST['password'];
    $mobile  = sanitize($conn, $_POST['mobile']);
    $address = sanitize($conn, $_POST['address']);
    if (empty($name) || empty($email) || empty($pass) || empty($mobile)) {
        $error = "All required fields must be filled.";
    } else {
        $check = $conn->query("SELECT id FROM customers WHERE email='$email'");
        if ($check->num_rows > 0) {
            $error = "Email already registered.";
        } else {
            $hashed = password_hash($pass, PASSWORD_DEFAULT);
            $photo  = 'default.png';
            if (!empty($_FILES['photo']['name'])) {
                $ext   = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
                $photo = 'cust_' . time() . '.' . $ext;
                move_uploaded_file($_FILES['photo']['tmp_name'], UPLOAD_PATH . 'profiles/' . $photo);
            }
            $conn->query("INSERT INTO customers (full_name,email,password,mobile,address,profile_photo)
                          VALUES ('$name','$email','$hashed','$mobile','$address','$photo')");
            $success = "Account created successfully!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Customer Signup - AutoCare</title>
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
            position: fixed;
            inset: 0;
            pointer-events: none;
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
            width: 100%; max-width: 500px;
            position: relative; z-index: 10;
            backdrop-filter: blur(24px);
            box-shadow: 0 30px 80px rgba(0,0,0,0.6), inset 0 1px 0 rgba(255,255,255,0.05);
            animation: rise 0.55s cubic-bezier(0.22,1,0.36,1) both;
        }
        @keyframes rise {
            from { opacity:0; transform:translateY(28px) scale(0.97); }
            to   { opacity:1; transform:translateY(0) scale(1); }
        }

        /* Brand */
        .brand { font-size:26px; font-weight:800; color:#fff; letter-spacing:-0.8px; margin-bottom:2px; display:flex; align-items:center; gap:8px; }
        .brand-dot { width:8px; height:8px; border-radius:50%; background:#22d3ee; box-shadow:0 0 10px #22d3ee; flex-shrink:0; }
        .brand span { color:#22d3ee; }
        .sub { font-size:12px; color:rgba(255,255,255,0.38); margin-bottom:28px; font-weight:500; }

        /* PHP Alerts */
        .alert { display:flex; align-items:center; gap:10px; border-radius:10px; padding:12px 14px; font-size:12px; font-weight:600; margin-bottom:18px; animation:rise 0.3s ease both; }
        .alert-danger  { background:rgba(248,113,113,0.07); border:1px solid rgba(248,113,113,0.25); color:#f87171; }
        .alert-success { background:rgba(74,222,128,0.07);  border:1px solid rgba(74,222,128,0.25);  color:#4ade80; }
        .alert a { color:#22d3ee; font-weight:700; text-decoration:none; }

        /* Grid */
        .row2 { display:grid; grid-template-columns:1fr 1fr; gap:12px; }

        /* Field wrapper */
        .field { margin-bottom:14px; }
        .field label {
            display:flex; align-items:center; justify-content:space-between;
            font-size:10px; font-weight:700; color:rgba(255,255,255,0.5);
            letter-spacing:1.3px; text-transform:uppercase; margin-bottom:6px;
        }
        .lbl-ok { font-size:10px; color:#4ade80; letter-spacing:0; text-transform:none; display:none; }
        .field.valid .lbl-ok { display:inline; }

        /* Inputs */
        .field input,
        .field textarea {
            width:100%; padding:12px 14px;
            background:rgba(255,255,255,0.04);
            border:1px solid rgba(255,255,255,0.09);
            border-radius:10px; color:#fff;
            font-family:'Plus Jakarta Sans',sans-serif;
            font-size:14px; font-weight:500; outline:none;
            transition:border-color 0.2s, background 0.2s, box-shadow 0.2s;
            resize:vertical;
        }
        .field textarea { min-height:70px; line-height:1.5; }
        .field input[type="file"] { padding:10px 14px; color:rgba(255,255,255,0.45); cursor:pointer; }
        .field input[type="file"]::-webkit-file-upload-button {
            background:rgba(34,211,238,0.1); border:1px solid rgba(34,211,238,0.25);
            border-radius:6px; color:#22d3ee; font-size:11px; font-weight:700;
            padding:4px 10px; margin-right:10px; cursor:pointer;
            font-family:'Plus Jakarta Sans',sans-serif; transition:background 0.2s;
        }
        .field input[type="file"]::-webkit-file-upload-button:hover { background:rgba(34,211,238,0.2); }
        .field input:focus, .field textarea:focus {
            border-color:#22d3ee; background:rgba(34,211,238,0.04);
            box-shadow:0 0 0 3px rgba(34,211,238,0.08);
        }
        .field input::placeholder, .field textarea::placeholder { color:rgba(255,255,255,0.18); }

        /* Valid / Error states */
        .field.valid  input, .field.valid  textarea { border-color:#4ade80 !important; background:rgba(74,222,128,0.04) !important; }
        .field.error  input, .field.error  textarea { border-color:#f87171 !important; background:rgba(248,113,113,0.04) !important; }

        /* Inline error message */
        .err-msg {
            font-size:11px; color:#f87171; margin-top:5px;
            display:none; align-items:center; gap:5px; font-weight:600;
        }
        .err-msg.show { display:flex; }
        .err-msg i { flex-shrink:0; font-size:10px; }

        /* Password */
        .pw-wrap { position:relative; }
        .pw-eye {
            position:absolute; right:11px; top:50%; transform:translateY(-50%);
            background:none; border:none; cursor:pointer;
            color:rgba(255,255,255,0.35); font-size:14px; transition:color 0.2s; padding:4px;
        }
        .pw-eye:hover { color:#22d3ee; }
        .pw-bar { height:3px; border-radius:2px; background:rgba(255,255,255,0.06); overflow:hidden; margin-top:7px; }
        .pw-fill { height:100%; border-radius:2px; width:0; transition:width 0.35s, background 0.35s; }
        .pw-meta { display:flex; justify-content:space-between; align-items:flex-end; margin-top:5px; }
        .pw-rules { display:flex; gap:5px; flex-wrap:wrap; }
        .pw-rule {
            font-size:10px; font-weight:600; padding:2px 7px; border-radius:20px;
            background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.08);
            color:rgba(255,255,255,0.3); transition:all 0.2s; white-space:nowrap;
        }
        .pw-rule.pass { background:rgba(74,222,128,0.08); border-color:rgba(74,222,128,0.25); color:#4ade80; }
        .pw-label { font-size:10px; font-weight:700; }

        /* Char counter */
        .char-counter { font-size:10px; color:rgba(255,255,255,0.28); text-align:right; margin-top:4px; font-weight:500; transition:color 0.2s; }
        .char-counter.warn  { color:#fbbf24; }
        .char-counter.limit { color:#f87171; }

        /* File preview */
        .file-preview {
            display:none; align-items:center; gap:10px; margin-top:8px;
            padding:8px 12px; background:rgba(34,211,238,0.05);
            border:1px solid rgba(34,211,238,0.15); border-radius:8px;
        }
        .file-preview.show { display:flex; }
        .file-preview img { width:36px; height:36px; border-radius:6px; object-fit:cover; border:1px solid rgba(255,255,255,0.1); }
        .file-preview-info { flex:1; min-width:0; }
        .file-preview-name { font-size:11px; font-weight:700; color:#fff; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
        .file-preview-size { font-size:10px; color:rgba(255,255,255,0.35); margin-top:1px; }
        .file-remove { background:none; border:none; color:rgba(255,255,255,0.3); cursor:pointer; font-size:14px; padding:2px; transition:color 0.2s; flex-shrink:0; }
        .file-remove:hover { color:#f87171; }

        /* CAPTCHA */
        .captcha-block { border:1px solid rgba(255,255,255,0.07); border-radius:12px; padding:16px; background:rgba(255,255,255,0.02); margin-bottom:14px; }
        .captcha-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:12px; }
        .captcha-label { font-size:10px; font-weight:700; letter-spacing:1.1px; text-transform:uppercase; color:rgba(255,255,255,0.45); display:flex; align-items:center; gap:6px; }
        .captcha-type-badge { font-size:9px; font-weight:700; padding:2px 8px; border-radius:20px; background:rgba(34,211,238,0.1); border:1px solid rgba(34,211,238,0.2); color:#22d3ee; letter-spacing:0.8px; text-transform:uppercase; }
        .captcha-refresh { background:none; border:none; cursor:pointer; color:rgba(255,255,255,0.3); font-size:18px; transition:all 0.3s; padding:2px 6px; border-radius:6px; }
        .captcha-refresh:hover { color:#22d3ee; background:rgba(34,211,238,0.07); transform:rotate(180deg); }
        .captcha-canvas { border-radius:8px; overflow:hidden; margin-bottom:10px; height:68px; border:1px solid rgba(255,255,255,0.05); background:#0d1117; }
        .captcha-canvas svg { display:block; width:100%; height:100%; }
        .captcha-hint { font-size:10px; color:rgba(255,255,255,0.28); margin-bottom:10px; line-height:1.5; font-weight:500; }
        .captcha-input {
            width:100%; padding:11px 14px;
            background:rgba(255,255,255,0.04); border:1px solid rgba(255,255,255,0.09);
            border-radius:10px; color:#fff;
            font-family:'Fira Code',monospace; font-size:16px; font-weight:600;
            letter-spacing:3px; text-align:center; outline:none;
            transition:all 0.2s; text-transform:uppercase;
        }
        .captcha-input:focus { border-color:#22d3ee; background:rgba(34,211,238,0.04); box-shadow:0 0 0 3px rgba(34,211,238,0.08); }
        .captcha-input.ok   { border-color:#4ade80; background:rgba(74,222,128,0.05); }
        .captcha-input.fail { border-color:#f87171; animation:shake 0.35s ease; }
        @keyframes shake { 0%,100%{transform:translateX(0)} 25%{transform:translateX(-5px)} 75%{transform:translateX(5px)} }
        .captcha-status { font-size:11px; margin-top:6px; font-weight:600; display:none; }
        .captcha-status.ok-s   { display:block; color:#4ade80; }
        .captcha-status.fail-s { display:block; color:#f87171; }

        /* Terms */
        .terms { display:flex; align-items:flex-start; gap:9px; margin:10px 0 4px; }
        .terms input[type="checkbox"] { accent-color:#22d3ee; flex-shrink:0; margin-top:3px; cursor:pointer; width:auto; }
        .terms label { font-size:11px; color:rgba(255,255,255,0.38); line-height:1.5; cursor:pointer; font-weight:500; }
        .terms a { color:#22d3ee; font-weight:700; text-decoration:none; }

        /* Submit */
        .btn-primary {
            width:100%; padding:14px;
            background:linear-gradient(135deg,#22d3ee,#0891b2);
            border:none; border-radius:11px; color:#03111a;
            font-family:'Plus Jakarta Sans',sans-serif; font-size:14px; font-weight:800;
            cursor:pointer; letter-spacing:0.3px; transition:all 0.2s; margin-top:10px;
            box-shadow:0 4px 20px rgba(34,211,238,0.22);
            display:flex; align-items:center; justify-content:center; gap:8px;
        }
        .btn-primary:hover { transform:translateY(-1px); box-shadow:0 8px 30px rgba(34,211,238,0.32); }
        .btn-primary:active { transform:none; }

        /* Footer */
        .auth-footer { text-align:center; font-size:12px; font-weight:500; color:rgba(255,255,255,0.3); margin-top:18px; }
        .auth-footer a { color:#22d3ee; font-weight:700; text-decoration:none; transition:opacity 0.2s; }
        .auth-footer a:hover { opacity:0.75; }
        .auth-footer .sep { margin:0 6px; opacity:0.3; }

        /* Optional label note */
        .opt-note { letter-spacing:0; text-transform:none; font-size:9px; color:rgba(255,255,255,0.22); font-weight:500; }
    </style>
</head>
<body>
    <div class="grid-bg"></div>
    <div class="card">

        <div class="brand"><div class="brand-dot"></div><span>Auto</span>Care</div>
        <div class="sub">Create your account — it only takes a minute</div>

        <?php if ($error): ?>
        <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $success ?> <a href="login.php">Login here →</a></div>
        <?php endif; ?>

        <form method="POST" action="" enctype="multipart/form-data" onsubmit="return validateForm()" novalidate>

            <!-- Full Name + Mobile -->
            <div class="row2">
                <div class="field" id="field-full_name">
                    <label>Full Name * <span class="lbl-ok">✔ Good</span></label>
                    <input type="text" id="full_name" name="full_name" placeholder="John Doe" maxlength="60"
                           value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>"
                           oninput="validateName()" onblur="validateName(true)" />
                    <div class="err-msg" id="err-full_name">
                        <i class="fas fa-circle-exclamation"></i><span id="err-full_name-text">Full name is required</span>
                    </div>
                </div>
                <div class="field" id="field-mobile">
                    <label>Mobile * <span class="lbl-ok">✔ Valid</span></label>
                    <input type="tel" id="mobile" name="mobile" placeholder="9876543210" maxlength="15"
                           value="<?= htmlspecialchars($_POST['mobile'] ?? '') ?>"
                           oninput="validateMobile()" onblur="validateMobile(true)" />
                    <div class="err-msg" id="err-mobile">
                        <i class="fas fa-circle-exclamation"></i><span id="err-mobile-text">Enter a valid mobile number</span>
                    </div>
                </div>
            </div>

            <!-- Email -->
            <div class="field" id="field-email">
                <label>Email Address * <span class="lbl-ok">✔ Valid</span></label>
                <input type="email" id="email" name="email" placeholder="you@example.com"
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                       oninput="validateEmail()" onblur="validateEmail(true)" />
                <div class="err-msg" id="err-email">
                    <i class="fas fa-circle-exclamation"></i><span id="err-email-text">Enter a valid email address</span>
                </div>
            </div>

            <!-- Password -->
            <div class="field" id="field-password">
                <label>Password * <span class="lbl-ok">✔ Strong</span></label>
                <div class="pw-wrap">
                    <input type="password" id="password" name="password" placeholder="Min. 8 characters"
                           style="padding-right:42px"
                           oninput="checkStrength(); validatePassword()" onblur="validatePassword(true)" />
                    <button class="pw-eye" type="button" onclick="togglePw()">
                        <i class="fas fa-eye" id="eye-icon"></i>
                    </button>
                </div>
                <div class="pw-bar"><div class="pw-fill" id="pw-fill"></div></div>
                <div class="pw-meta">
                    <div class="pw-rules">
                        <span class="pw-rule" id="rule-len">8+ chars</span>
                        <span class="pw-rule" id="rule-upper">A–Z</span>
                        <span class="pw-rule" id="rule-num">0–9</span>
                        <span class="pw-rule" id="rule-sym">!@#…</span>
                    </div>
                    <div class="pw-label" id="pw-label"></div>
                </div>
                <div class="err-msg" id="err-password" style="margin-top:6px">
                    <i class="fas fa-circle-exclamation"></i><span id="err-password-text">Password must be at least 8 characters</span>
                </div>
            </div>

            <!-- Address -->
            <div class="field" id="field-address">
                <label>Address <span class="opt-note">(optional · max 200 chars)</span></label>
                <textarea id="address" name="address" placeholder="Your full address" maxlength="200"
                          oninput="updateCharCount()" onblur="validateAddress(true)"><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea>
                <div class="char-counter" id="char-counter">0 / 200</div>
                <div class="err-msg" id="err-address">
                    <i class="fas fa-circle-exclamation"></i><span id="err-address-text"></span>
                </div>
            </div>

            <!-- Profile Photo -->
            <div class="field" id="field-photo">
                <label>Profile Photo <span class="opt-note">(optional · JPG/PNG/WEBP · max 2 MB)</span></label>
                <input type="file" id="photo" name="photo" accept="image/jpeg,image/png,image/webp,image/gif"
                       onchange="validatePhoto()" />
                <div class="file-preview" id="file-preview">
                    <img id="preview-img" src="" alt="Preview" />
                    <div class="file-preview-info">
                        <div class="file-preview-name" id="preview-name"></div>
                        <div class="file-preview-size" id="preview-size"></div>
                    </div>
                    <button class="file-remove" type="button" onclick="removePhoto()" title="Remove">
                        <i class="fas fa-xmark"></i>
                    </button>
                </div>
                <div class="err-msg" id="err-photo">
                    <i class="fas fa-circle-exclamation"></i><span id="err-photo-text"></span>
                </div>
            </div>

            <!-- CAPTCHA -->
            <div class="captcha-block">
                <div class="captcha-header">
                    <div class="captcha-label">🛡️ Security Check <span class="captcha-type-badge" id="captcha-badge">—</span></div>
                    <button class="captcha-refresh" type="button" onclick="newCaptcha()" title="New CAPTCHA">↻</button>
                </div>
                <div class="captcha-canvas" id="captcha-canvas"></div>
                <div class="captcha-hint" id="captcha-hint"></div>
                <input class="captcha-input" id="captcha-input" type="text"
                       placeholder="Enter answer" maxlength="6"
                       autocomplete="off" spellcheck="false" oninput="checkCaptcha()" />
                <div class="captcha-status ok-s"   id="captcha-ok">✅ CAPTCHA verified successfully</div>
                <div class="captcha-status fail-s"  id="captcha-fail">❌ Incorrect — try again</div>
            </div>
            <div class="err-msg" id="err-captcha">
                <i class="fas fa-circle-exclamation"></i> Please complete the CAPTCHA to continue
            </div>

            <!-- Terms -->
            <div class="terms">
                <input type="checkbox" id="terms" name="terms" value="1" onchange="validateTerms()" />
                <label for="terms">I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a></label>
            </div>
            <div class="err-msg" id="err-terms">
                <i class="fas fa-circle-exclamation"></i> You must accept the terms to continue
            </div>

            <button type="submit" class="btn-primary">
                <i class="fas fa-user-plus"></i> Create Account
            </button>

        </form>

        <p class="auth-footer">
            Already have an account? <a href="login.php">Sign In</a>
            <span class="sep">|</span>
            <a href="../index.php">← Home</a>
        </p>
    </div>

<script>
/* ══════════════════════════════════════════════
   SHARED HELPER — set field state + error text
══════════════════════════════════════════════ */
function fieldState(id, state, msg) {
    const wrap = document.getElementById('field-' + id);
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

/* ══════════════════════════════════════════════
   FULL NAME — letters, spaces, hyphens, apostrophes
══════════════════════════════════════════════ */
function validateName(blur) {
    const val = document.getElementById('full_name').value.trim();
    if (!val)              { if (blur) fieldState('full_name','error','Full name is required'); else fieldState('full_name','idle'); return false; }
    if (val.length < 2)   { fieldState('full_name','error','Name must be at least 2 characters'); return false; }
    if (val.length > 60)  { fieldState('full_name','error','Name must be under 60 characters'); return false; }
    if (!/^[a-zA-Z\s'\-.]+$/.test(val)) { fieldState('full_name','error','Only letters, spaces, hyphens & apostrophes allowed'); return false; }
    fieldState('full_name','valid'); return true;
}

/* ══════════════════════════════════════════════
   MOBILE — 10–15 digits, optional spaces/dashes
══════════════════════════════════════════════ */
function validateMobile(blur) {
    const raw = document.getElementById('mobile').value.trim();
    const digits = raw.replace(/[\s\-\(\)\+]/g,'');
    if (!raw)               { if (blur) fieldState('mobile','error','Mobile number is required'); else fieldState('mobile','idle'); return false; }
    if (!/^\d+$/.test(digits)) { fieldState('mobile','error','Only digits allowed (spaces/dashes are fine)'); return false; }
    if (digits.length < 10)    { fieldState('mobile','error','Must be at least 10 digits'); return false; }
    if (digits.length > 15)    { fieldState('mobile','error','Must be at most 15 digits'); return false; }
    fieldState('mobile','valid'); return true;
}

/* ══════════════════════════════════════════════
   EMAIL — RFC-like format check
══════════════════════════════════════════════ */
function validateEmail(blur) {
    const val = document.getElementById('email').value.trim();
    if (!val) { if (blur) fieldState('email','error','Email address is required'); else fieldState('email','idle'); return false; }
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(val)) { fieldState('email','error','Enter a valid email (e.g. you@example.com)'); return false; }
    fieldState('email','valid'); return true;
}

/* ══════════════════════════════════════════════
   PASSWORD — strength bar + rule pills + validation
══════════════════════════════════════════════ */
function checkStrength() {
    const pw = document.getElementById('password').value;
    const rules = { len:pw.length>=8, upper:/[A-Z]/.test(pw), num:/[0-9]/.test(pw), sym:/[^A-Za-z0-9]/.test(pw) };
    Object.entries(rules).forEach(([k,p]) => document.getElementById('rule-'+k).classList.toggle('pass',p));
    const score = Object.values(rules).filter(Boolean).length;
    const map = [
        {color:'',label:''},
        {color:'#f87171',label:'Too weak'},
        {color:'#fb923c',label:'Weak'},
        {color:'#fbbf24',label:'Fair'},
        {color:'#4ade80',label:'Strong'},
        {color:'#22d3ee',label:'Very strong'},
    ];
    const fill=document.getElementById('pw-fill'), lbl=document.getElementById('pw-label');
    if (!pw) { fill.style.width='0'; lbl.textContent=''; return; }
    fill.style.width=`${(score/4)*100}%`; fill.style.background=map[score].color;
    lbl.textContent=map[score].label; lbl.style.color=map[score].color;
}

function validatePassword(blur) {
    const pw = document.getElementById('password').value;
    if (!pw)             { if (blur) fieldState('password','error','Password is required'); else fieldState('password','idle'); return false; }
    if (pw.length < 8)   { fieldState('password','error','Password must be at least 8 characters'); return false; }
    if (!/[A-Z]/.test(pw)) { fieldState('password','error','Must include at least one uppercase letter (A–Z)'); return false; }
    if (!/[0-9]/.test(pw)) { fieldState('password','error','Must include at least one number (0–9)'); return false; }
    fieldState('password','valid'); return true;
}

/* ══════════════════════════════════════════════
   ADDRESS — optional, min 5 chars if filled, char counter
══════════════════════════════════════════════ */
function updateCharCount() {
    const len = document.getElementById('address').value.length;
    const el  = document.getElementById('char-counter');
    el.textContent = `${len} / 200`;
    el.className   = 'char-counter' + (len >= 200 ? ' limit' : len >= 160 ? ' warn' : '');
    validateAddress();
}
function validateAddress(blur) {
    const val = document.getElementById('address').value.trim();
    if (!val) { fieldState('address','idle'); return true; }
    if (val.length < 5) { fieldState('address','error','Address is too short (min 5 characters)'); return false; }
    fieldState('address','valid'); return true;
}

/* ══════════════════════════════════════════════
   PROFILE PHOTO — type + size + preview
══════════════════════════════════════════════ */
const ALLOWED = ['image/jpeg','image/png','image/webp','image/gif'];
const MAX_MB  = 2;

function validatePhoto() {
    const input   = document.getElementById('photo');
    const file    = input.files[0];
    const errEl   = document.getElementById('err-photo');
    const errTxt  = document.getElementById('err-photo-text');
    const preview = document.getElementById('file-preview');
    errEl.classList.remove('show');
    preview.classList.remove('show');
    if (!file) { fieldState('photo','idle'); return true; }
    if (!ALLOWED.includes(file.type)) {
        fieldState('photo','error');
        errTxt.textContent = 'Only JPG, PNG, WEBP or GIF images are allowed';
        errEl.classList.add('show');
        input.value = ''; return false;
    }
    if (file.size > MAX_MB * 1024 * 1024) {
        fieldState('photo','error');
        errTxt.textContent = `File must be under ${MAX_MB} MB (yours: ${(file.size/1024/1024).toFixed(1)} MB)`;
        errEl.classList.add('show');
        input.value = ''; return false;
    }
    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('preview-img').src = e.target.result;
        document.getElementById('preview-name').textContent = file.name;
        document.getElementById('preview-size').textContent = (file.size/1024).toFixed(0) + ' KB';
        preview.classList.add('show');
    };
    reader.readAsDataURL(file);
    fieldState('photo','valid'); return true;
}
function removePhoto() {
    document.getElementById('photo').value = '';
    document.getElementById('file-preview').classList.remove('show');
    fieldState('photo','idle');
}

/* ══════════════════════════════════════════════
   TERMS
══════════════════════════════════════════════ */
function validateTerms() {
    const ok = document.getElementById('terms').checked;
    document.getElementById('err-terms').classList.toggle('show', !ok);
    return ok;
}

/* ══════════════════════════════════════════════
   PASSWORD TOGGLE
══════════════════════════════════════════════ */
function togglePw() {
    const inp = document.getElementById('password'), icon = document.getElementById('eye-icon');
    inp.type === 'password'
        ? (inp.type='text',   icon.classList.replace('fa-eye','fa-eye-slash'))
        : (inp.type='password', icon.classList.replace('fa-eye-slash','fa-eye'));
}

/* ══════════════════════════════════════════════
   CAPTCHA
══════════════════════════════════════════════ */
let captcha = {}, captchaVerified = false;
const colors = ['#67e8f9','#a78bfa','#f9a8d4','#86efac','#fcd34d','#fb923c','#c084fc','#34d399'];

function generateCaptcha() {
    const type = Math.random() > 0.5 ? 'math' : 'text';
    if (type === 'math') {
        const ops=['+','-','×'], op=ops[Math.floor(Math.random()*ops.length)];
        let a,b,answer;
        if (op==='+')      { a=r(40)+5;  b=r(40)+5;  answer=a+b; }
        else if (op==='-') { a=r(40)+20; b=r(20)+1;  answer=a-b; }
        else               { a=r(9)+2;   b=r(9)+2;   answer=a*b; }
        return { type:'math', question:`${a} ${op} ${b} = ?`, answer:String(answer) };
    } else {
        const chars='ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        let code=''; for (let i=0;i<6;i++) code+=chars[r(chars.length)];
        return { type:'text', question:code, answer:code };
    }
}
function r(n) { return Math.floor(Math.random()*n); }

function drawCaptcha(c) {
    const isMath=c.type==='math', vw=isMath?260:220, letters=c.question.split('');
    let lines='',dots='',chars='';
    for (let i=0;i<6;i++) {
        const x1=Math.random()*vw,y1=Math.random()*70,x2=Math.random()*vw,y2=Math.random()*70;
        lines+=`<line x1="${x1.toFixed(1)}" y1="${y1.toFixed(1)}" x2="${x2.toFixed(1)}" y2="${y2.toFixed(1)}" stroke="rgba(255,255,255,${(0.04+Math.random()*0.06).toFixed(2)})" stroke-width="${(1+Math.random()).toFixed(1)}"/>`;
    }
    for (let i=0;i<28;i++) dots+=`<circle cx="${(Math.random()*vw).toFixed(1)}" cy="${(Math.random()*70).toFixed(1)}" r="${(0.8+Math.random()*1.5).toFixed(1)}" fill="rgba(255,255,255,${(0.05+Math.random()*0.12).toFixed(2)})"/>`;
    const sp=isMath?22:30, sx=isMath?12:10;
    letters.forEach((ch,i) => {
        const x=sx+i*sp, y=46+(Math.random()-0.5)*12, rot=((Math.random()-0.5)*28).toFixed(1), sz=(26+Math.random()*8).toFixed(1);
        chars+=`<text x="${x}" y="${y.toFixed(1)}" fill="${colors[i%colors.length]}" font-size="${sz}" font-family="Courier New,monospace" font-weight="bold" transform="rotate(${rot},${x},${y.toFixed(1)})" style="user-select:none">${ch}</text>`;
    });
    const qy=(15+Math.random()*20).toFixed(1);
    return `<svg viewBox="0 0 ${vw} 70" xmlns="http://www.w3.org/2000/svg"><rect width="100%" height="100%" fill="#0d1117" rx="8"/>${lines}${dots}${chars}<path d="M0,35 Q55,${qy} 110,35 T${vw},35" fill="none" stroke="rgba(255,255,255,0.07)" stroke-width="1.5"/></svg>`;
}

function newCaptcha() {
    captcha=generateCaptcha(); captchaVerified=false;
    document.getElementById('captcha-canvas').innerHTML=drawCaptcha(captcha);
    document.getElementById('captcha-badge').textContent=captcha.type==='math'?'Math':'Text';
    document.getElementById('captcha-hint').textContent=captcha.type==='math'
        ?'💡 Solve the equation above and enter the answer'
        :'💡 Type the characters you see in the image';
    const inp=document.getElementById('captcha-input');
    inp.value=''; inp.className='captcha-input'; inp.disabled=false;
    document.getElementById('captcha-ok').style.display='none';
    document.getElementById('captcha-fail').style.display='none';
    document.getElementById('err-captcha').classList.remove('show');
}

function checkCaptcha() {
    const inp=document.getElementById('captcha-input');
    const val=inp.value.trim().toUpperCase(), expected=captcha.answer.toUpperCase();
    if (!val) { inp.className='captcha-input'; return; }
    const ready=captcha.type==='math'?val.length>=expected.length:val.length===expected.length;
    if (!ready) return;
    if (val===expected) {
        inp.className='captcha-input ok'; inp.disabled=true; captchaVerified=true;
        document.getElementById('captcha-ok').style.display='block';
        document.getElementById('captcha-fail').style.display='none';
        document.getElementById('err-captcha').classList.remove('show');
    } else {
        inp.className='captcha-input fail'; captchaVerified=false;
        document.getElementById('captcha-fail').style.display='block';
        document.getElementById('captcha-ok').style.display='none';
        setTimeout(()=>{ inp.value=''; inp.className='captcha-input'; document.getElementById('captcha-fail').style.display='none'; newCaptcha(); },800);
    }
}

/* ══════════════════════════════════════════════
   FINAL SUBMIT VALIDATION — runs all validators
══════════════════════════════════════════════ */
function validateForm() {
    const results = [
        validateName(true),
        validateMobile(true),
        validateEmail(true),
        validatePassword(true),
        validateAddress(true),
        validatePhoto(),
        validateTerms(),
    ];
    if (!captchaVerified) {
        document.getElementById('err-captcha').classList.add('show');
        results.push(false);
    }
    if (results.includes(false)) {
        // Scroll to the first broken field smoothly
        const first = document.querySelector('.field.error, .err-msg.show');
        if (first) first.scrollIntoView({ behavior:'smooth', block:'center' });
        return false;
    }
    return true;
}

/* ── Init ── */
newCaptcha();
updateCharCount();
</script>
</body>
</html>