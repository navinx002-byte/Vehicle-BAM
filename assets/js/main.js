const themeKey = 'vs_theme';
const savedTheme = localStorage.getItem(themeKey) || 'light';
document.documentElement.setAttribute('data-theme', savedTheme);
function toggleTheme() {
  const current = document.documentElement.getAttribute('data-theme');
  const next = current === 'dark' ? 'light' : 'dark';
  document.documentElement.setAttribute('data-theme', next);
  localStorage.setItem(themeKey, next);
  const icon = document.querySelector('.theme-icon');
  if (icon) icon.className = `fas fa-${next === 'dark' ? 'sun' : 'moon'} theme-icon`;
}
function toggleSidebar() {
  const sidebar = document.querySelector('.sidebar');
  const overlay = document.querySelector('.overlay');
  sidebar?.classList.toggle('open');
  overlay?.classList.toggle('active');
}
function initTabs() {
  document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const group = btn.closest('.tab-group') || btn.parentElement.parentElement;
      const target = btn.dataset.tab;
      group.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      document.querySelectorAll('.tab-content').forEach(c => {
        if (c.id === target) c.classList.add('active');
        else c.classList.remove('active');
      });
    });
  });
}
function openModal(id) {
  const modal = document.getElementById(id);
  if (modal) {
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
  }
}
function closeModal(id) {
  const modal = document.getElementById(id);
  if (modal) {
    modal.classList.remove('active');
    document.body.style.overflow = '';
  }
}
document.addEventListener('click', (e) => {
  if (e.target.classList.contains('modal-overlay')) {
    e.target.classList.remove('active');
    document.body.style.overflow = '';
  }
});
function togglePassword(inputId, btn) {
  const input = document.getElementById(inputId);
  if (input.type === 'password') {
    input.type = 'text';
    btn.innerHTML = '<i class="fas fa-eye-slash"></i>';
  } else {
    input.type = 'password';
    btn.innerHTML = '<i class="fas fa-eye"></i>';
  }
}
function autoDismissAlerts() {
  setTimeout(() => {
    document.querySelectorAll('.alert').forEach(alert => {
      alert.style.opacity = '0';
      alert.style.transform = 'translateY(-10px)';
      alert.style.transition = 'all 0.4s ease';
      setTimeout(() => alert.remove(), 400);
    });
  }, 4000);
}
function confirmDelete(msg, form) {
  if (confirm(msg || 'Are you sure you want to delete this?')) {
    form.submit();
  }
}
function animateCounters() {
  document.querySelectorAll('.counter').forEach(el => {
    const target = parseInt(el.dataset.target || el.textContent);
    let current = 0;
    const step = Math.ceil(target / 40);
    const timer = setInterval(() => {
      current = Math.min(current + step, target);
      el.textContent = current.toLocaleString();
      if (current >= target) clearInterval(timer);
    }, 40);
  });
}
function previewPhoto(input, imgEl) {
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = e => {
      document.getElementById(imgEl).src = e.target.result;
    };
    reader.readAsDataURL(input.files[0]);
  }
}
function initiatePayment(orderId, amount, requestId, customerName, customerEmail, customerContact) {
  const options = {
    key: razorpayKeyId,
    amount: amount * 100,
    currency: 'INR',
    name: 'AutoCare Service Center',
    description: 'Vehicle Service Payment',
    image: siteUrl + '/assets/images/logo.png',
    order_id: orderId,
    handler: function(response) {
      document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
      document.getElementById('razorpay_order_id').value = response.razorpay_order_id;
      document.getElementById('razorpay_signature').value = response.razorpay_signature;
      document.getElementById('request_id_pay').value = requestId;
      document.getElementById('paymentForm').submit();
    },
    prefill: {
      name: customerName,
      email: customerEmail,
      contact: customerContact
    },
    theme: { color: '#e63946' },
    modal: {
      ondismiss: function() {
        showToast('Payment cancelled', 'error');
      }
    }
  };
  const rzp = new Razorpay(options);
  rzp.open();
}
function showToast(message, type = 'info') {
  let container = document.getElementById('toast-container');
  if (!container) {
    container = document.createElement('div');
    container.id = 'toast-container';
    container.style.cssText = 'position:fixed;top:80px;right:20px;z-index:99999;display:flex;flex-direction:column;gap:10px;';
    document.body.appendChild(container);
  }
  const toast = document.createElement('div');
  toast.style.cssText = `
    background:${type==='success'?'#10b981':type==='error'?'#ef4444':'#3b82f6'};
    color:white;padding:12px 20px;border-radius:10px;font-size:14px;font-weight:500;
    box-shadow:0 4px 20px rgba(0,0,0,0.15);animation:slideIn 0.3s ease;
    display:flex;align-items:center;gap:10px;min-width:250px;
  `;
  toast.innerHTML = `<i class="fas fa-${type==='success'?'check-circle':type==='error'?'exclamation-circle':'info-circle'}"></i> ${message}`;
  container.appendChild(toast);
  setTimeout(() => { toast.style.opacity = '0'; toast.style.transform = 'translateX(100px)'; setTimeout(() => toast.remove(), 300); }, 3000);
}
document.addEventListener('DOMContentLoaded', () => {
  const theme = localStorage.getItem(themeKey) || 'light';
  const icon = document.querySelector('.theme-icon');
  if (icon) icon.className = `fas fa-${theme === 'dark' ? 'sun' : 'moon'} theme-icon`;
  initTabs();
  autoDismissAlerts();
  animateCounters();
});
const style = document.createElement('style');
style.textContent = `
  @keyframes slideIn { from { opacity:0; transform:translateX(30px); } to { opacity:1; transform:translateX(0); } }
`;
document.head.appendChild(style);
let razorpayKeyId = '';
let siteUrl = '';
