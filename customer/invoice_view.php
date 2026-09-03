<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Invoice <?= htmlspecialchars($inv['invoice_number']) ?> - <?= SITE_NAME ?></title>
<link rel="stylesheet" href="../assets/css/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
  body { background: #f0f2f5; }
  .print-wrapper { max-width: 800px; margin: 30px auto; padding: 20px; }
  .print-actions { text-align:right; margin-bottom:20px; }
  @media print { .print-actions, .sidebar, .topbar { display:none!important; } body { background:white; } .print-wrapper { margin:0; padding:0; } }
</style>
</head>
<body>
<div class="print-wrapper">
  <div class="print-actions no-print">
    <a href="invoice.php" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Back</a>
    <button class="btn btn-primary btn-sm" onclick="window.print()"><i class="fas fa-print"></i> Print Invoice</button>
  </div>
  <div class="invoice-container">
    <div class="invoice-header">
      <div>
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:10px;">
          <div style="width:50px;height:50px;background:var(--primary);border-radius:12px;display:flex;align-items:center;justify-content:center;color:white;font-size:22px;">
            <i class="fas fa-car-crash"></i>
          </div>
          <div>
            <h2 style="font-size:1.4rem;font-weight:800;"><?= SITE_NAME ?></h2>
            <p style="color:var(--text-muted);font-size:13px;">Vehicle Breakdown Assistance</p>
          </div>
        </div>
        <p style="font-size:13px;color:var(--text-muted);">123 Service Road, Auto Nagar<br>City - 500001 | +91 98765 43210</p>
      </div>
      <div style="text-align:right;">
        <div class="invoice-title">INVOICE</div>
        <p style="margin-top:8px;color:var(--text-muted);font-size:14px;">
          <strong><?= htmlspecialchars($inv['invoice_number']) ?></strong><br>
          Date: <?= date('d M Y', strtotime($inv['payment_date'])) ?><br>
          Payment ID: <?= htmlspecialchars($inv['razorpay_payment_id']) ?>
        </p>
      </div>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:30px;margin:30px 0;padding:20px;background:#f9fafb;border-radius:12px;">
      <div>
        <h4 style="font-size:12px;text-transform:uppercase;letter-spacing:1px;color:var(--text-muted);margin-bottom:8px;">Bill To</h4>
        <p><strong><?= htmlspecialchars($customer['full_name']) ?></strong></p>
        <p style="font-size:14px;color:var(--text-muted);"><?= htmlspecialchars($customer['email']) ?></p>
        <p style="font-size:14px;color:var(--text-muted);"><?= htmlspecialchars($customer['mobile']) ?></p>
        <p style="font-size:14px;color:var(--text-muted);"><?= htmlspecialchars($customer['address']) ?></p>
      </div>
      <div>
        <h4 style="font-size:12px;text-transform:uppercase;letter-spacing:1px;color:var(--text-muted);margin-bottom:8px;">Vehicle Details</h4>
        <p><strong><?= htmlspecialchars($inv['vehicle_name']) ?></strong></p>
        <p style="font-size:14px;color:var(--text-muted);">Vehicle No: <?= htmlspecialchars($inv['vehicle_number']) ?></p>
        <p style="font-size:14px;color:var(--text-muted);">Category: <?= htmlspecialchars($inv['vehicle_category']) ?></p>
        <p style="font-size:14px;color:var(--text-muted);">Brand: <?= htmlspecialchars($inv['vehicle_brand'] ?? '-') ?></p>
      </div>
    </div>
    <table class="invoice-table" style="margin:0 0 20px;">
      <thead>
        <tr style="background:#1d3557;color:white;">
          <th style="border:none;color:white;">Description</th>
          <th style="border:none;color:white;text-align:right;">Amount</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>
            <strong>Vehicle Repair Service</strong><br>
            <span style="font-size:13px;color:var(--text-muted);"><?= htmlspecialchars($inv['problem_description']) ?></span>
          </td>
          <td style="text-align:right;">₹<?= number_format($inv['amount'],2) ?></td>
        </tr>
        <tr>
          <td>GST @ 18%</td>
          <td style="text-align:right;">₹<?= number_format($inv['tax'],2) ?></td>
        </tr>
      </tbody>
    </table>
    <div style="border-top:2px solid var(--primary);padding-top:15px;text-align:right;">
      <div style="font-size:1.4rem;font-weight:800;color:var(--primary);">
        Total Paid: ₹<?= number_format($inv['total_amount'],2) ?>
      </div>
    </div>
    <div style="margin-top:40px;padding-top:20px;border-top:1px dashed var(--border);text-align:center;color:var(--text-muted);font-size:13px;">
      <p><i class="fas fa-check-circle" style="color:#10b981;"></i> Payment received via Razorpay on <?= date('d M Y, h:i A', strtotime($inv['payment_date'])) ?></p>
      <p style="margin-top:8px;">Thank you for choosing <?= SITE_NAME ?>! Drive safe. 🚗</p>
    </div>
  </div>
</div>
</body>
</html>
