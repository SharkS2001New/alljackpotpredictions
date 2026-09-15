<?php
require_once __DIR__ . '/includes/link-functions.php';
// Prevent browsers/proxies from serving a stale copy after admin edits.
header('Cache-Control: no-cache, no-store, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: Sat, 01 Jan 2000 00:00:00 GMT');
header('X-LiteSpeed-Cache-Control: no-cache'); // harmless if your host isn't LiteSpeed
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Partners &amp; Link Exchange — Trusted Football Prediction Sites | AllJackpotPredictions</title>
<meta name="description" content="AllJackpotPredictions' partner websites — trusted football prediction and betting tips sites we work with. Link exchange and partnership enquiries welcome.">
<link rel="canonical" href="https://www.alljackpotpredictions.com/partners">

<meta property="og:type" content="website">
<meta property="og:url" content="https://www.alljackpotpredictions.com/partners">
<meta property="og:title" content="Partners &amp; Link Exchange | AllJackpotPredictions">
<meta property="og:description" content="AllJackpotPredictions' trusted partner sites in football predictions and betting tips. Partnership and link exchange enquiries welcome.">
<meta property="og:site_name" content="AllJackpotPredictions">

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "BreadcrumbList",
      "itemListElement": [
        { "@type": "ListItem", "position": 1, "name": "Home",     "item": "https://www.alljackpotpredictions.com/" },
        { "@type": "ListItem", "position": 2, "name": "Partners", "item": "https://www.alljackpotpredictions.com/partners" }
      ]
    },
    {
      "@type": "WebPage",
      "name": "Partners & Link Exchange",
      "url": "https://www.alljackpotpredictions.com/partners",
      "description": "AllJackpotPredictions' partner websites in football predictions and betting tips. Partnership and link exchange enquiries welcome.",
      "datePublished": "2026-03-21",
      "dateModified": "2026-03-21"
    }
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
   PARTNERS — page-scoped styles
═══════════════════════════════════════════ */

/* ── Hero ── */
.par-hero {
  background: var(--paper2);
  border-bottom: 2px solid var(--border2);
  padding: 52px 0 0;
  overflow: hidden;
}
[data-theme="dark"] .par-hero { background: var(--paper3); }

.par-hero-inner {
  max-width: 1280px; margin: 0 auto; padding: 0 32px;
  display: grid; grid-template-columns: 1fr 380px; gap: 52px; align-items: start;
}

.par-kicker { display: flex; align-items: center; gap: 10px; margin-bottom: 18px; }
.par-kicker-line { width: 24px; height: 2px; background: var(--pink); flex-shrink: 0; }
.par-kicker-text {
  font-family: var(--fmono); font-size: .6rem; font-weight: 700;
  letter-spacing: .2em; text-transform: uppercase; color: var(--muted);
}

/* ── H1 ── */
.par-h1 { margin: 0 0 24px; display: flex; flex-direction: column; gap: 0; line-height: 1; }
.par-h1-solid {
  font-family: 'Playfair Display', Georgia, serif;
  font-size: clamp(3rem, 6.5vw, 6rem);
  font-weight: 900; font-style: normal; line-height: 1.0;
  letter-spacing: -.02em; color: var(--navy); display: block;
}
[data-theme="dark"] .par-h1-solid { color: var(--ink); }
.par-h1-outline {
  font-family: 'Playfair Display', Georgia, serif;
  font-size: clamp(3.8rem, 8.5vw, 7.5rem);
  font-weight: 700; font-style: italic; line-height: 1.05;
  letter-spacing: -.03em; color: transparent;
  -webkit-text-stroke: 2px var(--pink);
  text-stroke: 2px var(--pink); display: block;
}
[data-theme="dark"] .par-h1-outline { -webkit-text-stroke-color: var(--pink); }

.par-hero-body {
  font-family: var(--fsans); font-size: .9rem; line-height: 1.85;
  color: var(--body); max-width: 520px; margin-bottom: 32px;
}
.par-hero-body strong { color: var(--ink); font-weight: 700; }

/* pills */
.par-pills { display: flex; flex-wrap: wrap; gap: 8px; padding-bottom: 40px; }
.par-pill {
  display: inline-flex; align-items: center;
  padding: 8px 14px; border: 1px solid var(--border2); border-radius: var(--r);
  background: var(--paper); color: var(--body);
  font-family: var(--fsans); font-size: .68rem; font-weight: 700;
  letter-spacing: .04em; text-transform: uppercase; text-decoration: none;
  transition: all .2s var(--ease);
}
.par-pill:hover { background: var(--navy); color: #fff; border-color: var(--navy); }
[data-theme="dark"] .par-pill:hover { background: var(--navy3); border-color: var(--navy3); }

/* ── Hero right — network stat boxes ── */
.par-stats { display: flex; flex-direction: column; gap: 12px; padding-top: 6px; }
.par-stat-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
.par-stat-box {
  background: var(--paper); border: 1px solid var(--border2);
  border-radius: var(--r-md); padding: 16px 15px;
  display: flex; flex-direction: column; gap: 5px;
}
.par-stat-val {
  font-family: var(--fdisp); font-size: 2.4rem; font-weight: 400;
  line-height: 1; letter-spacing: -.025em; color: var(--navy);
}
[data-theme="dark"] .par-stat-val { color: var(--navy2); }
.par-stat-val.pink  { color: var(--pink); }
.par-stat-val.green { color: var(--green); }
.par-stat-lbl {
  font-family: var(--fmono); font-size: .54rem; font-weight: 700;
  letter-spacing: .12em; text-transform: uppercase; color: var(--muted);
}

/* guidelines box in hero right */
.par-guidelines-box {
  background: var(--paper); border: 1px solid var(--border2);
  border-radius: var(--r-md); padding: 16px 18px;
}
.par-guidelines-box-title {
  font-family: var(--fmono); font-size: .58rem; font-weight: 700;
  letter-spacing: .12em; text-transform: uppercase; color: var(--muted);
  margin-bottom: 10px;
}
.par-guidelines-items { display: flex; flex-direction: column; gap: 7px; }
.par-guidelines-item {
  display: flex; align-items: flex-start; gap: 8px;
  font-family: var(--fsans); font-size: .74rem; line-height: 1.65; color: var(--body);
}
.par-guidelines-dot {
  width: 6px; height: 6px; border-radius: 50%; background: var(--green);
  flex-shrink: 0; margin-top: 6px;
}
.par-guidelines-dot.pink { background: var(--pink); }

/* ── Shared section divider ── */
.par-divider {
  display: flex; align-items: center; gap: 12px; margin: 48px 0 22px;
}
.par-divider-dot { width: 10px; height: 10px; border-radius: 50%; background: var(--pink); flex-shrink: 0; }
.par-divider-title {
  font-family: var(--fsans); font-size: .9rem; font-weight: 800;
  letter-spacing: .1em; text-transform: uppercase; color: var(--navy); white-space: nowrap;
}
[data-theme="dark"] .par-divider-title { color: var(--ink); }
.par-divider-sub { font-family: var(--fmono); font-size: .6rem; color: var(--muted); white-space: nowrap; }
.par-divider-line { flex: 1; height: 1px; background: var(--border2); }

/* ── Partner grid ── */
.par-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 10px;
  margin-bottom: 8px;
}

/* ── Partner card ── */
.par-card {
  background: var(--paper); border: 1px solid var(--border2);
  border-radius: var(--r-lg); padding: 22px 20px 18px;
  display: flex; flex-direction: column;
  text-decoration: none;
  transition: box-shadow .22s var(--ease), border-color .2s, transform .15s;
  position: relative; overflow: hidden;
}
.par-card:hover {
  box-shadow: var(--sh-md);
  border-color: var(--pink);
  transform: translateY(-2px);
}
/* dashed placeholder card */
.par-card.placeholder {
  border-style: dashed; opacity: .5;
  pointer-events: none; cursor: default;
}

.par-badge {
  display: inline-flex; align-items: center; gap: 5px;
  padding: 3px 9px; border-radius: 3px;
  font-family: var(--fmono); font-size: .55rem; font-weight: 700;
  letter-spacing: .08em; text-transform: uppercase;
  align-self: flex-start; margin-bottom: 12px; white-space: nowrap;
}
.par-badge.partner { background: rgba(201,162,39,.12); color: var(--gold);  border: 1px solid rgba(201,162,39,.3); }
.par-badge.network { background: rgba(11,61,46,.08);   color: var(--navy);  border: 1px solid rgba(11,61,46,.15); }
[data-theme="dark"] .par-badge.network { background: rgba(45,143,106,.1); color: var(--navy2); border-color: rgba(45,143,106,.2); }
.par-badge.tipster  { background: rgba(10,143,90,.1);  color: var(--green); border: 1px solid rgba(10,143,90,.25); }
.par-badge.slot     { background: var(--paper2); color: var(--muted); border: 1px solid var(--border2); }
.par-badge.sponsored { background: rgba(201,162,39,.1); color: var(--pink); border: 1px solid rgba(201,162,39,.25); }

.par-card-name {
  font-family: var(--fsans); font-size: .95rem; font-weight: 800;
  color: var(--navy); line-height: 1.2; margin-bottom: 3px;
}
[data-theme="dark"] .par-card-name { color: var(--ink); }
.par-card-kicker {
  font-family: var(--fmono); font-size: .55rem; font-weight: 700;
  letter-spacing: .1em; text-transform: uppercase; color: var(--muted);
  margin-bottom: 4px;
}
.par-card-url {
  font-family: var(--fmono); font-size: .62rem; font-weight: 700;
  color: var(--pink); margin-bottom: 12px; letter-spacing: .01em;
}
.par-card-desc {
  font-family: var(--fsans); font-size: .77rem; line-height: 1.8;
  color: var(--body); flex: 1; margin-bottom: 14px;
}
.par-card-tags {
  display: flex; flex-wrap: wrap; gap: 5px; margin-bottom: 14px;
}
.par-card-tag {
  font-family: var(--fmono); font-size: .52rem; font-weight: 700;
  letter-spacing: .06em; text-transform: uppercase;
  background: rgba(11,61,46,.06); border: 1px solid var(--border2);
  color: var(--navy); padding: 3px 7px; border-radius: 2px;
}
[data-theme="dark"] .par-card-tag { background: rgba(45,143,106,.08); color: var(--navy2); border-color: rgba(45,143,106,.15); }
.par-card-visit {
  display: inline-flex; align-items: center; gap: 6px;
  font-family: var(--fmono); font-size: .65rem; font-weight: 700;
  letter-spacing: .05em; text-transform: uppercase; color: var(--pink);
  padding-top: 12px; border-top: 1px solid var(--border);
  transition: gap .15s;
}
.par-card:hover .par-card-visit { gap: 10px; }

/* ── Guidelines section ── */
.par-guide-grid {
  display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px;
  margin-bottom: 8px;
}
.par-guide-card {
  background: var(--paper); border: 1px solid var(--border2);
  border-radius: var(--r-lg); padding: 18px 16px 14px;
  display: flex; flex-direction: column; gap: 8px;
}
.par-guide-icon { font-size: 1.2rem; line-height: 1; }
.par-guide-title {
  font-family: var(--fsans); font-size: .82rem; font-weight: 800;
  color: var(--navy); line-height: 1.3;
}
[data-theme="dark"] .par-guide-title { color: var(--ink); }
.par-guide-body {
  font-family: var(--fsans); font-size: .75rem; line-height: 1.8; color: var(--body);
}

/* ── CTA strip ── */
.par-cta-strip {
  background: var(--navy); border-radius: var(--r-lg);
  padding: 32px 36px; margin-top: 48px; margin-bottom: 48px;
  display: flex; align-items: center; justify-content: space-between; gap: 32px;
}
[data-theme="dark"] .par-cta-strip { background: var(--navy3); }
.par-cta-title {
  font-family: 'Playfair Display', Georgia, serif;
  font-size: clamp(1.4rem, 3vw, 2rem);
  font-weight: 900; color: #fff; line-height: 1.1; margin-bottom: 8px;
}
.par-cta-sub {
  font-family: var(--fsans); font-size: .8rem; color: rgba(255,255,255,.6); line-height: 1.7;
  max-width: 480px;
}
.par-cta-btn {
  display: inline-flex; align-items: center; gap: 8px;
  padding: 13px 24px; border-radius: var(--r);
  background: var(--pink); color: #0b3d2e; border: none;
  font-family: var(--fsans); font-size: .78rem; font-weight: 700;
  letter-spacing: .05em; text-transform: uppercase; text-decoration: none;
  white-space: nowrap; flex-shrink: 0; transition: background .18s;
}
.par-cta-btn:hover { background: #c8144f; }

/* ── Responsive ── */
@media(max-width:960px) {
  .par-hero-inner { grid-template-columns: 1fr; gap: 32px; }
  .par-stats { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
  .par-grid  { grid-template-columns: 1fr 1fr; }
  .par-guide-grid { grid-template-columns: 1fr 1fr; }
  .par-cta-strip { flex-direction: column; align-items: flex-start; gap: 20px; }
}
@media(max-width:700px) {
  .par-hero-inner { padding: 0 16px; }
  .par-h1-solid   { font-size: 2.6rem; }
  .par-h1-outline { font-size: 3.6rem; }
  .par-grid       { grid-template-columns: 1fr; }
  .par-guide-grid { grid-template-columns: 1fr; }
  .par-stat-row   { grid-template-columns: 1fr 1fr; }
  .par-cta-strip  { padding: 24px 20px; }
}
</style>

<!-- Google Tag Manager: add your AllJackpotPredictions GTM container here -->
</head>
<body>

<?php include 'header.php'; ?>

<!-- ══════════════════════════════════
     HERO
══════════════════════════════════ -->
<div class="par-hero">
  <div class="par-hero-inner">

    <!-- LEFT -->
    <div>
      <div class="par-kicker">
        <div class="par-kicker-line"></div>
        <span class="par-kicker-text">Network &amp; Partnerships</span>
      </div>

      <h1 class="par-h1">
        <span class="par-h1-solid">PARTNERS &amp;</span>
        <span class="par-h1-outline">Links</span>
      </h1>

      <p class="par-hero-body">
        AllJackpotPredictions works with a trusted network of football prediction and betting tips sites.
        Below are the partner sites we recommend — each independently operated and sharing
        our commitment to <strong>free, data-driven, transparent predictions</strong>.
      </p>

      <div class="par-pills">
        <a class="par-pill" href="#our-networks-partners">Our Networks &amp; Partners</a>
        <a class="par-pill" href="#guidelines">Guidelines</a>
        <a class="par-pill" href="#enquire">Enquire</a>
      </div>
    </div>

    <!-- RIGHT -->
    <div>
      <div class="par-stats">
        <div class="par-stat-row">
          <div class="par-stat-box">
            <div class="par-stat-val">10</div>
            <div class="par-stat-lbl">Network Sites</div>
          </div>
          <div class="par-stat-box">
            <div class="par-stat-val pink">2</div>
            <div class="par-stat-lbl">Partners</div>
          </div>
        </div>

        <div class="par-guidelines-box">
          <div class="par-guidelines-box-title">✅ What We Look For</div>
          <div class="par-guidelines-items">
            <div class="par-guidelines-item">
              <div class="par-guidelines-dot"></div>
              <span>Free tips as the primary offering</span>
            </div>
            <div class="par-guidelines-item">
              <div class="par-guidelines-dot"></div>
              <span>Responsible gambling messaging</span>
            </div>
            <div class="par-guidelines-item">
              <div class="par-guidelines-dot"></div>
              <span>18+ compliance on all pages</span>
            </div>
            <div class="par-guidelines-item">
              <div class="par-guidelines-dot"></div>
              <span>Transparent track record or results history</span>
            </div>
            <div class="par-guidelines-item">
              <div class="par-guidelines-dot pink"></div>
              <span>No guaranteed tips or scam messaging</span>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

<!-- breadcrumb -->
<nav class="breadcrumb-wrap" aria-label="Breadcrumb">
  <div class="breadcrumb">
    <a href="/">Home</a><span class="bc-sep">›</span>
    <span class="bc-cur">Partners</span>
  </div>
</nav>

<!-- market strip -->
<div class="markets-strip">
  <div class="ms-inner">
    <a class="ms-tab active" href="/partners">Partners</a>
    <a class="ms-tab" href="/predictions-today">Today's Tips</a>
    <a class="ms-tab jackpot" href="/jackpot-picks-today">◈ Jackpot Picks</a>
    <a class="ms-tab" href="/how-it-works">How It Works</a>
    <a class="ms-tab" href="/about">About</a>
    <a class="ms-tab" href="/contact">Contact</a>
  </div>
</div>

<!-- ══ CONTENT ══ -->
<div class="below-page">

  <!-- ══════════════════════════════════
       OUR NETWORKS & PARTNERS
       (Partner Cards admin tab + Text Links admin tab, combined)
  ══════════════════════════════════ -->
  <div class="par-divider" id="our-networks-partners">
    <span class="par-divider-dot" style="background:var(--pink)"></span>
    <span class="par-divider-title">Our Networks &amp; Partners</span>
    <span class="par-divider-sub">— Sites we recommend</span>
    <div class="par-divider-line"></div>
  </div>

  <div class="par-grid">
    <?php
    $partnerCards = ll_get_active('card');
    $partnerLinks = ll_get_active('partner');
    $hasPartners = !empty($partnerCards) || !empty($partnerLinks);

    /* Partner Cards tab (Prediction Partners + Our Network) */
    foreach ($partnerCards as $card):
      $host = parse_url($card['url'] ?? '', PHP_URL_HOST) ?: ($card['url'] ?? '');
      $anchor = ($card['name'] ?? '') ?: ll_domain_to_anchor($card['url'] ?? '');
    ?>
      <a class="par-card" href="<?= htmlspecialchars($card['url'] ?? '#') ?>" target="_blank"<?= ll_rel_attr($card['rel_type'] ?? 'nofollow') ?>>
        <div class="par-card-kicker">Anchor</div>
        <div class="par-card-name"><?= htmlspecialchars($anchor) ?></div>
        <div class="par-card-kicker">Domain</div>
        <div class="par-card-url"><?= htmlspecialchars($host) ?></div>
        <p class="par-card-desc"><?= htmlspecialchars($card['description'] ?? '') ?></p>
      </a>
    <?php endforeach; ?>

    <?php
    /* Text Links tab */
    foreach ($partnerLinks as $link):
      $host = parse_url($link['url'] ?? '', PHP_URL_HOST) ?: ($link['url'] ?? '');
      $anchor = ($link['anchor_text'] ?? '') ?: (($link['name'] ?? '') ?: ll_domain_to_anchor($link['url'] ?? ''));
    ?>
      <a class="par-card" href="<?= htmlspecialchars($link['url'] ?? '#') ?>" target="_blank"<?= ll_rel_attr($link['rel_type'] ?? 'nofollow') ?>>
        <div class="par-card-kicker">Anchor</div>
        <div class="par-card-name"><?= htmlspecialchars($anchor) ?></div>
        <div class="par-card-kicker">Domain</div>
        <div class="par-card-url"><?= htmlspecialchars($host) ?></div>
        <p class="par-card-desc"><?= htmlspecialchars(($link['description'] ?? '') ?: 'Partner placement.') ?></p>
      </a>
    <?php endforeach; ?>

    <?php if (!$hasPartners): ?>
    <div class="par-card placeholder" style="grid-column: 1 / -1; max-width: 520px;">
      <div class="par-card-kicker">Coming soon</div>
      <div class="par-card-name">No partners listed yet</div>
      <div class="par-card-kicker">Your site</div>
      <div class="par-card-url">yoursite.com</div>
      <p class="par-card-desc">AllJackpotPredictions is building its partner network. If you run a free football tips site with transparent results and responsible gambling messaging, get in touch — we review partnership enquiries within 5 working days.</p>
    </div>
    <?php else: ?>
    <!-- Open slot -->
    <div class="par-card placeholder">
      <div class="par-card-kicker">Anchor</div>
      <div class="par-card-name" style="color:var(--muted)">Your Site Here</div>
      <div class="par-card-kicker">Domain</div>
      <div class="par-card-url" style="color:var(--muted)">yoursite.com</div>
      <p class="par-card-desc">Interested in a partnership or link exchange with AllJackpotPredictions? We work with football prediction sites that share our values — free tips, transparent track records and responsible gambling messaging.</p>
    </div>
    <?php endif; ?>

  </div><!-- /par-grid -->



  <!-- ══════════════════════════════════
       PARTNERSHIP GUIDELINES
  ══════════════════════════════════ -->
  <div class="par-divider" id="guidelines">
    <span class="par-divider-dot" style="background:var(--green)"></span>
    <span class="par-divider-title">Partnership Guidelines</span>
    <span class="par-divider-sub">— What we look for in a partner site</span>
    <div class="par-divider-line"></div>
  </div>

  <div class="par-guide-grid">
    <div class="par-guide-card">
      <div class="par-guide-icon">✅</div>
      <div class="par-guide-title">Free predictions</div>
      <p class="par-guide-body">Your primary tips offering must be free to access. We don't partner with paywalled-only services — the core value to users has to be freely available.</p>
    </div>
    <div class="par-guide-card">
      <div class="par-guide-icon">🛡</div>
      <div class="par-guide-title">Responsible gambling messaging</div>
      <p class="par-guide-body">Your site must display responsible gambling messaging and link to recognised support organisations such as GamCare, GamStop or equivalent.</p>
    </div>
    <div class="par-guide-card">
      <div class="par-guide-icon">🔞</div>
      <div class="par-guide-title">18+ compliance</div>
      <p class="par-guide-body">Your site must make clear that all content is for adults only. An 18+ notice should be visible on key pages including the homepage and any tips pages.</p>
    </div>
    <div class="par-guide-card">
      <div class="par-guide-icon">📊</div>
      <div class="par-guide-title">Transparent track record</div>
      <p class="par-guide-body">A published win rate, results history or track record is preferred. We value honest reporting of past performance over unverifiable self-promotional claims.</p>
    </div>
    <div class="par-guide-card">
      <div class="par-guide-icon">⚽</div>
      <div class="par-guide-title">Quality content</div>
      <p class="par-guide-body">Tips should be supported by at least brief analysis — form, stats or context. Bare selections with no supporting rationale don't meet our editorial standard.</p>
    </div>
    <div class="par-guide-card">
      <div class="par-guide-icon">🚫</div>
      <div class="par-guide-title">No prohibited content</div>
      <p class="par-guide-body">We do not partner with sites promoting unlicensed gambling operators, guaranteed tips schemes, scam services or any content that violates responsible gambling standards.</p>
    </div>
  </div>


  <!-- ══════════════════════════════════
       CTA — ENQUIRE
  ══════════════════════════════════ -->
  <div class="par-cta-strip" id="enquire">
    <div>
      <div class="par-cta-title">Interested in a Partnership?</div>
      <p class="par-cta-sub">If you operate a football predictions site and would like to discuss a link exchange, network listing or joint promotion, get in touch. We review all enquiries and respond within 5 working days.</p>
    </div>
    <a class="par-cta-btn" href="mailto:partners@alljackpotpredictions.com?subject=Partnership%20Enquiry%20%E2%80%94%20[Your%20Site%20Name]">
      ✉ Get in Touch →
    </a>
  </div>


  <!-- ── RG disclaimer ── -->
  <div class="rg-box" style="margin-bottom:40px">
    <div class="rg-top">
      <span class="rg-icon">🛡</span>
      <div>
        <div class="rg-title">Tips Are Entertainment — Not Financial Advice</div>
        <p class="rg-sub">All tips on AllJackpotPredictions and partner sites are for entertainment purposes only. 18+ only. Please gamble responsibly.</p>
      </div>
    </div>
    <div class="rg-cols">
      <div class="rg-col">
        <h4>⚠ Disclaimer</h4>
        <p>No tip can guarantee a winning outcome. Tips are research-backed opinions, not financial advice. Past performance does not guarantee future results.</p>
        <h4 class="rg-col-h4-spaced">🔞 18+ Only</h4>
        <p>Gambling is only legal for persons aged 18 or over in the UK.</p>
      </div>
      <div class="rg-col">
        <h4>🆘 Free Help &amp; Support</h4>
        <div class="rg-links-grid">
          <a href="https://www.begambleaware.org" target="_blank" rel="noopener" class="rg-link"><strong>BeGambleAware</strong><span>begambleaware.org</span></a>
          <a href="https://www.gamcare.org.uk"    target="_blank" rel="noopener" class="rg-link"><strong>GamCare</strong><span>gamcare.org.uk</span></a>
          <a href="https://www.gamstop.co.uk"    target="_blank" rel="noopener" class="rg-link"><strong>GamStop</strong><span>gamstop.co.uk</span></a>
          <a href="https://www.gamblersanonymous.org.uk" target="_blank" rel="noopener" class="rg-link"><strong>Gamblers Anonymous</strong><span>gamblersanonymous.org.uk</span></a>
        </div>
      </div>
    </div>
  </div>

</div><!-- /below-page -->

<?php include 'footer.php'; ?>
<button class="btt" id="btt" aria-label="Back to top">↑</button>
<script src="main.js"></script>
</body>
</html>