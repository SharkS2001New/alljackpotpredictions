<?php
// Send the correct HTTP 400 status header
http_response_code(400);
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>400 — Bad Request | AllJackpotPredictions</title>
<meta name="description" content="The request could not be understood by AllJackpotPredictions. Please check the URL and try again.">
<meta name="robots" content="noindex, nofollow">

<!-- ═══════════════════════════════════════════════════════
     FAVICON & ICONS — AllJackpotPredictions  (img/ folder)
     ═══════════════════════════════════════════════════════ -->
 
<!-- Classic .ico — IE, legacy browsers -->
<link rel="icon" href="/img/favicon.ico" sizes="any">
 
<!-- SVG icon — Chrome 80+, Firefox, Edge (scales to any size) -->
<link rel="icon" href="/img/favicon.svg" type="image/svg+xml">
 
<!-- PNG fallbacks for browsers that prefer explicit sizes -->
<link rel="icon" type="image/png" sizes="16x16" href="/img/favicon-16x16.png">
<link rel="icon" type="image/png" sizes="32x32" href="/img/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="48x48" href="/img/favicon-48x48.png">
<link rel="icon" type="image/png" sizes="96x96" href="/img/favicon-96x96.png">
 
<!-- Apple Touch Icons — iOS Safari (add to home screen) -->
<link rel="apple-touch-icon"             href="/img/apple-touch-icon.png">
<link rel="apple-touch-icon" sizes="57x57"   href="/img/apple-touch-icon-57x57.png">
<link rel="apple-touch-icon" sizes="60x60"   href="/img/apple-touch-icon-60x60.png">
<link rel="apple-touch-icon" sizes="72x72"   href="/img/apple-touch-icon-72x72.png">
<link rel="apple-touch-icon" sizes="76x76"   href="/img/apple-touch-icon-76x76.png">
<link rel="apple-touch-icon" sizes="114x114" href="/img/apple-touch-icon-114x114.png">
<link rel="apple-touch-icon" sizes="120x120" href="/img/apple-touch-icon-120x120.png">
<link rel="apple-touch-icon" sizes="144x144" href="/img/apple-touch-icon-144x144.png">
<link rel="apple-touch-icon" sizes="152x152" href="/img/apple-touch-icon-152x152.png">
<link rel="apple-touch-icon" sizes="180x180" href="/img/apple-touch-icon-180x180.png">
 
<!-- Android Chrome / PWA manifest -->
<link rel="manifest" href="/img/site.webmanifest">
 
<!-- Microsoft Edge / IE11 — pinned tiles -->
<meta name="msapplication-config"    content="/img/browserconfig.xml">
<meta name="msapplication-TileColor" content="#0b3d2e">
<meta name="msapplication-TileImage" content="/img/mstile-150x150.png">
 
<!-- Theme colour — Chrome for Android toolbar + Safari 15+ tab bar -->
<meta name="theme-color" content="#c9a227">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,900;1,700&display=swap" rel="stylesheet">

<link rel="stylesheet" href="/styles.css">

<style>
/* ═══════════════════════════════════════════
   400 BAD REQUEST — page-scoped styles
═══════════════════════════════════════════ */

.err-page {
  min-height: 72vh;
  display: flex; align-items: center; justify-content: center;
  padding: 60px 32px;
}

.err-inner {
  max-width: 1100px; width: 100%; margin: 0 auto;
  display: grid; grid-template-columns: 1fr 340px; gap: 64px; align-items: center;
}

/* ── Left — main content ── */
.err-kicker { display: flex; align-items: center; gap: 10px; margin-bottom: 20px; }
.err-kicker-line { width: 24px; height: 2px; background: var(--amber); flex-shrink: 0; }
.err-kicker-text {
  font-family: var(--fmono); font-size: .6rem; font-weight: 700;
  letter-spacing: .2em; text-transform: uppercase; color: var(--muted);
}

/* Big 400 number */
.err-code {
  font-family: 'Playfair Display', Georgia, serif;
  font-size: clamp(6rem, 16vw, 13rem);
  font-weight: 900; font-style: normal;
  line-height: .9; letter-spacing: -.04em;
  color: var(--navy); display: block; margin-bottom: 0;
}
[data-theme="dark"] .err-code { color: var(--ink); }

/* Italic outline subtitle */
.err-label {
  font-family: 'Playfair Display', Georgia, serif;
  font-size: clamp(2.4rem, 6vw, 5rem);
  font-weight: 700; font-style: italic;
  line-height: 1.05; letter-spacing: -.025em;
  color: transparent;
  -webkit-text-stroke: 2px var(--amber);
  text-stroke: 2px var(--amber);
  display: block; margin-bottom: 28px;
}
[data-theme="dark"] .err-label { -webkit-text-stroke-color: var(--amber); }

.err-desc {
  font-family: var(--fsans); font-size: .9rem; line-height: 1.85;
  color: var(--body); max-width: 480px; margin-bottom: 32px;
}
.err-desc strong { color: var(--ink); font-weight: 700; }

/* Cause list */
.err-causes { display: flex; flex-direction: column; gap: 0; margin-bottom: 32px; }
.err-cause-item {
  display: flex; align-items: flex-start; gap: 12px;
  padding: 10px 0; border-bottom: 1px solid var(--border);
  font-family: var(--fsans); font-size: .8rem; line-height: 1.7; color: var(--body);
}
.err-cause-item:last-child { border-bottom: none; }
.err-cause-icon { font-size: .85rem; flex-shrink: 0; margin-top: 1px; }

/* Action buttons */
.err-actions { display: flex; flex-wrap: wrap; gap: 10px; }
.err-btn {
  display: inline-flex; align-items: center; gap: 8px;
  padding: 12px 22px; border-radius: var(--r);
  font-family: var(--fsans); font-size: .76rem; font-weight: 700;
  letter-spacing: .04em; text-transform: uppercase; text-decoration: none;
  transition: all .2s var(--ease);
}
.err-btn.primary {
  background: var(--navy); color: #fff; border: 1px solid var(--navy);
}
.err-btn.primary:hover { background: var(--pink); border-color: var(--pink); }
[data-theme="dark"] .err-btn.primary { background: var(--navy3); border-color: var(--navy3); }
.err-btn.ghost {
  background: var(--paper); color: var(--body);
  border: 1px solid var(--border2);
}
.err-btn.ghost:hover { border-color: var(--navy); color: var(--navy); }

/* ── Right — quick nav ── */
.err-nav { display: flex; flex-direction: column; gap: 8px; }

.err-nav-title {
  font-family: var(--fmono); font-size: .58rem; font-weight: 700;
  letter-spacing: .14em; text-transform: uppercase; color: var(--muted);
  margin-bottom: 4px;
}

.err-nav-card {
  display: flex; align-items: center; justify-content: space-between; gap: 12px;
  padding: 13px 16px; border-radius: var(--r-md);
  background: var(--paper); border: 1px solid var(--border2);
  text-decoration: none; transition: all .18s var(--ease);
}
.err-nav-card:hover { border-color: var(--pink); box-shadow: var(--sh-md); }
.err-nav-card.jackpot { border-color: rgba(201,162,39,.3); background: rgba(201,162,39,.04); }
.err-nav-card.jackpot:hover { border-color: var(--gold); }

.err-nav-left {}
.err-nav-card-title {
  font-family: var(--fsans); font-size: .82rem; font-weight: 700;
  color: var(--navy); line-height: 1; margin-bottom: 3px;
}
[data-theme="dark"] .err-nav-card-title { color: var(--ink); }
.err-nav-card-sub {
  font-family: var(--fmono); font-size: .54rem; font-weight: 700;
  letter-spacing: .08em; text-transform: uppercase; color: var(--muted);
}
.err-nav-card.jackpot .err-nav-card-sub { color: var(--gold); }
.err-nav-arr { font-family: var(--fmono); font-size: .7rem; color: var(--faint); transition: all .15s; }
.err-nav-card:hover .err-nav-arr { color: var(--pink); transform: translateX(3px); }
.err-nav-card.jackpot:hover .err-nav-arr { color: var(--gold); }

/* status strip */
.err-status-strip {
  display: flex; align-items: center; gap: 10px;
  padding: 11px 14px; border-radius: var(--r-md);
  background: rgba(180,83,9,.06); border: 1px solid rgba(180,83,9,.2);
  margin-top: 4px;
}
.err-status-dot {
  width: 8px; height: 8px; border-radius: 50%; background: var(--amber);
  flex-shrink: 0; animation: err-pulse 2s ease-in-out infinite;
}
@keyframes err-pulse {
  0%, 100% { opacity: 1; }
  50%       { opacity: .35; }
}
.err-status-text {
  font-family: var(--fmono); font-size: .58rem; font-weight: 700;
  letter-spacing: .08em; text-transform: uppercase; color: var(--amber);
}

/* ── Responsive ── */
@media(max-width:900px) {
  .err-inner { grid-template-columns: 1fr; gap: 40px; }
  .err-nav    { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
  .err-nav-title { grid-column: span 2; }
  .err-status-strip { grid-column: span 2; }
}
@media(max-width:600px) {
  .err-page   { padding: 40px 20px; }
  .err-code   { font-size: 7rem; }
  .err-label  { font-size: 2.2rem; }
  .err-nav    { grid-template-columns: 1fr; }
  .err-nav-title,
  .err-status-strip { grid-column: span 1; }
  .err-actions { flex-direction: column; }
  .err-btn { justify-content: center; }
}
</style>

<!-- Google Tag Manager: add your AllJackpotPredictions GTM container here -->
</head>
<body>

<?php include 'header.php'; ?>

<!-- breadcrumb -->
<nav class="breadcrumb-wrap" aria-label="Breadcrumb">
  <div class="breadcrumb">
    <a href="/">Home</a><span class="bc-sep">›</span>
    <span class="bc-cur">400 Bad Request</span>
  </div>
</nav>

<!-- ══════════════════════════════════
     ERROR PAGE
══════════════════════════════════ -->
<div class="err-page">
  <div class="err-inner">

    <!-- LEFT -->
    <div>
      <div class="err-kicker">
        <div class="err-kicker-line"></div>
        <span class="err-kicker-text">HTTP Error</span>
      </div>

      <span class="err-code">400</span>
      <span class="err-label">Bad Request</span>

      <p class="err-desc">
        The server couldn't understand your request — something about the way it was
        formed didn't add up. <strong>This isn't a missing page</strong> (that's a 404) —
        it's a problem with the request itself.
      </p>

      <div class="err-causes">
        <div class="err-cause-item">
          <span class="err-cause-icon">🔗</span>
          <span>The URL may contain invalid characters, malformed syntax or a broken query string.</span>
        </div>
        <div class="err-cause-item">
          <span class="err-cause-icon">🍪</span>
          <span>Corrupt or oversized cookies from a previous session can sometimes trigger a 400. Try clearing your browser cookies and reloading.</span>
        </div>
        <div class="err-cause-item">
          <span class="err-cause-icon">⏱</span>
          <span>A cached version of the page may be stale. A hard refresh (<strong>Ctrl+Shift+R</strong> on Windows, <strong>Cmd+Shift+R</strong> on Mac) often resolves this.</span>
        </div>
        <div class="err-cause-item">
          <span class="err-cause-icon">📋</span>
          <span>If you copied and pasted a link, check it didn't pick up extra spaces or characters at the start or end.</span>
        </div>
      </div>

      <div class="err-actions">
        <a class="err-btn primary" href="/">← Back to Home</a>
        <a class="err-btn ghost" href="/predictions-today">Today's Tips</a>
        <a class="err-btn ghost" href="/contact">Report This</a>
      </div>
    </div>

    <!-- RIGHT — quick nav -->
    <div class="err-nav">
      <div class="err-nav-title">Where would you like to go?</div>

      <a class="err-nav-card jackpot" href="/jackpot-picks-today">
        <div class="err-nav-left">
          <div class="err-nav-card-title">◈ Jackpot Picks</div>
          <div class="err-nav-card-sub">Today's top 3 selections</div>
        </div>
        <span class="err-nav-arr">→</span>
      </a>

      <a class="err-nav-card" href="/predictions-today">
        <div class="err-nav-left">
          <div class="err-nav-card-title">Today's Tips</div>
          <div class="err-nav-card-sub">All predictions · All markets</div>
        </div>
        <span class="err-nav-arr">→</span>
      </a>

      <a class="err-nav-card" href="/must-win-teams-today">
        <div class="err-nav-left">
          <div class="err-nav-card-title">⚡ Must Win Teams</div>
          <div class="err-nav-card-sub">High-confidence selections</div>
        </div>
        <span class="err-nav-arr">→</span>
      </a>

      <a class="err-nav-card" href="/accumulator-tips">
        <div class="err-nav-left">
          <div class="err-nav-card-title">Accumulator Tips</div>
          <div class="err-nav-card-sub">Pre-built daily accas</div>
        </div>
        <span class="err-nav-arr">→</span>
      </a>

      <a class="err-nav-card" href="/track-record">
        <div class="err-nav-left">
          <div class="err-nav-card-title">Track Record</div>
          <div class="err-nav-card-sub">Full results history</div>
        </div>
        <span class="err-nav-arr">→</span>
      </a>

      <a class="err-nav-card" href="/how-it-works">
        <div class="err-nav-left">
          <div class="err-nav-card-title">How It Works</div>
          <div class="err-nav-card-sub">Our six-stage methodology</div>
        </div>
        <span class="err-nav-arr">→</span>
      </a>

      <a class="err-nav-card" href="/contact">
        <div class="err-nav-left">
          <div class="err-nav-card-title">Contact</div>
          <div class="err-nav-card-sub">Report this error to us</div>
        </div>
        <span class="err-nav-arr">→</span>
      </a>

      <div class="err-status-strip">
        <div class="err-status-dot"></div>
        <span class="err-status-text">HTTP 400 · Bad Request</span>
      </div>
    </div>

  </div>
</div>

<?php include 'footer.php'; ?>
<button class="btt" id="btt" aria-label="Back to top">↑</button>
<script src="/main.js"></script>
</body>
</html>