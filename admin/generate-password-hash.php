<?php
/**
 * ONE-TIME SETUP TOOL. Use this once to generate your password hash for
 * config.php, then DELETE this file from the server. Leaving it live
 * means anyone who finds the URL can generate hashes — harmless on its
 * own, but there's no reason to leave the door open.
 */
$hash = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['password'])) {
    $hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Generate Password Hash</title>
<style>
  body { font-family: system-ui, sans-serif; background: #f6f4ef; color: #3a3f52; max-width: 560px; margin: 60px auto; padding: 0 20px; }
  h1 { font-size: 1.3rem; color: #0b3d2e; }
  input[type=password] { width: 100%; padding: 10px 12px; border: 1px solid #d8d3c8; border-radius: 6px; font-size: .9rem; }
  button { margin-top: 12px; background: #c9a227; color: #fff; border: none; padding: 10px 20px; border-radius: 6px; font-weight: 700; cursor: pointer; }
  .hash-box { margin-top: 20px; padding: 14px; background: #fff; border: 1px solid #d8d3c8; border-radius: 8px; word-break: break-all; font-family: monospace; font-size: .85rem; }
  .warn { margin-top: 24px; padding: 12px 14px; background: rgba(201,41,42,.08); border: 1px solid rgba(201,41,42,.25); border-radius: 6px; font-size: .82rem; color: #c9292a; }
</style>
</head>
<body>
  <h1>Generate an Admin Password Hash</h1>
  <form method="post">
    <input type="password" name="password" placeholder="Type the password you want to use" required autofocus>
    <button type="submit">Generate Hash</button>
  </form>
  <?php if ($hash): ?>
    <div class="hash-box"><?= htmlspecialchars($hash) ?></div>
    <p>Copy that whole string into <code>ADMIN_PASSWORD_HASH</code> in <code>config.php</code>.</p>
  <?php endif; ?>
  <div class="warn">⚠ Delete this file from the server once you're done — don't leave it live.</div>
</body>
</html>
