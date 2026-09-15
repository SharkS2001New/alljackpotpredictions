<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/config.php';

// already logged in? skip straight through
if (!empty($_SESSION['admin_logged_in'])) {
    header('Location: index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (hash_equals(ADMIN_USERNAME, $username) && password_verify($password, ADMIN_PASSWORD_HASH)) {
        session_regenerate_id(true);
        $_SESSION['admin_logged_in']  = true;
        $_SESSION['admin_last_active'] = time();
        $redirect = $_GET['redirect'] ?? 'index.php';
        header('Location: ' . ($redirect ?: 'index.php'));
        exit;
    }
    $error = 'Incorrect username or password.';
}

$timedOut = isset($_GET['timeout']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login</title>
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
  [data-theme="light"] { --navy:#0b3d2e; --pink:#c9a227; --pink2:#e0b93a; --paper:#f6f4ef; --border:#d8d3c8; --body:#3a3f52; --muted:#8a8fa8; --red:#c9292a; --card-bg:#fff; }
  [data-theme="dark"]  { --navy:#0b3d2e; --pink:#c9a227; --pink2:#e0b93a; --paper:#0e0f14; --border:#252838; --body:#d8dae2; --muted:#7b80a0; --red:#ff5252; --card-bg:#161822; }
  * { box-sizing: border-box; }
  body {
    margin: 0; min-height: 100vh; display: flex; align-items: center; justify-content: center;
    background: var(--navy); font-family: 'Manrope', 'Avenir Next', sans-serif;
    background-image: linear-gradient(rgba(255,255,255,.03) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.03) 1px, transparent 1px);
    background-size: 40px 40px;
  }
  .login-card {
    width: 100%; max-width: 380px; background: var(--card-bg); border-radius: 14px; padding: 36px 32px;
    box-shadow: 0 24px 64px rgba(0,0,0,.35);
  }
  .top-row { display: flex; align-items: center; justify-content: space-between; margin-bottom: 26px; }
  .logo { display: flex; align-items: center; gap: 10px; }
  .logo-mark { width: 34px; height: 34px; flex-shrink: 0; }
  .logo-text { font-family: 'Outfit', sans-serif; font-size: 1.25rem; font-weight: 800; color: var(--body); letter-spacing: -0.02em; }
  .logo-text span { color: var(--pink); }
  .theme-toggle-btn {
    width: 34px; height: 34px; border-radius: 8px; background: var(--paper); border: 1px solid var(--border);
    display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 1rem; transition: border-color .18s;
  }
  .theme-toggle-btn:hover { border-color: var(--pink); }
  .ti-light { display: none; }
  [data-theme="light"] .ti-dark { display: none; }
  [data-theme="light"] .ti-light { display: inline; }
  h1 { font-family: 'Outfit', sans-serif; font-size: 1.1rem; font-weight: 700; color: var(--body); margin: 0 0 4px; letter-spacing: -0.02em; }
  .sub { font-size: .82rem; color: var(--muted); margin: 0 0 24px; }
  label { display: block; font-size: .7rem; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: var(--muted); margin-bottom: 6px; }
  .field { margin-bottom: 16px; }
  input[type=text], input[type=password] {
    width: 100%; padding: 11px 13px; background: var(--paper); border: 1px solid var(--border); border-radius: 7px;
    font-size: .9rem; font-family: inherit; color: var(--body); transition: border-color .18s;
  }
  input:focus { outline: none; border-color: var(--pink); }
  button[type=submit] {
    width: 100%; margin-top: 6px; padding: 12px; border: none; border-radius: 7px;
    background: var(--pink); color: #0b3d2e; font-weight: 700; font-size: .88rem; cursor: pointer;
    transition: background .18s;
  }
  button[type=submit]:hover { background: var(--pink2); }
  .msg { padding: 10px 12px; border-radius: 6px; font-size: .82rem; font-weight: 600; margin-bottom: 18px; }
  .msg.error { background: rgba(201,41,42,.08); color: var(--red); border: 1px solid rgba(201,41,42,.25); }
  .msg.info { background: rgba(11,61,46,.06); color: var(--navy); border: 1px solid rgba(11,61,46,.15); }
  [data-theme="dark"] .msg.info { background: rgba(45,143,106,.1); color: #e8d48b; }
</style>
</head>
<body>
  <div class="login-card">
    <div class="top-row">
      <div class="logo">
        <svg class="logo-mark" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M18 2L4 8v10c0 9 6.5 15.5 14 17 7.5-1.5 14-8 14-17V8L18 2z" fill="#c9a227"/>
          <path d="M11 14h14M18 14v10" stroke="white" stroke-width="2.8" stroke-linecap="round"/>
          <circle cx="18" cy="10.5" r="2" fill="white"/>
        </svg>
        <div class="logo-text">AllJackpot<span>Predictions</span></div>
      </div>
      <button class="theme-toggle-btn" id="themeToggleBtn" type="button" title="Toggle light/dark">
        <span class="ti-dark">🌙</span><span class="ti-light">☀️</span>
      </button>
    </div>

    <h1>Admin Login</h1>
    <p class="sub">Sign in to manage links and partners.</p>

    <?php if ($error): ?>
      <div class="msg error"><?= htmlspecialchars($error) ?></div>
    <?php elseif ($timedOut): ?>
      <div class="msg info">You were signed out after a period of inactivity.</div>
    <?php endif; ?>

    <form method="post">
      <div class="field">
        <label>Username</label>
        <input type="text" name="username" required autofocus autocomplete="username">
      </div>
      <div class="field">
        <label>Password</label>
        <input type="password" name="password" required autocomplete="current-password">
      </div>
      <button type="submit">Sign In</button>
    </form>
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