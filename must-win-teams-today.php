<?php
$mwActiveCat = $mwActiveCat ?? 'all';
if (!in_array($mwActiveCat, ['all', 'home', 'away', 'never', 'trust'], true)) {
    $mwActiveCat = 'all';
}
$__mwDefaults = [
    'all' => [
        'title' => 'Must Win Teams Today — Home & Away Teams To Win | AllJackpotPredictions',
        'desc' => 'Must win teams today — home teams to win today, away teams to win today and the most trusted teams that will never lose. Data-backed picks updated daily.',
        'keywords' => 'must win teams today, home teams to win today, away teams to win today, teams that will never lose, most trusted teams to win today',
        'canonical' => 'https://www.alljackpotpredictions.com/must-win-teams-today',
        'h1' => 'Today',
        'kicker' => 'Must Win Teams Today',
    ],
    'home' => [
        'title' => 'Home Teams To Win Today — Must Win Home Tips | AllJackpotPredictions',
        'desc' => 'Home teams to win today — high-confidence must-win home tips. Free daily selections on AllJackpotPredictions.',
        'keywords' => 'home teams to win today, must win home teams, home win tips today',
        'canonical' => 'https://www.alljackpotpredictions.com/home-teams-to-win-today',
        'h1' => 'Home Teams To Win',
        'kicker' => 'Must Win · Home',
    ],
    'away' => [
        'title' => 'Away Teams To Win Today — Must Win Away Tips | AllJackpotPredictions',
        'desc' => 'Away teams to win today — strong away-win must-win picks. Free daily tips on AllJackpotPredictions.',
        'keywords' => 'away teams to win today, must win away teams, away win tips today',
        'canonical' => 'https://www.alljackpotpredictions.com/away-teams-to-win-today',
        'h1' => 'Away Teams To Win',
        'kicker' => 'Must Win · Away',
    ],
    'never' => [
        'title' => 'Teams That Will Never Lose Today | AllJackpotPredictions',
        'desc' => 'Teams that will never lose today — highest no-loss probability selections. Free on AllJackpotPredictions.',
        'keywords' => 'teams that will never lose, teams that will never lose today, no lose tips',
        'canonical' => 'https://www.alljackpotpredictions.com/teams-that-will-never-lose',
        'h1' => 'Will Never Lose',
        'kicker' => 'Must Win · Never Lose',
    ],
    'trust' => [
        'title' => 'Most Trusted Teams To Win Today | AllJackpotPredictions',
        'desc' => 'Most trusted teams to win today — elite must-win banker tips. Free on AllJackpotPredictions.',
        'keywords' => 'most trusted teams to win, most trusted teams to win today, banker tips today',
        'canonical' => 'https://www.alljackpotpredictions.com/most-trusted-teams-to-win',
        'h1' => 'Most Trusted',
        'kicker' => 'Must Win · Most Trusted',
    ],
];
$__mw = $__mwDefaults[$mwActiveCat];
$mwPageTitle = $mwPageTitle ?? $__mw['title'];
$mwMetaDescription = $mwMetaDescription ?? $__mw['desc'];
$mwMetaKeywords = $mwMetaKeywords ?? $__mw['keywords'];
$mwCanonical = $mwCanonical ?? $__mw['canonical'];
$mwH1Accent = $mwH1Accent ?? $__mw['h1'];
$mwKicker = $mwKicker ?? $__mw['kicker'];
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title><?php echo htmlspecialchars($mwPageTitle); ?></title>
<meta name="description" content="<?php echo htmlspecialchars($mwMetaDescription); ?>">
<meta name="keywords" content="<?php echo htmlspecialchars($mwMetaKeywords); ?>">
<meta name="author" content="AllJackpotPredictions Analyst Team">
<link rel="canonical" href="<?php echo htmlspecialchars($mwCanonical); ?>">

<meta property="og:type" content="website">
<meta property="og:url" content="<?php echo htmlspecialchars($mwCanonical); ?>">
<meta property="og:title" content="<?php echo htmlspecialchars($mwPageTitle); ?>">
<meta property="og:description" content="<?php echo htmlspecialchars($mwMetaDescription); ?>">
<meta property="og:site_name" content="AllJackpotPredictions">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:site" content="@AllJackpotPreds">
<meta name="twitter:title" content="<?php echo htmlspecialchars($mwPageTitle); ?>">
<meta name="twitter:description" content="<?php echo htmlspecialchars($mwMetaDescription); ?>">

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

<link rel="stylesheet" href="styles.css">

<style>
/* Must-Win Page Styling */
:root {
  --fire: #c9a227;
  --fire-light: #fff3ed;
  --gold: #f5a623;
  --gold-light: #fffbef;
}

.hero {
  background: linear-gradient(135deg, #0b3d2e 0%, #06261c 60%, #0b3d2e 100%);
}
.h1-accent { color: #c9a227; }
.hero-kicker-dot { background: #c9a227; }

.mw-tabs {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
  margin-bottom: 22px;
}
.mw-tab {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 7px 16px;
  border-radius: 99px;
  font-size: .78rem;
  font-weight: 700;
  border: 1.5px solid transparent;
  cursor: pointer;
  transition: all .18s;
}
.mw-tab-all,
.mw-tab-home,
.mw-tab-away,
.mw-tab-never,
.mw-tab-trust {
  background: var(--paper2, #e7eee9);
  color: var(--body, #3a4a42);
  border-color: var(--border2, rgba(11,61,46,.2));
  text-decoration: none;
}
.mw-tab:hover { border-color: var(--gold, #c9a227); color: var(--navy, #0b3d2e); transform: translateY(-1px); }
.mw-tab.active {
  background: #0b3d2e;
  color: #fff;
  border-color: #0b3d2e;
  box-shadow: 0 2px 10px rgba(11,61,46,.18);
}

.fire-badge, .gold-badge, .trust-badge {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  font-size: .56rem;
  font-weight: 800;
  letter-spacing: .06em;
  text-transform: uppercase;
  padding: 2px 8px;
  border-radius: 4px;
}
.fire-badge { background: linear-gradient(90deg, #c9a227, #e0b93a); color: #fff; }
.gold-badge { background: linear-gradient(90deg, #f5a623, #e8b800); color: #5a3200; }
.trust-badge { background: linear-gradient(90deg, #1a9e4a, #0d7a37); color: #fff; }

.cat-divider {
  display: flex;
  align-items: center;
  gap: 12px;
  margin: 32px 0 16px;
}
.cat-divider-line { flex: 1; height: 1px; background: var(--border, #e2e8f2); }
.cat-divider-label {
  display: flex;
  align-items: center;
  gap: 7px;
  font-size: .72rem;
  font-weight: 800;
  letter-spacing: .08em;
  text-transform: uppercase;
  padding: 4px 12px;
  border-radius: 6px;
}
.cdl-home   { background: #146048; color: #fff; }
.cdl-away   { background: #9c0f35; color: #fff; }
.cdl-never  { background: linear-gradient(90deg,#f5a623,#e8b800); color: #5a3200; }
.cdl-trust  { background: #1a9e4a; color: #fff; }

.mw-reason { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 8px; }
.mw-reason-tag {
  background: #f1f4fb;
  border: 1px solid #d4daf0;
  border-radius: 4px;
  font-size: .64rem;
  font-weight: 700;
  padding: 3px 9px;
}
.mw-reason-tag.fire-tag { background: var(--fire-light); border-color: #ffc5a0; color: #b33500; }
.mw-reason-tag.gold-tag { background: var(--gold-light); border-color: #f5d78a; color: #7a4e00; }

.win-prob-wrap { margin-top: 9px; }
.win-prob-label { font-size: .62rem; color: #8a94aa; font-weight: 600; margin-bottom: 4px; text-transform: uppercase; }
.win-prob-bar { height: 5px; background: #e8ebf4; border-radius: 99px; overflow: hidden; }
.win-prob-fill { height: 100%; border-radius: 99px; background: linear-gradient(90deg, #0b3d2e, #146048); }
.win-prob-fill.fire-fill { background: linear-gradient(90deg, #c9a227, #e0b93a); }

.pcard[data-cat="home"],
.pcard[data-cat="away"],
.pcard[data-cat="never"],
.pcard[data-cat="trust"] { border-left: 1px solid var(--border); }
.pcard.hidden { display: none; }

.hero-stats-strip {
  display: flex;
  gap: 20px;
  flex-wrap: wrap;
  margin-top: 20px;
}
.hss-val { font-size: 1.35rem; font-weight: 900; color: #fff; line-height: 1; }
.hss-lbl { font-size: .62rem; color: rgba(255,255,255,.55); font-weight: 600; text-transform: uppercase; margin-top: 3px; }

.sh-cat-pills { display: flex; gap: 6px; flex-wrap: wrap; }
.sh-cat-pill {
  font-size: .62rem;
  font-weight: 700;
  padding: 2px 9px;
  border-radius: 99px;
  text-transform: uppercase;
  text-decoration: none;
}
.scp-home,
.scp-away,
.scp-never,
.scp-trust { background: rgba(11,61,46,.08); color: #0b3d2e; }
</style>

<!-- Google Tag Manager: add your AllJackpotPredictions GTM container here -->
</head>
<body>

<?php include 'header.php'; ?>

<?php
// ========== Bao-style DB API (same stack as baopredictions) ==========
require_once __DIR__ . '/includes/api-curl.php';
$__ajp = ajp_fetch_fixtures('must-win-teams-today');
$fixtures = $__ajp['fixtures'];
$apiError = !$__ajp['ok'];
$apiErrorMessage = $__ajp['error'] ?? '';
// Helper functions
function mw_is_home_tip($prediction, $tip = '') {
    $t = strtoupper(trim((string) $tip));
    if ($t === '1' || $t === '1X' || $t === 'HOME') return true;
    if ($t === '2' || $t === 'X2' || $t === 'AWAY') return false;
    $p = (string) $prediction;
    if (stripos($p, 'Away') !== false) return false;
    return stripos($p, 'Home') !== false || stripos($p, 'Win') !== false;
}

function mw_fixture_side(array $f) {
    $pred = $f['prediction'] ?? [];
    $text = (string) ($pred['prediction'] ?? '');
    $tip = (string) ($pred['tip'] ?? '');
    return mw_is_home_tip($text, $tip) ? 'home' : 'away';
}

function mw_fixture_conf(array $f) {
    return (int) (($f['prediction']['confidence'] ?? $f['confidence'] ?? 75));
}

/** Primary badge category (exclusive). */
function getCategoryForPrediction($type, $prediction, $confidence = 75, $tip = '') {
    $conf = (int) $confidence;
    $isHome = mw_is_home_tip($prediction, $tip);
    if ($conf >= 79) return 'trust';
    if ($conf >= 78) return 'never';
    return $isHome ? 'home' : 'away';
}

/** Whether a fixture belongs on a category page / tab. */
function mw_in_category(array $f, string $cat) {
    if ($cat === 'all') return true;
    $side = mw_fixture_side($f);
    $conf = mw_fixture_conf($f);
    if ($cat === 'home') return $side === 'home';
    if ($cat === 'away') return $side === 'away';
    if ($cat === 'never') return $conf >= 78;
    if ($cat === 'trust') return $conf >= 79;
    return true;
}

function getCategoryLabel($cat) {
    $labels = ['home' => '🏠 Home Teams To Win', 'away' => '✈ Away Teams To Win', 'never' => '🛡 Teams That Will Never Lose', 'trust' => '✅ Most Trusted'];
    return $labels[$cat] ?? 'Must Win';
}

function getConfidenceLevel($conf) {
    if ($conf >= 80) return 'high';
    if ($conf >= 65) return 'medium';
    return 'low';
}

function getConfidencePips($conf) {
    $full = floor($conf / 20);
    $pips = '';
    for ($i = 0; $i < 5; $i++) {
        $pips .= ($i < $full) ? '<div class="cb on"></div>' : '<div class="cb"></div>';
    }
    return $pips;
}

function getLeagueClass($leagueName) {
    if (stripos($leagueName, 'Bundesliga') !== false) return 'bl';
    if (stripos($leagueName, 'Premier') !== false) return 'epl';
    if (stripos($leagueName, 'La Liga') !== false) return 'liga';
    if (stripos($leagueName, 'Serie A') !== false) return 'sa';
    if (stripos($leagueName, 'Champions League') !== false) return 'ucl';
    if (stripos($leagueName, 'UEFA Nations') !== false) return 'ucl';
    return 'ucl';
}

// Categorize fixtures (home/away by tip; never/trust by confidence bands)
$allFixtures = $fixtures;
$categorized = ['home' => [], 'away' => [], 'never' => [], 'trust' => []];
foreach ($allFixtures as $f) {
    if (mw_in_category($f, 'home')) $categorized['home'][] = $f;
    if (mw_in_category($f, 'away')) $categorized['away'][] = $f;
    if (mw_in_category($f, 'never')) $categorized['never'][] = $f;
    if (mw_in_category($f, 'trust')) $categorized['trust'][] = $f;
}

// Active category page: show only matching fixtures
if ($mwActiveCat !== 'all') {
    $fixtures = array_values(array_filter($allFixtures, static function ($f) use ($mwActiveCat) {
        return mw_in_category($f, $mwActiveCat);
    }));
}

$totalPicks = count($allFixtures);
$visiblePicks = count($fixtures);
$topTips = array_slice($mwActiveCat === 'all' ? $allFixtures : $fixtures, 0, 3);
?>

<!-- HERO -->
<section class="hero">
  <div class="hero-diagonal"></div>
  <div class="hero-inner">
    <div class="hero-left">
      <div>
        <div class="hero-kicker">
          <span class="hero-kicker-dot"></span>
          <span class="hero-kicker-text">⚡ <?php echo htmlspecialchars($mwKicker); ?> · <?php echo date('j F Y'); ?></span>
        </div>
        <h1 class="hero-h1">
          Must Win<br>
          Teams<br>
          <span class="h1-accent"><?php echo htmlspecialchars($mwH1Accent); ?></span>
          <span class="h1-sub"><?php echo (int) $visiblePicks; ?> picks in this view · <?php echo (int) $totalPicks; ?> must-win tips today</span>
        </h1>
        <p class="hero-desc">
          <strong>Must win teams today — selected where the data, context and competitive pressure all point one way.</strong>
          Our analysts identify home teams to win today, away teams to win today, teams that will never lose today
          and the most trusted teams to win today — backed by form tables, xG data, injury reports and league-position pressure.
        </p>
        <div class="hero-stats-strip">
          <div class="hss-item"><span class="hss-val"><?php echo $totalPicks; ?></span><span class="hss-lbl">Must Win Picks</span></div>
          <div class="hss-item"><span class="hss-val">84%</span><span class="hss-lbl">Monthly Win Rate</span></div>
          <div class="hss-item"><span class="hss-val"><?php echo count($categorized['home']); ?></span><span class="hss-lbl">Home Teams</span></div>
          <div class="hss-item"><span class="hss-val"><?php echo count($categorized['away']); ?></span><span class="hss-lbl">Away Teams</span></div>
        </div>
        <div class="hero-ctas" style="margin-top:20px">
          <a class="btn-primary" href="#tips">↓ See All Must Win Tips</a>
          <a class="btn-outline" href="/jackpot-picks-today">◈ Jackpot Picks</a>
          <a class="btn-outline" href="/accumulator-tips">Build an Acca</a>
        </div>
      </div>
    </div>

    <?php
      $hrTitle = 'Top Must Win Teams Today';
      $hrTips = ((!isset($apiError) || !$apiError) ? ($topTips ?? []) : []);
      $hrTipFn = static function ($tip) {
          $pred = $tip['prediction'] ?? [];
          $type = $pred['type'] ?? '1X2';
          $predText = $pred['prediction'] ?? 'Home Win';
          if ($type === '1X2' && strpos($predText, 'Home') !== false) {
              return 'Home Win';
          }
          if ($type === '1X2' && strpos($predText, 'Away') !== false) {
              return 'Away Win';
          }
          return $predText;
      };
      $hrEmpty = 'No must win tips available for today';
      $hrEmptySub = 'Check back later for predictions';
      include __DIR__ . '/includes/hero-picks-panel.php';
    ?>
  </div>
</section>

<!-- MARKETS STRIP -->
<div class="markets-strip">
  <div class="ms-inner">
    <a class="ms-tab" href="/predictions-today">All <span class="ms-n"><?php echo $totalPicks; ?></span></a>
    <a class="ms-tab fire active" href="/must-win-teams-today">⚡ Must Win <span class="ms-n"><?php echo $totalPicks; ?></span></a>
    <a class="ms-tab jackpot" href="/jackpot-picks-today">◈ Jackpot <span class="ms-n">3</span></a>
    <a class="ms-tab" href="/1x2-prediction">1X2 <span class="ms-n">-</span></a>
    <a class="ms-tab" href="/btts-tips">BTTS <span class="ms-n">-</span></a>
    <a class="ms-tab" href="/over-2-5-goals">Over 2.5 <span class="ms-n">-</span></a>
    <a class="ms-tab" href="/over-1-5-goals">Over 1.5 <span class="ms-n">-</span></a>
    <a class="ms-tab" href="/under-2-5-goals">Under 2.5 <span class="ms-n">-</span></a>
    <a class="ms-tab" href="/accumulator-tips">Accas <span class="ms-n">6</span></a>
    <a class="ms-tab" href="/correct-score-predictions">Correct Score <span class="ms-n">6</span></a>
    <a class="ms-tab" href="/double-chance-tips">Double Chance <span class="ms-n">8</span></a>
    <a class="ms-tab" href="/half-time-predictions">Half Time <span class="ms-n">7</span></a>
    <a class="ms-tab" href="/ht-ft-predictions">Half Time / Full Time <span class="ms-n">5</span></a>
  </div>
</div>

<!-- BREADCRUMB -->
<nav class="breadcrumb-wrap" aria-label="Breadcrumb">
  <div class="breadcrumb">
    <a href="/">Home</a>
    <span class="bc-sep">/</span>
    <a href="/predictions-today">Football Prediction Tips</a>
    <span class="bc-sep">/</span>
    <span class="bc-cur">Must Win Teams Today</span>
  </div>
</nav>

<!-- PAGE LAYOUT -->
<div class="page-wrap" id="tips">
  <main>

    <div class="section-head">
      <div class="sh-left">
        <span class="sh-num" style="display:flex;align-items:center;gap:6px"><span class="live-pulse"></span>Live</span>
        <h2 class="sh-title"><?php echo $mwActiveCat === 'all' ? 'Must Win Teams Today' : htmlspecialchars($mwH1Accent); ?></h2>
      </div>
      <div class="sh-divider"></div>
      <div class="sh-cat-pills">
        <a class="sh-cat-pill scp-home" href="/home-teams-to-win-today#tips">🏠 Home <?php echo count($categorized['home']); ?></a>
        <a class="sh-cat-pill scp-away" href="/away-teams-to-win-today#tips">✈ Away <?php echo count($categorized['away']); ?></a>
        <a class="sh-cat-pill scp-never" href="/teams-that-will-never-lose#tips">🛡 Never Lose <?php echo count($categorized['never']); ?></a>
        <a class="sh-cat-pill scp-trust" href="/most-trusted-teams-to-win#tips">✅ Most Trusted <?php echo count($categorized['trust']); ?></a>
      </div>
    </div>

    <!-- CATEGORY TABS -->
    <div class="mw-tabs" id="mwTabs">
      <a class="mw-tab mw-tab-all<?php echo $mwActiveCat === 'all' ? ' active' : ''; ?>" href="/must-win-teams-today#tips">All Teams <span class="ms-n"><?php echo (int) $totalPicks; ?></span></a>
      <a class="mw-tab mw-tab-home<?php echo $mwActiveCat === 'home' ? ' active' : ''; ?>" href="/home-teams-to-win-today#tips">🏠 Home Teams To Win Today <span class="ms-n"><?php echo count($categorized['home']); ?></span></a>
      <a class="mw-tab mw-tab-away<?php echo $mwActiveCat === 'away' ? ' active' : ''; ?>" href="/away-teams-to-win-today#tips">✈ Away Teams To Win Today <span class="ms-n"><?php echo count($categorized['away']); ?></span></a>
      <a class="mw-tab mw-tab-never<?php echo $mwActiveCat === 'never' ? ' active' : ''; ?>" href="/teams-that-will-never-lose#tips">🛡 Teams That Will Never Lose <span class="ms-n"><?php echo count($categorized['never']); ?></span></a>
      <a class="mw-tab mw-tab-trust<?php echo $mwActiveCat === 'trust' ? ' active' : ''; ?>" href="/most-trusted-teams-to-win#tips">✅ Most Trusted Teams To Win <span class="ms-n"><?php echo count($categorized['trust']); ?></span></a>
    </div>

    <!-- FILTER BAR -->
    <div class="filter-bar">
      <div class="filter-left">
        <span class="filter-label">Filter:</span>
        <select class="fsel" id="fsel-league">
          <option value="">All Leagues</option>
          <?php
          $leagues = array_unique(array_map(function($f) { return $f['league']['name'] ?? 'Other'; }, $fixtures));
          foreach($leagues as $league):
          ?>
          <option value="<?php echo htmlspecialchars(strtolower($league)); ?>"><?php echo htmlspecialchars($league); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="filter-right">
        <span class="conf-pill hi">● High</span>
        <span class="conf-pill md">● Medium</span>
        <span class="conf-pill lo">● Low</span>
      </div>
    </div>

    <?php if ($apiError): ?>
      <div class="empty-state" style="text-align: center; padding: 3rem 2rem; background: white; border-radius: 24px;">
        <div class="empty-icon">⚠️</div>
        <div class="empty-title">Unable to Load Must Win Tips</div>
        <div class="empty-desc"><?php echo htmlspecialchars($apiErrorMessage); ?></div>
      </div>
    <?php elseif ($visiblePicks === 0): ?>
      <div class="empty-state" style="text-align: center; padding: 3rem 2rem; background: white; border-radius: 24px;">
        <div class="empty-icon">📋</div>
        <div class="empty-title"><?php echo $mwActiveCat === 'all' ? 'No Must Win Tips Available Today' : 'No tips in this category right now'; ?></div>
        <div class="empty-desc"><?php echo $mwActiveCat === 'all' ? ('There are no must win predictions for ' . date('j F Y') . '.') : 'Try All Teams or another Must Win category.'; ?></div>
        <?php if ($mwActiveCat !== 'all'): ?>
          <p style="margin-top:1rem"><a href="/must-win-teams-today#tips" style="color:var(--gold);font-weight:700">← All Must Win Teams</a></p>
        <?php endif; ?>
      </div>
    <?php else: ?>
      <div class="pcards" id="pcards">
        <?php foreach($fixtures as $fixture):
            $pred = $fixture['prediction'] ?? [];
            $type = $pred['type'] ?? '1X2';
            $predText = $pred['prediction'] ?? 'Home Win';
            $confidence = $pred['confidence'] ?? 75;
            $odds = $pred['odds'] ?? 1.55;
            $homeTeam = htmlspecialchars($fixture['home_team']['name'] ?? 'Unknown');
            $awayTeam = htmlspecialchars($fixture['away_team']['name'] ?? 'Unknown');
            $leagueName = $fixture['league']['name'] ?? 'Football';
            $matchTime = isset($fixture['date']) ? date('H:i', strtotime($fixture['date'])) : 'TBD';
            $leagueClass = getLeagueClass($leagueName);
            $pipsHtml = getConfidencePips($confidence);
            $confLevel = getConfidenceLevel($confidence);
            $cat = getCategoryForPrediction($type, $predText, $confidence, $pred['tip'] ?? '');
            $side = mw_fixture_side($fixture);
            
            $shortPred = '';
            if ($type === '1X2') $shortPred = strpos($predText, 'Home') !== false ? 'Home Win' : (strpos($predText, 'Away') !== false ? 'Away Win' : 'Draw');
            elseif ($type === 'BTTS') $shortPred = strpos($predText, 'No') !== false ? 'No Goal' : 'Both Score';
            else $shortPred = $predText;
            
            $reason = $pred['reason'] ?? ($type === '1X2' ? "Must win context for " . ($shortPred) : "Strong probability based on form and data");
            $badgeClass = ($cat === 'home') ? 'fire-badge' : (($cat === 'away') ? 'fire-badge' : (($cat === 'never') ? 'gold-badge' : 'trust-badge'));
            $badgeText = ($cat === 'home') ? '⚡ Must Win Home' : (($cat === 'away') ? '⚡ Must Win Away' : (($cat === 'never') ? '🛡 Will Not Lose' : '✅ Most Trusted'));
        ?>
        <article class="pcard" data-league="<?php echo htmlspecialchars(strtolower($leagueName)); ?>" data-cat="<?php echo $cat; ?>" data-side="<?php echo $side; ?>" data-conf-n="<?php echo (int) $confidence; ?>" data-conf="<?php echo $confLevel; ?>">
          <div class="pc-row">
            <div class="pc-league-col lc-<?php echo $leagueClass; ?>">
              <span class="league-dot ld-<?php echo $leagueClass; ?>"></span>
            </div>
            <div class="pc-body">
              <div class="pc-info">
                <div class="pc-meta"><?php echo htmlspecialchars($leagueName); ?> · <?php echo $matchTime; ?></div>
                <div class="pc-match"><?php echo $homeTeam . " vs " . $awayTeam; ?></div>
                <div class="pc-live-wrap">
                  <span class="<?php echo $badgeClass; ?>"><?php echo $badgeText; ?></span>
                </div>
                <div class="mw-reason">
                  <span class="mw-reason-tag <?php echo ($cat === 'home' || $cat === 'away') ? 'fire-tag' : (($cat === 'never') ? 'gold-tag' : ''); ?>"><?php echo htmlspecialchars($reason); ?></span>
                </div>
              </div>
              <div class="pc-pred-col">
                <div class="pc-pred-tag"><?php echo $shortPred; ?></div>
                <div class="pc-market">Must Win · <?php echo ucfirst($cat); ?></div>
              </div>
              <div class="pc-odds-col">
                <div class="odds-tag"><?php echo number_format((float)$odds, 2); ?></div>
                <div class="conf-bar">
                  <div class="cb-pips"><?php echo $pipsHtml; ?></div>
                  <span class="cb-pct"><?php echo $confidence; ?>%</span>
                </div>
              </div>
            </div>
            <div class="pc-toggle"><button class="pc-tog-btn">▾</button></div>
          </div>
          <div class="pc-expand">
            <div class="pc-expand-inner">
              <div>
                <div class="pc-stats">
                  <div class="ps-box"><div class="ps-val"><?php echo $shortPred; ?></div><div class="ps-lbl">Prediction</div></div>
                  <div class="ps-box"><div class="ps-val"><?php echo $confidence; ?>%</div><div class="ps-lbl">Confidence</div></div>
                  <?php if($pred['details']['home_percent'] ?? false): ?>
                  <div class="ps-box"><div class="ps-val">H:<?php echo $pred['details']['home_percent']; ?>%</div><div class="ps-lbl">Home Win</div></div>
                  <div class="ps-box"><div class="ps-val">D:<?php echo $pred['details']['draw_percent']; ?>%</div><div class="ps-lbl">Draw</div></div>
                  <div class="ps-box"><div class="ps-val">A:<?php echo $pred['details']['away_percent']; ?>%</div><div class="ps-lbl">Away Win</div></div>
                  <?php endif; ?>
                </div>
                <p class="pc-note"><?php echo htmlspecialchars($reason); ?></p>
                <div class="win-prob-wrap">
                  <div class="win-prob-label">Must Win Probability</div>
                  <div class="win-prob-bar"><div class="win-prob-fill <?php echo ($cat === 'home' || $cat === 'away') ? 'fire-fill' : ''; ?>" style="width:<?php echo $confidence; ?>%"></div></div>
                </div>
              </div>
              <div class="pc-form-panel">
                <div class="form-lbl">Analysis</div>
                <div class="form-dots"><span class="fd">🎯 High confidence must win pick</span></div>
                <a class="acca-btn" href="/accumulator-tips" style="margin-top:11px">+ Add to Acca</a>
              </div>
            </div>
          </div>
        </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

  </main>

  <?php include 'sidebar.php'; ?>
</div>

<!-- SHARE -->
<div class="share-wrap">
  <span class="share-lbl">Share:</span>
  <a class="share-btn" href="https://twitter.com/intent/tweet?text=Must+win+teams+today+%E2%80%94+home+%26+away+tips&url=https://www.alljackpotpredictions.com/must-win-teams-today" target="_blank" rel="noopener">X / Twitter</a>
  <a class="share-btn" href="https://www.facebook.com/sharer/sharer.php?u=https://www.alljackpotpredictions.com/must-win-teams-today" target="_blank" rel="noopener">Facebook</a>
  <a class="share-btn" href="https://wa.me/?text=Must+win+teams+today+https://www.alljackpotpredictions.com/must-win-teams-today" target="_blank" rel="noopener">WhatsApp</a>
  <a class="share-btn" href="https://t.me/share/url?url=https://www.alljackpotpredictions.com/must-win-teams-today" target="_blank" rel="noopener">Telegram</a>
</div>

<!-- ═══ SEO ARTICLE SECTION ══════════════════════════════ -->
<section class="article-section" style="margin-bottom:0">
  <div class="article-card">
    <div class="art-header">
      <div class="art-header-inner">
        <div class="art-kicker">
          <div class="art-kicker-line"></div>
          <span class="art-kicker-text">Editorial · AllJackpotPredictions Desk</span>
        </div>
        <h2 class="art-h2">Must-Win Teams Today — Motivation Edges for Slip Anchors</h2>
        <div class="art-meta">
          <span>AllJackpotPredictions Analyst Team</span>
          <span class="art-sep">·</span>
          <time datetime="2026-03-21">21 March 2026</time>
          <span class="art-sep">·</span>
          <span>Reviewed by Senior Analyst</span>
          <span class="art-sep">·</span>
          <span>12 min read</span>
          <span class="art-sep">·</span>
          <span class="art-tag">Must Win</span>
          <span class="art-tag">Home Teams</span>
          <span class="art-tag">Away Teams</span>
          <span class="art-tag warn">⚠ 18+ | Gamble Responsibly</span>
        </div>
      </div>
    </div>

    <div class="art-body">
      <div class="art-col">
        <h3>Motivation edges we trust enough to publish</h3>
        <p><strong>Must win teams today</strong> are football sides identified by our analysts as having an overwhelming combination of competitive incentive, squad quality and contextual pressure to secure all three points. Unlike simple favourites, must win selections are teams where the match situation — title race, relegation fight, European qualification, cup elimination — makes dropping points genuinely unacceptable. Our must win predictions carry an <strong>84% win rate this month</strong>, our strongest category.</p>

        <h4>What Makes a Side a True Must Win Team Today?</h4>
        <ul>
          <li><strong>League position pressure</strong> — title contenders, relegation fighters and top-four chasers all have heightened incentive</li>
          <li><strong>Home ground advantage</strong> — home teams to win today are backed by crowd, pitch familiarity and no travel fatigue</li>
          <li><strong>Opponent weakness</strong> — a fragile away record, injuries or low morale in the opposition amplifies the must-win case</li>
          <li><strong>Knockout urgency</strong> — European knockout legs create instant must-win pressure for the tie-chasing side</li>
          <li><strong>xG modelling</strong> — expected goals data helps identify sides consistently outperforming (or underperforming) their results</li>
          <li><strong>Confirmed injury list</strong> — key absentees in the opposition dramatically increase must-win probability</li>
        </ul>

        <h3>Home Teams To Win Today</h3>
        <p>Across Europe's top five leagues, home teams win approximately <strong>46% of all matches</strong>. But today's selected <strong>home teams to win today</strong> are not average home sides — they are teams with exceptional home records, motivated by their league situation and facing opponents who are weak on the road. When a home team has a specific contextual incentive (relegation survival, title chase, European qualification), their win rate against struggling away sides exceeds 70% historically.</p>
        <p>Our analysts weigh each home team's last 10 home results separately from their overall form, apply a home-advantage xG uplift and cross-reference the opposition's specific away record. Only sides meeting all three threshold criteria appear in today's home teams to win list.</p>

        <p><em>All must win team predictions on AllJackpotPredictions are for informational and entertainment purposes only and do not constitute financial or betting advice. Please read our <a href="/responsible-gambling">responsible gambling guidelines</a> before placing any bet.</em></p>
      </div>

      <div class="art-col">
        <h3>Away Teams To Win Today</h3>
        <p><strong>Away teams to win today</strong> represent a smaller subset of our must win selections — and rightly so. Winning away from home is significantly harder across all levels of football. Our away win selections are reserved for sides that meet at least three of the following criteria: a strong recent away record (3+ wins from last 5 away matches), a dominant quality gap vs the host, clear motivational incentive and a host side in poor home form or with key absentees.</p>
        <p>Today's away teams to win today have been picked on exactly this basis — each one travels with a genuine case for all three points, not simply because they are the nominally stronger club.</p>

        <h3>Teams That Will Never Lose Today</h3>
        <p>Our <strong>"teams that will never lose today"</strong> section highlights sides where a defeat is not just unlikely — it is almost inconceivable based on available data. These selections combine an extended unbeaten run (typically 10+ matches at home or away), an opponent in dramatically poor form or mid-table/relegated with nothing at stake, and a host side operating under maximum motivational pressure. We rate these selections on a no-loss probability scale rather than a simple win percentage.</p>
        <p>No tip can ever truly guarantee a side will never lose — football's unpredictability is its nature. What "teams that will never lose today" communicates is that based on every measurable factor, a defeat for this side would be a genuine statistical outlier.</p>

        <h3>Most Trusted Teams To Win Today</h3>
        <p>The <strong>most trusted teams to win today</strong> are our overall editorial picks where data confidence, context and value combine. A team can be heavily favoured and still not make this list if the odds are too compressed to offer value. Equally, a team at slightly longer odds can qualify if their underlying data — xG, form, head-to-head and squad depth — makes the win highly probable and the price attractive. These are the picks our analysts personally back.</p>

        <h4>Building an Accumulator With Must Win Teams</h4>
        <ul>
          <li>Prioritise the highest-confidence home win must-win teams as your accumulator anchors</li>
          <li>Away win must-win picks add value but carry more variance — use one per acca at most</li>
          <li>"Teams that will never lose" can serve as near-certainty legs at short price</li>
          <li>Cap your accumulator at 4–5 legs — adding more significantly reduces your hit rate</li>
          <li>Never stack all picks from the same league — diversify across competitions</li>
        </ul>
      </div>

      <div class="art-col art-full">
        <h3>Frequently Asked Questions</h3>
        <div class="faq-list">
          <input type="checkbox" id="fq1" class="faq-ck">
          <div class="faq-item">
            <label for="fq1" class="faq-q">What are must win teams today in football betting? <span class="faq-icon">+</span></label>
            <div class="faq-a"><p>Must win teams today are sides identified by our analysts as having an overwhelming competitive, tactical and situational incentive to secure all three points — driven by league position, relegation pressure, title race urgency or knockout stage elimination. These are not simply favourites; they are teams where the context makes anything other than a win unacceptable.</p></div>
          </div>
          <input type="checkbox" id="fq2" class="faq-ck">
          <div class="faq-item">
            <label for="fq2" class="faq-q">What is the difference between home teams to win today and away teams to win today? <span class="faq-icon">+</span></label>
            <div class="faq-a"><p>Home teams to win today are sides playing on their own ground with the added advantage of home support, familiar conditions and no travel fatigue. Away teams to win today are sides strong enough — or sufficiently motivated — to overcome those disadvantages and win on the road. Both categories are selected independently based on form data, squad fitness and contextual incentives.</p></div>
          </div>
          <input type="checkbox" id="fq3" class="faq-ck">
          <div class="faq-item">
            <label for="fq3" class="faq-q">Which teams will never lose today? <span class="faq-icon">+</span></label>
            <div class="faq-a"><p>No team in football is guaranteed never to lose. However, AllJackpotPredictions' "Teams That Will Never Lose" section highlights sides with a combination of exceptional current form, dominant squad quality and must-win pressure that makes a defeat extremely unlikely based on all available data. These are our highest no-loss probability selections of the day.</p></div>
          </div>
          <input type="checkbox" id="fq4" class="faq-ck">
          <div class="faq-item">
            <label for="fq4" class="faq-q">How does AllJackpotPredictions select the most trusted teams to win today? <span class="faq-icon">+</span></label>
            <div class="faq-a"><p>The most trusted teams to win today are selected using a multi-factor analysis: recent form over the last 5–10 matches (weighted by home/away), head-to-head records in the exact fixture, confirmed injury and suspension lists, xG (expected goals) modelling, league position and motivational context. Only teams scoring highly across all factors qualify.</p></div>
          </div>
          <input type="checkbox" id="fq5" class="faq-ck">
          <div class="faq-item">
            <label for="fq5" class="faq-q">Are must win team predictions guaranteed? <span class="faq-icon">+</span></label>
            <div class="faq-a"><p>No. Must win team predictions are informed analytical opinions published for entertainment purposes only. Football is unpredictable and no tip can guarantee a profit. Always gamble responsibly, set a fixed budget before betting and never stake more than you can comfortably afford to lose.</p></div>
          </div>
        </div>
      </div>

      <div class="art-col art-full">
        <div class="eeat-box">
          <div class="eeat-av">AJ</div>
          <div class="eeat-body">
            <div class="eeat-name">AllJackpotPredictions Analyst Team</div>
            <span class="eeat-role">Professional Football Analysts · Must Win Team Identification Methodology · Senior Review Process</span>
            <p>AllJackpotPredictions' must win team predictions are produced by a team with over a decade of combined experience in football statistics, betting market analysis and tactical scouting. Every must win selection — whether a home team to win today, an away team to win today, a team that will never lose or one of our most trusted teams to win today — goes through a two-stage process: raw data research followed by editorial sign-off from a senior analyst. We never publish a must win tip based on narrative or speculation alone.</p>
            <div class="eeat-creds">
              <span>📊 Multi-factor must win methodology</span>
              <span>📋 Published &amp; verified track record</span>
              <span>🏆 10+ years combined experience</span>
              <span>🔍 Daily squad &amp; injury monitoring</span>
              <span>✅ Senior analyst sign-off on every pick</span>
            </div>
            <a class="eeat-link" href="/about">Meet the analyst team →</a>
          </div>
        </div>
      </div>

      <div class="art-col art-full">
        <div class="rg-box">
          <div class="rg-top">
            <span class="rg-icon">🛡</span>
            <div>
              <div class="rg-title">Responsible Gambling — Please Read Before Betting</div>
              <p class="rg-sub">AllJackpotPredictions is committed to promoting safe, responsible gambling. All must win team predictions are for entertainment only and are not financial advice.</p>
            </div>
          </div>
          <div class="rg-cols">
            <div class="rg-col">
              <h4>⚠ Important Disclaimer</h4>
              <p>All must win team predictions on AllJackpotPredictions are published for <strong>entertainment purposes only</strong>. They do not constitute financial, investment or betting advice. Football betting involves real financial risk. Past win rates do <strong>not</strong> guarantee future results. Even the most trusted teams to win today can and do lose.</p>
              <h4>🔞 Age Restriction</h4>
              <p>Gambling is only permitted for persons aged <strong>18 or over</strong> (or the legal age in your jurisdiction). By accessing AllJackpotPredictions you confirm you meet the legal gambling age in your country.</p>
            </div>
            <div class="rg-col">
              <h4>✅ Safer Gambling Practices</h4>
              <ul>
                <li>Set a fixed weekly or monthly budget before you start</li>
                <li>Never bet more than you can comfortably afford to lose</li>
                <li>Never chase losses by increasing your stake</li>
                <li>Take regular breaks and keep betting in perspective</li>
                <li>Use deposit limits and cooling-off tools at your bookmaker</li>
                <li>Treat must win team tips as entertainment — not a source of income</li>
              </ul>
              <h4>🆘 Free Help &amp; Support</h4>
              <div class="rg-links-grid">
                <a href="https://www.begambleaware.org" target="_blank" rel="noopener" class="rg-link"><strong>BeGambleAware</strong><span>begambleaware.org</span></a>
                <a href="https://www.gamcare.org.uk" target="_blank" rel="noopener" class="rg-link"><strong>GamCare</strong><span>gamcare.org.uk</span></a>
                <a href="https://www.gamstop.co.uk" target="_blank" rel="noopener" class="rg-link"><strong>GamStop</strong><span>gamstop.co.uk</span></a>
                <a href="https://www.gamblersanonymous.org.uk" target="_blank" rel="noopener" class="rg-link"><strong>Gamblers Anonymous</strong><span>gamblersanonymous.org.uk</span></a>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<?php include 'footer.php'; ?>

<button class="btt" id="btt" aria-label="Back to top" onclick="window.scrollTo({top:0,behavior:'smooth'})">↑</button>

<script src="main.js"></script>
<script>
// League filter (category pages are separate URLs)
const leagueSelect = document.getElementById('fsel-league');
if (leagueSelect) {
  leagueSelect.addEventListener('change', function() {
    const val = this.value;
    document.querySelectorAll('.pcard').forEach(card => {
      if (!val || card.dataset.league === val) {
        card.classList.remove('hidden');
      } else {
        card.classList.add('hidden');
      }
    });
  });
}

// Toggle expand/collapse
document.querySelectorAll('.pc-tog-btn').forEach(btn => {
  btn.addEventListener('click', function() {
    const expand = this.closest('.pcard').querySelector('.pc-expand');
    expand.classList.toggle('show');
    this.textContent = expand.classList.contains('show') ? '▴' : '▾';
  });
});
</script>
</body>
</html>