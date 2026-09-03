# 🚗 AutoCare Vehicle Breakdown Assistance System

A complete PHP + MySQL vehicle service management system with Razorpay payment integration.

---

## 📁 Project Structure

```
vehicle_service/
├── index.php                   ← Public Home Page
├── database.sql                ← Complete Database Schema + Sample Data
├── includes/
│   ├── config.php              ← DB + Razorpay + App Configuration
│   └── functions.php           ← Helper Functions (auth, upload, payment)
├── assets/
│   ├── css/style.css           ← Complete Stylesheet (Light/Dark Mode)
│   ├── js/main.js              ← JavaScript (tabs, modals, Razorpay)
│   └── images/default.png     ← Default Profile Photo
├── uploads/profiles/           ← Uploaded Profile Photos (auto-created)
│
├── customer/
│   ├── signup.php              ← Customer Registration
│   ├── login.php               ← Customer Login
│   ├── forgot-password.php     ← Forgot Password
│   ├── reset-password.php      ← Reset Password (shared for all roles)
│   ├── dashboard.php           ← Customer Dashboard
│   ├── requests.php            ← Service Requests (4 tabs)
│   ├── payment.php             ← Razorpay Payment Page
│   ├── invoice.php             ← Invoice List
│   ├── invoice_view.php        ← Invoice Detail / Print
│   ├── feedback.php            ← Send Feedback
│   ├── profile.php             ← Edit Profile
│   ├── change-password.php     ← Change Password
│   └── logout.php
│
├── admin/
│   ├── login.php               ← Admin Login
│   ├── forgot-password.php     ← Forgot Password
│   ├── sidebar.php             ← Sidebar Include
│   ├── dashboard.php           ← Admin Dashboard
│   ├── customers.php           ← Manage Customers (CRUD)
│   ├── customer-invoice.php    ← Customer Invoice History
│   ├── mechanics.php           ← Manage Mechanics (CRUD)
│   ├── requests.php            ← Manage Service Requests
│   ├── invoices.php            ← All Invoices
│   ├── feedback.php            ← View All Feedbacks
│   ├── profile.php             ← Admin Profile + Change Password
│   ├── change-password.php     ← Change Password (standalone)
│   └── logout.php
│
└── mechanic/
    ├── login.php               ← Mechanic Login
    ├── forgot-password.php     ← Forgot Password
    ├── sidebar.php             ← Sidebar Include
    ├── dashboard.php           ← Mechanic Dashboard
    ├── assignments.php         ← View + Update Assignment Status
    ├── completed.php           ← Completed Work History
    ├── feedback.php            ← Send Feedback to Admin
    ├── profile.php             ← Edit Profile
    ├── change-password.php     ← Change Password
    └── logout.php
```

---

## ⚙️ Setup Instructions

### Step 1 — Import Database
```sql
-- In phpMyAdmin or MySQL CLI:
source /path/to/database.sql;
```

### Step 2 — Configure the App
Edit `includes/config.php`:

```php
// Database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');        // Your MySQL password
define('DB_NAME', 'vehicle_service_db');

// Razorpay (get keys from razorpay.com/dashboard)
define('RAZORPAY_KEY_ID', 'rzp_test_YOUR_KEY_ID');
define('RAZORPAY_KEY_SECRET', 'YOUR_KEY_SECRET');

// Site URL
define('SITE_URL', 'http://localhost/vehicle_service');

// Email (for password reset)
define('MAIL_FROM_EMAIL', 'your_email@gmail.com');
```

### Step 3 — Place Project
Copy the `vehicle_service` folder to:
- **XAMPP**: `C:/xampp/htdocs/vehicle_service`
- **WAMP**: `C:/wamp/www/vehicle_service`
- **Linux**: `/var/www/html/vehicle_service`

### Step 4 — Set Permissions (Linux/Mac)
```bash
chmod -R 755 vehicle_service/
chmod -R 777 vehicle_service/uploads/
```

---

## 🔐 Default Login Credentials

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@vehicleservice.com | password |
| Customer | john@example.com | password |
| Customer | jane@example.com | password |
| Mechanic | mike@example.com | password |
| Mechanic | tom@example.com | password |

---

## 💳 Razorpay Integration

### How Payment Flow Works:
1. Mechanic updates status → **"Repairing Done"**
2. Admin clicks **"Notify Pay"** → payment notification sent to customer
3. Customer sees **"Pay Now"** button in dashboard
4. Customer clicks → **Razorpay checkout** opens
5. After payment → status auto-updates to **"Released"**
6. **Invoice** generated automatically with GST (18%)

### Get Razorpay Test Keys:
1. Go to [razorpay.com](https://razorpay.com) → Sign Up
2. Dashboard → Settings → API Keys
3. Generate Test Keys
4. Paste in `includes/config.php`

---

## 🌟 Features

### Customer
- ✅ Signup / Login / Forgot Password
- ✅ Submit service request with vehicle details
- ✅ Track request status (Pending → Approved → Repairing → Repairing Done → Released)
- ✅ Delete pending requests
- ✅ Pay via Razorpay (after admin notification)
- ✅ Download/Print invoice with GST
- ✅ Send feedback to admin
- ✅ Edit profile with photo upload
- ✅ Change password

### Admin
- ✅ Dashboard with stats
- ✅ Manage Customers (Add/Edit/Delete + view invoices)
- ✅ Manage Mechanics (Add/Edit/Delete)
- ✅ Approve requests + Assign mechanic + Set cost
- ✅ Add walk-in requests
- ✅ Send payment notification to customer via Razorpay
- ✅ View all invoices with revenue summary
- ✅ View all feedbacks (customer + mechanic)
- ✅ Profile management + Change password

### Mechanic
- ✅ Dashboard with work overview
- ✅ View active assignments with full vehicle + customer details
- ✅ Update repair status (Repairing / Repairing Done)
- ✅ View completed repair history
- ✅ Send feedback to admin
- ✅ Edit profile + Change password

### System
- ✅ Light / Dark mode toggle (persisted in localStorage)
- ✅ Responsive mobile-friendly design
- ✅ Role-based access control
- ✅ Secure password hashing (bcrypt)
- ✅ File upload with validation
- ✅ Invoice PDF print support
- ✅ GST calculation (18%)
- ✅ Razorpay signature verification

---

## 🛠️ Tech Stack
- **Backend**: PHP 8.x
- **Database**: MySQL / MariaDB
- **Frontend**: HTML5, CSS3, Vanilla JavaScript
- **Fonts**: Syne + DM Sans (Google Fonts)
- **Icons**: Font Awesome 6
- **Payments**: Razorpay
- **Server**: Apache (XAMPP / WAMP / cPanel)
