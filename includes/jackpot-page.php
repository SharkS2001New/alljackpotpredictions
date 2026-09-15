<?php
/**
 * Shared jackpot detail page renderer (clean tips table).
 * Expected vars (set by caller before include):
 *   $slug (string, required)
 *   $pageTitle, $metaDescription, $metaKeywords (optional)
 *   $h1, $intro (optional)
 *   $seoHtml (optional HTML below fixtures)
 */
require_once __DIR__ . '/jackpot-api.php';

$slug = preg_replace('/[^a-z0-9\-]/', '', strtolower((string) ($slug ?? ''))) ?? '';
if ($slug === '') {
    if (!headers_sent()) {
        header('Location: /jackpot-picks-today', true, 302);
    }
    exit;
}

$dbName = ajp_jackpot_slug_to_db_name($slug);
$defaultTitle = ajp_jackpot_slug_to_title($slug);
$pageTitle = trim((string) ($pageTitle ?? '')) ?: ($defaultTitle . ' — Free Jackpot Tips | AllJackpotPredictions');
$metaDescription = trim((string) ($metaDescription ?? ''))
    ?: ('Free ' . $defaultTitle . ' with 1X2 odds and our prediction for every game. Updated daily on AllJackpotPredictions.');
$metaKeywords = trim((string) ($metaKeywords ?? ''));
$h1 = trim((string) ($h1 ?? '')) ?: $defaultTitle;
$intro = trim((string) ($intro ?? ''));
$seoHtml = (string) ($seoHtml ?? '');
$canonical = 'https://www.alljackpotpredictions.com/jackpots/' . $slug;

$fixtures = ($dbName !== 'Unknown Jackpot') ? ajp_fetch_jackpot_fixtures($dbName) : [];
$gameCount = count($fixtures);

// Sort by jackpot position
usort($fixtures, static function ($a, $b) {
    return ((int) ($a['jackpot_position'] ?? 0)) <=> ((int) ($b['jackpot_position'] ?? 0));
});

$completed = 0;
$correct = 0;
$tipCodes = [];
$avgConf = 0.0;
$firstKick = null;
$lastKick = null;
$rows = [];

foreach ($fixtures as $f) {
    $pos = (int) ($f['jackpot_position'] ?? 0);
    $home = (string) ($f['home_team_name'] ?? 'Home');
    $away = (string) ($f['away_team_name'] ?? 'Away');
    $league = (string) ($f['league_name'] ?? '');
    if (strpos($league, ':') !== false) {
        $league = trim(explode(':', $league, 2)[1]);
    }
    $hPct = ajp_percent($f['percent_pred_home'] ?? 0);
    $dPct = ajp_percent($f['percent_pred_draw'] ?? 0);
    $aPct = ajp_percent($f['percent_pred_away'] ?? 0);
    $code = ajp_tip_code($f['tip'] ?? '', $hPct, $dPct, $aPct);
    [$pickLabel, $pickPct] = ajp_tip_label($f['tip'] ?? '', $hPct, $dPct, $aPct);
    $conf = ajp_tip_confidence_level($pickPct);
    $avgConf += $pickPct;
    $tipCodes[] = $code;

    $status = strtoupper((string) ($f['status_short'] ?? 'NS'));
    $isDone = in_array($status, ['FT', 'AET', 'PEN'], true);
    $isLive = in_array($status, ['1H', '2H', 'HT', 'LIVE', 'ET', 'P'], true);
    $gh = $f['goals_home'] ?? null;
    $ga = $f['goals_away'] ?? null;
    $hasScore = $gh !== null && $gh !== '' && $ga !== null && $ga !== '';

    if ($isDone) {
        $completed++;
        if ($hasScore) {
            $winner = ((int) $gh > (int) $ga) ? '1' : (((int) $ga > (int) $gh) ? '2' : 'X');
            if ($winner === $code) {
                $correct++;
            }
        }
    }

    $dt = null;
    $rawDate = (string) ($f['date'] ?? '');
    if ($rawDate !== '') {
        $dt = DateTime::createFromFormat('m/d/Y H:i', $rawDate) ?: date_create($rawDate) ?: null;
    }
    if ($dt) {
        if ($firstKick === null || $dt < $firstKick) {
            $firstKick = clone $dt;
        }
        if ($lastKick === null || $dt > $lastKick) {
            $lastKick = clone $dt;
        }
    }

    $predText = $code === '1' ? 'Home' : ($code === '2' ? 'Away' : 'Draw');
    $pickOdd = $code === '1' ? ($f['bets_home'] ?? '—') : ($code === 'X' ? ($f['bets_draw'] ?? '—') : ($f['bets_away'] ?? '—'));

    $rows[] = [
        'pos' => $pos ?: (count($rows) + 1),
        'home' => $home,
        'away' => $away,
        'league' => $league,
        'code' => $code,
        'predText' => $predText,
        'pickLabel' => $pickLabel,
        'pickPct' => $pickPct,
        'conf' => $conf,
        'hPct' => $hPct,
        'dPct' => $dPct,
        'aPct' => $aPct,
        'oh' => $f['bets_home'] ?? '—',
        'od' => $f['bets_draw'] ?? '—',
        'oa' => $f['bets_away'] ?? '—',
        'pickOdd' => $pickOdd,
        'status' => $status,
        'isDone' => $isDone,
        'isLive' => $isLive,
        'hasScore' => $hasScore,
        'gh' => $gh,
        'ga' => $ga,
        'time' => $dt ? $dt->format('H:i') : '',
        'dateShort' => $dt ? $dt->format('D j M') : '',
        'correct' => $isDone && $hasScore ? ((((int) $gh > (int) $ga) ? '1' : (((int) $ga > (int) $gh) ? '2' : 'X')) === $code) : null,
    ];
}

$avgConf = $gameCount > 0 ? round($avgConf / $gameCount) : 0;
$overallConf = ajp_tip_confidence_level((float) $avgConf);
$statusLabel = 'Not Started';
$statusClass = 'not-started';
if ($completed > 0 && $completed >= $gameCount && $gameCount > 0) {
    $statusLabel = 'Completed';
    $statusClass = 'completed';
} elseif ($completed > 0 || array_filter($rows, static fn ($r) => $r['isLive'])) {
    $statusLabel = 'In Progress';
    $statusClass = 'in-progress';
}

$dateRange = '';
$kickoffLine = '';
if ($firstKick && $lastKick) {
    if ($firstKick->format('Y-m-d') === $lastKick->format('Y-m-d')) {
        $dateRange = $firstKick->format('D j M');
    } else {
        $dateRange = $firstKick->format('j M') . ' – ' . $lastKick->format('j M');
    }
    $kickoffLine = $dateRange . ' · First kick-off ' . $firstKick->format('H:i');
}

$homeTips = count(array_filter($tipCodes, static fn ($c) => $c === '1'));
$drawTips = count(array_filter($tipCodes, static fn ($c) => $c === 'X'));
$awayTips = count(array_filter($tipCodes, static fn ($c) => $c === '2'));
$progPct = $gameCount > 0 ? round(($completed / $gameCount) * 100) : 0;
$shortName = preg_replace('/\s+Predictions$/i', '', $defaultTitle) ?: $defaultTitle;
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($pageTitle) ?></title>
<meta name="description" content="<?= htmlspecialchars($metaDescription) ?>">
<?php if ($metaKeywords !== ''): ?>
<meta name="keywords" content="<?= htmlspecialchars($metaKeywords) ?>">
<?php endif; ?>
<link rel="canonical" href="<?= htmlspecialchars($canonical) ?>">
<meta property="og:type" content="website">
<meta property="og:title" content="<?= htmlspecialchars($h1) ?> | AllJackpotPredictions">
<meta property="og:description" content="<?= htmlspecialchars($metaDescription) ?>">
<meta property="og:url" content="<?= htmlspecialchars($canonical) ?>">
<meta property="og:site_name" content="AllJackpotPredictions">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= htmlspecialchars($h1) ?> | AllJackpotPredictions">
<meta name="twitter:description" content="<?= htmlspecialchars($metaDescription) ?>">
<meta name="theme-color" content="#c9a227">
<link rel="icon" href="/img/favicon.svg" type="image/svg+xml">
<link rel="stylesheet" href="/styles.css">
<style>
/* ── Jackpot detail ── */
.ajp-jd-hero {
  background:
    radial-gradient(ellipse 80% 60% at 10% 0%, rgba(201,162,39,.18), transparent 55%),
    linear-gradient(160deg, #06261c 0%, #0b3d2e 70%, #0f4f3a 100%);
  color: #fff; padding: 36px 0 30px; border-bottom: 2px solid var(--gold);
}
.ajp-jd-wrap { max-width: 1080px; margin: 0 auto; padding: 0 24px; }
.ajp-jd-crumb {
  font-family: var(--fmono); font-size: .72rem; font-weight: 600;
  color: rgba(255,255,255,.5); margin-bottom: 12px; letter-spacing: .04em;
}
.ajp-jd-crumb a { color: #e8d48b; text-decoration: none; }
.ajp-jd-crumb a:hover { color: #fff; }
.ajp-jd-hero h1 {
  font-family: var(--fdisp); font-size: clamp(1.7rem, 3.6vw, 2.5rem);
  font-weight: 800; margin: 0 0 10px; letter-spacing: -.025em; line-height: 1.12;
}
.ajp-jd-hero > .ajp-jd-wrap > p {
  margin: 0 0 22px; color: rgba(255,255,255,.68); font-size: .95rem;
  max-width: 58ch; line-height: 1.65;
}
.ajp-jd-hero-stats { display: flex; gap: 32px; flex-wrap: wrap; }
.ajp-jd-hero-stats .stat-num {
  font-family: var(--fdisp); font-size: 1.75rem; font-weight: 800; color: var(--gold); line-height: 1;
}
.ajp-jd-hero-stats .stat-label {
  font-family: var(--fmono); font-size: .68rem; font-weight: 600;
  text-transform: uppercase; letter-spacing: .06em; color: rgba(255,255,255,.5); margin-top: 5px;
}

.ajp-jd-body { max-width: 1080px; margin: 0 auto; padding: 28px 24px 72px; }

.ajp-jd-toolbar {
  display: flex; align-items: center; justify-content: space-between;
  gap: 12px; flex-wrap: wrap; margin-bottom: 18px;
}
.ajp-jd-toolbar h2 {
  font-family: var(--fdisp); font-size: 1.2rem; margin: 0;
  letter-spacing: -.015em; color: var(--ink);
}
.ajp-jd-toolbar a {
  font-family: var(--fmono); font-size: .72rem; font-weight: 700;
  color: var(--gold); text-decoration: none; letter-spacing: .03em;
}
.ajp-jd-toolbar a:hover { color: var(--navy); }

/* Slim overview strip */
.jp-overview {
  display: grid; grid-template-columns: 1.4fr repeat(4, 1fr);
  gap: 1px; background: var(--border);
  border: 1px solid var(--border); border-radius: 12px;
  overflow: hidden; margin-bottom: 20px;
}
.jp-ov-main {
  background: var(--paper2); padding: 16px 18px;
  display: flex; flex-direction: column; justify-content: center; gap: 8px;
}
.jp-ov-title {
  font-family: var(--fdisp); font-size: 1.05rem; font-weight: 800;
  color: var(--ink); letter-spacing: -.015em;
}
.jp-ov-sub { font-size: .78rem; color: var(--muted); line-height: 1.4; }
.jp-ov-badges { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 2px; }
.jp-badge {
  font-family: var(--fmono); font-size: .65rem; font-weight: 700;
  padding: 3px 10px; border-radius: 4px; letter-spacing: .04em; text-transform: uppercase;
  background: rgba(11,61,46,.08); color: #0b3d2e;
}
.jp-badge.status-not-started { background: #0b3d2e; color: #fff; }
.jp-badge.status-in-progress { background: #c9a227; color: #06261c; }
.jp-badge.status-completed { background: #6b7280; color: #fff; }
.jp-ov-cell {
  background: var(--paper); padding: 14px 12px; text-align: center;
  display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 4px;
}
.jp-ov-val {
  font-family: var(--fdisp); font-size: 1.35rem; font-weight: 800;
  color: var(--ink); line-height: 1; letter-spacing: -.02em;
}
.jp-ov-val.gold { color: var(--gold); }
.jp-ov-lbl {
  font-family: var(--fmono); font-size: .62rem; font-weight: 600;
  color: var(--muted); text-transform: uppercase; letter-spacing: .05em;
}
.jp-ov-progress {
  grid-column: 1 / -1; background: var(--paper2); padding: 10px 16px;
  display: flex; align-items: center; gap: 12px;
}
.jp-ov-progress span {
  font-family: var(--fmono); font-size: .68rem; font-weight: 600;
  color: var(--muted); white-space: nowrap;
}
.jp-prog-track { flex: 1; height: 5px; background: rgba(11,61,46,.1); border-radius: 3px; overflow: hidden; }
.jp-prog-fill { height: 100%; background: #0b3d2e; border-radius: 3px; }

/* Tips table */
.jp-board {
  background: var(--paper); border: 1px solid var(--border);
  border-radius: 12px; overflow: hidden; margin-bottom: 28px;
}
.jp-board-hd {
  display: grid;
  grid-template-columns: 44px 72px minmax(0, 1.6fr) minmax(0, .9fr) 88px 168px 72px;
  gap: 8px; align-items: center;
  padding: 10px 16px;
  background: rgba(11,61,46,.05);
  border-bottom: 1px solid var(--border);
  font-family: var(--fmono); font-size: .62rem; font-weight: 700;
  letter-spacing: .07em; text-transform: uppercase; color: var(--muted);
}
.jp-row {
  display: grid;
  grid-template-columns: 44px 72px minmax(0, 1.6fr) minmax(0, .9fr) 88px 168px 72px;
  gap: 8px; align-items: center;
  padding: 13px 16px;
  border-bottom: 1px solid var(--border);
  transition: background .15s;
}
.jp-row:last-child { border-bottom: none; }
.jp-row:hover { background: rgba(11,61,46,.03); }
.jp-row.is-live { background: rgba(201,162,39,.06); }
.jp-row.is-done { opacity: .92; }

.jp-num {
  width: 28px; height: 28px; border-radius: 6px;
  background: rgba(11,61,46,.08); color: #0b3d2e;
  font-family: var(--fdisp); font-size: .85rem; font-weight: 800;
  display: inline-flex; align-items: center; justify-content: center;
}
.jp-kick {
  display: flex; flex-direction: column; gap: 2px;
  font-family: var(--fmono); font-size: .72rem; font-weight: 600; color: var(--muted);
}
.jp-kick strong { color: var(--ink); font-size: .82rem; font-weight: 700; }

.jp-match { min-width: 0; }
.jp-match-name {
  font-family: var(--fdisp); font-size: .95rem; font-weight: 700;
  color: var(--ink); letter-spacing: -.015em; line-height: 1.25;
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.jp-match-vs { color: var(--muted); font-weight: 500; }
.jp-score {
  display: inline-flex; align-items: center; gap: 4px;
  margin-top: 3px; font-family: var(--fmono); font-size: .7rem; font-weight: 700;
}
.jp-score.ok { color: var(--green); }
.jp-score.bad { color: #b45309; }
.jp-live-tag {
  display: inline-block; margin-left: 6px;
  font-size: .58rem; font-weight: 800; letter-spacing: .06em;
  color: #b45309; text-transform: uppercase;
}

.jp-league {
  font-size: .78rem; font-weight: 600; color: var(--muted);
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}

.jp-tip {
  display: inline-flex; align-items: center; justify-content: center; gap: 6px;
  min-width: 72px; padding: 6px 10px; border-radius: 6px;
  font-family: var(--fdisp); font-size: .82rem; font-weight: 800;
  background: #0b3d2e; color: #fff; letter-spacing: -.01em;
}
.jp-tip.draw { background: #c9a227; color: #06261c; }
.jp-tip.away { background: #146048; color: #fff; }
.jp-tip-code {
  opacity: .75; font-size: .7rem; font-weight: 700;
}

.jp-odds {
  display: grid; grid-template-columns: repeat(3, 1fr); gap: 4px;
}
.jp-od {
  text-align: center; padding: 5px 2px; border-radius: 5px;
  background: var(--paper2); border: 1px solid transparent;
  font-family: var(--fmono); font-size: .62rem; color: var(--muted);
}
.jp-od strong {
  display: block; font-family: var(--fdisp); font-size: .88rem;
  font-weight: 800; color: var(--ink); line-height: 1.15; margin-top: 1px;
}
.jp-od.on {
  background: rgba(201,162,39,.14);
  border-color: rgba(201,162,39,.45);
}
.jp-od.on strong { color: #8a6d12; }

.jp-conf {
  text-align: center; font-family: var(--fmono);
  font-size: .78rem; font-weight: 800; color: var(--ink);
}
.jp-conf small {
  display: block; font-size: .58rem; font-weight: 600;
  text-transform: uppercase; letter-spacing: .05em; color: var(--muted); margin-top: 2px;
}

.ajp-jd-empty {
  padding: 36px 24px; text-align: center;
  border: 1px dashed var(--border2); border-radius: 12px;
  color: var(--muted); margin-bottom: 24px; background: var(--paper);
}
.ajp-jd-empty a { color: var(--gold); font-weight: 700; }

/* Compact picks strip */
.jp-slip {
  background: #0b3d2e; border-radius: 12px; overflow: hidden;
  margin-bottom: 36px; color: #fff;
}
.jp-slip-hd {
  padding: 14px 18px;
  font-family: var(--fdisp); font-weight: 800; font-size: 1rem;
  border-bottom: 1px solid rgba(255,255,255,.1); color: #e8d48b;
  display: flex; align-items: center; justify-content: space-between; gap: 10px;
}
.jp-slip-hd span {
  font-family: var(--fmono); font-size: .68rem; font-weight: 600;
  color: rgba(255,255,255,.45); letter-spacing: .04em;
}
.jp-slip-grid {
  display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
}
.jp-slip-item {
  display: flex; align-items: center; gap: 10px;
  padding: 11px 16px; border-bottom: 1px solid rgba(255,255,255,.07);
  border-right: 1px solid rgba(255,255,255,.07);
}
.jp-slip-n {
  font-family: var(--fmono); font-size: .68rem; font-weight: 700;
  color: rgba(255,255,255,.4); width: 18px; flex-shrink: 0;
}
.jp-slip-match {
  flex: 1; min-width: 0; font-size: .8rem; color: rgba(255,255,255,.8);
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.jp-slip-pick {
  flex-shrink: 0; width: 28px; height: 28px; border-radius: 5px;
  display: inline-flex; align-items: center; justify-content: center;
  font-family: var(--fdisp); font-weight: 800; font-size: .9rem;
  background: #c9a227; color: #06261c;
}
.jp-slip-pick.draw { background: rgba(255,255,255,.14); color: #fff; }
.jp-slip-pick.away { background: #146048; color: #fff; }

.ajp-jd-seo {
  margin-top: 8px; padding-top: 28px; border-top: 1px solid var(--border);
  color: var(--body); font-size: .95rem; line-height: 1.7;
}
.ajp-jd-seo h2 {
  font-family: var(--fdisp); font-size: 1.3rem; color: var(--ink);
  margin: 1.5em 0 .5em; letter-spacing: -.015em;
}
.ajp-jd-seo h3 {
  font-family: var(--fdisp); font-size: 1.05rem; color: var(--ink);
  margin: 1.25em 0 .4em;
}
.ajp-jd-seo p { margin: 0 0 1em; }
.ajp-jd-seo ul { margin: 0 0 1em; padding-left: 1.2em; }
.ajp-jd-seo li { margin-bottom: .35em; }
.ajp-jd-seo strong { color: var(--ink); }
.ajp-jd-faq details {
  border: 1px solid var(--border); border-radius: 8px;
  padding: 12px 14px; margin-bottom: 8px; background: var(--paper);
}
.ajp-jd-faq summary {
  cursor: pointer; font-weight: 700; color: var(--ink); font-family: var(--fdisp);
}
.ajp-jd-faq details p { margin: .65em 0 0; }

@media (max-width: 900px) {
  .jp-overview { grid-template-columns: 1fr 1fr; }
  .jp-ov-main { grid-column: 1 / -1; }
  .jp-board-hd { display: none; }
  .jp-row {
    grid-template-columns: 36px 1fr auto;
    grid-template-areas:
      "num match tip"
      "num kick odds"
      "num league conf";
    gap: 6px 10px; padding: 14px;
  }
  .jp-num { grid-area: num; }
  .jp-match { grid-area: match; }
  .jp-tip { grid-area: tip; align-self: start; }
  .jp-kick { grid-area: kick; }
  .jp-odds { grid-area: odds; width: 140px; }
  .jp-league { grid-area: league; }
  .jp-conf { grid-area: conf; text-align: right; }
  .jp-match-name { white-space: normal; }
}
@media (max-width: 520px) {
  .jp-overview { grid-template-columns: 1fr 1fr; }
  .ajp-jd-hero-stats { gap: 20px; }
  .jp-row {
    grid-template-columns: 32px 1fr;
    grid-template-areas:
      "num match"
      "num kick"
      "num league"
      "tip tip"
      "odds odds"
      "conf conf";
  }
  .jp-tip { justify-self: start; }
  .jp-odds { width: 100%; }
  .jp-conf { text-align: left; display: flex; align-items: baseline; gap: 8px; }
  .jp-conf small { display: inline; margin: 0; }
}
</style>
</head>
<body>
<?php include __DIR__ . '/../header.php'; ?>

<section class="ajp-jd-hero">
  <div class="ajp-jd-wrap">
    <div class="ajp-jd-crumb">
      <a href="/jackpot-picks-today">All Jackpots</a> · <?= htmlspecialchars($defaultTitle) ?>
    </div>
    <h1><?= htmlspecialchars($h1) ?></h1>
    <p><?php
      if ($intro !== '') {
          echo htmlspecialchars($intro);
      } elseif ($gameCount > 0) {
          echo 'Free expert predictions for all ' . (int) $gameCount . ' games — with odds, confidence, and a full picks slip.';
      } else {
          echo 'Coupon fixtures will appear when this jackpot is active.';
      }
    ?></p>
    <div class="ajp-jd-hero-stats">
      <div>
        <div class="stat-num"><?= (int) $gameCount ?></div>
        <div class="stat-label">Games</div>
      </div>
      <div>
        <div class="stat-num"><?= $gameCount > 0 ? (int) $avgConf . '%' : '—' ?></div>
        <div class="stat-label">Avg confidence</div>
      </div>
      <div>
        <div class="stat-num">Free</div>
        <div class="stat-label">No login</div>
      </div>
    </div>
  </div>
</section>

<main class="ajp-jd-body">
  <div class="ajp-jd-toolbar">
    <h2>Coupon — <?= (int) $gameCount ?> games</h2>
    <a href="/jackpot-picks-today">← All jackpots</a>
  </div>

  <?php if ($gameCount === 0): ?>
    <div class="ajp-jd-empty">
      No fixtures available for this jackpot right now.
      <br><a href="/jackpot-picks-today">← Back to all jackpots</a>
    </div>
  <?php else: ?>

  <div class="jp-overview" role="region" aria-label="Jackpot overview">
    <div class="jp-ov-main">
      <div class="jp-ov-title"><?= htmlspecialchars($shortName) ?></div>
      <?php if ($kickoffLine !== ''): ?>
        <div class="jp-ov-sub"><?= htmlspecialchars($kickoffLine) ?></div>
      <?php endif; ?>
      <div class="jp-ov-badges">
        <span class="jp-badge status-<?= htmlspecialchars($statusClass) ?>"><?= htmlspecialchars($statusLabel) ?></span>
        <span class="jp-badge"><?= (int) $homeTips ?>H · <?= (int) $drawTips ?>X · <?= (int) $awayTips ?>A</span>
      </div>
    </div>
    <div class="jp-ov-cell">
      <div class="jp-ov-val"><?= (int) $gameCount ?></div>
      <div class="jp-ov-lbl">Games</div>
    </div>
    <div class="jp-ov-cell">
      <div class="jp-ov-val gold"><?= (int) $avgConf ?>%</div>
      <div class="jp-ov-lbl">Confidence</div>
    </div>
    <div class="jp-ov-cell">
      <div class="jp-ov-val"><?= (int) $completed ?>/<?= (int) $gameCount ?></div>
      <div class="jp-ov-lbl">Played</div>
    </div>
    <div class="jp-ov-cell">
      <div class="jp-ov-val"><?= (int) $correct ?></div>
      <div class="jp-ov-lbl">Correct</div>
    </div>
    <?php if ($completed > 0): ?>
    <div class="jp-ov-progress">
      <span>Progress</span>
      <div class="jp-prog-track"><div class="jp-prog-fill" style="width:<?= (int) $progPct ?>%"></div></div>
      <span><?= (int) $progPct ?>%</span>
    </div>
    <?php endif; ?>
  </div>

  <div class="jp-board" role="table" aria-label="Jackpot tips">
    <div class="jp-board-hd" role="row">
      <div>#</div>
      <div>Kick-off</div>
      <div>Match</div>
      <div>League</div>
      <div>Tip</div>
      <div>Odds</div>
      <div>Conf</div>
    </div>
    <?php foreach ($rows as $r):
      $pickClass = $r['code'] === '1' ? 'home' : ($r['code'] === '2' ? 'away' : 'draw');
      $rowClass = 'jp-row';
      if ($r['isLive']) {
          $rowClass .= ' is-live';
      }
      if ($r['isDone']) {
          $rowClass .= ' is-done';
      }
    ?>
    <div class="<?= $rowClass ?>" role="row">
      <div class="jp-num"><?= (int) $r['pos'] ?></div>
      <div class="jp-kick">
        <?php if ($r['time'] !== ''): ?>
          <strong><?= htmlspecialchars($r['time']) ?></strong>
        <?php endif; ?>
        <?php if ($r['dateShort'] !== ''): ?>
          <span><?= htmlspecialchars($r['dateShort']) ?></span>
        <?php else: ?>
          <span>—</span>
        <?php endif; ?>
      </div>
      <div class="jp-match">
        <div class="jp-match-name">
          <?= htmlspecialchars($r['home']) ?>
          <span class="jp-match-vs">vs</span>
          <?= htmlspecialchars($r['away']) ?>
          <?php if ($r['isLive']): ?><span class="jp-live-tag">Live</span><?php endif; ?>
        </div>
        <?php if ($r['hasScore'] && ($r['isDone'] || $r['isLive'])): ?>
          <div class="jp-score <?= $r['correct'] === false ? 'bad' : ($r['correct'] === true ? 'ok' : '') ?>">
            <?= htmlspecialchars((string) $r['gh']) ?>–<?= htmlspecialchars((string) $r['ga']) ?>
            <?php if ($r['correct'] === true): ?> · Won<?php elseif ($r['correct'] === false): ?> · Lost<?php endif; ?>
          </div>
        <?php endif; ?>
      </div>
      <div class="jp-league"><?= htmlspecialchars($r['league'] ?: 'Football') ?></div>
      <div>
        <span class="jp-tip <?= $pickClass ?>">
          <span class="jp-tip-code"><?= htmlspecialchars($r['code']) ?></span>
          <?= htmlspecialchars($r['predText']) ?>
        </span>
      </div>
      <div class="jp-odds">
        <div class="jp-od <?= $r['code'] === '1' ? 'on' : '' ?>">
          1<strong><?= htmlspecialchars((string) $r['oh']) ?></strong>
        </div>
        <div class="jp-od <?= $r['code'] === 'X' ? 'on' : '' ?>">
          X<strong><?= htmlspecialchars((string) $r['od']) ?></strong>
        </div>
        <div class="jp-od <?= $r['code'] === '2' ? 'on' : '' ?>">
          2<strong><?= htmlspecialchars((string) $r['oa']) ?></strong>
        </div>
      </div>
      <div class="jp-conf">
        <?= (int) round($r['pickPct']) ?>%
        <small><?= htmlspecialchars(ucfirst($r['conf'])) ?></small>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

  <div class="jp-slip">
    <div class="jp-slip-hd">
      Full picks slip
      <span><?= (int) $gameCount ?> selections</span>
    </div>
    <div class="jp-slip-grid">
      <?php foreach ($rows as $r):
        $pickClass = $r['code'] === '1' ? 'home' : ($r['code'] === '2' ? 'away' : 'draw');
      ?>
        <div class="jp-slip-item">
          <span class="jp-slip-n"><?= (int) $r['pos'] ?></span>
          <span class="jp-slip-match"><?= htmlspecialchars($r['home']) ?> vs <?= htmlspecialchars($r['away']) ?></span>
          <span class="jp-slip-pick <?= $pickClass ?>"><?= htmlspecialchars($r['code']) ?></span>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <?php endif; ?>

  <?php if ($seoHtml !== ''): ?>
    <section class="ajp-jd-seo">
      <?= $seoHtml ?>
    </section>
  <?php endif; ?>
</main>

<?php include __DIR__ . '/../footer.php'; ?>
<button class="btt" id="btt" aria-label="Back to top" onclick="window.scrollTo({top:0,behavior:'smooth'})">↑</button>
<script src="/main.js"></script>
</body>
</html>
