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
<title>Jackpot Predictions, Daily Accas &amp; Free Football Tips | AllJackpotPredictions</title>
<meta name="description" content="Build smarter jackpot slips and daily accumulators with free football tips from AllJackpotPredictions. SportPesa, Betika and multi-market picks updated every day — entertainment only, 18+.">
<meta name="keywords" content="jackpot predictions, sportpesa jackpot, betika midweek jackpot, accumulator tips, football tips today, free betting tips, jackpot picks, BTTS tips, over 2.5 tips">
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
<meta property="og:title"       content="Jackpot Predictions, Daily Accas &amp; Free Football Tips | AllJackpotPredictions">
<meta property="og:description" content="Jackpot sheets, banker anchors and free multi-market tips — built for accumulator builders. Updated daily. 18+ gamble responsibly.">
<meta property="og:image"       content="https://www.alljackpotpredictions.com/img/og-image.png">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:image:alt"   content="AllJackpotPredictions — Jackpot predictions and football tips">
<meta property="og:site_name"   content="AllJackpotPredictions">
<meta property="og:locale"      content="en_GB">
 
<!-- Twitter / X Card -->
<meta name="twitter:card"        content="summary_large_image">
<meta name="twitter:site"        content="@AllJackpotPredictions">
<meta name="twitter:title"       content="Jackpot Predictions &amp; Free Acca Tips | AllJackpotPredictions">
<meta name="twitter:description" content="SportPesa &amp; Betika jackpot ideas plus free daily football tips. Entertainment only.">
<meta name="twitter:image"       content="https://www.alljackpotpredictions.com/img/og-image.png">
<meta name="twitter:image:alt"   content="AllJackpotPredictions — Jackpot predictions and football tips">
 
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
      "description": "Jackpot predictions, accumulator ideas and free multi-market football tips. Entertainment only. 18+. Gamble responsibly."
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
          <span class="hero-kicker-text">Jackpot slips &amp; daily accas · <?php echo date('j F Y'); ?></span>
        </div>
        <h1 class="hero-h1">
          Jackpot<br>
          Predictions<br>
          <span class="h1-accent">&amp; Accas</span>
          <span class="h1-sub"><?php echo $allCount; ?> free tips today — bankers, supporting legs and multi-market ideas for SportPesa, Betika and beyond</span>
        </h1>
        <p class="hero-desc">
          <strong>Built for jackpot sheets — not tip spam.</strong> AllJackpotPredictions filters every fixture
          for slip role: banker anchor, supporting leg, or leave it off. Free board for SportPesa Mega &amp; Midweek,
          Betika, Mozzart, Odibet and everyday accumulators — updated daily. Entertainment only, 18+.
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
      $hrTitle = 'Today\'s Banker Shortlist';
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
    <span class="bc-cur">Jackpot Predictions &amp; Accas</span>
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
          <div class="op-note"><?php echo $pred['reason'] ?? "High-conviction slip anchor. Suggested stake: " . $stakeRec; ?></div>
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
        <h2 class="sh-title">Today's Full Tips Board</h2>
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
      <div class="rec-title">Weekend Tips Board — Saturday &amp; Sunday</div>
      <div class="rec-desc">Plan ahead — weekend board for Saturday/Sunday slips and jackpot planning.</div>
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

<!-- ARTICLE — original AllJackpotPredictions SEO (not TipOracle template) -->
<section class="article-section" style="margin-bottom:0">
  <div class="article-card">
    <div class="art-header">
      <div class="art-header-inner">
        <div class="art-kicker">
          <div class="art-kicker-line"></div>
          <span class="art-kicker-text">Editorial · AllJackpotPredictions Desk</span>
        </div>
        <h2 class="art-h2">How AllJackpotPredictions Builds Jackpot Slips, Bankers and Daily Accas</h2>
        <div class="art-meta">
          <span>AllJackpotPredictions Desk</span>
          <span class="art-sep">·</span>
          <time datetime="<?php echo date('Y-m-d'); ?>"><?php echo date('j F Y'); ?></time>
          <span class="art-sep">·</span>
          <span>Updated daily</span>
          <span class="art-sep">·</span>
          <span class="art-tag">Jackpot focus</span>
          <span class="art-tag">Acca building</span>
          <span class="art-tag warn">⚠ 18+ | Gamble Responsibly</span>
        </div>
      </div>
    </div>

    <div class="art-body">
      <div class="art-col">
        <h3>Built for jackpot sheets — not tip spam</h3>
        <p>AllJackpotPredictions exists for people who fill <strong>SportPesa, Betika, Mozzart, Odibet</strong> and similar multi-leg jackpots, plus everyday accumulators. We publish a full board of free tips, but our editorial priority is clear: high-conviction anchors you can actually place in a slip, not a wall of low-edge noise.</p>
        <p>Each selection starts with form, expected goals, head-to-head context and confirmed absences. We then ask a jackpot-specific question: does this fixture belong as a <em>banker</em>, a supporting leg, or should it stay off the sheet? That filter is what separates this site from generic “tips today” blogs.</p>

        <h4>What we publish every day</h4>
        <ul>
          <li><strong>Jackpot Picks</strong> — shortlist of highest-conviction bankers for singles or slip anchors</li>
          <li><strong>Operator jackpot pages</strong> — SportPesa Mega / Midweek, Betika Midweek, Mozzart, Odibet and related sheets</li>
          <li><strong>Market boards</strong> — 1X2, BTTS, Over/Under, Double Chance, HT/FT and Correct Score</li>
          <li><strong>Must-win sides</strong> — teams with clear competitive pressure (table, knockout, or survival)</li>
          <li><strong>Public track record</strong> — wins and losses logged after full time</li>
        </ul>

        <p><em>Everything here is for entertainment and information only — not financial advice. Read our <a href="/responsible-gambling">responsible gambling guide</a> before you stake.</em></p>
      </div>

      <div class="art-col">
        <h3>How we treat a jackpot leg</h3>
        <p>A jackpot slip fails if one weak leg collapses. So we bias toward fixtures with aligned motivation, manageable prices, and a story that still holds if a favourite key player is rotated. Draws are rarely treated as jackpot bankers on this desk — variance is simply too high for long sheets.</p>
        <p>For shorter daily accas we still prefer 3–5 legs over marathon tickets. Start with one or two <a href="/jackpot-picks-today">Jackpot Picks</a>, then add supporting legs from <a href="/1x2-prediction">1X2</a>, <a href="/btts-tips">BTTS</a> or <a href="/over-2-5-goals">Over 2.5</a> only when confidence and kick-off times fit.</p>

        <h4>Practical staking habits we recommend</h4>
        <ul>
          <li>Decide the slip size before you open the app — not after a loss</li>
          <li>Use Jackpot Picks as anchors; never pad a sheet with “maybe” legs</li>
          <li>Compare prices — displayed odds are illustrative, not live bookmaker prices</li>
          <li>Skip a day when the board is thin — sitting out is a valid decision</li>
          <li>Never chase a busted jackpot by doubling the next stake</li>
        </ul>
      </div>

      <div class="art-col art-full">
        <h3>Markets we cover on the daily board</h3>
        <table class="mkt-table">
          <thead><tr><th>Market</th><th>Role on an AJP slip</th><th>Best when</th></tr></thead>
          <tbody>
            <tr><td>Jackpot Picks</td><td>Banker / single</td><td>You need a high-conviction anchor</td></tr>
            <tr><td>Operator jackpots</td><td>Full multi-leg sheet</td><td>SportPesa, Betika, Mozzart, Odibet rounds</td></tr>
            <tr><td>1X2</td><td>Core match-result legs</td><td>Clear home/away edge without draw noise</td></tr>
            <tr><td>Double Chance</td><td>Safer supporting leg</td><td>You like a side but want draw cover</td></tr>
            <tr><td>BTTS</td><td>Goals-based supporting leg</td><td>Both attacks are live; defences leak</td></tr>
            <tr><td>Over / Under</td><td>Total-goals leg</td><td>xG and tempo point the same way</td></tr>
            <tr><td>HT / FT</td><td>Specialist / value</td><td>Strong half-time patterns, not volume filling</td></tr>
            <tr><td>Correct Score</td><td>Long-shot spice only</td><td>Never as a jackpot banker</td></tr>
          </tbody>
        </table>
      </div>

      <div class="art-col art-full">
        <h3>Questions bettors ask us</h3>
        <div class="faq-list">

          <input type="checkbox" id="fq1" class="faq-ck">
          <div class="faq-item">
            <label for="fq1" class="faq-q">Do I need an account to see AllJackpotPredictions tips? <span class="faq-icon">+</span></label>
            <div class="faq-a"><p>No. Tips, jackpot pages and track record pages are free to browse — no registration wall.</p></div>
          </div>

          <input type="checkbox" id="fq2" class="faq-ck">
          <div class="faq-item">
            <label for="fq2" class="faq-q">Which jackpots do you cover? <span class="faq-icon">+</span></label>
            <div class="faq-a"><p>We publish dedicated pages for major East African and regional jackpots — including SportPesa Mega and Midweek, Betika Midweek, Mozzart and Odibet — alongside daily Jackpot Picks you can use on any bookmaker.</p></div>
          </div>

          <input type="checkbox" id="fq3" class="faq-ck">
          <div class="faq-item">
            <label for="fq3" class="faq-q">How is this different from a generic tips site? <span class="faq-icon">+</span></label>
            <div class="faq-a"><p>Our editorial workflow is slip-first: we ask whether a pick belongs on a jackpot or accumulator before we publish it as a “banker”. Volume markets still exist for research, but the homepage and Jackpot Picks board are built for sheet builders.</p></div>
          </div>

          <input type="checkbox" id="fq4" class="faq-ck">
          <div class="faq-item">
            <label for="fq4" class="faq-q">Where can I verify past results? <span class="faq-icon">+</span></label>
            <div class="faq-a"><p>On our <a href="/track-record">Track Record</a> page. Settled tips are logged after full time — wins and losses — so you can judge the board yourself.</p></div>
          </div>

          <input type="checkbox" id="fq5" class="faq-ck">
          <div class="faq-item">
            <label for="fq5" class="faq-q">Can tips guarantee jackpot wins? <span class="faq-icon">+</span></label>
            <div class="faq-a"><p>No. Multi-leg jackpots are high-variance by design. Our content is entertainment research, not a promise of profit. Stake only what you can afford to lose and use bookmaker safer-gambling tools if you need them.</p></div>
          </div>

        </div>
      </div>

      <div class="art-col art-full">
        <div class="eeat-box">
          <div class="eeat-av">AJ</div>
          <div class="eeat-body">
            <div class="eeat-name">AllJackpotPredictions Desk</div>
            <span class="eeat-role">Jackpot &amp; accumulator research · Multi-market football tips · Public results log</span>
            <p>We write for bettors who live in jackpot and accumulator culture — East African operator sheets as well as European league boards. Research covers form, xG, injuries and match context; publication prefers fewer high-conviction legs over endless tip lists. Nothing here is automated spam, and nothing is financial advice.</p>
            <div class="eeat-creds">
              <span>🎯 Jackpot-first editorial</span>
              <span>📋 Public track record</span>
              <span>🧾 Operator sheet coverage</span>
              <span>🔍 Daily squad checks</span>
            </div>
            <a class="eeat-link" href="/about">About the desk →</a>
          </div>
        </div>
      </div>

      <div class="art-col art-full">
        <div class="rg-box">
          <div class="rg-top">
            <span class="rg-icon">🛡</span>
            <div>
              <div class="rg-title">Responsible Gambling — Please Read Before Betting</div>
              <p class="rg-sub">AllJackpotPredictions publishes entertainment research only. Betting involves real financial risk.</p>
            </div>
          </div>
          <div class="rg-cols">
            <div class="rg-col">
              <h4>⚠ Important Disclaimer</h4>
              <p>Tips and jackpot ideas on this site are <strong>not</strong> financial, investment or betting advice. Past results do not guarantee future outcomes. Only stake money you can afford to lose.</p>
              <h4>🔞 Age Restriction</h4>
              <p>Gambling is for persons aged <strong>18+</strong> (or the legal age where you live).</p>
            </div>
            <div class="rg-col">
              <h4>✅ Safer habits</h4>
              <ul>
                <li>Set a budget before you open a slip</li>
                <li>Never chase a lost jackpot</li>
                <li>Take breaks; keep betting in perspective</li>
                <li>Use deposit limits and self-exclusion tools when needed</li>
              </ul>
              <h4>🆘 Help</h4>
              <div class="rg-links-grid">
                <a href="https://www.begambleaware.org" target="_blank" rel="noopener" class="rg-link"><strong>BeGambleAware</strong><span>begambleaware.org</span></a>
                <a href="https://www.gamcare.org.uk" target="_blank" rel="noopener" class="rg-link"><strong>GamCare</strong><span>gamcare.org.uk</span></a>
                <a href="https://www.gamstop.co.uk" target="_blank" rel="noopener" class="rg-link"><strong>GamStop</strong><span>gamstop.co.uk</span></a>
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