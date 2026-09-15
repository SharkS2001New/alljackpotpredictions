<?php
/**
 * Show More Matches helpers (Bao/Pitch-style window loading).
 */

require_once __DIR__ . '/api-curl.php';

function ajp_h($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/**
 * @param list<mixed> $items
 * @return array{items:list,total:int,has_more:bool,next_start:int,shown:int}
 */
function ajp_paginate_initial(array $items, int $pageSize = 10): array
{
    $pageSize = max(1, $pageSize);
    $total = count($items);
    $visible = array_slice($items, 0, $pageSize);
    return [
        'items' => $visible,
        'total' => $total,
        'has_more' => $total > $pageSize,
        'next_start' => $pageSize,
        'shown' => count($visible),
    ];
}

function ajp_pred_class(string $type, string $prediction): string
{
    if ($type === '1X2') {
        if (strpos($prediction, 'Home') !== false) {
            return 'pp-home';
        }
        if (strpos($prediction, 'Away') !== false) {
            return 'pp-away';
        }
        if (strpos($prediction, 'Draw') !== false) {
            return 'pp-draw';
        }
    }
    if ($type === 'Over/Under') {
        if (strpos($prediction, 'Over') !== false) {
            return 'pp-over';
        }
        if (strpos($prediction, 'Under') !== false) {
            return 'pp-under';
        }
    }
    if ($type === 'Double Chance') {
        if ($prediction === 'Home or Away Win' || $prediction === 'Home Win or Draw') {
            return 'pp-home';
        }
        if ($prediction === 'Draw or Away Win') {
            return 'pp-away';
        }
    }
    if ($type === 'BTTS') {
        if (strpos($prediction, 'Both') !== false || stripos($prediction, 'Yes') !== false) {
            return 'pp-gg';
        }
        if (strpos($prediction, 'No Goal') !== false || stripos($prediction, 'No') !== false) {
            return 'pp-ng';
        }
    }
    return '';
}

function ajp_pred_short(string $type, string $prediction): string
{
    if ($type === '1X2') {
        if (strpos($prediction, 'Home') !== false) {
            return '1';
        }
        if (strpos($prediction, 'Away') !== false) {
            return '2';
        }
        if (strpos($prediction, 'Draw') !== false) {
            return 'X';
        }
    }
    if ($type === 'Over/Under') {
        if (preg_match('/Over\s+(\d+\.?\d*)/i', $prediction, $m)) {
            return 'Over ' . $m[1];
        }
        if (stripos($prediction, 'Over') !== false) {
            return 'Over';
        }
        if (preg_match('/Under\s+(\d+\.?\d*)/i', $prediction, $m)) {
            return 'Under ' . $m[1];
        }
        if (stripos($prediction, 'Under') !== false) {
            return 'Under';
        }
    }
    if ($type === 'Double Chance') {
        if ($prediction === 'Home or Away Win' || stripos($prediction, '12') !== false) {
            return '12';
        }
        if ($prediction === 'Home Win or Draw' || stripos($prediction, '1X') !== false) {
            return '1X';
        }
        if ($prediction === 'Draw or Away Win' || stripos($prediction, 'X2') !== false) {
            return 'X2';
        }
    }
    if ($type === 'BTTS') {
        if (stripos($prediction, 'Both') !== false || stripos($prediction, 'Yes') !== false) {
            return 'GG';
        }
        if (stripos($prediction, 'No') !== false) {
            return 'NG';
        }
    }
    return substr($prediction, 0, 12);
}

function ajp_tips_link(string $type): string
{
    if ($type === '1X2') {
        return '/1x2-prediction';
    }
    if ($type === 'Over/Under') {
        return '/over-2-5-goals';
    }
    if ($type === 'Double Chance') {
        return '/double-chance-tips';
    }
    if ($type === 'BTTS') {
        return '/btts-tips';
    }
    return '/predictions-today';
}

function ajp_league_class(string $leagueName): string
{
    if (stripos($leagueName, 'Bundesliga') !== false) {
        return 'ld-bl';
    }
    if (stripos($leagueName, 'Premier') !== false) {
        return 'ld-epl';
    }
    if (stripos($leagueName, 'La Liga') !== false) {
        return 'ld-liga';
    }
    if (stripos($leagueName, 'Serie A') !== false) {
        return 'ld-sa';
    }
    if (stripos($leagueName, 'Champions League') !== false) {
        return 'ld-ucl';
    }
    return 'ld-ucl';
}

/**
 * @param array<string,mixed> $fixture TipOracle-shaped
 */
function ajp_render_pt_row(array $fixture): string
{
    $prediction = is_array($fixture['prediction'] ?? null) ? $fixture['prediction'] : [];
    $type = (string) ($prediction['type'] ?? '1X2');
    $predText = (string) ($prediction['prediction'] ?? 'Home Win');
    $confidence = (int) ($prediction['confidence'] ?? 70);
    $home = ajp_h($fixture['home_team']['name'] ?? 'Unknown');
    $away = ajp_h($fixture['away_team']['name'] ?? 'Unknown');
    $country = ajp_h($fixture['league']['country'] ?? 'Football');
    $league = ajp_h($fixture['league']['short_name'] ?? $fixture['league']['name'] ?? 'Football');
    $matchTime = !empty($fixture['date']) ? date('H:i', strtotime((string) $fixture['date'])) : 'TBD';
    $predClass = ajp_pred_class($type, $predText);
    $shortPred = ajp_h(ajp_pred_short($type, $predText));
    $tipsLink = ajp_h(ajp_tips_link($type));
    $leagueKey = ajp_h(strtolower((string) ($fixture['league']['name'] ?? '')));

    return '<tr data-league="' . $leagueKey . '">'
        . '<td class="pt-td-time">' . ajp_h($matchTime) . '</td>'
        . '<td class="pt-td-match">' . $home . ' vs ' . $away . '</td>'
        . '<td class="pt-td-league">' . $country . ' : ' . $league . '</td>'
        . '<td class="pt-td-market">' . ajp_h($type) . '</td>'
        . '<td><span class="pt-pred ' . ajp_h($predClass) . '">' . $shortPred . '</span></td>'
        . '<td><span class="pt-conf">' . $confidence . '%</span></td>'
        . '<td><a class="pt-tips-link" href="' . $tipsLink . '">→ Tips</a></td>'
        . '</tr>';
}

/**
 * @param array<string,mixed> $fixture TipOracle-shaped
 */
function ajp_render_pt_mcard(array $fixture): string
{
    $prediction = is_array($fixture['prediction'] ?? null) ? $fixture['prediction'] : [];
    $type = (string) ($prediction['type'] ?? '1X2');
    $predText = (string) ($prediction['prediction'] ?? 'Home Win');
    $confidence = (int) ($prediction['confidence'] ?? 70);
    $home = ajp_h($fixture['home_team']['name'] ?? 'Unknown');
    $away = ajp_h($fixture['away_team']['name'] ?? 'Unknown');
    $country = ajp_h($fixture['league']['country'] ?? 'Football');
    $league = ajp_h($fixture['league']['short_name'] ?? $fixture['league']['name'] ?? 'Football');
    $matchTime = !empty($fixture['date']) ? date('H:i', strtotime((string) $fixture['date'])) : 'TBD';
    $predClass = ajp_pred_class($type, $predText);
    $shortPred = ajp_h(ajp_pred_short($type, $predText));
    $tipsLink = ajp_h(ajp_tips_link($type));
    $leagueKey = ajp_h(strtolower((string) ($fixture['league']['name'] ?? '')));

    return '<div class="pt-mcard" data-league="' . $leagueKey . '">'
        . '<div class="pt-mcard-top">'
        . '<span class="pt-mcard-league">' . $country . ' · ' . $league . '</span>'
        . '<span class="pt-mcard-time">' . ajp_h($matchTime) . '</span>'
        . '</div>'
        . '<div class="pt-mcard-body">'
        . '<div class="pt-mcard-teams">'
        . '<div class="pt-mcard-match">' . $home . ' vs ' . $away . '</div>'
        . '<div class="pt-mcard-market-lbl">' . ajp_h($type) . '</div>'
        . '</div>'
        . '<div class="pt-mcard-pred">'
        . '<span class="pt-pred ' . ajp_h($predClass) . '">' . $shortPred . '</span>'
        . '<span class="pt-mcard-conf">' . $confidence . '%</span>'
        . '</div>'
        . '</div>'
        . '<div class="pt-mcard-footer">'
        . '<a class="pt-tips-link" href="' . $tipsLink . '">View Tips →</a>'
        . '</div>'
        . '</div>';
}

/**
 * Generic market tip card (.pcard) for load-more appends.
 *
 * @param array<string,mixed> $fixture
 * @param array{market_label?:string} $opts
 */
function ajp_render_pcard(array $fixture, array $opts = []): string
{
    $prediction = is_array($fixture['prediction'] ?? null) ? $fixture['prediction'] : [];
    $predText = (string) ($prediction['prediction'] ?? $fixture['tip'] ?? 'Home Win');
    $confidence = (int) ($prediction['confidence'] ?? $fixture['confidence'] ?? 50);
    $oddRaw = $fixture['odds']['prediction_odd'] ?? $prediction['odds'] ?? null;
    $oddValue = is_numeric($oddRaw) ? (float) $oddRaw : 1.80;
    $home = ajp_h($fixture['home_team']['name'] ?? 'Unknown');
    $away = ajp_h($fixture['away_team']['name'] ?? 'Unknown');
    $leagueName = (string) ($fixture['league']['name'] ?? 'World Football');
    $matchDate = !empty($fixture['date']) ? date('H:i', strtotime((string) $fixture['date'])) : 'TBD';
    $status = (string) ($fixture['status'] ?? 'NS');
    $isLive = ($status === '1H' || $status === '2H' || $status === 'HT');
    $leagueClass = ajp_league_class($leagueName);
    $marketLabel = (string) ($opts['market_label'] ?? 'Prediction');
    $confLevel = $confidence >= 75 ? 'high' : ($confidence >= 60 ? 'medium' : 'low');
    $hour = !empty($fixture['date']) ? (int) date('G', strtotime((string) $fixture['date'])) : 12;

    $scoreInfo = '';
    if ($status === 'FT' && isset($fixture['scores']['home'], $fixture['scores']['away'])) {
        $scoreInfo = 'FT ' . $fixture['scores']['home'] . ':' . $fixture['scores']['away'];
    } elseif ($status === 'NS') {
        $scoreInfo = 'Kick-off ' . $matchDate;
    } else {
        $scoreInfo = ucfirst(strtolower($status));
    }

    $html = '<article class="pcard"'
        . ' data-league="' . ajp_h(strtolower($leagueName)) . '"'
        . ' data-result="' . ajp_h($predText) . '"'
        . ' data-conf="' . ajp_h($confLevel) . '"'
        . ' data-hour="' . $hour . '">';
    $html .= '<div class="pc-row">';
    $html .= '<div class="pc-league-col"></div>';
    $html .= '<div class="pc-body"><div class="pc-info">';
    $html .= '<div class="pc-meta">' . ajp_h($leagueName) . ' · ' . ajp_h($matchDate) . '</div>';
    $html .= '<div class="pc-match">' . $home . ' vs ' . $away . '</div>';
    $html .= '<div class="pc-live-wrap">';
    if ($isLive) {
        $html .= '<span class="live-dot"></span><span class="live-tag" style="color:#ef4444;">LIVE</span> ';
    } else {
        $html .= ajp_h($scoreInfo);
    }
    $html .= '</div></div>';
    $html .= '<div class="pc-pred-col">';
    $html .= '<div class="pc-pred-tag">' . ajp_h($predText) . '</div>';
    $html .= '<div class="pc-market">' . ajp_h($marketLabel) . '</div>';
    $html .= '</div>';
    $html .= '<div class="pc-odds-col">';
    $html .= '<div class="odds-tag">' . number_format($oddValue, 2) . '</div>';
    $html .= '<div class="conf-bar"><span class="cb-pct">' . $confidence . '%</span></div>';
    $html .= '</div></div>';
    $html .= '<div class="pc-toggle"><button type="button" class="pc-tog-btn">▾</button></div>';
    $html .= '</div>';
    $html .= '<div class="pc-expand"><div class="pc-expand-inner"><div>';
    $html .= '<div class="pc-stats">';
    $html .= '<div class="ps-box"><div class="ps-val">' . ajp_h($predText) . '</div><div class="ps-lbl">Prediction</div></div>';
    $html .= '<div class="ps-box"><div class="ps-val">' . $confidence . '%</div><div class="ps-lbl">Confidence</div></div>';
    $html .= '</div>';
    $html .= '<p class="pc-note">AI prediction suggests <strong>' . ajp_h($predText) . '</strong> with '
        . $confidence . '% confidence based on form, head-to-head, and squad data.</p>';
    $html .= '</div><div class="pc-form-panel"><a class="acca-btn" href="/accumulator-tips">+ Add to Acca</a></div>';
    $html .= '</div></div></article>';

    return $html;
}

/**
 * @param array{layout?:string,market_label?:string,league?:string,type?:string,exclude_ft?:bool,market_line?:string,under?:bool,known_total?:int,chunk?:int} $attrs
 */
function ajp_load_more_html(string $apiPage, int $nextStart, array $attrs = []): string
{
    $chunk = max(1, (int) ($attrs['chunk'] ?? 10));
    $layout = (string) ($attrs['layout'] ?? 'pcard');
    $api = '/api/' . ltrim($apiPage, '/');

    $html = '<div class="ajp-load-more-wrap">';
    $html .= '<button type="button" class="ajp-load-more"'
        . ' data-api="' . ajp_h($api) . '"'
        . ' data-start="' . (int) $nextStart . '"'
        . ' data-chunk="' . $chunk . '"'
        . ' data-layout="' . ajp_h($layout) . '"';

    if (!empty($attrs['market_label'])) {
        $html .= ' data-market-label="' . ajp_h((string) $attrs['market_label']) . '"';
    }
    if (!empty($attrs['league'])) {
        $html .= ' data-league="' . ajp_h((string) $attrs['league']) . '"';
    }
    if (!empty($attrs['type'])) {
        $html .= ' data-type="' . ajp_h((string) $attrs['type']) . '"';
    }
    if (!empty($attrs['exclude_ft'])) {
        $html .= ' data-exclude-ft="1"';
    }
    if (!empty($attrs['market_line'])) {
        $html .= ' data-market-line="' . ajp_h((string) $attrs['market_line']) . '"';
    }
    if (!empty($attrs['under'])) {
        $html .= ' data-under="1"';
    }
    if (!empty($attrs['known_total'])) {
        $html .= ' data-known-total="' . (int) $attrs['known_total'] . '"';
    }

    $html .= '>';
    $html .= '<span class="ajp-load-more-label">Show More Matches</span>';
    $html .= '<span class="ajp-load-more-busy" hidden>Loading…</span>';
    $html .= '</button></div>';

    return $html;
}

/**
 * Slice fixtures for Show More (same order as SSR lists).
 *
 * @param array<string,mixed> $opts
 * @return array{ok:bool,fixtures:list<array>,count:int,has_more:bool,next_start:?int,max:int,error:?string}
 */
function ajp_fixtures_window(string $pageHint, int $startIndex, int $endIndex, array $opts = []): array
{
    $fetchOpts = [];
    if (!empty($opts['market_line'])) {
        $fetchOpts['market_line'] = (string) $opts['market_line'];
    }
    if (!empty($opts['under'])) {
        $fetchOpts['under'] = true;
    }

    $fetched = ajp_fetch_fixtures($pageHint, $fetchOpts);
    if (!$fetched['ok']) {
        return [
            'ok' => false,
            'fixtures' => [],
            'count' => 0,
            'has_more' => false,
            'next_start' => null,
            'max' => 0,
            'error' => $fetched['error'] ?? 'unavailable',
        ];
    }

    $fixtures = $fetched['fixtures'];

    if (!empty($opts['exclude_ft'])) {
        $fixtures = array_values(array_filter($fixtures, static function ($f) {
            return ($f['status'] ?? 'NS') !== 'FT';
        }));
    }

    $league = strtolower(trim((string) ($opts['league'] ?? '')));
    if ($league !== '') {
        $fixtures = array_values(array_filter($fixtures, static function ($f) use ($league) {
            return strtolower((string) ($f['league']['name'] ?? '')) === $league;
        }));
    }

    $type = trim((string) ($opts['type'] ?? ''));
    if ($type !== '') {
        $fixtures = array_values(array_filter($fixtures, static function ($f) use ($type) {
            return (($f['prediction']['type'] ?? '') === $type);
        }));
    }

    $total = count($fixtures);
    $startIndex = max(0, $startIndex);
    $endIndex = max($startIndex, $endIndex);
    $want = $endIndex - $startIndex + 1;
    $slice = array_slice($fixtures, $startIndex, $want);
    $hasMore = $total > ($endIndex + 1);

    return [
        'ok' => true,
        'fixtures' => $slice,
        'count' => count($slice),
        'has_more' => $hasMore,
        'next_start' => $hasMore ? ($endIndex + 1) : null,
        'max' => $total,
        'error' => null,
    ];
}
