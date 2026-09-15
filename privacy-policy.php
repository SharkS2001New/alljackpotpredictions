<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Privacy Policy | AllJackpotPredictions</title>
<meta name="description" content="AllJackpotPredictions' privacy policy. How we collect, use and protect your data, your rights under UK GDPR, our cookie policy and how to contact us.">
<link rel="canonical" href="https://www.alljackpotpredictions.com/privacy-policy">

<meta property="og:type" content="website">
<meta property="og:url" content="https://www.alljackpotpredictions.com/privacy-policy">
<meta property="og:title" content="Privacy Policy | AllJackpotPredictions">
<meta property="og:site_name" content="AllJackpotPredictions">

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "BreadcrumbList",
  "itemListElement": [
    { "@type": "ListItem", "position": 1, "name": "Home",           "item": "https://www.alljackpotpredictions.com/" },
    { "@type": "ListItem", "position": 2, "name": "Privacy Policy", "item": "https://www.alljackpotpredictions.com/privacy-policy" }
  ]
}
</script>

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

<link rel="stylesheet" href="styles.css">

<style>
/* ═══════════════════════════════════════════
   PRIVACY POLICY — page-scoped styles
═══════════════════════════════════════════ */

/* ── Hero ── */
.pp-hero {
  background: var(--paper2);
  border-bottom: 2px solid var(--border2);
  padding: 52px 0 0;
  overflow: hidden;
}
[data-theme="dark"] .pp-hero { background: var(--paper3); }

.pp-hero-inner {
  max-width: 1280px; margin: 0 auto; padding: 0 32px;
  display: grid; grid-template-columns: 1fr 340px; gap: 52px; align-items: start;
}

.pp-kicker { display: flex; align-items: center; gap: 10px; margin-bottom: 18px; }
.pp-kicker-line { width: 24px; height: 2px; background: var(--pink); flex-shrink: 0; }
.pp-kicker-text {
  font-family: var(--fmono); font-size: .6rem; font-weight: 700;
  letter-spacing: .2em; text-transform: uppercase; color: var(--muted);
}

/* ── H1 ── */
.pp-h1 { margin: 0 0 24px; display: flex; flex-direction: column; gap: 0; line-height: 1; }
.pp-h1-solid {
  font-family: 'Playfair Display', Georgia, serif;
  font-size: clamp(2.6rem, 5.5vw, 5rem);
  font-weight: 900; font-style: normal; line-height: 1.0;
  letter-spacing: -.02em; color: var(--navy); display: block;
}
[data-theme="dark"] .pp-h1-solid { color: var(--ink); }
.pp-h1-outline {
  font-family: 'Playfair Display', Georgia, serif;
  font-size: clamp(3.2rem, 7.5vw, 7rem);
  font-weight: 700; font-style: italic; line-height: 1.05;
  letter-spacing: -.03em; color: transparent;
  -webkit-text-stroke: 2px var(--pink);
  text-stroke: 2px var(--pink); display: block;
}
[data-theme="dark"] .pp-h1-outline { -webkit-text-stroke-color: var(--pink); }

.pp-hero-body {
  font-family: var(--fsans); font-size: .88rem; line-height: 1.85;
  color: var(--body); max-width: 520px; margin-bottom: 32px;
}
.pp-hero-body strong { color: var(--ink); font-weight: 700; }

/* quick-jump pills */
.pp-pills { display: flex; flex-wrap: wrap; gap: 8px; padding-bottom: 40px; }
.pp-pill {
  display: inline-flex; align-items: center;
  padding: 8px 14px; border: 1px solid var(--border2); border-radius: var(--r);
  background: var(--paper); color: var(--body);
  font-family: var(--fsans); font-size: .68rem; font-weight: 700;
  letter-spacing: .04em; text-transform: uppercase; text-decoration: none;
  transition: all .2s var(--ease);
}
.pp-pill:hover { background: var(--navy); color: #fff; border-color: var(--navy); }
[data-theme="dark"] .pp-pill:hover { background: var(--navy3); border-color: var(--navy3); }

/* ── Right — at-a-glance box ── */
.pp-glance {
  background: var(--paper); border: 1px solid var(--border2);
  border-radius: var(--r-lg); padding: 22px 20px 18px; margin-bottom: 12px;
}
.pp-glance-title {
  font-family: var(--fmono); font-size: .6rem; font-weight: 700;
  letter-spacing: .15em; text-transform: uppercase; color: var(--muted);
  margin-bottom: 14px;
}
.pp-glance-rows { display: flex; flex-direction: column; gap: 10px; }
.pp-glance-row { display: flex; gap: 10px; align-items: flex-start; }
.pp-glance-dot {
  width: 8px; height: 8px; border-radius: 50%; background: var(--pink);
  flex-shrink: 0; margin-top: 5px;
}
.pp-glance-dot.green { background: var(--green); }
.pp-glance-dot.navy  { background: var(--navy); }
.pp-glance-text {
  font-family: var(--fsans); font-size: .76rem; line-height: 1.7; color: var(--body);
}
.pp-glance-text strong { color: var(--ink); font-weight: 700; }

/* last updated strip */
.pp-meta-strip {
  background: var(--paper); border: 1px solid var(--border2);
  border-radius: var(--r-md); padding: 12px 16px;
  display: flex; align-items: center; justify-content: space-between; gap: 12px;
}
.pp-meta-item {
  display: flex; flex-direction: column; gap: 2px;
}
.pp-meta-val {
  font-family: var(--fmono); font-size: .72rem; font-weight: 700;
  color: var(--navy); letter-spacing: -.01em;
}
[data-theme="dark"] .pp-meta-val { color: var(--navy2); }
.pp-meta-lbl {
  font-family: var(--fmono); font-size: .5rem; font-weight: 700;
  letter-spacing: .1em; text-transform: uppercase; color: var(--muted);
}
.pp-meta-divider { width: 1px; height: 32px; background: var(--border2); }

/* ── Shared section divider ── */
.pp-divider {
  display: flex; align-items: center; gap: 12px; margin: 44px 0 20px;
}
.pp-divider-dot { width: 10px; height: 10px; border-radius: 50%; background: var(--pink); flex-shrink: 0; }
.pp-divider-title {
  font-family: var(--fsans); font-size: .88rem; font-weight: 800;
  letter-spacing: .1em; text-transform: uppercase; color: var(--navy); white-space: nowrap;
}
[data-theme="dark"] .pp-divider-title { color: var(--ink); }
.pp-divider-line { flex: 1; height: 1px; background: var(--border2); }

/* ── Content layout ── */
.pp-content {
  max-width: 860px;
}

/* section anchor offset so sticky header doesn't overlap */
.pp-section { scroll-margin-top: 80px; }

/* prose */
.pp-prose {
  font-family: var(--fsans); font-size: .82rem; line-height: 1.9; color: var(--body);
  margin-bottom: 16px;
}
.pp-prose strong { color: var(--ink); font-weight: 700; }
.pp-prose a { color: var(--pink); font-weight: 700; text-decoration: none; }
.pp-prose a:hover { text-decoration: underline; }

/* subsection heading */
.pp-sub {
  font-family: var(--fsans); font-size: .84rem; font-weight: 800;
  color: var(--navy); margin: 22px 0 8px; line-height: 1.3;
}
[data-theme="dark"] .pp-sub { color: var(--ink); }

/* inline tag */
.pp-tag {
  display: inline-flex; align-items: center;
  padding: 2px 8px; border-radius: 2px;
  font-family: var(--fmono); font-size: .55rem; font-weight: 700;
  letter-spacing: .07em; text-transform: uppercase;
  background: rgba(11,61,46,.07); border: 1px solid var(--border2);
  color: var(--navy); white-space: nowrap; vertical-align: middle; margin: 0 2px;
}
[data-theme="dark"] .pp-tag { background: rgba(45,143,106,.1); color: var(--navy2); border-color: rgba(45,143,106,.2); }
.pp-tag.pink  { background: rgba(201,162,39,.08); color: var(--pink);  border-color: rgba(201,162,39,.2); }
.pp-tag.green { background: rgba(10,143,90,.08);  color: var(--green); border-color: rgba(10,143,90,.2); }

/* data table */
.pp-data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
.pp-data-table thead tr { border-bottom: 1px solid var(--border2); }
.pp-data-table thead th {
  font-family: var(--fmono); font-size: .54rem; font-weight: 700;
  letter-spacing: .12em; text-transform: uppercase; color: var(--muted);
  padding: 0 0 10px; text-align: left;
}
.pp-data-table thead th:not(:first-child) { padding-left: 16px; }
.pp-data-table tbody tr { border-bottom: 1px solid var(--border); }
.pp-data-table tbody tr:last-child { border-bottom: none; }
.pp-data-table tbody td {
  padding: 13px 0; vertical-align: top;
  font-family: var(--fsans); font-size: .78rem; color: var(--body); line-height: 1.7;
}
.pp-data-table tbody td:not(:first-child) { padding-left: 16px; }
.pp-data-table tbody td:first-child { font-weight: 700; color: var(--navy); width: 180px; }
[data-theme="dark"] .pp-data-table tbody td:first-child { color: var(--ink); }

/* rights grid */
.pp-rights-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; margin-bottom: 20px; }
.pp-right-card {
  background: var(--paper); border: 1px solid var(--border2);
  border-radius: var(--r-lg); padding: 18px 16px 14px;
  display: flex; flex-direction: column; gap: 6px;
}
.pp-right-title {
  font-family: var(--fsans); font-size: .82rem; font-weight: 800;
  color: var(--navy); line-height: 1.3;
}
[data-theme="dark"] .pp-right-title { color: var(--ink); }
.pp-right-body { font-family: var(--fsans); font-size: .75rem; line-height: 1.8; color: var(--body); }

/* cookie table */
.pp-cookie-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
.pp-cookie-table thead tr { border-bottom: 1px solid var(--border2); }
.pp-cookie-table thead th {
  font-family: var(--fmono); font-size: .54rem; font-weight: 700;
  letter-spacing: .12em; text-transform: uppercase; color: var(--muted);
  padding: 0 0 10px; text-align: left;
}
.pp-cookie-table thead th:not(:first-child) { padding-left: 14px; }
.pp-cookie-table tbody tr { border-bottom: 1px solid var(--border); }
.pp-cookie-table tbody tr:last-child { border-bottom: none; }
.pp-cookie-table tbody td {
  padding: 12px 0; vertical-align: top;
  font-family: var(--fsans); font-size: .77rem; color: var(--body); line-height: 1.65;
}
.pp-cookie-table tbody td:not(:first-child) { padding-left: 14px; }
.pp-cookie-table tbody td:first-child { font-family: var(--fmono); font-size: .7rem; font-weight: 700; color: var(--navy); width: 140px; }
[data-theme="dark"] .pp-cookie-table tbody td:first-child { color: var(--navy2); }

/* contact card */
.pp-contact-card {
  background: var(--paper); border: 1px solid var(--border2);
  border-radius: var(--r-lg); padding: 24px 22px 20px;
  display: flex; flex-direction: column; gap: 12px; margin-bottom: 20px;
}
.pp-contact-rows { display: flex; flex-direction: column; gap: 8px; }
.pp-contact-row {
  display: flex; align-items: flex-start; gap: 12px;
  font-family: var(--fsans); font-size: .8rem; line-height: 1.6; color: var(--body);
}
.pp-contact-label {
  font-family: var(--fmono); font-size: .56rem; font-weight: 700;
  letter-spacing: .1em; text-transform: uppercase; color: var(--muted);
  min-width: 80px; margin-top: 2px; flex-shrink: 0;
}
.pp-contact-val { color: var(--ink); font-weight: 600; }
.pp-contact-val a { color: var(--pink); font-weight: 700; text-decoration: none; }
.pp-contact-val a:hover { text-decoration: underline; }

/* ── Responsive ── */
@media(max-width:960px) {
  .pp-hero-inner { grid-template-columns: 1fr; gap: 32px; }
  .pp-rights-grid { grid-template-columns: 1fr; }
}
@media(max-width:700px) {
  .pp-hero-inner { padding: 0 16px; }
  .pp-h1-solid   { font-size: 2.4rem; }
  .pp-h1-outline { font-size: 3.2rem; }
  .pp-data-table tbody td:first-child { width: 120px; font-size: .72rem; }
  .pp-cookie-table tbody td:first-child { width: 100px; font-size: .65rem; }
  .pp-meta-strip { flex-wrap: wrap; gap: 8px; }
  .pp-meta-divider { display: none; }
}
</style>

<!-- Google Tag Manager: add your AllJackpotPredictions GTM container here -->
</head>
<body>

<?php include 'header.php'; ?>

<!-- ══════════════════════════════════
     HERO
══════════════════════════════════ -->
<div class="pp-hero">
  <div class="pp-hero-inner">

    <!-- LEFT -->
    <div>
      <div class="pp-kicker">
        <div class="pp-kicker-line"></div>
        <span class="pp-kicker-text">Legal</span>
      </div>

      <h1 class="pp-h1">
        <span class="pp-h1-solid">PRIVACY</span>
        <span class="pp-h1-outline">Policy</span>
      </h1>

      <p class="pp-hero-body">
        This policy explains what data AllJackpotPredictions collects when you visit the site, how we use it,
        who we share it with, and what your rights are under UK GDPR.
        <strong>We do not sell personal data. We do not require registration to use the site.</strong>
        If you have any questions, contact us at <a href="mailto:privacy@alljackpotpredictions.com" style="color:var(--pink);font-weight:700">privacy@alljackpotpredictions.com</a>.
      </p>

      <div class="pp-pills">
        <a class="pp-pill" href="#who-we-are">Who We Are</a>
        <a class="pp-pill" href="#data-collected">Data We Collect</a>
        <a class="pp-pill" href="#how-we-use">How We Use It</a>
        <a class="pp-pill" href="#cookies">Cookies</a>
        <a class="pp-pill" href="#third-parties">Third Parties</a>
        <a class="pp-pill" href="#your-rights">Your Rights</a>
        <a class="pp-pill" href="#retention">Retention</a>
        <a class="pp-pill" href="#contact">Contact</a>
      </div>
    </div>

    <!-- RIGHT — at a glance + meta -->
    <div style="display:flex;flex-direction:column;gap:12px;padding-top:6px;">
      <div class="pp-glance">
        <div class="pp-glance-title">📋 At a Glance</div>
        <div class="pp-glance-rows">
          <div class="pp-glance-row">
            <div class="pp-glance-dot green"></div>
            <p class="pp-glance-text"><strong>No account required.</strong> You do not need to register or log in to use AllJackpotPredictions.</p>
          </div>
          <div class="pp-glance-row">
            <div class="pp-glance-dot green"></div>
            <p class="pp-glance-text"><strong>We do not sell your data.</strong> Your personal data is never sold to third parties.</p>
          </div>
          <div class="pp-glance-row">
            <div class="pp-glance-dot"></div>
            <p class="pp-glance-text"><strong>We use analytics cookies.</strong> Anonymous usage data helps us improve the site. You can opt out.</p>
          </div>
          <div class="pp-glance-row">
            <div class="pp-glance-dot"></div>
            <p class="pp-glance-text"><strong>We use advertising.</strong> Display ads are served by third parties and may use cookies. See the Cookies section.</p>
          </div>
          <div class="pp-glance-row">
            <div class="pp-glance-dot navy"></div>
            <p class="pp-glance-text"><strong>UK GDPR applies.</strong> You have rights including access, erasure and objection. We respond within 30 days.</p>
          </div>
        </div>
      </div>

      <div class="pp-meta-strip">
        <div class="pp-meta-item">
          <div class="pp-meta-val">March 2026</div>
          <div class="pp-meta-lbl">Last Updated</div>
        </div>
        <div class="pp-meta-divider"></div>
        <div class="pp-meta-item">
          <div class="pp-meta-val">UK GDPR</div>
          <div class="pp-meta-lbl">Applicable Law</div>
        </div>
        <div class="pp-meta-divider"></div>
        <div class="pp-meta-item">
          <div class="pp-meta-val">v3.0</div>
          <div class="pp-meta-lbl">Policy Version</div>
        </div>
      </div>
    </div>

  </div>
</div>

<!-- breadcrumb -->
<nav class="breadcrumb-wrap" aria-label="Breadcrumb">
  <div class="breadcrumb">
    <a href="/">Home</a><span class="bc-sep">›</span>
    <span class="bc-cur">Privacy Policy</span>
  </div>
</nav>

<!-- market strip -->
<div class="markets-strip">
  <div class="ms-inner">
    <a class="ms-tab active" href="/privacy-policy">Privacy Policy</a>
    <a class="ms-tab" href="/terms">Terms of Use</a>
    <a class="ms-tab" href="/responsible-gambling">Responsible Gambling</a>
    <a class="ms-tab" href="/about">About</a>
    <a class="ms-tab" href="/predictions-today">Today's Tips</a>
  </div>
</div>

<!-- ══ CONTENT ══ -->
<div class="below-page">
<div class="pp-content">

  <!-- ══════════════════════════════════
       1. WHO WE ARE
  ══════════════════════════════════ -->
  <div class="pp-divider pp-section" id="who-we-are">
    <span class="pp-divider-dot"></span>
    <span class="pp-divider-title">1. Who We Are</span>
    <div class="pp-divider-line"></div>
  </div>

  <p class="pp-prose">
    AllJackpotPredictions is a football prediction tips website operated by <strong>AllJackpotPredictions Ltd</strong>
    (referred to in this policy as "AllJackpotPredictions", "we", "us" or "our"). We are the data controller
    for personal data collected through this website.
  </p>

  <div class="pp-contact-card">
    <div class="pp-contact-rows">
      <div class="pp-contact-row">
        <span class="pp-contact-label">Operator</span>
        <span class="pp-contact-val">AllJackpotPredictions Ltd</span>
      </div>
      <div class="pp-contact-row">
        <span class="pp-contact-label">Website</span>
        <span class="pp-contact-val"><a href="https://www.alljackpotpredictions.com">www.alljackpotpredictions.com</a></span>
      </div>
      <div class="pp-contact-row">
        <span class="pp-contact-label">Privacy</span>
        <span class="pp-contact-val"><a href="mailto:privacy@alljackpotpredictions.com">privacy@alljackpotpredictions.com</a></span>
      </div>
      <div class="pp-contact-row">
        <span class="pp-contact-label">Jurisdiction</span>
        <span class="pp-contact-val">England &amp; Wales — UK GDPR and the Data Protection Act 2018 apply</span>
      </div>
    </div>
  </div>

  <p class="pp-prose">
    If you have any questions about this privacy policy or how we handle your data, please contact us
    at <a href="mailto:privacy@alljackpotpredictions.com">privacy@alljackpotpredictions.com</a>. We aim to respond to all
    privacy enquiries within 5 working days.
  </p>


  <!-- ══════════════════════════════════
       2. DATA WE COLLECT
  ══════════════════════════════════ -->
  <div class="pp-divider pp-section" id="data-collected">
    <span class="pp-divider-dot" style="background:var(--navy)"></span>
    <span class="pp-divider-title">2. Data We Collect</span>
    <div class="pp-divider-line"></div>
  </div>

  <p class="pp-prose">
    AllJackpotPredictions does not require you to create an account. Most visitors to the site provide no personal
    data at all. The following data may be collected depending on how you interact with the site:
  </p>

  <div class="pp-sub">2a. Data collected automatically</div>
  <table class="pp-data-table">
    <thead>
      <tr>
        <th>Data Type</th>
        <th>What It Includes</th>
        <th>Why Collected</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>Server log data</td>
        <td>IP address, browser type, operating system, referring URL, pages visited, time and date of visit</td>
        <td>Security monitoring, fraud prevention, debugging. Logs are retained for 30 days then deleted automatically.</td>
      </tr>
      <tr>
        <td>Analytics data</td>
        <td>Anonymised page views, session duration, device type, country-level location. No personally identifiable information is stored in analytics.</td>
        <td>Understanding how the site is used so we can improve it. See the Cookies section for details.</td>
      </tr>
      <tr>
        <td>Cookie data</td>
        <td>Cookie identifiers stored in your browser by AllJackpotPredictions and third-party services. See the full cookie table in Section 4.</td>
        <td>Site functionality, analytics, advertising. Consent is requested for non-essential cookies.</td>
      </tr>
    </tbody>
  </table>

  <div class="pp-sub">2b. Data you provide voluntarily</div>
  <table class="pp-data-table">
    <thead>
      <tr>
        <th>Data Type</th>
        <th>When Provided</th>
        <th>How Used</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>Email address</td>
        <td>If you subscribe to tip alerts or a newsletter (if offered)</td>
        <td>Sending the requested tips or newsletters only. Never shared with third parties for marketing. You can unsubscribe at any time.</td>
      </tr>
      <tr>
        <td>Contact form data</td>
        <td>Name and email address submitted via any contact form on the site</td>
        <td>Responding to your enquiry only. Retained for up to 12 months then deleted.</td>
      </tr>
    </tbody>
  </table>

  <p class="pp-prose">
    We do not collect special category data (health, religion, ethnicity, biometric data etc.) and
    we do not knowingly collect data from children under the age of 18. If you believe a child has
    provided us with personal data, please contact us at
    <a href="mailto:privacy@alljackpotpredictions.com">privacy@alljackpotpredictions.com</a> and we will delete it promptly.
  </p>


  <!-- ══════════════════════════════════
       3. HOW WE USE IT
  ══════════════════════════════════ -->
  <div class="pp-divider pp-section" id="how-we-use">
    <span class="pp-divider-dot" style="background:var(--green)"></span>
    <span class="pp-divider-title">3. How We Use Your Data</span>
    <div class="pp-divider-line"></div>
  </div>

  <p class="pp-prose">
    We process personal data only where we have a lawful basis to do so under UK GDPR. The table below
    sets out each processing activity, its purpose and the lawful basis we rely on.
  </p>

  <table class="pp-data-table">
    <thead>
      <tr>
        <th>Processing Activity</th>
        <th>Purpose</th>
        <th>Lawful Basis</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>Serving the website</td>
        <td>Delivering pages, tips and content to your browser</td>
        <td><span class="pp-tag">Legitimate Interests</span></td>
      </tr>
      <tr>
        <td>Server logging</td>
        <td>Security, fraud prevention, debugging</td>
        <td><span class="pp-tag">Legitimate Interests</span></td>
      </tr>
      <tr>
        <td>Analytics</td>
        <td>Understanding site usage to improve content and performance</td>
        <td><span class="pp-tag pink">Consent</span></td>
      </tr>
      <tr>
        <td>Advertising</td>
        <td>Displaying relevant display ads to fund the free service</td>
        <td><span class="pp-tag pink">Consent</span></td>
      </tr>
      <tr>
        <td>Email communications</td>
        <td>Sending tip alerts or newsletters you have subscribed to</td>
        <td><span class="pp-tag pink">Consent</span></td>
      </tr>
      <tr>
        <td>Responding to enquiries</td>
        <td>Answering contact form submissions or emails sent to us</td>
        <td><span class="pp-tag">Legitimate Interests</span></td>
      </tr>
      <tr>
        <td>Legal compliance</td>
        <td>Complying with legal obligations including data subject rights requests</td>
        <td><span class="pp-tag">Legal Obligation</span></td>
      </tr>
    </tbody>
  </table>

  <p class="pp-prose">
    We do not use personal data for automated decision-making or profiling that produces legal or
    similarly significant effects on individuals.
  </p>


  <!-- ══════════════════════════════════
       4. COOKIES
  ══════════════════════════════════ -->
  <div class="pp-divider pp-section" id="cookies">
    <span class="pp-divider-dot" style="background:var(--amber)"></span>
    <span class="pp-divider-title">4. Cookies</span>
    <div class="pp-divider-line"></div>
  </div>

  <p class="pp-prose">
    Cookies are small text files stored in your browser. AllJackpotPredictions uses the following categories of
    cookies. When you first visit the site, a cookie consent banner asks for your permission for
    non-essential cookies. You can change your preferences at any time via the cookie settings link
    in the footer.
  </p>

  <div class="pp-sub">Strictly necessary cookies <span class="pp-tag green">Always active</span></div>
  <table class="pp-cookie-table">
    <thead>
      <tr><th>Cookie</th><th>Provider</th><th>Purpose</th><th>Duration</th></tr>
    </thead>
    <tbody>
      <tr>
        <td>cookie_consent</td>
        <td>AllJackpotPredictions</td>
        <td>Stores your cookie consent preferences so the banner is not shown on every page</td>
        <td>12 months</td>
      </tr>
      <tr>
        <td>theme_pref</td>
        <td>AllJackpotPredictions</td>
        <td>Remembers your light/dark theme preference</td>
        <td>12 months</td>
      </tr>
      <tr>
        <td>PHPSESSID</td>
        <td>AllJackpotPredictions</td>
        <td>Session management — maintains session state across page loads</td>
        <td>Session</td>
      </tr>
    </tbody>
  </table>

  <div class="pp-sub">Analytics cookies <span class="pp-tag pink">Consent required</span></div>
  <table class="pp-cookie-table">
    <thead>
      <tr><th>Cookie</th><th>Provider</th><th>Purpose</th><th>Duration</th></tr>
    </thead>
    <tbody>
      <tr>
        <td>_ga</td>
        <td>Google Analytics</td>
        <td>Distinguishes unique users. All data is anonymised — IP addresses are truncated before storage.</td>
        <td>2 years</td>
      </tr>
      <tr>
        <td>_ga_*</td>
        <td>Google Analytics</td>
        <td>Maintains session state for GA4</td>
        <td>2 years</td>
      </tr>
      <tr>
        <td>_gid</td>
        <td>Google Analytics</td>
        <td>Distinguishes users within a 24-hour period</td>
        <td>24 hours</td>
      </tr>
    </tbody>
  </table>

  <div class="pp-sub">Advertising cookies <span class="pp-tag pink">Consent required</span></div>
  <table class="pp-cookie-table">
    <thead>
      <tr><th>Cookie</th><th>Provider</th><th>Purpose</th><th>Duration</th></tr>
    </thead>
    <tbody>
      <tr>
        <td>_gcl_au</td>
        <td>Google</td>
        <td>Conversion tracking for Google Ads</td>
        <td>3 months</td>
      </tr>
      <tr>
        <td>IDE</td>
        <td>Google DoubleClick</td>
        <td>Used for targeted advertising and ad performance measurement</td>
        <td>13 months</td>
      </tr>
      <tr>
        <td>Varies</td>
        <td>Display ad networks</td>
        <td>Third-party advertising partners may set cookies for ad delivery and frequency capping. See Section 5.</td>
        <td>Varies</td>
      </tr>
    </tbody>
  </table>

  <div class="pp-sub">How to manage cookies</div>
  <p class="pp-prose">
    You can withdraw or change your cookie consent at any time using the Cookie Settings link in the
    site footer. You can also control cookies directly in your browser settings — most browsers allow
    you to block all cookies, block third-party cookies only, or delete existing cookies. Note that
    blocking strictly necessary cookies will affect site functionality.
  </p>
  <p class="pp-prose">
    For more information about managing cookies, visit
    <a href="https://www.allaboutcookies.org" target="_blank" rel="noopener">allaboutcookies.org</a> or
    <a href="https://www.ico.org.uk/your-data-matters/online/cookies/" target="_blank" rel="noopener">ico.org.uk</a>.
  </p>


  <!-- ══════════════════════════════════
       5. THIRD PARTIES
  ══════════════════════════════════ -->
  <div class="pp-divider pp-section" id="third-parties">
    <span class="pp-divider-dot" style="background:var(--navy)"></span>
    <span class="pp-divider-title">5. Third Parties &amp; Data Sharing</span>
    <div class="pp-divider-line"></div>
  </div>

  <p class="pp-prose">
    We do not sell personal data to third parties. We may share data in the following limited circumstances:
  </p>

  <table class="pp-data-table">
    <thead>
      <tr>
        <th>Third Party</th>
        <th>Purpose</th>
        <th>Data Shared</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>Google Analytics</td>
        <td>Website analytics — understanding visitor behaviour to improve the site</td>
        <td>Anonymised usage data (IP addresses are truncated). No personally identifiable data is sent to Google Analytics.</td>
      </tr>
      <tr>
        <td>Google Ads / DoubleClick</td>
        <td>Display advertising — showing ads that help fund the free service</td>
        <td>Cookie-based advertising identifiers (with your consent). Subject to Google's privacy policy.</td>
      </tr>
      <tr>
        <td>Hosting provider</td>
        <td>Web hosting and server infrastructure</td>
        <td>Server log data as described in Section 2. The hosting provider processes this data as a data processor under a data processing agreement.</td>
      </tr>
      <tr>
        <td>Email service provider</td>
        <td>Sending tip alert emails or newsletters (if you subscribe)</td>
        <td>Email address only. Used solely for sending the content you requested. Subject to a data processing agreement.</td>
      </tr>
      <tr>
        <td>Law enforcement / regulators</td>
        <td>Compliance with legal obligations</td>
        <td>We may disclose data if required by law, court order or regulatory authority. We will notify you where legally permitted to do so.</td>
      </tr>
    </tbody>
  </table>

  <p class="pp-prose">
    Where third-party processors handle personal data on our behalf, they do so under a written data
    processing agreement that requires them to process data only on our instructions and to maintain
    appropriate security measures.
  </p>

  <div class="pp-sub">International transfers</div>
  <p class="pp-prose">
    Some of our third-party providers (including Google) are based outside the UK and EEA. Where data
    is transferred internationally, we ensure appropriate safeguards are in place — typically Standard
    Contractual Clauses (SCCs) approved by the UK ICO, or equivalent adequacy mechanisms. For more
    information on any specific transfer, contact us at
    <a href="mailto:privacy@alljackpotpredictions.com">privacy@alljackpotpredictions.com</a>.
  </p>


  <!-- ══════════════════════════════════
       6. YOUR RIGHTS
  ══════════════════════════════════ -->
  <div class="pp-divider pp-section" id="your-rights">
    <span class="pp-divider-dot" style="background:var(--pink)"></span>
    <span class="pp-divider-title">6. Your Rights Under UK GDPR</span>
    <div class="pp-divider-line"></div>
  </div>

  <p class="pp-prose">
    Under UK GDPR you have the following rights in relation to your personal data.
    To exercise any of these rights, contact us at
    <a href="mailto:privacy@alljackpotpredictions.com">privacy@alljackpotpredictions.com</a>.
    We will respond within <strong>30 calendar days</strong>. There is no fee for most requests.
  </p>

  <div class="pp-rights-grid">
    <div class="pp-right-card">
      <div class="pp-right-title">Right of Access</div>
      <p class="pp-right-body">You have the right to request a copy of all personal data we hold about you (a Subject Access Request). We will provide this within 30 days.</p>
    </div>
    <div class="pp-right-card">
      <div class="pp-right-title">Right to Rectification</div>
      <p class="pp-right-body">You can ask us to correct any inaccurate personal data we hold about you, or to complete any incomplete data.</p>
    </div>
    <div class="pp-right-card">
      <div class="pp-right-title">Right to Erasure</div>
      <p class="pp-right-body">You can ask us to delete your personal data ("the right to be forgotten"). We will comply unless we are required to retain the data by law.</p>
    </div>
    <div class="pp-right-card">
      <div class="pp-right-title">Right to Restrict Processing</div>
      <p class="pp-right-body">You can ask us to pause processing of your personal data in certain circumstances — for example, while you contest its accuracy.</p>
    </div>
    <div class="pp-right-card">
      <div class="pp-right-title">Right to Data Portability</div>
      <p class="pp-right-body">Where we process your data based on consent or contract, you can ask us to provide it to you in a structured, machine-readable format.</p>
    </div>
    <div class="pp-right-card">
      <div class="pp-right-title">Right to Object</div>
      <p class="pp-right-body">You can object to processing based on legitimate interests. We will stop unless we can demonstrate compelling grounds that override your interests.</p>
    </div>
    <div class="pp-right-card">
      <div class="pp-right-title">Right to Withdraw Consent</div>
      <p class="pp-right-body">Where processing is based on your consent, you can withdraw it at any time. This does not affect the lawfulness of processing before withdrawal.</p>
    </div>
    <div class="pp-right-card">
      <div class="pp-right-title">Right to Complain</div>
      <p class="pp-right-body">You have the right to lodge a complaint with the UK Information Commissioner's Office (ICO) at <a href="https://ico.org.uk" target="_blank" rel="noopener">ico.org.uk</a> or on 0303 123 1113.</p>
    </div>
  </div>


  <!-- ══════════════════════════════════
       7. RETENTION
  ══════════════════════════════════ -->
  <div class="pp-divider pp-section" id="retention">
    <span class="pp-divider-dot" style="background:var(--navy)"></span>
    <span class="pp-divider-title">7. Data Retention</span>
    <div class="pp-divider-line"></div>
  </div>

  <p class="pp-prose">
    We retain personal data only for as long as necessary for the purpose for which it was collected,
    or as required by law.
  </p>

  <table class="pp-data-table">
    <thead>
      <tr>
        <th>Data Type</th>
        <th>Retention Period</th>
        <th>Reason</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>Server logs</td>
        <td>30 days</td>
        <td>Security and debugging. Automatically deleted after 30 days.</td>
      </tr>
      <tr>
        <td>Analytics data</td>
        <td>14 months (Google Analytics default)</td>
        <td>Trend analysis. Data is anonymised before storage — no personal data is retained after the session.</td>
      </tr>
      <tr>
        <td>Email addresses (subscribers)</td>
        <td>Until unsubscribed + 30 days</td>
        <td>Sending the service you requested. Deleted within 30 days of unsubscription.</td>
      </tr>
      <tr>
        <td>Contact form submissions</td>
        <td>12 months</td>
        <td>Responding to and following up on enquiries. Deleted after 12 months.</td>
      </tr>
      <tr>
        <td>Cookie consent records</td>
        <td>12 months</td>
        <td>Evidence of consent. Renewed when consent is updated.</td>
      </tr>
    </tbody>
  </table>


  <!-- ══════════════════════════════════
       8. SECURITY
  ══════════════════════════════════ -->
  <div class="pp-divider pp-section" id="security">
    <span class="pp-divider-dot" style="background:var(--green)"></span>
    <span class="pp-divider-title">8. Security</span>
    <div class="pp-divider-line"></div>
  </div>

  <p class="pp-prose">
    AllJackpotPredictions implements appropriate technical and organisational measures to protect personal data
    against unauthorised access, accidental loss, destruction or disclosure. These include:
  </p>
  <p class="pp-prose">
    <span class="pp-tag green">HTTPS / TLS</span> — all data in transit is encrypted using TLS.
    <span class="pp-tag green" style="margin-left:6px">Access Controls</span> — administrative access to systems is restricted to authorised personnel only.
    <span class="pp-tag green" style="margin-left:6px">Log Minimisation</span> — server logs are retained for 30 days then permanently deleted.
  </p>
  <p class="pp-prose">
    In the event of a personal data breach that is likely to result in a risk to your rights and freedoms,
    we will notify the ICO within 72 hours and, where required, notify affected individuals without undue delay.
  </p>


  <!-- ══════════════════════════════════
       9. LINKS TO OTHER SITES
  ══════════════════════════════════ -->
  <div class="pp-divider pp-section" id="links">
    <span class="pp-divider-dot"></span>
    <span class="pp-divider-title">9. Links to Other Websites</span>
    <div class="pp-divider-line"></div>
  </div>

  <p class="pp-prose">
    AllJackpotPredictions contains links to third-party websites including bookmakers, responsible gambling
    organisations and data providers. This privacy policy applies only to alljackpotpredictions.com. We are not
    responsible for the privacy practices of linked third-party websites and encourage you to read
    their privacy policies before providing any personal data to them.
  </p>


  <!-- ══════════════════════════════════
       10. CHANGES TO THIS POLICY
  ══════════════════════════════════ -->
  <div class="pp-divider pp-section" id="changes">
    <span class="pp-divider-dot"></span>
    <span class="pp-divider-title">10. Changes to This Policy</span>
    <div class="pp-divider-line"></div>
  </div>

  <p class="pp-prose">
    We may update this privacy policy from time to time to reflect changes in our practices, technology
    or legal requirements. When we make material changes, we will update the "Last Updated" date at the
    top of this page. We encourage you to review this policy periodically.
  </p>
  <p class="pp-prose">
    Continued use of AllJackpotPredictions after a policy update constitutes acceptance of the updated policy.
    If you do not agree with the updated policy, please stop using the site and contact us if you
    wish to exercise any of your data rights.
  </p>


  <!-- ══════════════════════════════════
       11. CONTACT
  ══════════════════════════════════ -->
  <div class="pp-divider pp-section" id="contact">
    <span class="pp-divider-dot" style="background:var(--navy)"></span>
    <span class="pp-divider-title">11. Contact &amp; Complaints</span>
    <div class="pp-divider-line"></div>
  </div>

  <p class="pp-prose">
    For any privacy-related questions, data subject rights requests or to report a concern about
    how we handle personal data, contact us at:
  </p>

  <div class="pp-contact-card">
    <div class="pp-contact-rows">
      <div class="pp-contact-row">
        <span class="pp-contact-label">Email</span>
        <span class="pp-contact-val"><a href="mailto:privacy@alljackpotpredictions.com">privacy@alljackpotpredictions.com</a></span>
      </div>
      <div class="pp-contact-row">
        <span class="pp-contact-label">Response</span>
        <span class="pp-contact-val">Within 5 working days for general enquiries. Within 30 calendar days for data subject rights requests.</span>
      </div>
      <div class="pp-contact-row">
        <span class="pp-contact-label">ICO</span>
        <span class="pp-contact-val">If you are not satisfied with our response, you have the right to lodge a complaint with the UK Information Commissioner's Office at <a href="https://ico.org.uk" target="_blank" rel="noopener">ico.org.uk</a> or 0303 123 1113.</span>
      </div>
    </div>
  </div>

  <p class="pp-prose" style="margin-top:8px;font-size:.74rem;color:var(--muted);">
    This privacy policy was last updated in March 2026 and applies to alljackpotpredictions.com only.
    Version 3.0.
  </p>

</div><!-- /pp-content -->
</div><!-- /below-page -->

<?php include 'footer.php'; ?>
<button class="btt" id="btt" aria-label="Back to top">↑</button>
<script src="main.js"></script>
</body>
</html>