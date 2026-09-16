<?php
// Cache-bust headers — pages often emit HTML before including this file,
// so only send headers when nothing has been output yet.
if (!headers_sent()) {
    header('Cache-Control: no-cache, no-store, must-revalidate, max-age=0');
    header('Pragma: no-cache');
    header('Expires: Sat, 01 Jan 2000 00:00:00 GMT');
    header('X-LiteSpeed-Cache-Control: no-cache'); // harmless if host isn't LiteSpeed
}

$__navPath = parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH);
$__navPath = is_string($__navPath) ? rtrim(urldecode($__navPath), '/') : '';
if ($__navPath === '' || $__navPath === '/index.php') {
    $__navPath = '/';
}

$__navIs = static function (string ...$prefixes) use ($__navPath): bool {
    foreach ($prefixes as $p) {
        $p = rtrim($p, '/');
        if ($p === '') {
            continue;
        }
        if ($__navPath === $p || str_starts_with($__navPath, $p . '/')) {
            return true;
        }
    }
    return false;
};

$__navClass = static function (string $base, bool $on): string {
    return $on ? trim($base . ' active') : $base;
};
?>

<!-- Thin top progress during navigation (same pattern as Bao Predictions) -->
<div id="ajp-page-loader" class="ajp-page-progress" role="status" aria-live="polite" aria-busy="false" aria-hidden="true">
  <span class="ajp-page-progress-bar" aria-hidden="true"></span>
</div>
<noscript><style>.ajp-page-progress{display:none!important}</style></noscript>

<!-- MOBILE DRAWER -->
<input type="checkbox" id="mob-ck">
<label for="mob-ck" class="mob-overlay"></label>
<div class="mob-drawer">
  <div class="mob-head">
    <div class="mob-logo">AllJackpot<span>Predictions</span></div>
    <label for="mob-ck" class="mob-close">✕</label>
  </div>
  <nav class="mob-nav">
    <div class="mob-section">Jackpot desk</div>
    <a href="/jackpot-picks-today" class="jackpot<?= $__navIs('/jackpot-picks-today', '/jackpot-predictions') || str_contains($__navPath, 'jackpot') ? ' on' : '' ?>">All Jackpots</a>
    <a href="/must-win-teams-today" class="fire<?= $__navIs('/must-win-teams-today', '/home-teams-to-win-today', '/away-teams-to-win-today', '/teams-that-will-never-lose', '/most-trusted-teams-to-win') ? ' on' : '' ?>">Banker Shortlist</a>
    <a href="/jackpots/sportpesa-mega-jackpot-predictions"<?= $__navIs('/jackpots/sportpesa-mega-jackpot-predictions') ? ' class="on"' : '' ?>>SportPesa Mega</a>
    <a href="/jackpots/betika-midweek-jackpot-predictions"<?= $__navIs('/jackpots/betika-midweek-jackpot-predictions') ? ' class="on"' : '' ?>>Betika Midweek</a>
    <div class="mob-divider"></div>
    <div class="mob-section">Slip builders</div>
    <a href="/predictions-today"<?= $__navIs('/predictions-today') || $__navPath === '/' ? ' class="on"' : '' ?>>Today's Board</a>
    <a href="/predictions-tomorrow"<?= $__navIs('/predictions-tomorrow') ? ' class="on"' : '' ?>>Tomorrow's Board</a>
    <a href="/predictions-weekend"<?= $__navIs('/predictions-weekend') ? ' class="on"' : '' ?>>Weekend Board</a>
    <a href="/accumulator-tips"<?= $__navIs('/accumulator-tips') ? ' class="on"' : '' ?>>Build Acca</a>
    <div class="mob-divider"></div>
    <div class="mob-section">Markets</div>
    <a href="/1x2-prediction"<?= $__navIs('/1x2-prediction') ? ' class="on"' : '' ?>>Match Result (1X2)</a>
    <a href="/btts-tips"<?= $__navIs('/btts-tips') ? ' class="on"' : '' ?>>BTTS Legs</a>
    <a href="/over-2-5-goals"<?= $__navIs('/over-2-5-goals', '/over-1-5-goals', '/over-3-5-goals', '/under-2-5-goals', '/under-3-5-goals') ? ' class="on"' : '' ?>>Goals O/U</a>
    <a href="/double-chance-tips"<?= $__navIs('/double-chance-tips') ? ' class="on"' : '' ?>>Cover Legs (DC)</a>
    <a href="/correct-score-predictions"<?= $__navIs('/correct-score-predictions') ? ' class="on"' : '' ?>>Correct Score</a>
    <a href="/half-time-predictions"<?= $__navIs('/half-time-predictions') ? ' class="on"' : '' ?>>Half Time</a>
    <a href="/ht-ft-predictions"<?= $__navIs('/ht-ft-predictions') ? ' class="on"' : '' ?>>HT / FT</a>
    <div class="mob-divider"></div>
    <a href="/track-record"<?= $__navIs('/track-record') ? ' class="on"' : '' ?>>Results Log</a>
    <a href="/about"<?= $__navIs('/about') ? ' class="on"' : '' ?>>About the Desk</a>
    <a href="/responsible-gambling"<?= $__navIs('/responsible-gambling') ? ' class="on"' : '' ?>>Responsible Gambling</a>
  </nav>
</div>

<!-- RG STRIP -->
<div class="rg-strip">
  <div class="rg-strip-inner">
    <p class="rg-strip-text">
      <strong>⚠ Entertainment purposes only.</strong>
      Betting carries financial risk. Never stake more than you can afford.
      <a href="/responsible-gambling">Responsible Gambling</a>
      &middot;
      <a href="https://www.begambleaware.org" target="_blank" rel="noopener">BeGambleAware</a>
      &middot;
      <a href="https://www.gamcare.org.uk" target="_blank" rel="noopener">GamCare</a>
    </p>
    <span class="rg-age-badge">18+</span>
  </div>
</div>

<!-- NAV -->
<header class="nav">
  <!-- Top row: logo + controls -->
  <div class="nav-top">
    <a class="nav-logo" href="/">
      <div class="nav-logo-mark">
        <img src="/img/logo-mark.svg" alt="" width="36" height="36" />
      </div>
      <span class="logo-tip">AllJackpot</span><span class="logo-accent">Predictions</span>
    </a>
    <div class="nav-end">
      <div class="date-pill" id="datePill">FRI 20 MAR</div>
      <button class="theme-btn" id="themeBtn" aria-label="Toggle theme">
        <span class="ico-moon">🌙</span>
        <span class="ico-sun">☀️</span>
      </button>
      <label for="mob-ck" class="ham-btn" aria-label="Open menu">
        <span></span><span></span><span></span>
      </label>
    </div>
  </div>
  <!-- Bottom row: jackpot-first navigation -->
  <nav class="nav-links-row" aria-label="Primary">
    <a class="<?= htmlspecialchars($__navClass('nav-a jackpot', $__navIs('/jackpot-picks-today', '/jackpot-predictions') || str_contains($__navPath, 'jackpot'))) ?>" href="/jackpot-picks-today">Jackpots</a>
    <a class="<?= htmlspecialchars($__navClass('nav-a fire', $__navIs('/must-win-teams-today', '/home-teams-to-win-today', '/away-teams-to-win-today', '/teams-that-will-never-lose', '/most-trusted-teams-to-win'))) ?>" href="/must-win-teams-today">Bankers</a>
    <a class="<?= htmlspecialchars($__navClass('nav-a', $__navIs('/predictions-today') || $__navPath === '/')) ?>" href="/predictions-today">Today's Board</a>
    <a class="<?= htmlspecialchars($__navClass('nav-a', $__navIs('/accumulator-tips'))) ?>" href="/accumulator-tips">Accas</a>
    <a class="<?= htmlspecialchars($__navClass('nav-a', $__navIs('/1x2-prediction'))) ?>" href="/1x2-prediction">Match Result</a>
    <a class="<?= htmlspecialchars($__navClass('nav-a', $__navIs('/over-2-5-goals', '/over-1-5-goals', '/over-3-5-goals', '/under-2-5-goals', '/under-3-5-goals'))) ?>" href="/over-2-5-goals">Goals O/U</a>
    <a class="<?= htmlspecialchars($__navClass('nav-a', $__navIs('/btts-tips'))) ?>" href="/btts-tips">BTTS</a>
    <a class="<?= htmlspecialchars($__navClass('nav-a', $__navIs('/double-chance-tips'))) ?>" href="/double-chance-tips">Cover Legs</a>
    <a class="<?= htmlspecialchars($__navClass('nav-a', $__navIs('/predictions-weekend'))) ?>" href="/predictions-weekend">Weekend</a>
    <a class="<?= htmlspecialchars($__navClass('nav-a', $__navIs('/track-record'))) ?>" href="/track-record">Results</a>
  </nav>
</header>

<!-- STATS TICKER BAR -->
<div class="ticker">
  <div class="ticker-inner">
    <?php
    require_once __DIR__ . '/includes/api-curl.php';
    $__stats = ajp_api_stats();

    $winRate = '74%';
    $tipsToday = '52';
    $winStreak = '9';
    $monthlyYield = '+38.7u';
    $tipsThisMonth = '143';
    $winRateClass = 'pink';
    $winStreakClass = 'green';
    $monthlyYieldClass = 'green';

    if (is_array($__stats) && ($__stats['ok'] ?? false)) {
        $wr = $__stats['win_rate'] ?? null;
        if ($wr !== null) {
            $winRate = rtrim(rtrim(number_format((float) $wr, 1), '0'), '.') . '%';
            $winRateClass = ((float) $wr >= 60) ? 'green' : 'pink';
        }
        $tipsToday = (string) (($__stats['today']['predictions'] ?? $__stats['today']['total'] ?? null) ?? $tipsToday);
        $ws = $__stats['win_streak'] ?? ($__stats['recent']['current_streak'] ?? null);
        if ($ws !== null) {
            $winStreak = (string) $ws;
        }
        $roi = $__stats['roi'] ?? ($__stats['track']['roi'] ?? null);
        if ($roi !== null) {
            $monthlyYield = (($roi > 0) ? '+' : '') . number_format((float) $roi, 1) . 'u';
            $monthlyYieldClass = ((float) $roi >= 0) ? 'green' : 'pink';
        }
        $settled = $__stats['settled_tips'] ?? ($__stats['track']['settled_tips'] ?? null);
        if ($settled !== null) {
            $tipsThisMonth = (string) $settled;
        }
    }
    ?>
    <div class="ticker-stat">
      <span class="ts-label">Hit Rate</span>
      <span class="ts-value <?= $winRateClass ?>"><?= $winRate ?></span>
    </div>
    <div class="ticker-stat">
      <span class="ts-label">Board Today</span>
      <span class="ts-value"><?= $tipsToday ?></span>
    </div>
    <div class="ticker-stat">
      <span class="ts-label">Hit Streak</span>
      <span class="ts-value <?= $winStreakClass ?>"><?= $winStreak ?></span>
    </div>
    <div class="ticker-stat">
      <span class="ts-label">Month ROI</span>
      <span class="ts-value <?= $monthlyYieldClass ?>"><?= $monthlyYield ?></span>
    </div>
    <div class="ticker-stat">
      <span class="ts-label">Settled</span>
      <span class="ts-value"><?= $tipsThisMonth ?></span>
    </div>
    <div class="ticker-live-badge">
      <span class="live-pulse"></span>
      Desk Live
    </div>
  </div>
</div>
