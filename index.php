<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>AutoCare — Expert Vehicle Care for Every Wheel</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
:root {
  --blue-dark:   #0a1628;
  --blue-mid:    #0d1f3c;
  --blue-nav:    #102244;
  --blue-card:   #122550;
  --blue-border: #1c3d7a;
  --red:         #e8193c;
  --red-dark:    #c01030;
  --white:       #ffffff;
  --light:       #e8f0ff;
  --muted:       #8ba3cc;
  --text-dark:   #1a1a2e;
}
html { scroll-behavior: smooth; }
body {
  font-family: 'Poppins', sans-serif;
  background: var(--blue-dark);
  color: var(--white);
  overflow-x: hidden;
}
a { text-decoration: none; color: inherit; }
img { display: block; max-width: 100%; }
.topbar {
  background: var(--blue-nav);
  border-bottom: 1px solid var(--blue-border);
  padding: 8px 60px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-size: 13px;
  position: relative;
  z-index: 2000;
}
.topbar-left {
  display: flex;
  align-items: center;
  gap: 20px;
  color: var(--muted);
  font-size: 12px;
}
.topbar-left span { display: flex; align-items: center; gap: 6px; }
.topbar-left i { color: var(--red); }
.topbar-right {
  display: flex;
  align-items: center;
  gap: 16px;
}
.topbar-socials { display: flex; gap: 12px; }
.topbar-socials a { color: var(--muted); font-size: 14px; transition: color .2s; }
.topbar-socials a:hover { color: var(--white); }
.topbar-actions { display: flex; align-items: center; gap: 6px; }
.admin-link {
  color: var(--light);
  font-size: 12px;
  font-weight: 600;
  padding: 5px 12px;
  border-radius: 5px;
  background: rgba(232,25,60,0.15);
  border: 1px solid rgba(232,25,60,0.3);
  text-decoration: none;
  transition: all .2s;
  display: flex;
  align-items: center;
  gap: 5px;
}
.admin-link:hover { background: var(--red); border-color: var(--red); color: #fff; }
.top-dropdown { position: relative; }
.top-drop-btn {
  background: rgba(255,255,255,0.05);
  border: 1px solid var(--blue-border);
  color: var(--light);
  font-family: 'Poppins', sans-serif;
  font-size: 12px;
  font-weight: 600;
  padding: 5px 12px;
  border-radius: 5px;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 6px;
  transition: all .2s;
}
.top-drop-btn:hover, .top-dropdown.open .top-drop-btn {
  background: var(--blue-border); color: #fff;
}
.chev { font-size: 9px; transition: transform .2s; }
.top-dropdown.open .chev { transform: rotate(180deg); }
.top-drop-menu {
  display: none;
  position: absolute;
  top: calc(100% + 6px);
  right: 0;
  background: var(--blue-nav);
  border: 1px solid var(--blue-border);
  border-radius: 8px;
  min-width: 150px;
  overflow: hidden;
  box-shadow: 0 10px 30px rgba(0,0,0,0.4);
  z-index: 3000;
}
.top-dropdown.open .top-drop-menu { display: block; }
.top-drop-menu a {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 14px;
  color: var(--muted);
  font-size: 12px;
  font-weight: 600;
  transition: all .2s;
}
.top-drop-menu a:hover { background: var(--red); color: #fff; }
.navbar {
  background: #fff;
  padding: 14px 60px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  position: sticky;
  top: 0;
  z-index: 1000;
  box-shadow: 0 2px 20px rgba(0,0,0,0.15);
}
.logo {
  font-size: 26px;
  font-weight: 900;
  letter-spacing: 2px;
  background: linear-gradient(135deg, #0d1f3c, #1a4a9c);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  display: flex;
  align-items: center;
  gap: 8px;
}
.logo i {
  background: var(--red);
  -webkit-text-fill-color: transparent;
  -webkit-background-clip: text;
  background-clip: text;
}
.nav-links {
  display: flex;
  align-items: center;
  gap: 4px;
  list-style: none;
}
.nav-links li a {
  color: #333;
  font-size: 13px;
  font-weight: 600;
  letter-spacing: .8px;
  text-transform: uppercase;
  padding: 8px 16px;
  border-radius: 3px;
  transition: all .2s;
  display: flex;
  align-items: center;
  gap: 6px;
}
.nav-links li a:hover, .nav-links li a.active {
  background: var(--red); color: #fff;
}
.nav-links li a.nav-cta {
  background: var(--red);
  color: #fff;
  border: 2px solid var(--red);
}
.nav-links li a.nav-cta:hover {
  background: transparent;
  color: var(--red);
}
.nav-ddwrap { position: relative; }
.nav-ddwrap:hover .nav-ddmenu { display: block; }
.nav-ddmenu {
  display: none;
  position: absolute;
  top: 100%;
  right: 0;
  padding-top: 8px;
  min-width: 190px;
  z-index: 2000;
}
.nav-ddmenu-inner {
  background: #fff;
  border: 1px solid #e0e8ff;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 12px 40px rgba(0,0,0,0.15);
  animation: dropIn .2s ease;
}
@keyframes dropIn { from { opacity:0; transform:translateY(-8px); } to { opacity:1; transform:translateY(0); } }
.nav-ddmenu a {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 11px 16px;
  color: #444;
  font-size: 13px;
  font-weight: 600;
  transition: all .2s;
}
.nav-ddmenu a:hover { background: var(--red); color: #fff; }
.nav-ddmenu a i { width: 16px; }
.nav-ddiv { height: 1px; background: #e0e8ff; }
.hbtn {
  display: none;
  background: none;
  border: 1px solid #ddd;
  color: #333;
  padding: 8px 10px;
  border-radius: 4px;
  cursor: pointer;
  font-size: 1rem;
  transition: all .2s;
}
.hbtn:hover { border-color: var(--red); color: var(--red); }
.mobmenu {
  display: none;
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 990;
  background: var(--blue-dark);
  padding: 80px 1.5rem 1.5rem;
  flex-direction: column;
  gap: .5rem;
  overflow-y: auto;
}
.mobmenu.open { display: flex; animation: slideIn .3s ease; }
@keyframes slideIn { from { opacity:0; transform:translateX(-20px); } to { opacity:1; transform:translateX(0); } }
.mobmenu a {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 13px 16px;
  border-radius: 6px;
  color: var(--light);
  font-weight: 600;
  border: 1px solid var(--blue-border);
  transition: all .2s;
}
.mobmenu a:hover { border-color: var(--red); color: #fff; background: rgba(232,25,60,0.1); }
.mobmenu a i { color: var(--red); width: 18px; }
.mlbl {
  font-size: .65rem;
  color: var(--muted);
  letter-spacing: 2px;
  text-transform: uppercase;
  padding: 8px 16px;
  margin-top: 8px;
}
.mobdiv { height: 1px; background: var(--blue-border); margin: 4px 0; }
.hero {
  position: relative;
  height: 600px;
  overflow: hidden;
}
.slide {
  position: absolute;
  inset: 0;
  opacity: 0;
  transition: opacity .8s ease;
  display: flex;
  align-items: center;
}
.slide.active { opacity: 1; }
.slide-bg {
  position: absolute;
  inset: 0;
  background-size: cover;
  background-position: center;
}
.slide-1 .slide-bg {
  background: url('https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=1800&q=80') center/cover;
}
.slide-1 .slide-bg::after {
  content: '';
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, rgba(10,22,40,0.93) 0%, rgba(10,22,40,0.7) 60%, rgba(10,22,40,0.5) 100%);
}
.slide-2 .slide-bg {
  background: url('https://images.unsplash.com/photo-1558981806-ec527fa84c39?w=1800&q=80') center/cover;
}
.slide-2 .slide-bg::after {
  content: '';
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, rgba(10,22,40,0.93) 0%, rgba(10,22,40,0.7) 60%, rgba(10,22,40,0.5) 100%);
}
.slide-3 .slide-bg {
  background: url('https://images.unsplash.com/photo-1494976388531-d1058494cdd8?w=1800&q=80') center/cover;
}
.slide-3 .slide-bg::after {
  content: '';
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, rgba(10,22,40,0.93) 0%, rgba(10,22,40,0.7) 60%, rgba(10,22,40,0.5) 100%);
}
.slide-content {
  position: relative;
  z-index: 2;
  padding: 0 80px;
  max-width: 720px;
}
.slide-tag {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: rgba(232,25,60,0.15);
  border: 1px solid rgba(232,25,60,0.4);
  color: #ff6b80;
  padding: 6px 16px;
  border-radius: 4px;
  font-size: 12px;
  font-weight: 700;
  letter-spacing: 2px;
  text-transform: uppercase;
  margin-bottom: 20px;
}
.pulse {
  width: 7px; height: 7px;
  background: var(--red);
  border-radius: 50%;
  animation: pulse 1.5s ease-in-out infinite;
  flex-shrink: 0;
}
@keyframes pulse {
  0%,100% { box-shadow: 0 0 0 0 rgba(232,25,60,0.5); }
  70% { box-shadow: 0 0 0 8px rgba(232,25,60,0); }
}
.slide.active .slide-content { animation: fadeInUp .7s ease both; }
@keyframes fadeInUp { from { opacity:0; transform:translateY(30px); } to { opacity:1; transform:translateY(0); } }
.slide-content h1 {
  font-size: clamp(34px, 5vw, 58px);
  font-weight: 800;
  line-height: 1.1;
  color: #fff;
  margin-bottom: 16px;
  text-shadow: 0 2px 20px rgba(0,0,0,0.3);
}
.slide-content h1 span { color: var(--red); }
.slide-content p {
  font-size: 15px;
  color: rgba(255,255,255,0.75);
  line-height: 1.75;
  margin-bottom: 30px;
  max-width: 520px;
}
.slide-btns { display: flex; gap: 12px; flex-wrap: wrap; }
.btn-primary {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 13px 30px;
  background: var(--red);
  color: #fff;
  font-size: 13px;
  font-weight: 700;
  letter-spacing: 1px;
  text-transform: uppercase;
  border: 2px solid var(--red);
  border-radius: 3px;
  transition: all .25s;
}
.btn-primary:hover { background: transparent; color: #fff; }
.btn-outline {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 13px 30px;
  background: transparent;
  color: #fff;
  font-size: 13px;
  font-weight: 700;
  letter-spacing: 1px;
  text-transform: uppercase;
  border: 2px solid rgba(255,255,255,0.4);
  border-radius: 3px;
  transition: all .25s;
}
.btn-outline:hover { border-color: #fff; background: rgba(255,255,255,0.08); }
.slider-arrow {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  z-index: 10;
  width: 46px; height: 46px;
  background: rgba(255,255,255,0.1);
  border: 2px solid rgba(255,255,255,0.3);
  color: #fff;
  font-size: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all .2s;
  border-radius: 3px;
}
.slider-arrow:hover { background: var(--red); border-color: var(--red); }
.slider-arrow.prev { left: 20px; }
.slider-arrow.next { right: 20px; }
.slider-dots {
  position: absolute;
  bottom: 24px;
  left: 50%;
  transform: translateX(-50%);
  display: flex;
  gap: 8px;
  z-index: 10;
}
.dot {
  width: 10px; height: 10px;
  border-radius: 50%;
  background: rgba(255,255,255,0.35);
  cursor: pointer;
  transition: all .2s;
}
.dot.active { background: var(--red); transform: scale(1.3); }
.ticker-bar {
  background: var(--red);
  padding: 10px 0;
  overflow: hidden;
  position: relative;
}
.ticker-track {
  display: flex;
  gap: 0;
  animation: scrollTicker 28s linear infinite;
  width: max-content;
}
.ticker-track:hover { animation-play-state: paused; }
.ticker-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 0 2.5rem;
  border-right: 1px solid rgba(255,255,255,0.25);
  white-space: nowrap;
  font-size: 12px;
  font-weight: 700;
  letter-spacing: 1.5px;
  text-transform: uppercase;
  color: rgba(255,255,255,0.9);
}
.ticker-item i { font-size: .9rem; opacity: .8; }
@keyframes scrollTicker { from { transform: translateX(0); } to { transform: translateX(-50%); } }
.stats-bar {
  background: var(--blue-nav);
  border-bottom: 1px solid var(--blue-border);
  padding: 34px 60px;
}
.stats-inner {
  max-width: 1100px;
  margin: 0 auto;
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 20px;
  text-align: center;
}
.stat-item { position: relative; }
.stat-item:not(:last-child)::after {
  content: '';
  position: absolute;
  right: 0; top: 20%;
  height: 60%;
  width: 1px;
  background: var(--blue-border);
}
.stat-item h3 {
  font-size: 38px;
  font-weight: 900;
  color: var(--red);
  line-height: 1;
}
.stat-item p {
  font-size: 12px;
  color: var(--muted);
  font-weight: 600;
  letter-spacing: 1.5px;
  text-transform: uppercase;
  margin-top: 6px;
}
.stat-item .stat-icon {
  font-size: 1.4rem;
  margin-bottom: 4px;
}
.vehicles {
  background: #f8f9ff;
  padding: 72px 60px;
}
.section-header {
  text-align: center;
  margin-bottom: 50px;
}
.section-header .tag {
  display: inline-block;
  color: var(--red);
  font-size: 12px;
  font-weight: 700;
  letter-spacing: 3px;
  text-transform: uppercase;
  margin-bottom: 10px;
}
.section-header h2 {
  font-size: 36px;
  font-weight: 800;
  color: var(--text-dark);
  margin-bottom: 12px;
}
.section-header h2 span { color: var(--red); }
.section-header p {
  color: #666;
  font-size: 14px;
  max-width: 520px;
  margin: 0 auto;
  line-height: 1.7;
}
.vcat-grid {
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  gap: 20px;
  max-width: 1150px;
  margin: 0 auto;
}
.vcat {
  background: #fff;
  border: 1px solid #e0e8ff;
  border-radius: 12px;
  overflow: hidden;
  transition: all .35s;
  cursor: pointer;
  position: relative;
}
.vcat::before {
  content: '';
  position: absolute;
  top: 0; left: 0; right: 0;
  height: 3px;
  background: var(--red);
  transform: scaleX(0);
  transition: transform .3s;
}
.vcat:hover::before { transform: scaleX(1); }
.vcat:hover {
  transform: translateY(-8px);
  box-shadow: 0 20px 50px rgba(232,25,60,0.12);
  border-color: var(--red);
}
.vcat-img {
  height: 180px;
  overflow: hidden;
  position: relative;
}
.vcat-img img {
  width: 100%; height: 100%;
  object-fit: cover;
  transition: transform .5s;
  filter: saturate(.85);
}
.vcat:hover .vcat-img img { transform: scale(1.08); filter: saturate(1.2); }
.vcat-img::after {
  content: '';
  position: absolute;
  inset: 0;
  background: linear-gradient(180deg, transparent 40%, rgba(10,22,40,0.85));
}
.vcat-badge {
  position: absolute;
  top: 10px; right: 10px;
  background: var(--red);
  color: #fff;
  padding: 3px 10px;
  border-radius: 3px;
  font-size: 10px;
  font-weight: 700;
  letter-spacing: .5px;
  z-index: 3;
}
.vcat-info {
  position: absolute;
  bottom: 0; left: 0; right: 0;
  padding: 14px;
  z-index: 2;
}
.vcat-icon {
  width: 38px; height: 38px;
  background: rgba(232,25,60,0.15);
  border: 1px solid rgba(232,25,60,0.3);
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.1rem;
  margin-bottom: 6px;
}
.vcat:hover .vcat-icon { background: var(--red); border-color: var(--red); }
.vcat h3 {
  font-size: 1rem;
  font-weight: 700;
  color: #fff;
  letter-spacing: .5px;
  margin-bottom: 2px;
}
.vcat p {
  font-size: .7rem;
  color: rgba(255,255,255,0.65);
  line-height: 1.4;
}
.services {
  background: var(--blue-dark);
  padding: 72px 60px;
}
.services .section-header h2 { color: #fff; }
.services .section-header p { color: var(--muted); }
.services-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 24px;
  max-width: 1150px;
  margin: 0 auto;
}
.service-card {
  background: var(--blue-card);
  border: 1px solid var(--blue-border);
  border-radius: 12px;
  padding: 32px 24px;
  text-align: center;
  transition: all .3s;
  position: relative;
  overflow: hidden;
}
.service-card::before {
  content: '';
  position: absolute;
  top: 0; left: 0; right: 0;
  height: 3px;
  background: var(--red);
  transform: scaleX(0);
  transition: transform .3s;
}
.service-card:hover::before { transform: scaleX(1); }
.service-card:hover {
  transform: translateY(-6px);
  border-color: rgba(232,25,60,0.4);
  box-shadow: 0 20px 50px rgba(0,0,0,0.4);
}
.service-icon {
  width: 68px; height: 68px;
  background: linear-gradient(135deg, #0d1f3c, #1a4a9c);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 18px;
  font-size: 26px;
  border: 2px solid var(--blue-border);
  transition: all .3s;
}
.service-card:hover .service-icon {
  background: var(--red);
  border-color: var(--red);
}
.service-card h3 {
  font-size: 16px;
  font-weight: 700;
  color: var(--white);
  margin-bottom: 10px;
  letter-spacing: .5px;
}
.service-card p {
  font-size: 13px;
  color: var(--muted);
  line-height: 1.7;
}
.service-arr {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  color: var(--red);
  font-size: 12px;
  font-weight: 700;
  margin-top: 14px;
  opacity: 0;
  transform: translateX(-6px);
  transition: all .3s;
}
.service-card:hover .service-arr { opacity: 1; transform: translateX(0); }
.svc-tabs-sec {
  background: #fff;
  padding: 72px 60px;
}
.svc-tabs-sec .section-header h2 { color: var(--text-dark); }
.vtabs {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
  justify-content: center;
  margin-bottom: 36px;
}
.vtab {
  background: #f0f4ff;
  border: 1px solid #dde6ff;
  color: #555;
  padding: 10px 22px;
  border-radius: 3px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: all .25s;
  display: flex;
  align-items: center;
  gap: 8px;
  font-family: 'Poppins', sans-serif;
  text-transform: uppercase;
  letter-spacing: .5px;
}
.vtab:hover, .vtab.active {
  background: var(--red);
  border-color: var(--red);
  color: #fff;
}
.vtab-panel { display: none; animation: panelIn .4s ease; }
.vtab-panel.active {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 3rem;
  align-items: center;
  max-width: 1100px;
  margin: 0 auto;
}
@keyframes panelIn { from { opacity:0; transform:translateY(12px); } to { opacity:1; transform:translateY(0); } }
.vtab-img {
  border-radius: 10px;
  overflow: hidden;
  border: 1px solid #e0e8ff;
  box-shadow: 0 15px 50px rgba(0,0,0,0.12);
}
.vtab-img img { width: 100%; height: 300px; object-fit: cover; }
.vtab-txt h3 {
  font-size: 30px;
  font-weight: 800;
  color: var(--text-dark);
  margin-bottom: 12px;
  line-height: 1.1;
  letter-spacing: 1px;
  text-transform: uppercase;
}
.vtab-txt h3 span { color: var(--red); }
.vtab-txt > p { color: #666; line-height: 1.75; margin-bottom: 20px; font-size: 14px; }
.vtab-feats { display: flex; flex-direction: column; gap: 8px; margin-bottom: 24px; }
.vfeat { display: flex; align-items: center; gap: 10px; font-size: 13px; color: #444; }
.vfeat i { color: var(--red); }
.vtab-price {
  background: #f8f9ff;
  border: 1px solid #dde6ff;
  border-radius: 8px;
  padding: 14px 18px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.vp-from { font-size: 11px; color: #888; font-weight: 600; letter-spacing: .5px; text-transform: uppercase; }
.vp-val { font-size: 28px; font-weight: 900; color: var(--red); line-height: 1; margin-top: 2px; }
.process { background: var(--blue-nav); padding: 72px 60px; }
.process .section-header h2 { color: #fff; }
.process .section-header p { color: var(--muted); }
.steps {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 0;
  max-width: 1000px;
  margin: 0 auto;
  position: relative;
}
.steps::before {
  content: '';
  position: absolute;
  top: 38px;
  left: 12%; right: 12%;
  height: 2px;
  background: linear-gradient(90deg, var(--blue-border), var(--red), var(--blue-border));
  opacity: .6;
}
.step { text-align: center; padding: 2rem 1rem; position: relative; z-index: 1; }
.step-n {
  width: 76px; height: 76px;
  border-radius: 50%;
  border: 2px solid var(--blue-border);
  background: var(--blue-card);
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 18px;
  font-size: 28px;
  font-weight: 900;
  color: var(--red);
  transition: all .3s;
}
.step:hover .step-n {
  background: var(--red);
  border-color: var(--red);
  color: #fff;
  transform: scale(1.1);
  box-shadow: 0 0 30px rgba(232,25,60,0.35);
}
.step h3 { font-size: 14px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; margin-bottom: 6px; color: #fff; }
.step p { color: var(--muted); font-size: 12px; line-height: 1.6; }
.about { background: #fff; padding: 72px 60px; }
.about-inner {
  max-width: 1100px;
  margin: 0 auto;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 60px;
  align-items: center;
}
.about-img-wrap {
  position: relative;
  border-radius: 10px;
  overflow: hidden;
  border: 1px solid #e0e8ff;
  box-shadow: 0 20px 60px rgba(0,0,0,0.12);
}
.about-img-wrap img { width: 100%; height: 420px; object-fit: cover; }
.a-float {
  position: absolute;
  background: #fff;
  border: 1px solid #e0e8ff;
  border-radius: 10px;
  padding: 12px 16px;
  display: flex;
  align-items: center;
  gap: 10px;
  box-shadow: 0 8px 30px rgba(0,0,0,0.12);
  z-index: 2;
}
.af1 { bottom: -16px; left: -16px; animation: float1 5s ease-in-out infinite; }
.af2 { top: -16px; right: -16px; animation: float2 5s ease-in-out infinite; }
@keyframes float1 { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-8px); } }
@keyframes float2 { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-6px); } }
.af-ico {
  width: 38px; height: 38px;
  background: rgba(232,25,60,0.1);
  border: 1px solid rgba(232,25,60,0.2);
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--red);
}
.af-big { font-size: 1.2rem; font-weight: 900; color: var(--red); line-height: 1; }
.af-sm { font-size: 11px; color: #888; }
.about-text .tag {
  display: inline-block;
  color: var(--red);
  font-size: 12px;
  font-weight: 700;
  letter-spacing: 3px;
  text-transform: uppercase;
  margin-bottom: 12px;
}
.about-text h2 {
  font-size: 32px;
  font-weight: 800;
  color: var(--text-dark);
  margin-bottom: 16px;
  line-height: 1.2;
}
.about-text h2 span { color: var(--red); }
.about-text > p {
  color: #666;
  font-size: 14px;
  line-height: 1.8;
  margin-bottom: 14px;
}
.feat-list { display: flex; flex-direction: column; gap: 12px; margin: 20px 0 28px; }
.fi { display: flex; align-items: flex-start; gap: 12px; }
.fi-ico {
  width: 28px; height: 28px;
  background: var(--red);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  margin-top: 2px;
}
.fi-ico i { color: #fff; font-size: 11px; }
.fi h4 { font-size: 14px; font-weight: 700; color: var(--text-dark); margin-bottom: 2px; }
.fi p { font-size: 13px; color: #666; line-height: 1.5; }
.gallery { background: #f8f9ff; padding: 72px 60px; }
.gallery .section-header h2 { color: var(--text-dark); }
.gallery-grid {
  display: grid;
  grid-template-columns: repeat(12, 1fr);
  grid-template-rows: 210px 210px;
  gap: 14px;
  max-width: 1150px;
  margin: 0 auto;
}
.gc {
  border-radius: 10px;
  overflow: hidden;
  position: relative;
  cursor: pointer;
  border: 1px solid #e0e8ff;
  transition: all .35s;
}
.gc img { width: 100%; height: 100%; object-fit: cover; transition: transform .5s; filter: saturate(.8); }
.gc:hover img { transform: scale(1.07); filter: saturate(1.2); }
.gc::after {
  content: '';
  position: absolute;
  inset: 0;
  background: linear-gradient(180deg, transparent 40%, rgba(10,22,40,0.75));
}
.gc-lbl {
  position: absolute;
  bottom: 0; left: 0; right: 0;
  padding: 14px;
  z-index: 2;
  transform: translateY(8px);
  opacity: 0;
  transition: all .3s;
}
.gc:hover .gc-lbl { transform: translateY(0); opacity: 1; }
.gc:hover { box-shadow: 0 12px 40px rgba(232,25,60,0.15); border-color: rgba(232,25,60,0.3); }
.gc-lbl span {
  background: var(--red);
  color: #fff;
  padding: 4px 12px;
  border-radius: 3px;
  font-size: 12px;
  font-weight: 700;
  letter-spacing: .5px;
}
.gc1 { grid-column: span 4; grid-row: span 2; }
.gc2 { grid-column: span 4; }
.gc3 { grid-column: span 4; }
.gc4 { grid-column: span 3; }
.gc5 { grid-column: span 5; }
.contact { background: var(--blue-dark); padding: 72px 60px; }
.contact .section-header h2 { color: #fff; }
.contact .section-header p { color: var(--muted); }
.contact-grid {
  display: grid;
  grid-template-columns: 1fr 1.4fr;
  gap: 50px;
  max-width: 1100px;
  margin: 0 auto;
}
.ci h3 {
  font-size: 22px;
  font-weight: 800;
  color: #fff;
  letter-spacing: 1px;
  text-transform: uppercase;
  margin-bottom: 10px;
}
.ci > p { color: var(--muted); line-height: 1.7; margin-bottom: 28px; font-size: 14px; }
.cits { display: flex; flex-direction: column; gap: 16px; }
.cit { display: flex; align-items: flex-start; gap: 14px; }
.ci-ico {
  width: 40px; height: 40px;
  background: rgba(232,25,60,0.1);
  border: 1px solid rgba(232,25,60,0.2);
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  color: var(--red);
  font-size: 15px;
}
.ci-lbl { font-size: 10px; color: var(--muted); text-transform: uppercase; letter-spacing: 1.5px; font-weight: 600; }
.ci-val { font-size: 14px; font-weight: 600; color: var(--light); margin-top: 2px; }
.csoc { display: flex; gap: 8px; margin-top: 24px; }
.sb {
  width: 38px; height: 38px;
  border-radius: 6px;
  background: var(--blue-card);
  border: 1px solid var(--blue-border);
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--muted);
  font-size: 14px;
  transition: all .25s;
}
.sb:hover { background: var(--red); border-color: var(--red); color: #fff; transform: translateY(-3px); }
.cf {
  background: var(--blue-card);
  border: 1px solid var(--blue-border);
  border-radius: 12px;
  padding: 28px;
}
.cf h3 {
  font-size: 18px;
  font-weight: 700;
  color: #fff;
  margin-bottom: 20px;
  display: flex;
  align-items: center;
  gap: 8px;
}
.cf h3 i { color: var(--red); }
.r2 { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
.fg { margin-bottom: 14px; }
.fg label {
  display: block;
  font-size: 11px;
  font-weight: 700;
  color: var(--muted);
  margin-bottom: 6px;
  letter-spacing: .8px;
  text-transform: uppercase;
}
.fi3 {
  width: 100%;
  background: var(--blue-mid);
  border: 1px solid var(--blue-border);
  color: var(--white);
  padding: 11px 14px;
  border-radius: 6px;
  font-family: 'Poppins', sans-serif;
  font-size: 13px;
  outline: none;
  transition: all .25s;
}
.fi3:focus { border-color: var(--red); box-shadow: 0 0 0 3px rgba(232,25,60,0.1); }
.fi3::placeholder { color: #3a4f7a; }
textarea.fi3 { resize: vertical; min-height: 100px; }
select.fi3 { cursor: pointer; }
select.fi3 option { background: var(--blue-mid); }
.bsub {
  width: 100%;
  background: var(--red);
  color: #fff;
  padding: 13px;
  border-radius: 6px;
  font-weight: 700;
  font-size: 13px;
  border: 2px solid var(--red);
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  transition: all .25s;
  font-family: 'Poppins', sans-serif;
  letter-spacing: .5px;
  text-transform: uppercase;
}
.bsub:hover { background: transparent; }
.fsok { display: none; text-align: center; padding: 2rem; }
.fsok i { font-size: 3rem; color: var(--red); margin-bottom: 1rem; display: block; }
.fsok h3 { color: #fff; font-size: 1.5rem; font-weight: 800; }
.fsok p { color: var(--muted); margin-top: .5rem; }
.map-wrap {
  border-radius: 10px;
  overflow: hidden;
  border: 1px solid var(--blue-border);
  max-width: 1100px;
  margin: 48px auto 0;
}
.cta-band {
  background: var(--red);
  padding: 52px 60px;
  position: relative;
  overflow: hidden;
}
.cta-band::before {
  content: '';
  position: absolute;
  right: -60px; top: -60px;
  width: 320px; height: 320px;
  background: rgba(255,255,255,0.06);
  border-radius: 50%;
}
.cta-band::after {
  content: '';
  position: absolute;
  left: -40px; bottom: -40px;
  width: 200px; height: 200px;
  background: rgba(255,255,255,0.04);
  border-radius: 50%;
}
.cta-inner {
  max-width: 1100px;
  margin: 0 auto;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 24px;
  position: relative;
  z-index: 1;
}
.cta-text h2 {
  font-size: 28px;
  font-weight: 900;
  color: #fff;
  letter-spacing: 2px;
  text-transform: uppercase;
}
.cta-text p { color: rgba(255,255,255,0.75); font-size: 14px; margin-top: 6px; }
.cta-btns { display: flex; gap: 12px; }
footer {
  background: var(--blue-nav);
  border-top: 1px solid var(--blue-border);
  padding: 60px 60px 28px;
}
.footer-grid {
  max-width: 1100px;
  margin: 0 auto 36px;
  display: grid;
  grid-template-columns: 1.5fr 1fr 1fr 1fr;
  gap: 40px;
}
.footer-brand .logo-f {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 22px;
  font-weight: 900;
  letter-spacing: 2px;
  color: #fff;
  margin-bottom: 14px;
}
.footer-brand .logo-f i { color: var(--red); }
.footer-brand p { color: var(--muted); font-size: 13px; line-height: 1.8; max-width: 240px; }
.footer-brand .contact-line { color: var(--light); font-size: 12px; margin-top: 6px; }
.footer-brand .contact-line span { color: var(--red); font-weight: 700; }
.footer-col h4 {
  font-size: 12px;
  font-weight: 700;
  letter-spacing: 2px;
  text-transform: uppercase;
  color: #fff;
  margin-bottom: 18px;
  padding-bottom: 8px;
  border-bottom: 2px solid var(--red);
  display: inline-block;
}
.footer-col ul { list-style: none; display: flex; flex-direction: column; gap: 9px; }
.footer-col ul li a {
  color: var(--muted);
  font-size: 13px;
  transition: color .2s;
  display: flex;
  align-items: center;
  gap: 8px;
}
.footer-col ul li a::before { content: '›'; color: var(--red); font-size: 16px; line-height: 1; }
.footer-col ul li a:hover { color: #fff; }
.footer-bottom {
  max-width: 1100px;
  margin: 0 auto;
  border-top: 1px solid var(--blue-border);
  padding-top: 22px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 12px;
}
.footer-socials { display: flex; gap: 12px; }
.footer-socials a {
  width: 36px; height: 36px;
  background: var(--blue-card);
  border: 1px solid var(--blue-border);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--muted);
  font-size: 13px;
  transition: all .2s;
}
.footer-socials a:hover { background: var(--red); border-color: var(--red); color: #fff; }
.footer-copy { font-size: 12px; color: var(--muted); }
.footer-links { display: flex; gap: 18px; }
.footer-links a { font-size: 12px; color: var(--muted); transition: color .2s; }
.footer-links a:hover { color: var(--red); }
.back-top {
  position: fixed;
  bottom: 24px; right: 24px;
  width: 42px; height: 42px;
  background: var(--red);
  color: #fff;
  border: none;
  font-size: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  border-radius: 4px;
  transition: opacity .3s, transform .3s;
  opacity: 0;
  transform: translateY(20px);
  z-index: 999;
}
.back-top.show { opacity: 1; transform: translateY(0); }
.back-top:hover { background: var(--red-dark); }
.rv { opacity: 0; transform: translateY(30px); transition: all .65s cubic-bezier(.16,1,.3,1); }
.rv.vs { opacity: 1; transform: translateY(0); }
.d1 { transition-delay: .1s; } .d2 { transition-delay: .2s; }
.d3 { transition-delay: .3s; } .d4 { transition-delay: .4s; }
@media (max-width: 1024px) {
  .vcat-grid { grid-template-columns: repeat(3, 1fr); }
  .gallery-grid { grid-template-columns: repeat(2, 1fr); grid-template-rows: auto; }
  .gc1,.gc2,.gc3,.gc4,.gc5 { grid-column: span 1; }
  .vtab-panel.active { grid-template-columns: 1fr; }
  .vtab-img { display: none; }
}
@media (max-width: 900px) {
  .topbar, .navbar, footer, .vehicles, .services, .svc-tabs-sec, .process, .about, .gallery, .contact, .cta-band, .stats-bar { padding-left: 24px; padding-right: 24px; }
  .hero .slide-content { padding: 0 24px; }
  .about-inner { grid-template-columns: 1fr; }
  .af1,.af2 { display: none; }
  .contact-grid { grid-template-columns: 1fr; }
  .stats-inner { grid-template-columns: 1fr 1fr; }
  .footer-grid { grid-template-columns: 1fr 1fr; gap: 24px; }
  .footer-bottom { flex-direction: column; text-align: center; }
  .steps { grid-template-columns: repeat(2,1fr); }
  .steps::before { display: none; }
  .cta-inner { flex-direction: column; text-align: center; }
  .cta-btns { justify-content: center; }
}
@media (max-width: 768px) {
  .nav-links { display: none; }
  .hbtn { display: flex; align-items: center; }
  .vcat-grid { grid-template-columns: repeat(2, 1fr); }
  .r2 { grid-template-columns: 1fr; }
  .topbar { display: none; }
}
@media (max-width: 520px) {
  .vcat-grid { grid-template-columns: 1fr 1fr; }
  .services-grid { grid-template-columns: 1fr; }
  .footer-grid { grid-template-columns: 1fr; }
  .slide-btns { flex-direction: column; }
  .gallery-grid { grid-template-columns: 1fr; }
  .steps { grid-template-columns: 1fr; }
}
</style>
</head>
<body>
<div class="topbar">
  <div class="topbar-left">
    <span><i class="fas fa-phone-alt"></i> +91 98765 43210</span>
    <span><i class="fas fa-envelope"></i> support@autocare.com</span>
    <span><i class="fas fa-clock"></i> Mon–Sat: 8AM–8PM</span>
  </div>
  <div class="topbar-right">
    <div class="topbar-socials">
      <a href="#"><i class="fab fa-facebook-f"></i></a>
      <a href="#"><i class="fab fa-instagram"></i></a>
      <a href="#"><i class="fab fa-twitter"></i></a>
      <a href="#"><i class="fab fa-whatsapp"></i></a>
    </div>
    <div class="topbar-actions">
      <a href="admin/login.php" class="admin-link"><i class="fas fa-shield-alt"></i> Admin</a>
      <div class="top-dropdown">
        <div class="top-drop-btn" onclick="toggleDrop(this)">
          <i class="fas fa-wrench"></i> Mechanic <i class="fas fa-chevron-down chev"></i>
        </div>
        <div class="top-drop-menu">
          <a href="mechanic/login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
        </div>
      </div>
      <div class="top-dropdown">
        <div class="top-drop-btn" onclick="toggleDrop(this)">
          <i class="fas fa-user"></i> Customer <i class="fas fa-chevron-down chev"></i>
        </div>
        <div class="top-drop-menu">
          <a href="customer/signup.php"><i class="fas fa-user-plus"></i> Register</a>
          <a href="customer/login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
        </div>
      </div>
    </div>
  </div>
</div>
<nav class="navbar" id="nav">
  <div class="logo"><i class="fas fa-fire"></i> Auto<span style="color:var(--red);">Care</span></div>
  <ul class="nav-links">
    <li><a href="#home" class="active">HOME</a></li>
    <li><a href="#vehicles">VEHICLES</a></li>
    <li><a href="#services">SERVICES</a></li>
    <li><a href="#about">ABOUT US</a></li>
    <li><a href="#contact">CONTACT</a></li>
    <li class="nav-ddwrap">
      <a class="nav-cta" style="cursor:pointer;">GET STARTED <i class="fas fa-chevron-down" style="font-size:.6rem"></i></a>
      <div class="nav-ddmenu">
        <div class="nav-ddmenu-inner">
          <a href="customer/login.php"><i class="fas fa-user"></i> Customer Login</a>
          <div class="nav-ddiv"></div>
          <a href="admin/login.php"><i class="fas fa-user-shield"></i> Admin Login</a>
          <div class="nav-ddiv"></div>
          <a href="mechanic/login.php"><i class="fas fa-wrench"></i> Mechanic Login</a>
        </div>
      </div>
    </li>
  </ul>
  <button class="hbtn" onclick="toggleMob()" id="hb"><i class="fas fa-bars"></i></button>
</nav>
<div class="mobmenu" id="mob">
  <span class="mlbl">Navigation</span>
  <a href="#home" onclick="closeMob()"><i class="fas fa-home"></i> Home</a>
  <a href="#vehicles" onclick="closeMob()"><i class="fas fa-car"></i> Vehicles</a>
  <a href="#services" onclick="closeMob()"><i class="fas fa-tools"></i> Services</a>
  <a href="#about" onclick="closeMob()"><i class="fas fa-info-circle"></i> About</a>
  <a href="#contact" onclick="closeMob()"><i class="fas fa-envelope"></i> Contact</a>
  <div class="mobdiv"></div>
  <span class="mlbl">Portals</span>
  <a href="customer/signup.php"><i class="fas fa-user-plus"></i> Customer Signup</a>
  <a href="customer/login.php"><i class="fas fa-user"></i> Customer Login</a>
  <a href="admin/login.php"><i class="fas fa-user-shield"></i> Admin Login</a>
  <a href="mechanic/login.php"><i class="fas fa-wrench"></i> Mechanic Login</a>
</div>
<section class="hero" id="home">
  <div class="slide active slide-1">
    <div class="slide-bg"></div>
    <div class="slide-content">
      <div class="slide-tag"><span class="pulse"></span>&nbsp; NOW ACCEPTING ONLINE BOOKINGS</div>
      <h1>Expert Care for<br><span>Every Vehicle</span><br>We Serve</h1>
      <p>2-wheelers, 3-wheelers, 4-wheelers — cars, motorcycles, scooters, autos & taxis. Expert breakdown assistance, real-time repair tracking, and secure online payments.</p>
      <div class="slide-btns">
        <a href="customer/signup.php" class="btn-primary"><i class="fas fa-wrench"></i> Book Service Now</a>
        <a href="#vehicles" class="btn-outline"><i class="fas fa-chevron-down"></i> Explore Vehicles</a>
      </div>
    </div>
  </div>
  <div class="slide slide-2">
    <div class="slide-bg"></div>
    <div class="slide-content">
      <div class="slide-tag"><span class="pulse"></span>&nbsp; 24/7 ROADSIDE ASSISTANCE</div>
      <h1>Two-Wheelers,<br>Three-Wheelers &<br><span>Four-Wheelers</span></h1>
      <p>Motorcycles, scooters, auto rickshaws, cars and taxis — our certified specialist teams handle every vehicle type with dedicated tools and transparent pricing.</p>
      <div class="slide-btns">
        <a href="customer/signup.php" class="btn-primary"><i class="fas fa-user-plus"></i> Create Free Account</a>
        <a href="#services" class="btn-outline"><i class="fas fa-list"></i> Our Services</a>
      </div>
    </div>
  </div>
  <div class="slide slide-3">
    <div class="slide-bg"></div>
    <div class="slide-content">
      <div class="slide-tag"><span class="pulse"></span>&nbsp; 200+ EXPERT MECHANICS</div>
      <h1>Fast, Reliable &<br><span>Transparent</span><br>Vehicle Service</h1>
      <p>Real-time tracking, clear invoices before work begins, and a 30-day re-repair guarantee. Sign up in under 60 seconds and get your vehicle back on the road.</p>
      <div class="slide-btns">
        <a href="customer/signup.php" class="btn-primary"><i class="fas fa-bolt"></i> Get Started Today</a>
        <a href="#about" class="btn-outline"><i class="fas fa-info-circle"></i> Learn More</a>
      </div>
    </div>
  </div>
  <button class="slider-arrow prev" onclick="changeSlide(-1)"><i class="fas fa-chevron-left"></i></button>
  <button class="slider-arrow next" onclick="changeSlide(1)"><i class="fas fa-chevron-right"></i></button>
  <div class="slider-dots">
    <div class="dot active" onclick="goSlide(0)"></div>
    <div class="dot" onclick="goSlide(1)"></div>
    <div class="dot" onclick="goSlide(2)"></div>
  </div>
</section>
<div class="ticker-bar">
  <div class="ticker-track">
    <div class="ticker-item"><i class="fas fa-motorcycle"></i> MOTORCYCLES</div>
    <div class="ticker-item"><i class="fas fa-bicycle"></i> SCOOTERS</div>
    <div class="ticker-item"><i class="fas fa-car"></i> CARS</div>
    <div class="ticker-item"><i class="fas fa-taxi"></i> TAXIS</div>
    <div class="ticker-item"><i class="fas fa-shuttle-van"></i> AUTOS</div>
    <div class="ticker-item"><i class="fas fa-truck"></i> TRUCKS</div>
    <div class="ticker-item"><i class="fas fa-car-side"></i> SUVS</div>
    <div class="ticker-item"><i class="fas fa-bus"></i> VANS</div>
    <div class="ticker-item"><i class="fas fa-motorcycle"></i> MOTORCYCLES</div>
    <div class="ticker-item"><i class="fas fa-bicycle"></i> SCOOTERS</div>
    <div class="ticker-item"><i class="fas fa-car"></i> CARS</div>
    <div class="ticker-item"><i class="fas fa-taxi"></i> TAXIS</div>
    <div class="ticker-item"><i class="fas fa-shuttle-van"></i> AUTOS</div>
    <div class="ticker-item"><i class="fas fa-truck"></i> TRUCKS</div>
    <div class="ticker-item"><i class="fas fa-car-side"></i> SUVS</div>
    <div class="ticker-item"><i class="fas fa-bus"></i> VANS</div>
  </div>
</div>
<div class="stats-bar">
  <div class="stats-inner">
    <div class="stat-item rv">
      <div class="stat-icon">🏍️</div>
      <h3 data-count="5000">0</h3>
      <p>Vehicles Serviced</p>
    </div>
    <div class="stat-item rv d1">
      <div class="stat-icon">👨‍🔧</div>
      <h3 data-count="200">0</h3>
      <p>Expert Mechanics</p>
    </div>
    <div class="stat-item rv d2">
      <div class="stat-icon">⭐</div>
      <h3 data-count="98">0</h3>
      <p>% Satisfaction Rate</p>
    </div>
    <div class="stat-item rv d3">
      <div class="stat-icon">⚡</div>
      <h3>24/7</h3>
      <p>Support Available</p>
    </div>
  </div>
</div>
<section class="vehicles" id="vehicles">
  <div class="section-header rv">
    <div class="tag">— Vehicle Types —</div>
    <h2>Every <span>Wheel</span> We Serve</h2>
    <p>From two-wheelers to four-wheelers — motorcycles to taxis, we have certified specialists for every vehicle type.</p>
  </div>
  <div class="vcat-grid">
    <div class="vcat rv">
      <div class="vcat-img">
        <img src="https://images.unsplash.com/photo-1558981806-ec527fa84c39?w=500&q=85" alt="Motorcycle" loading="lazy">
      </div>
      <div class="vcat-badge">2-WHEELER</div>
      <div class="vcat-info">
        <div class="vcat-icon">🏍️</div>
        <h3>MOTORCYCLES</h3>
        <p>Bikes, cruisers, sports & naked bikes</p>
      </div>
    </div>
    <div class="vcat rv d1">
      <div class="vcat-img">
        <img src="two.jpg" alt="Scooter" loading="lazy" onerror="this.src='https://images.unsplash.com/photo-1571987502951-0c8fbfed6af8?w=500&q=85'">
      </div>
      <div class="vcat-badge">2-WHEELER</div>
      <div class="vcat-info">
        <div class="vcat-icon">🛵</div>
        <h3>SCOOTERS</h3>
        <p>Activa, Dio, Vespa & all gearless</p>
      </div>
    </div>
    <div class="vcat rv d2">
      <div class="vcat-img">
        <img src="auto.jpg" alt="Auto Rickshaw" loading="lazy" onerror="this.src='https://images.unsplash.com/photo-1548013146-72479768bada?w=500&q=85'">
      </div>
      <div class="vcat-badge">3-WHEELER</div>
      <div class="vcat-info">
        <div class="vcat-icon">🛺</div>
        <h3>AUTO RICKSHAWS</h3>
        <p>CNG, electric & petrol autos</p>
      </div>
    </div>
    <div class="vcat rv d3">
      <div class="vcat-img">
        <img src="https://images.unsplash.com/photo-1494976388531-d1058494cdd8?w=500&q=85" alt="Car" loading="lazy">
      </div>
      <div class="vcat-badge">4-WHEELER</div>
      <div class="vcat-info">
        <div class="vcat-icon">🚗</div>
        <h3>CARS</h3>
        <p>Hatchbacks, sedans, SUVs & EVs</p>
      </div>
    </div>
    <div class="vcat rv d4">
      <div class="vcat-img">
        <img src="https://images.unsplash.com/photo-1541899481282-d53bffe3c35d?w=500&q=85" alt="Taxi" loading="lazy">
      </div>
      <div class="vcat-badge">4-WHEELER</div>
      <div class="vcat-info">
        <div class="vcat-icon">🚕</div>
        <h3>TAXIS & CABS</h3>
        <p>Ola, Uber, Innova & all cab types</p>
      </div>
    </div>
  </div>
</section>
<section class="svc-tabs-sec">
  <div class="section-header rv">
    <div class="tag">— Tailored Service —</div>
    <h2>Service Built <span>For Your Ride</span></h2>
    <p>Each vehicle class gets a specialist mechanic, purpose-built tools, and dedicated pricing.</p>
  </div>
  <div class="vtabs">
    <button class="vtab active" onclick="switchTab('moto')" id="tab-moto"><i class="fas fa-motorcycle"></i> Motorcycles</button>
    <button class="vtab" onclick="switchTab('scooter')" id="tab-scooter"><i class="fas fa-bicycle"></i> Scooters</button>
    <button class="vtab" onclick="switchTab('auto')" id="tab-auto"><i class="fas fa-shuttle-van"></i> Auto Rickshaws</button>
    <button class="vtab" onclick="switchTab('car')" id="tab-car"><i class="fas fa-car"></i> Cars</button>
    <button class="vtab" onclick="switchTab('taxi')" id="tab-taxi"><i class="fas fa-taxi"></i> Taxis & Cabs</button>
  </div>
  <div class="vtab-panel active" id="panel-moto">
    <div class="vtab-img"><img src="https://images.unsplash.com/photo-1558981806-ec527fa84c39?w=700&q=85" alt="Motorcycle" loading="lazy"></div>
    <div class="vtab-txt">
      <h3>MOTORCYCLE <span>SERVICE</span></h3>
      <p>Comprehensive care for all bike types — sports, naked, cruiser, adventure and commuter. Our 2-wheeler specialists handle everything from chain adjustments to full engine rebuilds.</p>
      <div class="vtab-feats">
        <div class="vfeat"><i class="fas fa-check-circle"></i> Engine tune-up & oil change</div>
        <div class="vfeat"><i class="fas fa-check-circle"></i> Chain, sprocket & clutch service</div>
        <div class="vfeat"><i class="fas fa-check-circle"></i> Brake disc & pad replacement</div>
        <div class="vfeat"><i class="fas fa-check-circle"></i> Tyre fitting & wheel alignment</div>
        <div class="vfeat"><i class="fas fa-check-circle"></i> Electrical & carburetor tuning</div>
      </div>
      <div class="vtab-price">
        <div><div class="vp-from">Basic Service Starting</div><div class="vp-val">₹499</div></div>
        <a href="customer/signup.php" class="btn-primary" style="font-size:12px;padding:10px 20px"><i class="fas fa-wrench"></i> Book Now</a>
      </div>
    </div>
  </div>
  <div class="vtab-panel" id="panel-scooter">
    <div class="vtab-img"><img src="https://images.unsplash.com/photo-1571987502951-0c8fbfed6af8?w=700&q=85" alt="Scooter" loading="lazy"></div>
    <div class="vtab-txt">
      <h3>SCOOTER <span>SERVICE</span></h3>
      <p>Specialised care for gearless scooters including Honda Activa, TVS Jupiter, Yamaha Fascino and all electric scooters. Quick, affordable and efficient turnaround.</p>
      <div class="vtab-feats">
        <div class="vfeat"><i class="fas fa-check-circle"></i> CVT belt & roller replacement</div>
        <div class="vfeat"><i class="fas fa-check-circle"></i> Engine oil & air filter service</div>
        <div class="vfeat"><i class="fas fa-check-circle"></i> Battery & electric system check</div>
        <div class="vfeat"><i class="fas fa-check-circle"></i> Brake drum & disc inspection</div>
        <div class="vfeat"><i class="fas fa-check-circle"></i> Ignition & spark plug tuning</div>
      </div>
      <div class="vtab-price">
        <div><div class="vp-from">Basic Service Starting</div><div class="vp-val">₹349</div></div>
        <a href="customer/signup.php" class="btn-primary" style="font-size:12px;padding:10px 20px"><i class="fas fa-wrench"></i> Book Now</a>
      </div>
    </div>
  </div>
  <div class="vtab-panel" id="panel-auto">
    <div class="vtab-img"><img src="https://images.unsplash.com/photo-1548013146-72479768bada?w=700&q=85" alt="Auto Rickshaw" loading="lazy"></div>
    <div class="vtab-txt">
      <h3>AUTO <span>RICKSHAW</span></h3>
      <p>Dedicated service for 3-wheelers — CNG autorickshaws, electric e-rickshaws and petrol autos. We understand your vehicle is your livelihood.</p>
      <div class="vtab-feats">
        <div class="vfeat"><i class="fas fa-check-circle"></i> CNG kit inspection & tuning</div>
        <div class="vfeat"><i class="fas fa-check-circle"></i> 2-stroke / 4-stroke engine service</div>
        <div class="vfeat"><i class="fas fa-check-circle"></i> Tyre, rim & axle service</div>
        <div class="vfeat"><i class="fas fa-check-circle"></i> Electrical & indicator wiring</div>
        <div class="vfeat"><i class="fas fa-check-circle"></i> Suspension & steering repair</div>
      </div>
      <div class="vtab-price">
        <div><div class="vp-from">Basic Service Starting</div><div class="vp-val">₹699</div></div>
        <a href="customer/signup.php" class="btn-primary" style="font-size:12px;padding:10px 20px"><i class="fas fa-wrench"></i> Book Now</a>
      </div>
    </div>
  </div>
  <div class="vtab-panel" id="panel-car">
    <div class="vtab-img"><img src="https://images.unsplash.com/photo-1494976388531-d1058494cdd8?w=700&q=85" alt="Car" loading="lazy"></div>
    <div class="vtab-txt">
      <h3>CAR <span>SERVICE</span></h3>
      <p>Full-spectrum car service for hatchbacks, sedans, SUVs and electric vehicles. From periodic servicing to complex repairs — we handle every make and model.</p>
      <div class="vtab-feats">
        <div class="vfeat"><i class="fas fa-check-circle"></i> Full engine diagnostics (OBD2)</div>
        <div class="vfeat"><i class="fas fa-check-circle"></i> Oil, filter & coolant service</div>
        <div class="vfeat"><i class="fas fa-check-circle"></i> AC service & gas recharge</div>
        <div class="vfeat"><i class="fas fa-check-circle"></i> Brake, suspension & alignment</div>
        <div class="vfeat"><i class="fas fa-check-circle"></i> Bodywork & interior detailing</div>
      </div>
      <div class="vtab-price">
        <div><div class="vp-from">Basic Service Starting</div><div class="vp-val">₹1,499</div></div>
        <a href="customer/signup.php" class="btn-primary" style="font-size:12px;padding:10px 20px"><i class="fas fa-wrench"></i> Book Now</a>
      </div>
    </div>
  </div>
  <div class="vtab-panel" id="panel-taxi">
    <div class="vtab-img"><img src="https://images.unsplash.com/photo-1541899481282-d53bffe3c35d?w=700&q=85" alt="Taxi" loading="lazy"></div>
    <div class="vtab-txt">
      <h3>TAXI & CAB <span>SERVICE</span></h3>
      <p>Priority service plans for commercial taxi operators — Ola, Uber, Rapido partners and private cabs. Minimize downtime, maximize revenue with our rapid-turnaround program.</p>
      <div class="vtab-feats">
        <div class="vfeat"><i class="fas fa-check-circle"></i> Priority queue — same-day service</div>
        <div class="vfeat"><i class="fas fa-check-circle"></i> High-mileage engine inspection</div>
        <div class="vfeat"><i class="fas fa-check-circle"></i> CNG / LPG kit service</div>
        <div class="vfeat"><i class="fas fa-check-circle"></i> Commercial vehicle compliance</div>
        <div class="vfeat"><i class="fas fa-check-circle"></i> Fleet maintenance contracts</div>
      </div>
      <div class="vtab-price">
        <div><div class="vp-from">Commercial Plan Starting</div><div class="vp-val">₹2,999</div></div>
        <a href="customer/signup.php" class="btn-primary" style="font-size:12px;padding:10px 20px"><i class="fas fa-wrench"></i> Book Now</a>
      </div>
    </div>
  </div>
</section>
<section class="services" id="services">
  <div class="section-header rv">
    <div class="tag">— What We Offer —</div>
    <h2>Our <span>Services</span></h2>
    <p>Comprehensive vehicle care solutions for every wheel type — engine repair to interior detailing.</p>
  </div>
  <div class="services-grid">
    <div class="service-card rv">
      <div class="service-icon">🔧</div>
      <h3>Engine Repair</h3>
      <p>Full diagnostics & repair for 2W, 3W and 4W engines by certified technicians.</p>
      <div class="service-arr"><i class="fas fa-arrow-right"></i> Learn more</div>
    </div>
    <div class="service-card rv d1">
      <div class="service-icon">🛞</div>
      <h3>Tyre Service</h3>
      <p>Flat repair, balancing, alignment & replacement for all vehicle types.</p>
      <div class="service-arr"><i class="fas fa-arrow-right"></i> Learn more</div>
    </div>
    <div class="service-card rv d2">
      <div class="service-icon">⚡</div>
      <h3>Electrical Systems</h3>
      <p>Battery, wiring, ECU, ignition & all electrical system repairs.</p>
      <div class="service-arr"><i class="fas fa-arrow-right"></i> Learn more</div>
    </div>
    <div class="service-card rv d3">
      <div class="service-icon">🚗</div>
      <h3>Body Work</h3>
      <p>Dent removal, painting, welding & body restoration for all vehicles.</p>
      <div class="service-arr"><i class="fas fa-arrow-right"></i> Learn more</div>
    </div>
    <div class="service-card rv">
      <div class="service-icon">❄️</div>
      <h3>AC Service</h3>
      <p>AC diagnosis, gas refill, compressor repair — cars, taxis & vans.</p>
      <div class="service-arr"><i class="fas fa-arrow-right"></i> Learn more</div>
    </div>
    <div class="service-card rv d1">
      <div class="service-icon">🔩</div>
      <h3>General Service</h3>
      <p>Oil change, filter replacement, brake check & full inspection.</p>
      <div class="service-arr"><i class="fas fa-arrow-right"></i> Learn more</div>
    </div>
    <div class="service-card rv d2">
      <div class="service-icon">🛑</div>
      <h3>Brakes & Suspension</h3>
      <p>Pads, discs, drums, shock absorbers & suspension tuning.</p>
      <div class="service-arr"><i class="fas fa-arrow-right"></i> Learn more</div>
    </div>
    <div class="service-card rv d3">
      <div class="service-icon">🧼</div>
      <h3>Interior Detailing</h3>
      <p>Deep clean, premium upholstery restoration & cabin sanitization.</p>
      <div class="service-arr"><i class="fas fa-arrow-right"></i> Learn more</div>
    </div>
  </div>
</section>
<section class="process">
  <div class="section-header rv">
    <div class="tag" style="color:var(--red)">— How It Works —</div>
    <h2>Fixed in <span>4 Steps</span></h2>
    <p>Simple, fast and transparent — from booking to vehicle pickup regardless of wheel count.</p>
  </div>
  <div class="steps">
    <div class="step rv"><div class="step-n">1</div><h3>SIGN UP</h3><p>Create your free account in under 60 seconds.</p></div>
    <div class="step rv d1"><div class="step-n">2</div><h3>SUBMIT</h3><p>Describe your issue & select your vehicle type.</p></div>
    <div class="step rv d2"><div class="step-n">3</div><h3>TRACK</h3><p>Specialist assigned, watch live repair progress.</p></div>
    <div class="step rv d3"><div class="step-n">4</div><h3>COLLECT</h3><p>Pay securely online, pick up your vehicle.</p></div>
  </div>
</section>
<section class="about" id="about">
  <div class="about-inner">
    <div class="about-img-wrap rv">
      <img src="https://images.unsplash.com/photo-1600880292203-757bb62b4baf?w=700&q=85" alt="AutoCare Team" loading="lazy">
      <div class="a-float af1">
        <div class="af-ico"><i class="fas fa-user-check"></i></div>
        <div><div class="af-big">98%</div><div class="af-sm">Happy Customers</div></div>
      </div>
      <div class="a-float af2">
        <div class="af-ico"><i class="fas fa-tools"></i></div>
        <div><div class="af-big">200+</div><div class="af-sm">Expert Mechanics</div></div>
      </div>
    </div>
    <div class="about-text rv d2">
      <div class="tag">— About AutoCare —</div>
      <h2>Passionate About <span>Every Wheel</span></h2>
      <p>Since 2015, AutoCare has served thousands of vehicle owners across Bengaluru. We deploy 2-wheeler, 3-wheeler and 4-wheeler specialist teams with cutting-edge diagnostic tools to deliver unmatched service quality every single time.</p>
      <p>Our Roadside Assistance Package offers 24/7 emergency support for any mechanical or electrical breakdown. If your vehicle is immovable, we'll tow it to the nearest workshop or legal authorities.</p>
      <div class="feat-list">
        <div class="fi">
          <div class="fi-ico"><i class="fas fa-check"></i></div>
          <div><h4>All Vehicle Specialists</h4><p>Separate certified teams for 2W, 3W and 4W vehicles.</p></div>
        </div>
        <div class="fi">
          <div class="fi-ico"><i class="fas fa-check"></i></div>
          <div><h4>Transparent Pricing</h4><p>No hidden charges. Clear invoice before work begins.</p></div>
        </div>
        <div class="fi">
          <div class="fi-ico"><i class="fas fa-check"></i></div>
          <div><h4>Real-time Tracking</h4><p>Live repair status through your customer dashboard.</p></div>
        </div>
        <div class="fi">
          <div class="fi-ico"><i class="fas fa-check"></i></div>
          <div><h4>30-Day Guarantee</h4><p>Free re-repair if the same issue reoccurs within 30 days.</p></div>
        </div>
      </div>
      <a href="customer/signup.php" class="btn-primary"><i class="fas fa-user-plus"></i> Join AutoCare Today</a>
    </div>
  </div>
</section>
<section class="gallery">
  <div class="section-header rv">
    <div class="tag">— Fleet Experience —</div>
    <h2>We Service <span>All Types</span></h2>
    <p>Every vehicle deserves expert care. From scooters to SUVs, autos to luxury sedans.</p>
  </div>
  <div class="gallery-grid rv">
    <div class="gc gc1">
      <img src="https://images.unsplash.com/photo-1558981806-ec527fa84c39?w=600&q=85" alt="Motorcycle" loading="lazy">
      <div class="gc-lbl"><span>Motorcycles</span></div>
    </div>
    <div class="gc gc2">
      <img src="https://images.unsplash.com/photo-1544636331-e26879cd4d9b?w=500&q=85" alt="Sports Car" loading="lazy">
      <div class="gc-lbl"><span>Sports Cars</span></div>
    </div>
    <div class="gc gc3">
      <img src="https://images.unsplash.com/photo-1571987502951-0c8fbfed6af8?w=500&q=85" alt="Scooter" loading="lazy">
      <div class="gc-lbl"><span>Scooters</span></div>
    </div>
    <div class="gc gc4">
      <img src="https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?w=500&q=85" alt="SUV" loading="lazy">
      <div class="gc-lbl"><span>SUVs & 4x4</span></div>
    </div>
    <div class="gc gc5">
      <img src="https://images.unsplash.com/photo-1548013146-72479768bada?w=500&q=85" alt="Auto" loading="lazy">
      <div class="gc-lbl"><span>Auto Rickshaws</span></div>
    </div>
  </div>
</section>
<section class="contact" id="contact">
  <div class="section-header rv">
    <div class="tag" style="color:var(--red)">— Get In Touch —</div>
    <h2>We're Here to <span>Help You</span></h2>
    <p>Have a question? Send us a message and we'll respond within 24 hours.</p>
  </div>
  <div class="contact-grid">
    <div class="ci rv">
      <h3>CONTACT INFO</h3>
      <p>Reach out through any channel — our team is always ready to assist any vehicle owner.</p>
      <div class="cits">
        <div class="cit">
          <div class="ci-ico"><i class="fas fa-map-marker-alt"></i></div>
          <div><div class="ci-lbl">Address</div><div class="ci-val">123 AutoCare Lane, Bengaluru, Karnataka 560001</div></div>
        </div>
        <div class="cit">
          <div class="ci-ico"><i class="fas fa-phone-alt"></i></div>
          <div><div class="ci-lbl">Phone</div><div class="ci-val">+91 98765 43210</div></div>
        </div>
        <div class="cit">
          <div class="ci-ico"><i class="fas fa-envelope"></i></div>
          <div><div class="ci-lbl">Email</div><div class="ci-val">support@autocare.com</div></div>
        </div>
        <div class="cit">
          <div class="ci-ico"><i class="fas fa-clock"></i></div>
          <div><div class="ci-lbl">Hours</div><div class="ci-val">Mon–Sat: 8AM–8PM &nbsp;|&nbsp; Sun: 10AM–4PM</div></div>
        </div>
      </div>
      <div class="csoc">
        <a href="#" class="sb"><i class="fab fa-facebook-f"></i></a>
        <a href="#" class="sb"><i class="fab fa-instagram"></i></a>
        <a href="#" class="sb"><i class="fab fa-twitter"></i></a>
        <a href="#" class="sb"><i class="fab fa-whatsapp"></i></a>
      </div>
    </div>
    <div class="cf rv d2">
      <h3><i class="fas fa-paper-plane"></i> Send A Message</h3>
      <div id="cForm">
        <div class="r2">
          <div class="fg"><label>Your Name</label><input type="text" class="fi3" placeholder="Your name" id="cn"></div>
          <div class="fg"><label>Phone / Email</label><input type="text" class="fi3" placeholder="+91 or email" id="ce"></div>
        </div>
        <div class="fg">
          <label>Vehicle Type</label>
          <select class="fi3" id="cveh">
            <option value="">— Select your vehicle —</option>
            <option>🏍️ Motorcycle</option>
            <option>🛵 Scooter / Moped</option>
            <option>🛺 Auto Rickshaw (3-Wheeler)</option>
            <option>🚗 Car (Hatchback / Sedan / SUV)</option>
            <option>🚕 Taxi / Cab</option>
            <option>🚛 Truck / Van / Other</option>
          </select>
        </div>
        <div class="fg"><label>Message</label><textarea class="fi3" placeholder="Describe your vehicle issue..." id="cm"></textarea></div>
        <button class="bsub" onclick="sendMsg()"><i class="fas fa-paper-plane"></i> Send Message</button>
      </div>
      <div class="fsok" id="fOk">
        <i class="fas fa-check-circle"></i>
        <h3>Message Sent!</h3>
        <p>Thank you! We'll respond within 24 hours.</p>
      </div>
    </div>
  </div>
  <div class="map-wrap">
    <iframe
      src="https://www.google.com/maps/embed?pb=!1m16!1m12!1m3!1d61555.51474136952!2d75.0886866649779!3d15.36531336427847!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!2m1!1sauto%20care%20hubli%20dharwad!5e0!3m2!1sen!2sin!4v1710150000000!5m2!1sen!2sin"
      width="100%" height="380" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
    </iframe>
  </div>
</section>
<div class="cta-band">
  <div class="cta-inner">
    <div class="cta-text">
      <h2>BOOK YOUR SERVICE TODAY</h2>
      <p>Join 5,000+ happy vehicle owners. Sign up free — bikes, scooters, autos & cars welcome.</p>
    </div>
    <div class="cta-btns">
      <a href="customer/signup.php" class="btn-primary"><i class="fas fa-user-plus"></i> Create Free Account</a>
      <a href="customer/login.php" class="btn-outline"><i class="fas fa-sign-in-alt"></i> Customer Login</a>
    </div>
  </div>
</div>
<footer>
  <div class="footer-grid">
    <div class="footer-brand">
      <div class="logo-f"><i class="fas fa-fire"></i> AutoCare</div>
      <p>India's trusted vehicle breakdown & service center for 2W, 3W and 4W vehicles. Professional, transparent, always on time.</p>
      <div class="contact-line"><span>PHONE:</span> +91 98765 43210</div>
      <div class="contact-line"><span>EMAIL:</span> support@autocare.com</div>
      <div class="contact-line"><span>HOURS:</span> Mon–Sat 8AM–8PM</div>
    </div>
    <div class="footer-col">
      <h4>Quick Links</h4>
      <ul>
        <li><a href="#home">Home</a></li>
        <li><a href="#vehicles">Vehicle Types</a></li>
        <li><a href="#services">Services</a></li>
        <li><a href="#about">About Us</a></li>
        <li><a href="#contact">Contact</a></li>
      </ul>
    </div>
    <div class="footer-col">
      <h4>Portals</h4>
      <ul>
        <li><a href="customer/signup.php">Customer Signup</a></li>
        <li><a href="customer/login.php">Customer Login</a></li>
        <li><a href="admin/login.php">Admin Login</a></li>
        <li><a href="mechanic/login.php">Mechanic Login</a></li>
      </ul>
    </div>
    <div class="footer-col">
      <h4>Services</h4>
      <ul>
        <li><a href="#">2-Wheeler Service</a></li>
        <li><a href="#">3-Wheeler Service</a></li>
        <li><a href="#">4-Wheeler Service</a></li>
        <li><a href="#">Taxi Fleet Plans</a></li>
        <li><a href="#">EV Service</a></li>
      </ul>
    </div>
  </div>
  <div class="footer-bottom">
    <div class="footer-socials">
      <a href="#"><i class="fab fa-facebook-f"></i></a>
      <a href="#"><i class="fab fa-instagram"></i></a>
      <a href="#"><i class="fab fa-twitter"></i></a>
      <a href="#"><i class="fab fa-whatsapp"></i></a>
    </div>
    <div class="footer-copy">&copy; 2025 AutoCare Service Center. All rights reserved.</div>
    <div class="footer-links">
      <a href="#">Privacy Policy</a>
      <a href="#">Terms of Service</a>
    </div>
  </div>
</footer>
<button class="back-top" id="backTop" onclick="window.scrollTo({top:0,behavior:'smooth'})">
  <i class="fas fa-chevron-up"></i>
</button>
<script>
function toggleDrop(btn) {
  const dropdown = btn.closest('.top-dropdown');
  const isOpen = dropdown.classList.contains('open');
  document.querySelectorAll('.top-dropdown').forEach(d => d.classList.remove('open'));
  if (!isOpen) dropdown.classList.add('open');
}
document.addEventListener('click', function(e) {
  if (!e.target.closest('.top-dropdown')) {
    document.querySelectorAll('.top-dropdown').forEach(d => d.classList.remove('open'));
  }
});
function toggleMob() {
  const m = document.getElementById('mob'), h = document.getElementById('hb');
  m.classList.toggle('open');
  h.innerHTML = m.classList.contains('open') ? '<i class="fas fa-times"></i>' : '<i class="fas fa-bars"></i>';
}
function closeMob() {
  document.getElementById('mob').classList.remove('open');
  document.getElementById('hb').innerHTML = '<i class="fas fa-bars"></i>';
}
let current = 0;
const slides = document.querySelectorAll('.slide');
const dots   = document.querySelectorAll('.dot');
let autoTimer;
function goSlide(n) {
  slides[current].classList.remove('active');
  dots[current].classList.remove('active');
  current = (n + slides.length) % slides.length;
  slides[current].classList.add('active');
  dots[current].classList.add('active');
}
function changeSlide(dir) {
  clearInterval(autoTimer);
  goSlide(current + dir);
  startAuto();
}
function startAuto() {
  autoTimer = setInterval(() => goSlide(current + 1), 5000);
}
startAuto();
window.addEventListener('scroll', () => {
  document.getElementById('backTop').classList.toggle('show', window.scrollY > 300);
});
const ro = new IntersectionObserver(entries => {
  entries.forEach(x => {
    if (x.isIntersecting) { x.target.classList.add('vs'); ro.unobserve(x.target); }
  });
}, { threshold: .1 });
document.querySelectorAll('.rv').forEach(r => ro.observe(r));
function anim(el, t) {
  let s = 0;
  const f = ts => {
    if (!s) s = ts;
    const p = Math.min((ts - s) / 1800, 1);
    const v = Math.floor(p * t);
    el.textContent = t >= 1000 ? (v/1000).toFixed(1)+'k+' : t >= 98 ? v+'' : v+'+';
    if (p < 1) requestAnimationFrame(f);
    else el.textContent = t >= 1000 ? (t/1000).toFixed(1)+'k+' : t >= 98 ? t+'' : t+'+';
  };
  requestAnimationFrame(f);
}
const co = new IntersectionObserver(entries => {
  entries.forEach(x => {
    if (x.isIntersecting) { const t = parseInt(x.target.dataset.count); if (t) anim(x.target, t); co.unobserve(x.target); }
  });
}, { threshold: .5 });
document.querySelectorAll('[data-count]').forEach(el => co.observe(el));
document.querySelectorAll('a[href^="#"]').forEach(a => {
  a.addEventListener('click', e => {
    const t = document.querySelector(a.getAttribute('href'));
    if (t) { e.preventDefault(); t.scrollIntoView({ behavior: 'smooth', block: 'start' }); closeMob(); }
  });
});
function switchTab(id) {
  document.querySelectorAll('.vtab').forEach(t => t.classList.remove('active'));
  document.querySelectorAll('.vtab-panel').forEach(p => p.classList.remove('active'));
  document.getElementById('tab-'+id).classList.add('active');
  document.getElementById('panel-'+id).classList.add('active');
}
function sendMsg() {
  const n = document.getElementById('cn').value;
  const e = document.getElementById('ce').value;
  const m = document.getElementById('cm').value;
  if (!n || !e || !m) { alert('Please fill in Name, Contact and Message.'); return; }
  document.getElementById('cForm').style.display = 'none';
  document.getElementById('fOk').style.display = 'block';
}
</script>
</body>
</html>