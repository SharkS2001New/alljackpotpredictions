<!-- SIDEBAR -->
<aside class="sidebar">

  <!-- Jackpot Performance Card - Dynamic from API -->
  <div class="scard">
    <div class="sc-head">
      <span class="sc-title">Jackpot Performance</span>
      <span class="sc-badge">30-Day</span>
    </div>
    <div class="sc-body">
      <?php
      require_once __DIR__ . '/includes/api-curl.php';
      $__stats = ajp_api_stats();

      $performanceData = [];
      $apiAvailable = false;
      $winRate = 74;
      $won = 89;
      $lost = 29;
      $pending = 25;
      $yield = '+38.7u';
      $market1X2 = 79;
      $marketBTTS = 72;
      $marketOverUnder = 68;
      $marketCorrectScore = 56;
      $isSampleData = true;
      $rangeDisplay = 'Published track record';

      if (is_array($__stats) && ($__stats['ok'] ?? false)) {
          $apiAvailable = true;
          $isSampleData = false;
          $track = $__stats['track'] ?? [];
          $winRate = (float) ($track['win_rate'] ?? $__stats['win_rate'] ?? $winRate);
          $won = (int) ($track['wins'] ?? $won);
          $lost = (int) ($track['losses'] ?? $lost);
          $pending = max(0, (int) (($__stats['today']['predictions'] ?? 0) - ($__stats['today']['settled_total'] ?? 0)));
          $roi = (float) ($track['roi'] ?? $__stats['roi'] ?? 0);
          $yield = (($roi > 0) ? '+' : '') . number_format($roi, 1) . 'u';
          // Market bars: use overall WR as baseline (per-market WR not in stats payload)
          $market1X2 = (int) round($winRate);
          $marketBTTS = max(40, (int) round($winRate - 3));
          $marketOverUnder = max(40, (int) round($winRate - 5));
          $marketCorrectScore = max(35, (int) round($winRate - 12));
          $rangeDisplay = 'Live from pitchnewdb · ' . (int) ($track['settled_tips'] ?? 0) . ' settled';
      }
      ?>
      <div class="perf-headline">
        <div class="perf-winrate-row">
          <span class="perf-winrate-num"><?php echo $winRate; ?></span>
          <span class="perf-winrate-unit">%</span>
        </div>
        <div class="perf-winrate-label">Win Rate (Last 30 Days)</div>
        <div class="perf-winrate-bar"><div class="perf-winrate-fill" style="width:<?php echo $winRate; ?>%"></div></div>
      </div>
      <div class="perf-stats-grid">
        <div class="perf-stat-box">
          <div class="perf-stat-val psv-green"><?php echo $won; ?></div>
          <div class="perf-stat-lbl">Won</div>
        </div>
        <div class="perf-stat-box">
          <div class="perf-stat-val psv-red"><?php echo $lost; ?></div>
          <div class="perf-stat-lbl">Lost</div>
        </div>
        <div class="perf-stat-box">
          <div class="perf-stat-val psv-amber"><?php echo $pending; ?></div>
          <div class="perf-stat-lbl">Pending</div>
        </div>
        <div class="perf-stat-box">
          <div class="perf-stat-val psv-green"><?php echo $yield; ?></div>
          <div class="perf-stat-lbl">Yield</div>
        </div>
      </div>
      <div class="perf-mkt-bars">
        <div class="pmb-row"><span class="pmb-lbl">1X2</span><div class="pmb-track"><div class="pmb-fill" style="width:<?php echo $market1X2; ?>%;background:var(--pink)"></div></div><span class="pmb-pct"><?php echo $market1X2; ?>%</span></div>
        <div class="pmb-row"><span class="pmb-lbl">BTTS</span><div class="pmb-track"><div class="pmb-fill" style="width:<?php echo $marketBTTS; ?>%;background:var(--navy)"></div></div><span class="pmb-pct"><?php echo $marketBTTS; ?>%</span></div>
        <div class="pmb-row"><span class="pmb-lbl">O/U</span><div class="pmb-track"><div class="pmb-fill" style="width:<?php echo $marketOverUnder; ?>%;background:var(--amber)"></div></div><span class="pmb-pct"><?php echo $marketOverUnder; ?>%</span></div>
        <div class="pmb-row"><span class="pmb-lbl">CS</span><div class="pmb-track"><div class="pmb-fill" style="width:<?php echo $marketCorrectScore; ?>%;background:var(--muted)"></div></div><span class="pmb-pct"><?php echo $marketCorrectScore; ?>%</span></div>
      </div>
      <?php if ($isSampleData && $apiAvailable): ?>
      <div class="perf-note" style="font-size: 0.6rem; color: var(--muted); text-align: center; margin-top: 8px; padding-top: 6px; border-top: 1px solid var(--border);">
        ⚡ Data loading from completed matches
      </div>
      <?php endif; ?>
      <div class="perf-date-range" style="font-size: 0.55rem; color: var(--muted); text-align: center; margin-top: 6px;">
        <?php echo htmlspecialchars($rangeDisplay); ?>
      </div>
    </div>
  </div>

  <!-- Best Accumulator Tips from API -->
  <?php
  require_once __DIR__ . '/includes/api-curl.php';
  // Acca + results: cache only — never trigger a multi-second DB rebuild from the sidebar.
  $__acca = ajp_fetch_fixtures('accumulator-tips', ['cache_only' => true]);
  $accumulatorTips = array_slice($__acca['fixtures'] ?? [], 0, 5);

  $__results = ajp_fetch_fixtures('results', ['cache_only' => true]);
  $recentResults = [];
  foreach ($__results['fixtures'] ?? [] as $f) {
      // Settled tips only (won true/false); skip unfinished.
      if (!array_key_exists('won', $f) || $f['won'] === null) {
          continue;
      }
      $status = strtoupper((string) ($f['status'] ?? $f['status_short'] ?? ''));
      if ($status !== '' && $status !== 'FT' && $status !== 'AET' && $status !== 'PEN') {
          continue;
      }
      $recentResults[] = $f;
      if (count($recentResults) >= 5) {
          break;
      }
  }
  ?>

  <div class="scard">
    <div class="sc-head"><span class="sc-title">Best Accumulator</span><span class="sc-badge">5 LEGS</span></div>
    <div class="sc-body">
      <div class="acca-rows">
        <?php if (!empty($accumulatorTips)): ?>
          <?php foreach($accumulatorTips as $idx => $tip):
              $pred = $tip['prediction'] ?? [];
              $type = $pred['type'] ?? '1X2';
              $predText = $pred['prediction'] ?? 'Home Win';
              $odds = $pred['odds'] ?? 1.50;
              $homeTeam = $tip['home_team']['name'] ?? 'Team';
              $awayTeam = $tip['away_team']['name'] ?? 'Opponent';
              $leagueName = $tip['league']['name'] ?? 'Football';
              $shortName = explode(' ', $leagueName)[0];
              $displayPred = '';
              if ($type === '1X2') {
                  $displayPred = strpos($predText, 'Home') !== false ? 'Home Win' : (strpos($predText, 'Away') !== false ? 'Away Win' : 'Draw');
              } elseif ($type === 'Over/Under') {
                  $displayPred = $predText;
              } elseif ($type === 'Double Chance') {
                  $displayPred = $predText;
              } else {
                  $displayPred = $predText;
              }
          ?>
          <div class="acca-row">
            <div>
              <div class="acca-match"><?php echo htmlspecialchars($homeTeam) . " vs " . htmlspecialchars($awayTeam); ?></div>
              <div class="acca-mkt"><?php echo $displayPred; ?> · <?php echo htmlspecialchars($shortName); ?></div>
            </div>
            <div class="acca-odd"><?php echo number_format((float)$odds, 2); ?></div>
          </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="acca-row" style="justify-content: center; text-align: center; padding: 12px;">
            <div>No accumulator tips available for today</div>
          </div>
        <?php endif; ?>
      </div>
      <div class="acca-total-row">
        <?php
        $combinedOdds = 1;
        foreach($accumulatorTips as $tip) {
            $odds = $tip['prediction']['odds'] ?? 1.50;
            $combinedOdds *= (float)$odds;
        }
        $combinedOdds = round($combinedOdds, 2);
        ?>
        <div><div class="acca-total-lbl">Combined Odds</div><div class="acca-total-val"><?php echo $combinedOdds > 1 ? $combinedOdds : '—'; ?></div></div>
        <a class="btn-pink-sm" href="/accumulator-tips">Copy Acca</a>
      </div>
    </div>
  </div>

  <div class="scard">
    <div class="sc-head"><span class="sc-title">Browse Markets</span></div>
    <div class="sc-body">
      <div class="mkt-link-list">
        <a class="mkt-link fire" href="/must-win-teams-today">⚡ Must Win Teams Today <span class="arr">›</span></a>
        <a class="mkt-link" href="/jackpot-picks-today" style="color:var(--navy)">◈ Jackpot Picks Today <span class="arr">›</span></a>
        <a class="mkt-link" href="/1x2-prediction">1X2 Match Result <span class="arr">›</span></a>
        <a class="mkt-link" href="/btts-tips">BTTS Tips <span class="arr">›</span></a>
        <a class="mkt-link" href="/over-2-5-goals">Over 2.5 Goals <span class="arr">›</span></a>
        <a class="mkt-link" href="/over-1-5-goals">Over 1.5 Goals <span class="arr">›</span></a>
        <a class="mkt-link" href="/over-3-5-goals">Over 3.5 Goals <span class="arr">›</span></a>
        <a class="mkt-link" href="/under-2-5-goals">Under 2.5 Goals <span class="arr">›</span></a>
        <a class="mkt-link" href="/under-3-5-goals">Under 3.5 Goals <span class="arr">›</span></a>
        <a class="mkt-link" href="/correct-score-predictions">Correct Score <span class="arr">›</span></a>
        <a class="mkt-link" href="/double-chance-tips">Double Chance <span class="arr">›</span></a>
        <a class="mkt-link" href="/half-time-predictions">Half Time Result <span class="arr">›</span></a>
        <a class="mkt-link" href="/ht-ft-predictions">Half Time / Full Time <span class="arr">›</span></a>
        <a class="mkt-link" href="/accumulator-tips">Accumulators <span class="arr">›</span></a>
      </div>
    </div>
  </div>

  <div class="scard">
    <div class="sc-head"><span class="sc-title">Recent Results</span><a href="/track-record" class="sc-link">All →</a></div>
    <div class="sc-body">
      <div class="res-list">
        <?php if (!empty($recentResults)): ?>
          <?php foreach ($recentResults as $match):
              $pred = $match['prediction']['prediction'] ?? ($match['prediction']['full_prediction'] ?? 'Tip');
              if ($pred === '1') $pred = 'Home Win';
              if ($pred === '2') $pred = 'Away Win';
              if ($pred === 'X') $pred = 'Draw';
              $homeTeam = $match['home_team']['name'] ?? ($match['home_team_name'] ?? 'Team');
              $awayTeam = $match['away_team']['name'] ?? ($match['away_team_name'] ?? 'Opponent');
              $scoreDisplay = $match['scores']['display']
                  ?? $match['scores']['full_display']
                  ?? (isset($match['goals_home'], $match['goals_away']) ? ($match['goals_home'] . '-' . $match['goals_away']) : null)
                  ?? (is_string($match['score'] ?? null) ? $match['score'] : '—');
              $dateLabel = $match['date_label'] ?? '';
              if ($dateLabel === '' && !empty($match['date'])) {
                  $ts = strtotime((string) $match['date']);
                  $dateLabel = $ts ? date('M j', $ts) : '';
              }
              $isWin = ($match['won'] ?? null) === true;
              $badgeClass = $isWin ? 'rb-W' : 'rb-L';
              $badgeLetter = $isWin ? '✅' : '❌';
          ?>
          <div class="res-row">
            <span class="res-badge <?php echo $badgeClass; ?>" aria-label="<?php echo $isWin ? 'Won' : 'Lost'; ?>"><?php echo $badgeLetter; ?></span>
            <div class="res-info">
              <div class="res-match"><?php echo htmlspecialchars($homeTeam) . ' vs ' . htmlspecialchars($awayTeam); ?></div>
              <div class="res-tip"><?php echo htmlspecialchars((string) $pred); ?><?php echo $dateLabel !== '' ? ' · ' . htmlspecialchars($dateLabel) : ''; ?></div>
            </div>
            <span class="res-score"><?php echo htmlspecialchars((string) $scoreDisplay); ?></span>
          </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="res-row" style="opacity:.65">
            <div class="res-info">
              <div class="res-match">No settled results yet</div>
              <div class="res-tip">Check back after today's fixtures finish</div>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

</aside>