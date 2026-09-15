<?php
// Cache-bust headers — pages often emit HTML before including this file,
// so only send headers when nothing has been output yet.
if (!headers_sent()) {
    header('Cache-Control: no-cache, no-store, must-revalidate, max-age=0');
    header('Pragma: no-cache');
    header('Expires: Sat, 01 Jan 2000 00:00:00 GMT');
    header('X-LiteSpeed-Cache-Control: no-cache'); // harmless if host isn't LiteSpeed
}
?>

<!-- MOBILE DRAWER -->
<input type="checkbox" id="mob-ck">
<label for="mob-ck" class="mob-overlay"></label>
<div class="mob-drawer">
  <div class="mob-head">
    <div class="mob-logo">AllJackpot<span>Predictions</span></div>
    <label for="mob-ck" class="mob-close">✕</label>
  </div>
  <nav class="mob-nav">
    <div class="mob-section">Predictions</div>
    <a href="/predictions-today" class="on">Today's Tips</a>
    <a href="/predictions-tomorrow">Tomorrow</a>
    <a href="/predictions-weekend">Weekend Picks</a>
    <a href="/accumulator-tips">Accumulators</a>
    <div class="mob-divider"></div>
    <div class="mob-section">Featured</div>
    <a href="/must-win-teams-today" class="fire">⚡ Must Win Teams</a>
    <a href="/jackpot-picks-today" class="jackpot">◈ Jackpot Picks</a>
    <div class="mob-divider"></div>
    <div class="mob-section">Markets</div>
    <a href="/1x2-prediction">1X2 Match Result</a>
    <a href="/btts-tips">BTTS Tips</a>
    <a href="/over-2-5-goals">Over 2.5 Goals</a>
    <a href="/over-1-5-goals">Over 1.5 Goals</a>
    <a href="/over-3-5-goals">Over 3.5 Goals</a>
    <a href="/under-2-5-goals">Under 2.5 Goals</a>
    <a href="/under-3-5-goals">Under 3.5 Goals</a>
    <a href="/correct-score-predictions">Correct Score</a>
    <a href="/double-chance-tips">Double Chance</a>
    <a href="/half-time-predictions">Half Time</a>
    <a href="/ht-ft-predictions">Half Time / Full Time</a>
    <div class="mob-divider"></div>
    <a href="/track-record">Track Record</a>
    <a href="/about">About Us</a>
    <a href="/responsible-gambling">Responsible Gambling</a>
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
    <!-- ★ NEW SVG LOGO ★ -->
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
  <!-- Bottom row: navigation links -->
  <nav class="nav-links-row">
    <a class="nav-a active" href="/predictions-today">Today</a>
    <a class="nav-a" href="/predictions-tomorrow">Tomorrow</a>
    <a class="nav-a" href="/predictions-weekend">Weekend</a>
    <a class="nav-a" href="/accumulator-tips">Accumulators</a>
    <a class="nav-a fire" href="/must-win-teams-today">⚡ Must Win</a>
    <a class="nav-a jackpot" href="/jackpot-picks-today">◈ Jackpot Picks</a>
    <a class="nav-a" href="/btts-tips">BTTS</a>
    <a class="nav-a" href="/correct-score-predictions">Correct Score</a>
    <a class="nav-a" href="/over-2-5-goals">Over / Under</a>
    <a class="nav-a" href="/1x2-prediction">1X2</a>
    <a class="nav-a" href="/double-chance-tips">Double Chance</a>
    <a class="nav-a" href="/half-time-predictions">Half Time</a>
    <a class="nav-a" href="/ht-ft-predictions">Half Time / Full Time</a>
    <a class="nav-a" href="/track-record">Track Record</a>
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
      <span class="ts-label">Win Rate</span>
      <span class="ts-value <?= $winRateClass ?>"><?= $winRate ?></span>
    </div>
    <div class="ticker-stat">
      <span class="ts-label">Tips Today</span>
      <span class="ts-value"><?= $tipsToday ?></span>
    </div>
    <div class="ticker-stat">
      <span class="ts-label">Win Streak</span>
      <span class="ts-value <?= $winStreakClass ?>"><?= $winStreak ?></span>
    </div>
    <div class="ticker-stat">
      <span class="ts-label">Monthly Yield</span>
      <span class="ts-value <?= $monthlyYieldClass ?>"><?= $monthlyYield ?></span>
    </div>
    <div class="ticker-stat">
      <span class="ts-label">Tips This Month</span>
      <span class="ts-value"><?= $tipsThisMonth ?></span>
    </div>
    <div class="ticker-live-badge">
      <span class="live-pulse"></span>
      Live Updates
    </div>
  </div>
</div>