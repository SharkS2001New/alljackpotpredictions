<?php
http_response_code(503);
header('Retry-After: 120');
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>503 — Service Unavailable | AllJackpotPredictions</title>
<meta name="description" content="AllJackpotPredictions is temporarily unavailable — we're carrying out maintenance or experiencing high load. We'll be back shortly.">
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
   503 SERVICE UNAVAILABLE — page-scoped
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

/* ── Kicker ── */
.err-kicker { display: flex; align-items: center; gap: 10px; margin-bottom: 20px; }
.err-kicker-line { width: 24px; height: 2px; background: var(--navy); flex-shrink: 0; }
.err-kicker-text {
  font-family: var(--fmono); font-size: .6rem; font-weight: 700;
  letter-spacing: .2em; text-transform: uppercase; color: var(--muted);
}

/* ── H1 treatment ── */
.err-code {
  font-family: 'Playfair Display', Georgia, serif;
  font-size: clamp(6rem, 16vw, 13rem);
  font-weight: 900; font-style: normal;
  line-height: .9; letter-spacing: -.04em;
  color: var(--navy); display: block;
}
[data-theme="dark"] .err-code { color: var(--ink); }

.err-label {
  font-family: 'Playfair Display', Georgia, serif;
  font-size: clamp(1.5rem, 4vw, 3.4rem);
  font-weight: 700; font-style: italic;
  line-height: 1.05; letter-spacing: -.02em;
  color: transparent;
  -webkit-text-stroke: 2px var(--navy);
  text-stroke: 2px var(--navy);
  display: block; margin-bottom: 28px;
}
[data-theme="dark"] .err-label { -webkit-text-stroke-color: var(--ink); }

.err-desc {
  font-family: var(--fsans); font-size: .9rem; line-height: 1.85;
  color: var(--body); max-width: 480px; margin-bottom: 28px;
}
.err-desc strong { color: var(--ink); font-weight: 700; }

/* ── Maintenance / overload toggle ── */
.err-reason-tabs {
  display: flex; gap: 8px; margin-bottom: 20px;
}
.err-reason-tab {
  padding: 7px 14px; border-radius: var(--r);
  border: 1px solid var(--border2); background: var(--paper);
  font-family: var(--fmono); font-size: .6rem; font-weight: 700;
  letter-spacing: .06em; text-transform: uppercase; color: var(--muted);
  cursor: pointer; transition: all .18s var(--ease);
}
.err-reason-tab.active {
  background: var(--navy); color: #fff; border-color: var(--navy);
}
[data-theme="dark"] .err-reason-tab.active { background: var(--navy3); border-color: var(--navy3); }

.err-reason-panel { display: none; }
.err-reason-panel.active { display: block; }

/* ── Callout ── */
.err-callout {
  border-left: 3px solid var(--navy);
  background: rgba(11,61,46,.05);
  border-radius: 0 var(--r) var(--r) 0;
  padding: 13px 18px; margin-bottom: 24px;
  font-family: var(--fsans); font-size: .8rem; line-height: 1.8; color: var(--body);
}
[data-theme="dark"] .err-callout { background: rgba(45,143,106,.07); }
.err-callout strong { color: var(--ink); font-weight: 700; }
.err-callout a { color: var(--pink); font-weight: 700; text-decoration: none; }
.err-callout a:hover { text-decoration: underline; }

/* ── Maintenance window card ── */
.err-maint-card {
  background: var(--paper); border: 1px solid var(--border2);
  border-radius: var(--r-lg); padding: 20px 20px 16px;
  margin-bottom: 24px;
}
.err-maint-title {
  font-family: var(--fmono); font-size: .58rem; font-weight: 700;
  letter-spacing: .12em; text-transform: uppercase; color: var(--muted);
  margin-bottom: 12px;
}
.err-maint-rows { display: flex; flex-direction: column; gap: 8px; }
.err-maint-row {
  display: grid; grid-template-columns: 100px 1fr; gap: 12px;
  font-family: var(--fsans); font-size: .78rem; line-height: 1.6;
}
.err-maint-row-label {
  font-family: var(--fmono); font-size: .58rem; font-weight: 700;
  letter-spacing: .08em; text-transform: uppercase; color: var(--muted);
  padding-top: 2px;
}
.err-maint-row-val { color: var(--body); }
.err-maint-row-val strong { color: var(--ink); font-weight: 700; }

/* ── Progress bar ── */
.err-progress-wrap {
  margin-bottom: 24px;
}
.err-progress-label {
  display: flex; align-items: center; justify-content: space-between;
  margin-bottom: 8px;
  font-family: var(--fmono); font-size: .56rem; font-weight: 700;
  letter-spacing: .08em; text-transform: uppercase; color: var(--muted);
}
.err-progress-label span { color: var(--navy); }
[data-theme="dark"] .err-progress-label span { color: var(--navy2); }
.err-progress-track {
  height: 6px; background: var(--border2); border-radius: 3px; overflow: hidden;
}
.err-progress-fill {
  height: 100%; border-radius: 3px;
  background: linear-gradient(90deg, var(--navy) 0%, var(--pink) 100%);
  animation: err-progress 3s ease-in-out infinite alternate;
}
@keyframes err-progress {
  0%   { width: 30%; }
  100% { width: 85%; }
}

/* ── Cause list ── */
.err-causes { display: flex; flex-direction: column; margin-bottom: 28px; }
.err-cause-item {
  display: flex; align-items: flex-start; gap: 12px;
  padding: 10px 0; border-bottom: 1px solid var(--border);
  font-family: var(--fsans); font-size: .8rem; line-height: 1.7; color: var(--body);
}
.err-cause-item:last-child { border-bottom: none; }
.err-cause-icon { font-size: .85rem; flex-shrink: 0; margin-top: 1px; }
.err-cause-item a { color: var(--pink); font-weight: 700; text-decoration: none; }
.err-cause-item a:hover { text-decoration: underline; }

/* ── Error meta ── */
.err-meta-strip {
  display: flex; flex-wrap: wrap; gap: 12px; margin-bottom: 28px;
}
.err-meta-item {
  background: var(--paper2); border: 1px solid var(--border2);
  border-radius: var(--r); padding: 8px 14px;
  display: flex; flex-direction: column; gap: 2px;
}
[data-theme="dark"] .err-meta-item { background: var(--paper3); }
.err-meta-val {
  font-family: var(--fmono); font-size: .68rem; font-weight: 700;
  color: var(--navy); letter-spacing: .01em;
}
[data-theme="dark"] .err-meta-val { color: var(--navy2); }
.err-meta-lbl {
  font-family: var(--fmono); font-size: .5rem; font-weight: 700;
  letter-spacing: .1em; text-transform: uppercase; color: var(--muted);
}

/* ── Retry ── */
.err-retry-wrap { display: flex; align-items: center; gap: 12px; margin-bottom: 16px; }
.err-retry-btn {
  display: inline-flex; align-items: center; gap: 8px;
  padding: 12px 22px; border-radius: var(--r);
  background: var(--navy); color: #fff; border: none; cursor: pointer;
  font-family: var(--fsans); font-size: .76rem; font-weight: 700;
  letter-spacing: .04em; text-transform: uppercase;
  transition: background .18s; text-decoration: none;
}
.err-retry-btn:hover { background: var(--pink); }
[data-theme="dark"] .err-retry-btn { background: var(--navy3); }
.err-retry-countdown {
  font-family: var(--fmono); font-size: .7rem; font-weight: 700;
  color: var(--muted); white-space: nowrap;
}
.err-retry-countdown span { color: var(--navy); }
[data-theme="dark"] .err-retry-countdown span { color: var(--navy2); }

/* ── Action buttons ── */
.err-actions { display: flex; flex-wrap: wrap; gap: 10px; }
.err-btn {
  display: inline-flex; align-items: center; gap: 8px;
  padding: 12px 22px; border-radius: var(--r);
  font-family: var(--fsans); font-size: .76rem; font-weight: 700;
  letter-spacing: .04em; text-transform: uppercase; text-decoration: none;
  transition: all .2s var(--ease);
}
.err-btn.primary { background: var(--navy); color: #fff; border: 1px solid var(--navy); }
.err-btn.primary:hover { background: var(--pink); border-color: var(--pink); }
[data-theme="dark"] .err-btn.primary { background: var(--navy3); border-color: var(--navy3); }
.err-btn.ghost { background: var(--paper); color: var(--body); border: 1px solid var(--border2); }
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

/* social follow row */
.err-social-row {
  display: flex; align-items: center; gap: 10px;
  padding: 11px 14px; border-radius: var(--r-md);
  background: var(--paper); border: 1px solid var(--border2);
  margin-top: 0; text-decoration: none;
  transition: border-color .18s;
}
.err-social-row:hover { border-color: var(--navy); }
.err-social-icon { font-size: 1rem; flex-shrink: 0; }
.err-social-text {
  font-family: var(--fsans); font-size: .76rem; font-weight: 700;
  color: var(--navy); line-height: 1; margin-bottom: 2px;
}
[data-theme="dark"] .err-social-text { color: var(--ink); }
.err-social-sub {
  font-family: var(--fmono); font-size: .54rem; font-weight: 700;
  letter-spacing: .08em; text-transform: uppercase; color: var(--muted);
}
.err-social-arr { font-family: var(--fmono); font-size: .7rem; color: var(--faint); margin-left: auto; }
.err-social-row:hover .err-social-arr { color: var(--navy); }

/* ── Status strip ── */
.err-status-strip {
  display: flex; align-items: center; gap: 10px;
  padding: 11px 14px; border-radius: var(--r-md);
  background: rgba(11,61,46,.05); border: 1px solid rgba(11,61,46,.12);
  margin-top: 4px;
}
[data-theme="dark"] .err-status-strip { background: rgba(45,143,106,.07); border-color: rgba(45,143,106,.18); }
.err-status-dot {
  width: 8px; height: 8px; border-radius: 50%;
  background: var(--navy); flex-shrink: 0;
  animation: err-pulse 1.4s ease-in-out infinite;
}
[data-theme="dark"] .err-status-dot { background: var(--navy2); }
@keyframes err-pulse {
  0%, 100% { opacity: 1; transform: scale(1); }
  50%       { opacity: .3; transform: scale(.8); }
}
.err-status-text {
  font-family: var(--fmono); font-size: .58rem; font-weight: 700;
  letter-spacing: .08em; text-transform: uppercase; color: var(--navy);
}
[data-theme="dark"] .err-status-text { color: var(--navy2); }

/* ── Responsive ── */
@media(max-width:900px) {
  .err-inner { grid-template-columns: 1fr; gap: 40px; }
  .err-nav { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
  .err-nav-title,
  .err-social-row,
  .err-status-strip { grid-column: span 2; }
}
@media(max-width:600px) {
  .err-page  { padding: 40px 20px; }
  .err-code  { font-size: 7rem; }
  .err-label { font-size: 1.4rem; }
  .err-nav   { grid-template-columns: 1fr; }
  .err-nav-title,
  .err-social-row,
  .err-status-strip { grid-column: span 1; }
  .err-actions { flex-direction: column; }
  .err-btn { justify-content: center; }
  .err-retry-wrap { flex-direction: column; align-items: flex-start; }
  .err-reason-tabs { flex-wrap: wrap; }
  .err-maint-row { grid-template-columns: 80px 1fr; }
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
    <span class="bc-cur">503 Service Unavailable</span>
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

      <span class="err-code">503</span>
      <span class="err-label">Service Unavailable</span>

      <p class="err-desc">
        AllJackpotPredictions is <strong>temporarily unavailable</strong>. The server is either
        undergoing scheduled maintenance or is currently overloaded. We'll be back
        very shortly — this is never permanent.
      </p>

      <!-- Reason tabs -->
      <div class="err-reason-tabs" role="tablist">
        <button class="err-reason-tab active" role="tab" data-panel="maintenance" aria-selected="true">
          🔧 Maintenance
        </button>
        <button class="err-reason-tab" role="tab" data-panel="overload" aria-selected="false">
          ⚡ High Load
        </button>
      </div>

      <!-- Maintenance panel -->
      <div class="err-reason-panel active" id="panel-maintenance">
        <div class="err-callout">
          <strong>Scheduled maintenance in progress.</strong> We carry out regular maintenance
          windows to keep AllJackpotPredictions fast and reliable. All tips, Jackpot Picks and track record
          data will be fully available once the window closes. We apologise for the inconvenience.
        </div>

        <div class="err-maint-card">
          <div class="err-maint-title">🔧 Maintenance Window</div>
          <div class="err-maint-rows">
            <div class="err-maint-row">
              <span class="err-maint-row-label">Status</span>
              <span class="err-maint-row-val"><strong>In Progress</strong> — estimated completion within 2 hours</span>
            </div>
            <div class="err-maint-row">
              <span class="err-maint-row-label">Affected</span>
              <span class="err-maint-row-val">Full site — all pages temporarily unavailable</span>
            </div>
            <div class="err-maint-row">
              <span class="err-maint-row-label">Updates</span>
              <span class="err-maint-row-val">Follow <a href="https://twitter.com/AllJackpotPreds" target="_blank" rel="noopener" style="color:var(--pink);font-weight:700">@AllJackpotPreds</a> on 𝕏 for live status updates</span>
            </div>
          </div>
        </div>

        <div class="err-progress-wrap">
          <div class="err-progress-label">
            <span>Maintenance progress</span>
            <span>In progress</span>
          </div>
          <div class="err-progress-track">
            <div class="err-progress-fill"></div>
          </div>
        </div>
      </div>

      <!-- Overload panel -->
      <div class="err-reason-panel" id="panel-overload">
        <div class="err-callout">
          <strong>Server under high load.</strong> AllJackpotPredictions is experiencing unusually high
          traffic — likely around a major match day or fixture release. The server is working
          to process requests but temporarily cannot take on more. This usually resolves within
          a few minutes.
        </div>

        <div class="err-causes">
          <div class="err-cause-item">
            <span class="err-cause-icon">⚽</span>
            <span>Heavy traffic around <strong>kick-off time</strong> for major fixtures — Champions League matchdays, Premier League gameweeks and international tournaments can spike our traffic significantly.</span>
          </div>
          <div class="err-cause-item">
            <span class="err-cause-icon">🔄</span>
            <span><strong>Wait 1–2 minutes</strong> and try again. The server queue typically clears quickly during traffic spikes.</span>
          </div>
          <div class="err-cause-item">
            <span class="err-cause-icon">✉️</span>
            <span>If the site is still down after 5 minutes, <a href="/contact">contact us</a> or check <a href="https://twitter.com/AllJackpotPreds" target="_blank" rel="noopener">@AllJackpotPreds</a> on 𝕏 for updates.</span>
          </div>
        </div>
      </div>

      <!-- Error meta -->
      <?php
        $error_id  = strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
        $timestamp = gmdate('Y-m-d H:i:s') . ' UTC';
        $retry_in  = '120 seconds';
      ?>
      <div class="err-meta-strip">
        <div class="err-meta-item">
          <div class="err-meta-val"><?php echo $error_id; ?></div>
          <div class="err-meta-lbl">Error ID</div>
        </div>
        <div class="err-meta-item">
          <div class="err-meta-val"><?php echo $timestamp; ?></div>
          <div class="err-meta-lbl">Timestamp</div>
        </div>
        <div class="err-meta-item">
          <div class="err-meta-val"><?php echo $retry_in; ?></div>
          <div class="err-meta-lbl">Retry After</div>
        </div>
      </div>

      <!-- Retry with countdown -->
      <div class="err-retry-wrap">
        <a class="err-retry-btn" href="javascript:location.reload()">↺ Try Again</a>
        <span class="err-retry-countdown">Auto-refreshing in <span id="countdown">60</span>s</span>
      </div>

      <div class="err-actions">
        <a class="err-btn primary" href="/">← Back to Home</a>
        <a class="err-btn ghost" href="/contact">Report This</a>
      </div>
    </div>

    <!-- RIGHT — quick nav -->
    <div class="err-nav">
      <div class="err-nav-title">Back when we're up</div>

      <a class="err-nav-card jackpot" href="/jackpot-picks-today">
        <div>
          <div class="err-nav-card-title">◈ Jackpot Picks</div>
          <div class="err-nav-card-sub">Today's top 3 selections</div>
        </div>
        <span class="err-nav-arr">→</span>
      </a>

      <a class="err-nav-card" href="/predictions-today">
        <div>
          <div class="err-nav-card-title">Today's Tips</div>
          <div class="err-nav-card-sub">All predictions · All markets</div>
        </div>
        <span class="err-nav-arr">→</span>
      </a>

      <a class="err-nav-card" href="/must-win-teams-today">
        <div>
          <div class="err-nav-card-title">⚡ Must Win Teams</div>
          <div class="err-nav-card-sub">High-confidence selections</div>
        </div>
        <span class="err-nav-arr">→</span>
      </a>

      <a class="err-nav-card" href="/accumulator-tips">
        <div>
          <div class="err-nav-card-title">Accumulator Tips</div>
          <div class="err-nav-card-sub">Pre-built daily accas</div>
        </div>
        <span class="err-nav-arr">→</span>
      </a>

      <a class="err-nav-card" href="/track-record">
        <div>
          <div class="err-nav-card-title">Track Record</div>
          <div class="err-nav-card-sub">Full results history</div>
        </div>
        <span class="err-nav-arr">→</span>
      </a>

      <a class="err-nav-card" href="/contact">
        <div>
          <div class="err-nav-card-title">Contact</div>
          <div class="err-nav-card-sub">Report with your Error ID</div>
        </div>
        <span class="err-nav-arr">→</span>
      </a>

      <!-- Follow on X for status updates -->
      <a class="err-social-row" href="https://twitter.com/AllJackpotPreds" target="_blank" rel="noopener">
        <span class="err-social-icon">𝕏</span>
        <div>
          <div class="err-social-text">@AllJackpotPreds</div>
          <div class="err-social-sub">Live status updates</div>
        </div>
        <span class="err-social-arr">→</span>
      </a>

      <div class="err-status-strip">
        <div class="err-status-dot"></div>
        <span class="err-status-text">HTTP 503 · Service Unavailable</span>
      </div>
    </div>

  </div>
</div>

<?php include 'footer.php'; ?>
<button class="btt" id="btt" aria-label="Back to top">↑</button>
<script src="/main.js"></script>
<script>
/* ── Reason tab switcher ── */
(function () {
  var tabs   = document.querySelectorAll('.err-reason-tab');
  var panels = document.querySelectorAll('.err-reason-panel');

  tabs.forEach(function (tab) {
    tab.addEventListener('click', function () {
      var target = tab.dataset.panel;

      tabs.forEach(function (t) {
        t.classList.remove('active');
        t.setAttribute('aria-selected', 'false');
      });
      panels.forEach(function (p) { p.classList.remove('active'); });

      tab.classList.add('active');
      tab.setAttribute('aria-selected', 'true');
      var panel = document.getElementById('panel-' + target);
      if (panel) panel.classList.add('active');
    });
  });
}());

/* ── Auto-refresh countdown (60s — longer than 500/502 since 503 is intentional) ── */
(function () {
  var el = document.getElementById('countdown');
  if (!el) return;
  var secs = 60;
  var timer = setInterval(function () {
    secs--;
    el.textContent = secs;
    if (secs <= 0) {
      clearInterval(timer);
      location.reload();
    }
  }, 1000);
  document.querySelectorAll('a, button').forEach(function (node) {
    node.addEventListener('click', function () { clearInterval(timer); });
  });
}());
</script>
</body>
</html>