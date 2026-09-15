<?php
/**
 * Shared hero right-panel: Best tips list with tip chips, conf bars, combined returns.
 *
 * Expected:
 *   $hrTitle   string  Panel heading
 *   $hrTips    array   Fixture rows OR normalized picks (see ajp_hr_normalize_tip)
 *
 * Optional:
 *   $hrBadge       string  Badge text (default: "{n} Selections")
 *   $hrHref        string  Default pick link (default: #tips)
 *   $hrEmpty       string  Empty-state title
 *   $hrEmptySub    string  Empty-state subtitle
 *   $hrShowFoot    bool    Combined odds + stake footer (default true when ≥2 picks)
 *   $hrCtaHref     string  Footer CTA href (default /accumulator-tips)
 *   $hrCtaLabel    string  Footer CTA label
 *   $hrTipFn       callable(array $tip): string  Custom tip label extractor
 *   $hrConfFn      callable(array $tip): int|string  Custom confidence extractor
 */

if (!function_exists('ajp_hr_team_name')) {
    function ajp_hr_team_name($team, string $fallback = 'Team'): string
    {
        if (is_array($team)) {
            return (string) ($team['name'] ?? $fallback);
        }
        return $team !== null && $team !== '' ? (string) $team : $fallback;
    }

    function ajp_hr_normalize_tip(array $tip, array $opts = []): array
    {
        // Already normalized pick
        if (isset($tip['home'], $tip['away']) && (isset($tip['tip']) || isset($tip['odds']))) {
            return [
                'home'   => (string) $tip['home'],
                'away'   => (string) $tip['away'],
                'tip'    => (string) ($tip['tip'] ?? 'Tip'),
                'league' => (string) ($tip['league'] ?? 'Football'),
                'time'   => (string) ($tip['time'] ?? 'TBD'),
                'odds'   => (float) ($tip['odds'] ?? 1.50),
                'conf'   => (int) ($tip['conf'] ?? 70),
                'href'   => (string) ($tip['href'] ?? ($opts['href'] ?? '#tips')),
                'vs'     => (bool) ($tip['vs'] ?? true),
            ];
        }

        $pred = is_array($tip['prediction'] ?? null) ? $tip['prediction'] : [];
        $home = ajp_hr_team_name($tip['home_team'] ?? ($tip['home_team_name'] ?? null), 'Team');
        $away = ajp_hr_team_name($tip['away_team'] ?? ($tip['away_team_name'] ?? null), 'Opponent');

        $league = $tip['league_name']
            ?? (is_array($tip['league'] ?? null) ? ($tip['league']['name'] ?? 'Football') : 'Football');

        $kick = $tip['fixture_date'] ?? $tip['date'] ?? null;
        $time = $kick ? date('H:i', strtotime((string) $kick)) : 'TBD';

        $odds = (float) (
            $pred['odds']
            ?? ($tip['odds']['prediction_odd'] ?? null)
            ?? (is_numeric($tip['odds'] ?? null) ? $tip['odds'] : null)
            ?? 1.50
        );

        $conf = $pred['confidence'] ?? ($tip['confidence'] ?? 70);
        if (!is_numeric($conf) && isset($opts['confFn']) && is_callable($opts['confFn'])) {
            $conf = ($opts['confFn'])($tip);
        }
        $conf = (int) (is_numeric($conf) ? $conf : 70);

        $tipLabel = 'Tip';
        if (isset($opts['tipFn']) && is_callable($opts['tipFn'])) {
            $tipLabel = (string) ($opts['tipFn'])($tip);
        } else {
            $tipLabel = (string) (
                $pred['score']
                ?? $pred['prediction']
                ?? ($tip['score'] ?? 'Tip')
            );
        }

        return [
            'home'   => $home,
            'away'   => $away,
            'tip'    => $tipLabel,
            'league' => (string) $league,
            'time'   => $time,
            'odds'   => $odds,
            'conf'   => max(0, min(100, $conf)),
            'href'   => (string) ($tip['href'] ?? ($opts['href'] ?? '#tips')),
            'vs'     => true,
        ];
    }
}

$hrTitle = (string) ($hrTitle ?? 'Best Tips Today');
$hrTips = is_array($hrTips ?? null) ? array_values($hrTips) : [];
$hrHref = (string) ($hrHref ?? '#tips');
$hrEmpty = (string) ($hrEmpty ?? 'No tips available right now');
$hrEmptySub = (string) ($hrEmptySub ?? 'Check back soon for fresh selections');
$hrShowFoot = isset($hrShowFoot) ? (bool) $hrShowFoot : true;
$hrCtaHref = (string) ($hrCtaHref ?? '/accumulator-tips');
$hrCtaLabel = (string) ($hrCtaLabel ?? 'Build acca →');

$normOpts = [
    'href' => $hrHref,
];
if (isset($hrTipFn) && is_callable($hrTipFn)) {
    $normOpts['tipFn'] = $hrTipFn;
}
if (isset($hrConfFn) && is_callable($hrConfFn)) {
    $normOpts['confFn'] = $hrConfFn;
}

$picks = [];
foreach ($hrTips as $row) {
    if (!is_array($row)) {
        continue;
    }
    $picks[] = ajp_hr_normalize_tip($row, $normOpts);
}

$hrBadge = isset($hrBadge) && $hrBadge !== ''
    ? (string) $hrBadge
    : (count($picks) . ' Selection' . (count($picks) === 1 ? '' : 's'));

$combined = 1.0;
foreach ($picks as $p) {
    $combined *= max(1.01, (float) $p['odds']);
}
$combined = round($combined, 2);

if (!isset($__cur) || !is_array($__cur)) {
    if (!function_exists('ajp_visitor_currency')) {
        require_once __DIR__ . '/currency.php';
    }
    $__cur = ajp_visitor_currency();
}
if (!isset($__stakeLabel)) {
    $__stakeLabel = ajp_stake_returns_label($__cur);
}
?>
<div class="hero-right">
  <div class="hr-panel">
    <div class="hr-label">
      <span class="hr-label-text"><?php echo htmlspecialchars($hrTitle); ?></span>
      <span class="hr-label-badge"><?php echo htmlspecialchars($hrBadge); ?></span>
    </div>

    <?php if (count($picks) === 0): ?>
      <div class="hr-empty">
        <p class="hr-empty-title"><?php echo htmlspecialchars($hrEmpty); ?></p>
        <p class="hr-empty-sub"><?php echo htmlspecialchars($hrEmptySub); ?></p>
      </div>
    <?php else: ?>
      <div class="hr-picks">
        <?php foreach ($picks as $idx => $pick):
            $confW = max(8, min(100, (int) $pick['conf']));
            $matchTitle = $pick['vs']
                ? ($pick['home'] . ' vs ' . $pick['away'])
                : $pick['home'];
        ?>
        <a class="hr-pick-card" href="<?php echo htmlspecialchars($pick['href']); ?>">
          <div class="hr-pick-num"><?php echo sprintf('%02d', $idx + 1); ?></div>
          <div class="hr-pick-body">
            <div class="hr-pick-match" title="<?php echo htmlspecialchars($matchTitle); ?>">
              <?php if (!empty($pick['vs'])): ?>
                <span class="hr-pick-home"><?php echo htmlspecialchars($pick['home']); ?></span>
                <span class="hr-pick-vs">vs</span>
                <span class="hr-pick-away"><?php echo htmlspecialchars($pick['away']); ?></span>
              <?php else: ?>
                <span class="hr-pick-home hr-pick-home-full"><?php echo htmlspecialchars($pick['home']); ?></span>
              <?php endif; ?>
            </div>
            <div class="hr-pick-meta">
              <span class="hr-pick-tip"><?php echo htmlspecialchars($pick['tip']); ?></span>
              <?php if ($pick['league'] !== ''): ?>
                <span class="hr-pick-sep" aria-hidden="true">·</span>
                <span class="hr-pick-league"><?php echo htmlspecialchars($pick['league']); ?></span>
              <?php endif; ?>
              <?php if ($pick['time'] !== '' && $pick['time'] !== 'TBD'): ?>
                <span class="hr-pick-sep" aria-hidden="true">·</span>
                <span class="hr-pick-time"><?php echo htmlspecialchars($pick['time']); ?></span>
              <?php elseif ($pick['time'] === 'TBD' && $pick['vs']): ?>
                <span class="hr-pick-sep" aria-hidden="true">·</span>
                <span class="hr-pick-time">TBD</span>
              <?php endif; ?>
            </div>
          </div>
          <div class="hr-pick-right">
            <div class="hr-pick-odds"><?php echo number_format((float) $pick['odds'], 2); ?></div>
            <div class="hr-pick-conf" title="<?php echo (int) $pick['conf']; ?>% confidence">
              <span class="hr-conf-track"><span class="hr-conf-fill" style="width:<?php echo $confW; ?>%"></span></span>
              <span class="hr-conf-pct"><?php echo (int) $pick['conf']; ?>%</span>
            </div>
          </div>
        </a>
        <?php endforeach; ?>
      </div>

      <?php if ($hrShowFoot && count($picks) >= 2): ?>
      <div class="hr-panel-foot">
        <div class="hr-foot-stat">
          <span class="hr-foot-label">Combined</span>
          <span class="hr-foot-val hr-foot-odds"><?php echo number_format($combined, 2); ?>×</span>
        </div>
        <div class="hr-foot-stat">
          <span class="hr-foot-label"><?php echo htmlspecialchars($__stakeLabel); ?></span>
          <span class="hr-foot-val hr-foot-return"><?php echo htmlspecialchars(ajp_stake_returns_amount($combined, $__cur)); ?></span>
        </div>
        <a class="hr-foot-cta" href="<?php echo htmlspecialchars($hrCtaHref); ?>"><?php echo htmlspecialchars($hrCtaLabel); ?></a>
      </div>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</div>
<?php
// Avoid leaking config into parent scope on next include
unset($hrTitle, $hrTips, $hrBadge, $hrHref, $hrEmpty, $hrEmptySub, $hrShowFoot, $hrCtaHref, $hrCtaLabel, $hrTipFn, $hrConfFn, $normOpts, $picks, $combined);
?>
