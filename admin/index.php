<?php
require_once __DIR__ . '/auth.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard</title>
<script>
  (function () {
    var saved = localStorage.getItem('to-admin-theme');
    var theme = saved || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
    document.documentElement.setAttribute('data-theme', theme);
  })();
</script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Outfit:wght@500;700;800&display=swap" rel="stylesheet">
<style>
  [data-theme="light"] { --navy:#0b3d2e; --navy2:#0f4f3a; --pink:#c9a227; --pink2:#e0b93a; --paper:#f6f4ef; --paper2:#ede9e1; --border:#d8d3c8; --body:#3a3f52; --muted:#8a8fa8; --green:#0a8f5a; --card-bg:#fff; }
  [data-theme="dark"]  { --navy:#0b3d2e; --navy2:#247a58; --pink:#c9a227; --pink2:#e0b93a; --paper:#0e0f14; --paper2:#161822; --border:#252838; --body:#d8dae2; --muted:#7b80a0; --green:#1fd17c; --card-bg:#161822; }
  * { box-sizing: border-box; }
  body {
    margin: 0; min-height: 100vh; background: var(--paper);
    font-family: 'Manrope', 'Avenir Next', sans-serif; color: var(--body);
    transition: background .2s, color .2s;
  }

  .topbar {
    background: var(--navy); padding: 0 32px; height: 64px;
    display: flex; align-items: center; justify-content: space-between;
  }
  .logo { display: flex; align-items: center; gap: 10px; }
  .logo-mark { width: 30px; height: 30px; flex-shrink: 0; }
  .logo-text { font-family: 'Outfit', sans-serif; font-size: 1.1rem; font-weight: 800; color: #fff; }
  .logo-text span { color: var(--pink); }
  .topbar-right { display: flex; align-items: center; gap: 16px; }
  .topbar-label { font-size: .78rem; color: rgba(255,255,255,.5); font-weight: 600; }
  .theme-toggle-btn {
    width: 34px; height: 34px; border-radius: 8px; background: rgba(255,255,255,.08);
    border: 1px solid rgba(255,255,255,.18); display: flex; align-items: center; justify-content: center;
    cursor: pointer; font-size: 1rem; transition: all .18s;
  }
  .theme-toggle-btn:hover { background: rgba(255,255,255,.14); }
  .ti-light { display: none; }
  [data-theme="light"] .ti-dark { display: none; }
  [data-theme="light"] .ti-light { display: inline; }
  .logout-btn {
    font-size: .78rem; font-weight: 700; color: rgba(255,255,255,.7);
    text-decoration: none; padding: 7px 14px; border: 1px solid rgba(255,255,255,.18);
    border-radius: 6px; transition: all .18s;
  }
  .logout-btn:hover { color: #fff; border-color: rgba(255,255,255,.4); background: rgba(255,255,255,.06); }

  .page { max-width: 1000px; margin: 0 auto; padding: 44px 32px 60px; }
  .page-head { margin-bottom: 32px; }
  h1 { font-family: 'Outfit', sans-serif; font-size: 1.7rem; font-weight: 800; color: var(--navy); margin: 0 0 6px; }
  [data-theme="dark"] h1 { color: var(--body); }
  .page-sub { font-size: .9rem; color: var(--muted); margin: 0; }

  .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 18px; }

  .card {
    display: flex; flex-direction: column; gap: 12px;
    background: var(--card-bg); border: 1px solid var(--border); border-radius: 14px;
    padding: 24px; text-decoration: none; color: inherit;
    transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
    position: relative; overflow: hidden;
  }
  .card::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
    background: linear-gradient(90deg, var(--navy), var(--pink));
    transform: scaleX(0); transform-origin: left; transition: transform .25s ease;
  }
  .card:hover { transform: translateY(-3px); box-shadow: 0 16px 40px rgba(11,61,46,.12); border-color: transparent; }
  .card:hover::before { transform: scaleX(1); }

  .card-icon {
    width: 44px; height: 44px; border-radius: 10px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center; font-size: 1.3rem;
    background: rgba(201,162,39,.08);
  }
  .card-title { font-family: 'Outfit', sans-serif; font-size: 1.05rem; font-weight: 800; color: var(--navy); }
  [data-theme="dark"] .card-title { color: var(--body); }
  .card-desc { font-size: .82rem; color: var(--muted); line-height: 1.65; }
  .card-arrow { margin-top: auto; font-size: .74rem; font-weight: 700; color: var(--pink); display: flex; align-items: center; gap: 5px; }
  .card:hover .card-arrow { gap: 8px; }

  .placeholder-card {
    display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px;
    border: 1.5px dashed var(--border); border-radius: 14px; padding: 24px;
    color: var(--muted); font-size: .8rem; text-align: center; min-height: 140px;
  }

  @media(max-width:600px) {
    .topbar { padding: 0 16px; flex-wrap: wrap; height: auto; padding-top: 10px; padding-bottom: 10px; gap: 10px; }
    .topbar-right { flex-wrap: wrap; gap: 8px; }
    .topbar-label { display: none; }
    .page { padding: 24px 16px 40px; }
  }
</style>
</head>
<body>

<div class="topbar">
  <div class="logo">
    <svg class="logo-mark" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
      <path d="M18 2L4 8v10c0 9 6.5 15.5 14 17 7.5-1.5 14-8 14-17V8L18 2z" fill="#c9a227"/>
      <path d="M11 14h14M18 14v10" stroke="white" stroke-width="2.8" stroke-linecap="round"/>
      <circle cx="18" cy="10.5" r="2" fill="white"/>
    </svg>
    <div class="logo-text">AllJackpot<span>Predictions</span> Admin</div>
  </div>
  <div class="topbar-right">
    <span class="topbar-label">Signed in as <?= htmlspecialchars(ADMIN_USERNAME) ?></span>
    <button class="theme-toggle-btn" id="themeToggleBtn" title="Toggle light/dark" type="button">
      <span class="ti-dark">🌙</span><span class="ti-light">☀️</span>
    </button>
    <a class="logout-btn" href="logout.php">Log Out</a>
  </div>
</div>

<div class="page">
  <div class="page-head">
    <h1>Dashboard</h1>
    <p class="page-sub">Pick a tool below.</p>
  </div>

  <div class="grid">

    <a class="card" href="link-manager.php?tab=footer">
      <div class="card-icon">🔗</div>
      <div class="card-title">Text Links &amp; Partner Cards</div>
      <div class="card-desc">Footer sponsor links, the Partners page text links, and the Partner Cards — three tabs. Add, edit, dofollow/nofollow, duration (including permanent), delete.</div>
      <div class="card-arrow">Open →</div>
    </a>

    <!-- Add more tool cards here as you build them:
    <a class="card" href="some-tool.php">
      <div class="card-icon">📊</div>
      <div class="card-title">Tool Name</div>
      <div class="card-desc">What it does.</div>
      <div class="card-arrow">Open →</div>
    </a>
    -->

    <div class="placeholder-card">More tools will appear here as they're built.</div>

  </div>
</div>

<script>
  document.getElementById('themeToggleBtn').addEventListener('click', function () {
    var html = document.documentElement;
    var next = html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
    html.setAttribute('data-theme', next);
    localStorage.setItem('to-admin-theme', next);
  });
</script>
</body>
</html>