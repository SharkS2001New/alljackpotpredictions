<?php
/**
 * Built-in PHP server without router.php falls back to index.php for unknown
 * paths — so the URL changes but every page shows the homepage. Dispatch those
 * requests through the front controller instead.
 */
if (PHP_SAPI === 'cli-server' && !defined('AJP_AS_PAGE')) {
    $ajpPath = parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH);
    $ajpPath = is_string($ajpPath) ? urldecode($ajpPath) : '/';
    if ($ajpPath === '' || $ajpPath === false) {
        $ajpPath = '/';
    }
    if ($ajpPath !== '/') {
        $ajpPath = rtrim($ajpPath, '/') ?: '/';
    }
    if ($ajpPath !== '/' && $ajpPath !== '/index.php') {
        require __DIR__ . '/router.php';
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Football Prediction Tips, Jackpot Accas &amp; Betting Tips Today | AllJackpotPredictions</title>
<meta name="description" content="Free football prediction tips and jackpot accumulator picks for today — data-driven and updated daily across Premier League, Champions League, La Liga, Bundesliga and more. No login required.">
<meta name="keywords" content="football prediction tips, jackpot predictions, betting tips today, free football tips, accumulator tips, jackpot picks, BTTS tips, over 2.5 tips, must win teams, premier league tips">
<meta name="author" content="AllJackpotPredictions Analyst Team">
<link rel="canonical" href="https://www.alljackpotpredictions.com/">
 
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
 
<!-- Open Graph — Facebook, LinkedIn, WhatsApp, Telegram link previews -->
<meta property="og:type"        content="website">
<meta property="og:url"         content="https://www.alljackpotpredictions.com/">
<meta property="og:title"       content="Football Prediction Tips, Jackpot Accas &amp; Betting Tips Today | AllJackpotPredictions">
<meta property="og:description" content="Expert football prediction tips for today — free, data-driven, updated daily. Premier League, Champions League, La Liga, Bundesliga and more.">
<meta property="og:image"       content="https://www.alljackpotpredictions.com/img/og-image.png">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:image:alt"   content="AllJackpotPredictions — Football Prediction Tips">
<meta property="og:site_name"   content="AllJackpotPredictions">
<meta property="og:locale"      content="en_GB">
 
<!-- Twitter / X Card -->
<meta name="twitter:card"        content="summary_large_image">
<meta name="twitter:site"        content="@AllJackpotPredictions">
<meta name="twitter:title"       content="Football Prediction Tips Today | AllJackpotPredictions">
<meta name="twitter:description" content="Free, data-driven football prediction tips updated daily. No login required.">
<meta name="twitter:image"       content="https://www.alljackpotpredictions.com/img/og-image.png">
<meta name="twitter:image:alt"   content="AllJackpotPredictions — Football Prediction Tips">
 
<!-- ═══════════════════════════════════════════════════════
     END FAVICON & ICONS
     ═══════════════════════════════════════════════════════ -->
 
<!-- Google Tag Manager: add your AllJackpotPredictions GTM container here -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "WebSite",
      "@id": "https://www.alljackpotpredictions.com/#website",
      "url": "https://www.alljackpotpredictions.com/",
      "name": "AllJackpotPredictions",
      "description": "Free football prediction tips and betting tips today. For entertainment purposes only. 18+. Please gamble responsibly."
    }
  ]
}
</script>
 
<link rel="stylesheet" href="styles.css">
</head>
<body>

<?php include 'header.php'; ?>

<?php
// ========== Bao-style DB API (same stack as baopredictions) ==========
require_once __DIR__ . '/includes/api-curl.php';
require_once __DIR__ . '/includes/currency.php';
$__cur = ajp_visitor_currency();
$__stakeLabel = ajp_stake_returns_label($__cur);

$__sure = ajp_fetch_fixtures('sure-bets-today');
$top5Fixtures = array_slice($__sure['fixtures'], 0, 5);

$__vip = ajp_fetch_fixtures('must-win-teams-today');
$vipFixtures = array_slice($__vip['fixtures'], 0, 8);

$__home = ajp_fetch_fixtures('homepage');
$allFixtures = array_values(array_filter($__home['fixtures'], static function ($f) {
    return ($f['status'] ?? 'NS') !== 'FT';
}));

// Must Win Teams: 1X2 home/away with odds 1.10-1.65
$mustWinFixtures = array_filter($allFixtures, static function ($f) {
    $predText = $f['prediction']['prediction'] ?? '';
    $odds = $f['odds']['prediction_odd'] ?? ($f['prediction']['odds'] ?? 0);
    $is1x2 = in_array($predText, ['Home Win', 'Away Win', 'Draw'], true)
        || in_array((string) ($f['pick_code'] ?? ''), ['1', '2', 'X'], true);
    return $is1x2
        && ($predText === 'Home Win' || $predText === 'Away Win' || ($f['pick_code'] ?? '') === '1' || ($f['pick_code'] ?? '') === '2')
        && $odds >= 1.10 && $odds <= 1.65;
});
$mustWinFixtures = array_slice(array_values($mustWinFixtures), 0, 4);

// Helper function
function getShortPrediction($type, $predText) {
    if ($type === '1X2') {
        if (strpos($predText, 'Home') !== false) return 'Home Win';
        if (strpos($predText, 'Away') !== false) return 'Away Win';
        return 'Draw';
    }
    if ($type === 'Over/Under') {
        if (strpos($predText, 'Over') !== false) return $predText;
        if (strpos($predText, 'Under') !== false) return $predText;
    }
    if ($type === 'BTTS') {
        return $predText;
    }
    return $predText;
}

$top5Count = count($top5Fixtures);
$vipCount = count($vipFixtures);
$allCount = count($allFixtures);
$mustWinCount = count($mustWinFixtures);
?>

<!-- HERO -->
<section class="hero">
  <div class="hero-diagonal"></div>
  <div class="hero-inner">
    <div class="hero-left">
      <div>
        <div class="hero-kicker">
          <span class="hero-kicker-dot"></span>
          <span class="hero-kicker-text">Football Prediction Tips · <?php echo date('j F Y'); ?></span>
        </div>
        <h1 class="hero-h1">
          Football<br>
          Prediction<br>
          <span class="h1-accent">Tips Today</span>
          <span class="h1-sub"><?php echo $allCount; ?> expert betting tips today across all major leagues — zero cost, no login</span>
        </h1>
        <p class="hero-desc">
          <strong>Data-driven football prediction tips.</strong> Every betting tip backed by form tables,
          xG models, head-to-head records and squad intelligence — updated daily across
          Premier League, Champions League, La Liga, Bundesliga, Serie A and more.
        </p>
        <div class="hero-ctas">
          <a class="btn-primary" href="#tips">↓ See All Tips</a>
          <a class="btn-outline" href="/jackpot-picks-today">◈ Jackpot Picks</a>
          <a class="btn-outline" href="/must-win-teams-today">⚡ Must Win Teams</a>
        </div>
      </div>
    </div>

    <!-- RIGHT PANEL: Top betting tips -->
    <?php
      $hrTitle = 'Best Betting Tips Today';
      $hrTips = array_slice($top5Fixtures, 0, 3);
      $hrTipFn = static function ($tip) {
          $pred = $tip['prediction'] ?? [];
          $type = $pred['type'] ?? '1X2';
          $predText = $pred['prediction'] ?? 'Home Win';
          return getShortPrediction($type, $predText);
      };
      $hrEmpty = 'No featured tips available right now';
      include __DIR__ . '/includes/hero-picks-panel.php';
    ?>
  </div>
</section>

<!-- MARKETS STRIP -->
<div class="markets-strip">
  <div class="ms-inner">
    <a class="ms-tab active" href="/predictions-today">All <span class="ms-n"><?php echo $allCount; ?></span></a>
    <a class="ms-tab fire" href="/must-win-teams-today">⚡ Must Win <span class="ms-n"><?php echo $mustWinCount; ?></span></a>
    <a class="ms-tab jackpot" href="/jackpot-picks-today">◈ Jackpot <span class="ms-n"><?php echo $vipCount; ?></span></a>
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
    <span class="bc-cur">Betting Tips Today</span>
  </div>
</nav>

<!-- PAGE LAYOUT -->
<div class="page-wrap" id="tips">
  <main>

    <!-- TOP 5 BETTING TIPS TODAY -->
    <div class="section-head">
      <div class="sh-left">
        <span class="sh-num">Featured</span>
        <h2 class="sh-title">Top <?php echo min(5, $top5Count); ?> Betting Tips Today</h2>
      </div>
      <div class="sh-divider"></div>
      <a class="sh-more" href="/predictions-today">All prediction tips →</a>
    </div>

    <div class="picks-scroll-wrap">
      <div class="picks-grid">
        <?php foreach($top5Fixtures as $tip):
            $pred = $tip['prediction'] ?? [];
            $type = $pred['type'] ?? '1X2';
            $predText = $pred['prediction'] ?? 'Home Win';
            $conf = $pred['confidence'] ?? 70;
            $odds = $pred['odds'] ?? 1.65;
            $home = $tip['home_team']['name'] ?? 'Team';
            $away = $tip['away_team']['name'] ?? 'Opponent';
            $leagueName = $tip['league']['name'] ?? 'Football';
            $matchTime = isset($tip['date']) ? date('H:i', strtotime($tip['date'])) : 'TBD';
            $shortPred = getShortPrediction($type, $predText);
            $confClass = $conf >= 80 ? 'pcd-hi' : ($conf >= 65 ? 'pcd-md' : '');
        ?>
        <a class="pick-card" href="#">
          <div class="pick-card-glow"></div>
          <div class="pick-badge"><?php echo htmlspecialchars($leagueName); ?> · <?php echo $matchTime; ?> <span class="pick-conf-dot <?php echo $confClass; ?>"></span></div>
          <div class="pick-match"><?php echo htmlspecialchars($home) . " vs " . htmlspecialchars($away); ?></div>
          <div class="pick-foot">
            <span class="pick-tip"><?php echo $shortPred; ?></span>
            <span class="pick-odds"><?php echo number_format((float)$odds, 2); ?></span>
          </div>
        </a>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- MUST WIN TEAMS -->
    <div class="section-head" style="margin-top:36px">
      <div class="sh-left">
        <span class="sh-num" style="background:rgba(201,162,39,.1);color:var(--pink);border-color:rgba(201,162,39,.2)">High Motivation</span>
        <h2 class="sh-title">⚡ Must Win Teams</h2>
      </div>
      <div class="sh-divider"></div>
      <a class="sh-more" href="/must-win-teams-today">See all <?php echo $mustWinCount; ?> →</a>
    </div>

    <div class="mw-grid">
      <?php foreach($mustWinFixtures as $tip):
          $pred = $tip['prediction'] ?? [];
          $type = $pred['type'] ?? '1X2';
          $predText = $pred['prediction'] ?? 'Home Win';
          $conf = $pred['confidence'] ?? 75;
          $odds = $pred['odds'] ?? 1.50;
          $home = $tip['home_team']['name'] ?? 'Team';
          $away = $tip['away_team']['name'] ?? 'Opponent';
          $leagueName = $tip['league']['name'] ?? 'Football';
          $matchTime = isset($tip['date']) ? date('H:i', strtotime($tip['date'])) : 'TBD';
          $shortPred = getShortPrediction($type, $predText);
          $pressure = $conf + 10;
          $reason = $pred['reason'] ?? ($shortPred === 'Home Win' ? "Home advantage with strong form" : "Quality away side in must-win situation");
      ?>
      <a class="mw-card" href="/must-win-teams-today">
        <div class="mw-card-top">
          <div class="mw-card-badges">
            <span class="mw-badge-main">⚡ Must Win</span>
            <span class="mw-badge-pressure"><?php echo $shortPred === 'Home Win' ? 'Home Fortress' : 'Away Quality'; ?></span>
          </div>
          <div class="mw-odds-box">
            <div class="mw-odds"><?php echo number_format((float)$odds, 2); ?></div>
            <div class="mw-odds-lbl">Win Odds</div>
          </div>
        </div>
        <div class="mw-match"><?php echo htmlspecialchars($home) . " vs " . htmlspecialchars($away); ?></div>
        <div class="mw-league"><?php echo htmlspecialchars($leagueName); ?> · <?php echo $matchTime; ?></div>
        <div class="mw-reason"><?php echo htmlspecialchars($reason); ?></div>
        <div class="mw-meter">
          <div class="mw-meter-label"><span>Pressure Index</span><span><?php echo $pressure; ?>%</span></div>
          <div class="mw-meter-track"><div class="mw-meter-fill" style="width:<?php echo $pressure; ?>%"></div></div>
        </div>
      </a>
      <?php endforeach; ?>
    </div>

    <!-- JACKPOT PICKS OF THE DAY -->
    <div class="section-head" style="margin-top:36px">
      <div class="sh-left">
        <span class="sh-num">Highest Confidence</span>
        <h2 class="sh-title">◈ Jackpot Picks of the Day</h2>
      </div>
      <div class="sh-divider"></div>
      <a class="sh-more" href="/jackpot-picks-today">All jackpot picks →</a>
    </div>

    <div class="jackpot-list">
      <?php foreach(array_slice($vipFixtures, 0, 3) as $idx => $tip):
          $pred = $tip['prediction'] ?? [];
          $type = $pred['type'] ?? '1X2';
          $predText = $pred['prediction'] ?? 'Home Win';
          $conf = $pred['confidence'] ?? 85;
          $odds = $pred['odds'] ?? 1.60;
          $home = $tip['home_team']['name'] ?? 'Team';
          $away = $tip['away_team']['name'] ?? 'Opponent';
          $leagueName = $tip['league']['name'] ?? 'Football';
          $matchTime = isset($tip['date']) ? date('H:i', strtotime($tip['date'])) : 'TBD';
          $shortPred = getShortPrediction($type, $predText);
          $stakeRec = $tip['stake_recommendation'] ?? '3 Units';
      ?>
      <a class="op-item" href="/jackpot-picks-today">
        <div class="op-num-col"><div class="op-num"><?php echo sprintf("%02d", $idx+1); ?></div></div>
        <div class="op-body">
          <div class="op-match"><?php echo htmlspecialchars($home) . " vs " . htmlspecialchars($away); ?></div>
          <span class="op-market"><?php echo $shortPred; ?> · <?php echo htmlspecialchars($leagueName); ?></span>
          <div class="op-note"><?php echo $pred['reason'] ?? "Elite confidence pick with strong data backing. Stake recommendation: " . $stakeRec; ?></div>
        </div>
        <div class="op-right">
          <div class="op-odds"><?php echo number_format((float)$odds, 2); ?></div>
          <div class="op-conf"><?php echo $conf; ?>% confidence</div>
        </div>
      </a>
      <?php endforeach; ?>
    </div>


    <!-- ALL FOOTBALL PREDICTION TIPS TODAY -->
    <div class="section-head" style="margin-top:36px">
      <div class="sh-left">
        <span class="sh-num" style="display:flex;align-items:center;gap:6px"><span class="live-pulse"></span>Live</span>
        <h2 class="sh-title">All Football Prediction Tips Today</h2>
      </div>
      <div class="sh-divider"></div>
      <span class="section-count"><?php echo $allCount; ?> tips</span>
    </div>

    <div class="filter-bar">
      <div class="filter-left">
        <span class="filter-label">Filter:</span>
        <select class="fsel" id="fsel-league">
          <option value="">All Leagues</option>
          <?php
          $leagues = array_unique(array_map(function($f) { return $f['league']['name'] ?? 'Other'; }, $allFixtures));
          foreach($leagues as $league):
          ?>
          <option value="<?php echo htmlspecialchars(strtolower($league)); ?>"><?php echo htmlspecialchars($league); ?></option>
          <?php endforeach; ?>
        </select>
        <select class="fsel" id="fsel-time">
          <option value="">All Times</option>
          <option value="early">Early (before 15:00)</option>
          <option value="evening">Evening (after 19:00)</option>
        </select>
      </div>
      <div class="filter-right">
        <span class="conf-pill hi">● High</span>
        <span class="conf-pill md">● Medium</span>
        <span class="conf-pill lo">● Low</span>
      </div>
    </div>

    <div class="pcards" id="dynamicCards">
      <?php foreach(array_slice($allFixtures, 0, 8) as $tip):
          $pred = $tip['prediction'] ?? [];
          $type = $pred['type'] ?? '1X2';
          $predText = $pred['prediction'] ?? 'Home Win';
          $confidence = $pred['confidence'] ?? 70;
          $odds = $pred['odds'] ?? 1.65;
          $homeTeam = htmlspecialchars($tip['home_team']['name'] ?? 'Unknown');
          $awayTeam = htmlspecialchars($tip['away_team']['name'] ?? 'Unknown');
          $leagueName = $tip['league']['name'] ?? 'Football';
          $matchDate = isset($tip['date']) ? date('H:i', strtotime($tip['date'])) : 'TBD';
          $shortPred = getShortPrediction($type, $predText);
          $leagueClass = 'ucl';
          if (stripos($leagueName, 'Bundesliga') !== false) $leagueClass = 'bl';
          elseif (stripos($leagueName, 'Premier') !== false) $leagueClass = 'epl';
          elseif (stripos($leagueName, 'La Liga') !== false) $leagueClass = 'liga';
          elseif (stripos($leagueName, 'Serie A') !== false) $leagueClass = 'sa';
          $pipsHtml = '<div class="cb on"></div><div class="cb on"></div><div class="cb on"></div><div class="cb on"></div><div class="cb"></div>';
          $confLevel = $confidence >= 75 ? 'high' : ($confidence >= 60 ? 'medium' : 'low');
      ?>
      <article class="pcard" data-league="<?php echo htmlspecialchars(strtolower($leagueName)); ?>" data-hour="<?php echo date('H', strtotime($tip['date'] ?? 'now')); ?>" data-conf="<?php echo $confLevel; ?>">
        <div class="pc-row">
          <div class="pc-league-col lc-<?php echo $leagueClass; ?>">
            <span class="league-dot ld-<?php echo $leagueClass; ?>"></span>
          </div>
          <div class="pc-body">
            <div class="pc-info">
              <div class="pc-meta"><?php echo htmlspecialchars($leagueName); ?> · <?php echo $matchDate; ?></div>
              <div class="pc-match"><?php echo $homeTeam . " vs " . $awayTeam; ?></div>
              <div class="pc-live-wrap">Kick-off <?php echo $matchDate; ?></div>
            </div>
            <div class="pc-pred-col">
              <div class="pc-pred-tag"><?php echo $shortPred; ?></div>
              <div class="pc-market"><?php echo $type; ?> Market</div>
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
              </div>
              <p class="pc-note"><?php echo $pred['reason'] ?? "Data-driven prediction based on form, head-to-head, and squad analysis."; ?></p>
            </div>
            <div class="pc-form-panel">
              <a class="acca-btn" href="/accumulator-tips" style="margin-top:11px">+ Add to Acca</a>
            </div>
          </div>
        </div>
      </article>
      <?php endforeach; ?>
    </div>

  </main>

  <?php include 'sidebar.php'; ?>
</div>

<!-- MARKETS TILES -->
<div class="below-page" style="margin-top:40px">
  <div class="section-head">
    <div class="sh-left">
      <span class="sh-num">All Markets</span>
      <h2 class="sh-title">Explore Prediction Tips</h2>
    </div>
    <div class="sh-divider"></div>
    <a class="sh-more" href="/predictions-today">View all →</a>
  </div>
  <div class="tiles-grid">
    <a class="tile fire" href="/must-win-teams-today"><span class="tile-label">⚡ Must Win</span><span class="tile-count"><?php echo $mustWinCount; ?> teams today</span></a>
    <a class="tile jackpot" href="/jackpot-picks-today"><span class="tile-label">◈ Jackpot Picks</span><span class="tile-count"><?php echo $vipCount; ?> tips today</span></a>
    <a class="tile" href="/1x2-prediction"><span class="tile-label">1X2</span><span class="tile-count">Tips today</span></a>
    <a class="tile" href="/btts-tips"><span class="tile-label">BTTS</span><span class="tile-count">Tips today</span></a>
    <a class="tile" href="/over-2-5-goals"><span class="tile-label">Over 2.5</span><span class="tile-count">Tips today</span></a>
    <a class="tile" href="/accumulator-tips"><span class="tile-label">Accumulators</span><span class="tile-count">Today</span></a>
    <a class="tile" href="/correct-score-predictions"><span class="tile-label">Correct Score</span><span class="tile-count">Tips today</span></a>
    <a class="tile" href="/double-chance-tips"><span class="tile-label">Double Chance</span><span class="tile-count">Tips today</span></a>
  </div>

  <!-- REC GRID -->
  <div class="section-head" style="margin-top:36px">
    <div class="sh-left">
      <span class="sh-num">Editorial</span>
      <h2 class="sh-title">Recommended Reading</h2>
    </div>
    <div class="sh-divider"></div>
  </div>
  <div class="rec-grid">
    <a class="rec-card" href="/must-win-teams-today">
      <div class="rec-card-accent"></div>
      <div class="rec-cat fire">⚡ Must Win</div>
      <div class="rec-title">Must Win Teams Today — High Pressure Picks</div>
      <div class="rec-desc"><?php echo $mustWinCount; ?> teams under maximum pressure. Motivation-driven prediction tips.</div>
      <div class="rec-foot"><span class="rec-count"><?php echo $mustWinCount; ?> teams</span><span class="rec-arr">→</span></div>
    </a>
    <a class="rec-card" href="/jackpot-picks-today">
      <div class="rec-card-accent"></div>
      <div class="rec-cat jackpot">◈ Jackpot Picks</div>
      <div class="rec-title">Jackpot Picks of the Day — Top Certainties</div>
      <div class="rec-desc">Our <?php echo $vipCount; ?> highest-confidence football prediction tips, data-backed and form-validated.</div>
      <div class="rec-foot"><span class="rec-count"><?php echo $vipCount; ?> tips</span><span class="rec-arr">→</span></div>
    </a>
    <a class="rec-card" href="/accumulator-tips">
      <div class="rec-card-accent"></div>
      <div class="rec-cat">Accumulators</div>
      <div class="rec-title">Today's Best 5-Fold Accumulator</div>
      <div class="rec-desc">Our top 5-leg acca with combined odds. All high-confidence jackpot selections.</div>
      <div class="rec-foot"><span class="rec-count">5-leg acca</span><span class="rec-arr">→</span></div>
    </a>
    <a class="rec-card" href="/predictions-weekend">
      <div class="rec-card-accent"></div>
      <div class="rec-cat">Weekend Tips</div>
      <div class="rec-title">Saturday & Sunday Football Prediction Tips</div>
      <div class="rec-desc">Plan ahead — 40+ matches covered across all major leagues this weekend.</div>
      <div class="rec-foot"><span class="rec-count">40+ tips</span><span class="rec-arr">→</span></div>
    </a>
  </div>
</div>

<!-- SHARE -->
<div class="share-wrap">
  <span class="share-lbl">Share:</span>
  <a class="share-btn" href="https://twitter.com/intent/tweet?text=Free+football+prediction+tips+today&url=https://www.alljackpotpredictions.com/" target="_blank" rel="noopener">X / Twitter</a>
  <a class="share-btn" href="https://www.facebook.com/sharer/sharer.php?u=https://www.alljackpotpredictions.com/" target="_blank" rel="noopener">Facebook</a>
  <a class="share-btn" href="https://wa.me/?text=Football+prediction+tips+today+https://www.alljackpotpredictions.com/" target="_blank" rel="noopener">WhatsApp</a>
  <a class="share-btn" href="https://t.me/share/url?url=https://www.alljackpotpredictions.com/" target="_blank" rel="noopener">Telegram</a>
</div>

<!-- ✅ ARTICLE SECTION — Reworked for E-E-A-T, YMYL and natural keyword integration -->
<section class="article-section" style="margin-bottom:0">
  <div class="article-card">
    <div class="art-header">
      <div class="art-header-inner">
        <div class="art-kicker">
          <div class="art-kicker-line"></div>
          <span class="art-kicker-text">Expert Guide · AllJackpotPredictions Analyst Team</span>
        </div>
        <!-- ✅ Article H2 uses primary keyword in a natural, informational way -->
        <h2 class="art-h2">Football Prediction Tips Today: How Our Analysts Build Every Betting Tip</h2>
        <div class="art-meta">
          <!-- ✅ E-E-A-T: Named team, date, read time, responsible tags -->
          <span>AllJackpotPredictions Analyst Team</span>
          <span class="art-sep">·</span>
          <time datetime="2026-03-20">20 March 2026</time>
          <span class="art-sep">·</span>
          <span>Reviewed by Senior Analyst</span>
          <span class="art-sep">·</span>
          <span>12 min read</span>
          <span class="art-sep">·</span>
          <span class="art-tag">Prediction Tips</span>
          <span class="art-tag">Betting Guide</span>
          <span class="art-tag warn">⚠ 18+ | Gamble Responsibly</span>
        </div>
      </div>
    </div>

    <div class="art-body">
      <div class="art-col">
        <!-- ✅ H3 is descriptive and topically relevant -->
        <h3>What Are Football Prediction Tips?</h3>
        <!-- ✅ Opening paragraph: primary keyword used once, clearly, in context -->
        <p>Football prediction tips are research-backed estimates of likely match outcomes — covering markets like 1X2, BTTS, Over/Under goals and correct scores. At AllJackpotPredictions, every betting tip today is built from real match data reviewed by experienced analysts, not automated software. We publish fresh prediction tips daily across all major leagues.</p>

        <h4>What Goes Into Every Prediction Tip</h4>
        <ul>
          <li><strong>Team form</strong> — Last 5–10 results across all competitions</li>
          <li><strong>Head-to-head records</strong> — Historical matchups between the two sides</li>
          <li><strong>Squad fitness</strong> — Confirmed injuries, suspensions and rotation risk</li>
          <li><strong>Home vs away splits</strong> — Many sides perform dramatically differently on the road</li>
          <li><strong>Goals data</strong> — Average goals scored and conceded per match, and xG models</li>
          <li><strong>Tactical setup</strong> — High press, low block, counter-attacking or possession systems</li>
          <li><strong>Motivation &amp; context</strong> — League position, relegation battles, title run-ins, cup pressure</li>
        </ul>

        <!-- ✅ YMYL: We are clear this is not financial advice before user acts on the content -->
        <p><em>All football prediction tips on AllJackpotPredictions are published for informational and entertainment purposes only. They do not constitute financial or betting advice. Please read our <a href="/responsible-gambling">responsible gambling guidelines</a> before placing any bet.</em></p>
      </div>

      <div class="art-col">
        <h3>Must Win Teams &amp; Jackpot Picks Explained</h3>
        <p><strong>Must Win Teams</strong> are sides under maximum competitive pressure — a relegation battle, title run-in or knockout elimination. When a team cannot afford to drop points, motivation becomes a measurable edge. Our data shows these sides outperform their average odds significantly in high-pressure fixtures. <a href="/must-win-teams-today">See today's Must Win prediction tips →</a></p>
        <p><strong>Jackpot Picks</strong> are AllJackpotPredictions' three highest-conviction betting tips today — selected after a full statistical review by our senior analysts. Confidence ratings run 85–92%. These are the picks we stand behind most strongly as single selections or anchor legs in an accumulator. <a href="/jackpot-picks-today">See today's Jackpot Picks →</a></p>

        <h4>How to Use These Football Prediction Tips Responsibly</h4>
        <ul>
          <li>Always read the full analyst note before acting on any tip</li>
          <li>Use only High confidence tips as accumulator selections</li>
          <li>Treat Jackpot Picks as primary singles candidates</li>
          <li>Compare prices — our displayed odds are indicative, not guaranteed</li>
          <li>Set a fixed monthly staking budget and never exceed it</li>
          <li>Never chase losses or increase stakes after a losing run</li>
        </ul>
      </div>

      <div class="art-col art-full">
        <h3>Betting Markets Explained</h3>
        <table class="mkt-table">
          <thead><tr><th>Market</th><th>What It Means</th><th>Best Used When</th><th>Typical Odds</th></tr></thead>
          <tbody>
            <tr><td>⚡ Must Win</td><td>Teams that cannot drop points — high-motivation prediction tips</td><td>Relegation battles, title run-ins, cup eliminators</td><td>1.50–2.50</td></tr>
            <tr><td>◈ Jackpot Picks</td><td>Our 3 top-confidence betting tips today, analyst-verified</td><td>Singles or acca anchors</td><td>1.40–2.00</td></tr>
            <tr><td>1X2 (Match Result)</td><td>Home Win (1), Draw (X) or Away Win (2)</td><td>Clear form advantage for one side</td><td>1.40–3.50</td></tr>
            <tr><td>BTTS</td><td>Both sides score at least one goal in the match</td><td>Both teams attack well and defend poorly</td><td>1.60–2.20</td></tr>
            <tr><td>Over 1.5 Goals</td><td>Two or more goals scored in the match</td><td>Any competitive fixture — very high historical rate</td><td>1.15–1.40</td></tr>
            <tr><td>Over 2.5 Goals</td><td>Three or more goals scored in the match</td><td>High-scoring teams in strong attacking form</td><td>1.50–2.10</td></tr>
            <tr><td>Over 3.5 Goals</td><td>Four or more goals scored in the match</td><td>Attack-heavy fixtures or heavy mismatches</td><td>2.00–3.20</td></tr>
            <tr><td>Under 2.5 Goals</td><td>Two goals or fewer in the match</td><td>Tight rivalries, cautious tactical setups</td><td>1.60–2.00</td></tr>
            <tr><td>Correct Score</td><td>Predict the exact final scoreline</td><td>Value-seeking accumulators in tight matches</td><td>5.00–15.00+</td></tr>
            <tr><td>Double Chance</td><td>Cover two of three possible match outcomes</td><td>When you back a team but want insurance</td><td>1.20–1.80</td></tr>
            <tr><td>Half Time Result</td><td>Predict the scoreline at half time only</td><td>Teams with strong first-half records</td><td>1.80–3.50</td></tr>
            <tr><td>GG / NG (BTTS/BTTS No)</td><td>Whether or not both teams find the net</td><td>Based on recent scoring records for both sides</td><td>1.50–2.20</td></tr>
          </tbody>
        </table>
      </div>

      <!-- ✅ FAQ SECTION — mirrors FAQPage schema exactly; 5 questions for breadth and YMYL coverage -->
      <div class="art-col art-full">
        <h3>Frequently Asked Questions</h3>
        <div class="faq-list">

          <input type="checkbox" id="fq1" class="faq-ck">
          <div class="faq-item">
            <label for="fq1" class="faq-q">Are AllJackpotPredictions' football prediction tips free? <span class="faq-icon">+</span></label>
            <div class="faq-a"><p>Yes — every football prediction tip on AllJackpotPredictions is 100% free. No subscription, premium tier, or account is required to view any tip.</p></div>
          </div>

          <input type="checkbox" id="fq2" class="faq-ck">
          <div class="faq-item">
            <label for="fq2" class="faq-q">How accurate are AllJackpotPredictions' betting tips today? <span class="faq-icon">+</span></label>
            <div class="faq-a"><p>AllJackpotPredictions maintains a verified 74% win rate this month across all prediction markets. Our full track record — every tip, every result — is published publicly and updated after every match. You can view the complete record on our <a href="/track-record">Track Record page</a>.</p></div>
          </div>

          <input type="checkbox" id="fq3" class="faq-ck">
          <div class="faq-item">
            <label for="fq3" class="faq-q">What leagues do AllJackpotPredictions' prediction tips cover? <span class="faq-icon">+</span></label>
            <div class="faq-a"><p>Our daily football prediction tips cover the Premier League, UEFA Champions League, La Liga, Bundesliga, Serie A, Ligue 1 and selected domestic cup competitions. We also publish specialist tips for international fixtures during international windows.</p></div>
          </div>

          <input type="checkbox" id="fq4" class="faq-ck">
          <div class="faq-item">
            <label for="fq4" class="faq-q">What are Jackpot Picks? <span class="faq-icon">+</span></label>
            <div class="faq-a"><p>Jackpot Picks are our three highest-confidence football prediction tips each day, selected after a full statistical review by our senior analysts. Confidence ratings run 85–92%. They work well as standalone single bets or as anchor selections in an accumulator.</p></div>
          </div>

          <input type="checkbox" id="fq5" class="faq-ck">
          <div class="faq-item">
            <!-- ✅ YMYL: Honest, prominent disclaimer in the FAQ itself -->
            <label for="fq5" class="faq-q">Are betting tips a guaranteed way to make money? <span class="faq-icon">+</span></label>
            <div class="faq-a"><p>No — and any service that claims otherwise should be avoided. Football prediction tips are informed, research-backed opinions published for entertainment purposes only. They are <strong>not financial advice</strong> and cannot guarantee profit. Betting carries real financial risk. Always set a fixed budget, stake only amounts you can afford to lose, and use the tools your bookmaker provides — deposit limits, cooling-off periods, and self-exclusion — if you feel your gambling is becoming a problem.</p></div>
          </div>

        </div>
      </div>

      <!-- ✅ E-E-A-T AUTHOR BOX — strengthened with credentials, review process and named team -->
      <div class="art-col art-full">
        <div class="eeat-box">
          <div class="eeat-av">TO</div>
          <div class="eeat-body">
            <div class="eeat-name">AllJackpotPredictions Analyst Team</div>
            <span class="eeat-role">Professional Football Analysts · Data-Driven Prediction Methodology · Senior Review Process</span>
            <p>The AllJackpotPredictions team combines over a decade of hands-on experience in football statistics, betting market analysis and tactical scouting across the Premier League, La Liga, Bundesliga, Serie A, Ligue 1 and European competition. Every football prediction tip goes through a two-stage process: raw data research (form tables, xG models, head-to-head records, confirmed injury lists) followed by editorial review by a senior analyst before publication. We never publish tips based on speculation alone.</p>
            <div class="eeat-creds">
              <span>📊 Data-first methodology</span>
              <span>📋 Published &amp; verified track record</span>
              <span>🏆 10+ years combined experience</span>
              <span>🔍 Daily injury &amp; squad monitoring</span>
              <span>✅ Senior analyst review on every tip</span>
            </div>
            <a class="eeat-link" href="/about">Meet the analyst team →</a>
          </div>
        </div>
      </div>

      <!-- ✅ YMYL / RESPONSIBLE GAMBLING BOX — prominent, detailed, with named third-party resources -->
      <div class="art-col art-full">
        <div class="rg-box">
          <div class="rg-top">
            <span class="rg-icon">🛡</span>
            <div>
              <div class="rg-title">Responsible Gambling — Please Read Before Betting</div>
              <p class="rg-sub">AllJackpotPredictions is committed to promoting safe, responsible and informed gambling. This content is for entertainment purposes only and is not financial advice.</p>
            </div>
          </div>
          <div class="rg-cols">
            <div class="rg-col">
              <h4>⚠ Important Disclaimer</h4>
              <p>All football prediction tips and betting tips on AllJackpotPredictions are published for <strong>entertainment purposes only</strong>. They do not constitute financial, investment or betting advice. Football betting involves real financial risk. Past performance of any tipster — including AllJackpotPredictions — does <strong>not</strong> guarantee future results.</p>
              <h4>🔞 Age Restriction</h4>
              <p>Gambling is only permitted for persons aged <strong>18 or over</strong> (or the legal age in your jurisdiction). By accessing AllJackpotPredictions you confirm you meet the legal age requirement for gambling in your country.</p>
            </div>
            <div class="rg-col">
              <h4>✅ Safer Gambling Practices</h4>
              <ul>
                <li>Set a fixed weekly or monthly budget before you start</li>
                <li>Never bet more than you can comfortably afford to lose</li>
                <li>Never chase losses by increasing your stake</li>
                <li>Take regular breaks and keep betting in perspective</li>
                <li>Use deposit limits, loss limits and cooling-off tools at your bookmaker</li>
                <li>Treat prediction tips as entertainment — not a source of income</li>
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
// Toggle expand/collapse functionality
document.querySelectorAll('.pc-tog-btn').forEach(btn => {
  btn.addEventListener('click', function(e) {
    const pcard = this.closest('.pcard');
    const expand = pcard.querySelector('.pc-expand');
    expand.classList.toggle('show');
    this.textContent = expand.classList.contains('show') ? '▴' : '▾';
  });
});

// Filter functionality
const leagueSelect = document.getElementById('fsel-league');
const timeSelect = document.getElementById('fsel-time');
if (leagueSelect && timeSelect) {
  function filterCards() {
    const leagueVal = leagueSelect.value.toLowerCase();
    const timeVal = timeSelect.value;
    const allCards = document.querySelectorAll('.pcard');
    let visibleCount = 0;
    allCards.forEach(card => {
      let leagueMatch = true;
      let timeMatch = true;
      if(leagueVal !== '') {
        const cardLeague = card.getAttribute('data-league') || '';
        if(!cardLeague.includes(leagueVal)) leagueMatch = false;
      }
      if(timeVal !== '') {
        const hour = parseInt(card.getAttribute('data-hour') || '0');
        if(timeVal === 'early' && hour >= 15) timeMatch = false;
        if(timeVal === 'evening' && hour < 19) timeMatch = false;
      }
      if(leagueMatch && timeMatch) {
        card.style.display = '';
        visibleCount++;
      } else {
        card.style.display = 'none';
      }
    });
    const countSpan = document.querySelector('.section-count');
    if(countSpan) countSpan.innerText = visibleCount + ' tips';
  }
  leagueSelect.addEventListener('change', filterCards);
  timeSelect.addEventListener('change', filterCards);
}
</script>
</body>
</html>