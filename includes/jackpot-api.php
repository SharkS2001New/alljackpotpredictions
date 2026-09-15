<?php
/**
 * Jackpot helpers — Bao-style pitchnewdb selections (same stack as baopredictions).
 * Falls back to commercial HTTP API only if DB returns empty.
 */

require_once __DIR__ . '/api-curl.php';

if (!defined('AJP_JACKPOT_API_BASE')) {
    define('AJP_JACKPOT_API_BASE', 'https://api.pitchpredictions.com/api');
    define('AJP_JACKPOT_API_FALLBACK', 'https://api.alljackpotpredictions.com/api');
    define('AJP_JACKPOT_API_KEY', getenv('JACKPOT_API_KEY') ?: 'jp_shared_8KxQm2NvR9pLwT4yHcF6uA1eZbD3sG7j');
    define('AJP_JACKPOT_ORIGIN', getenv('JACKPOT_API_ORIGIN') ?: 'https://www.alljackpotpredictions.com');
}

/** Catalog of all jackpot slugs (active + historical). */
function ajp_jackpot_all_slugs() {
    return [
        'sportpesa-mega-jackpot-predictions',
        'sportpesa-midweek-jackpot-predictions',
        'sportpesa-supa-jackpot-17-predictions-tz',
        'sportpesa-supa-jackpot-13-predictions-tz',
        'betika-midweek-jackpot-predictions',
        'betika-kitonga-jackpot-tz',
        'mozzart-super-grand-jackpot-predictions',
        'mozzart-super-daily-jackpot-predictions',
        'shabiki-jackpot-predictions',
        'odibet-laki-tatu-daily-jackpot-predictions',
        'sportybet-jackpot-predictions',
        'betpawa-pick13-jackpot-predictions-uganda',
        'betpawa-pick17-jackpot-predictions-uganda',
        'betpawa-pick13-jackpot-predictions-nigeria',
        'betpawa-pick17-jackpot-predictions-nigeria',
        'betpawa-pick13-jackpot-predictions-kenya',
        'betpawa-pick17-jackpot-predictions-kenya',
        'betpawa-pick13-jackpot-predictions-tanzania',
        'betpawa-pick17-jackpot-predictions-tanzania',
        'betpawa-pick13-jackpot-predictions-zambia',
        'betpawa-pick17-jackpot-predictions-zambia',
        'betpawa-pick13-jackpot-predictions-ghana',
        'betpawa-pick17-jackpot-predictions-ghana',
        'betpawa-pick13-jackpot-predictions-cameroon',
        'betpawa-pick17-jackpot-predictions-cameroon',
        'betpawa-pick13-jackpot-predictions-dr-congo',
        'betpawa-pick17-jackpot-predictions-dr-congo',
        'betway-jackpot-predictions-uganda',
        'betway-jackpot-predictions-kenya',
        'betway-jackpot-predictions-tanzania',
        '22-bet-toto-jackpot-predictions',
        'bet9ja-super9ja-jackpot-predictions',
        '1xbet-toto-15-jackpot-predictions',
        'merrybet-jackpot-predictions',
        'betking-jackpot-predictions',
        'betsafe-daily-jackpot-predictions',
        'betsafe-mita-tano-jackpot-predictions',
    ];
}

function ajp_jackpot_name_to_slug($name) {
    static $map = [
        'Sportpesa Mega Jackpot' => 'sportpesa-mega-jackpot-predictions',
        'Sportpesa Midweek Jackpot' => 'sportpesa-midweek-jackpot-predictions',
        'Betika Mega Jackpot' => 'betika-grand-jackpot-predictions',
        'Betika Midweek Jackpot' => 'betika-midweek-jackpot-predictions',
        'Odibet Laki Tatu Jackpot' => 'odibet-laki-tatu-daily-jackpot-predictions',
        'Mozzart Super Daily Jackpot' => 'mozzart-super-daily-jackpot-predictions',
        'Mozzart Bet Grand Jackpot' => 'mozzart-super-grand-jackpot-predictions',
        'Shabiki Midweek Jackpot' => 'shabiki-jackpot-predictions',
        'Sporty bet Jackpot' => 'sportybet-jackpot-predictions',
        'Betlion Daily Jackpot' => 'betlion-daily-jp-jackpot-predictions',
        'Betika Kitonga Tanzania' => 'betika-kitonga-jackpot-tz',
        'Sportpesa Midweek Tanzania Jackpot' => 'sportpesa-supa-jackpot-13-predictions-tz',
        'Sportpesa Supa Jackpot Tanzania' => 'sportpesa-supa-jackpot-17-predictions-tz',
        'Betway Uganda Jackpot' => 'betway-jackpot-predictions-uganda',
        'Betway Kenya Jackpot' => 'betway-jackpot-predictions-kenya',
        'Betway Tanzania Jackpot' => 'betway-jackpot-predictions-tanzania',
        'Betsafe Daily Jackpot' => 'betsafe-daily-jackpot-predictions',
        'Betsafe Mita Tano Jackpot' => 'betsafe-mita-tano-jackpot-predictions',
        '22 Bet Toto Jackpot' => '22-bet-toto-jackpot-predictions',
        'Bet9ja Supa9ja Jackpot' => 'bet9ja-super9ja-jackpot-predictions',
        '1XBet Toto 15 Jackpot' => '1xbet-toto-15-jackpot-predictions',
        'Merrybet Jackpot' => 'merrybet-jackpot-predictions',
        'MerryBet Jackpot' => 'merrybet-jackpot-predictions',
        'BetKing Jackpot' => 'betking-jackpot-predictions',
        'Betpawa Pick13 Jackpot' => 'betpawa-pick13-jackpot-predictions-kenya',
        'Betpawa Pick 17 Jackpot' => 'betpawa-pick17-jackpot-predictions-kenya',
    ];
    return $map[$name] ?? '';
}

function ajp_jackpot_slug_to_db_name($slug) {
    static $map = [
        'sportpesa-mega-jackpot-predictions' => 'Sportpesa Mega Jackpot',
        'sportpesa-midweek-jackpot-predictions' => 'Sportpesa Midweek Jackpot',
        'betika-grand-jackpot-predictions' => 'Betika Mega Jackpot',
        'betika-midweek-jackpot-predictions' => 'Betika Midweek Jackpot',
        'odibet-laki-tatu-daily-jackpot-predictions' => 'Odibet Laki Tatu Jackpot',
        'odibets-laki-tatu-predictions' => 'Odibet Laki Tatu Jackpot',
        'mozzart-super-daily-jackpot-predictions' => 'Mozzart Super Daily Jackpot',
        'mozzart-super-grand-jackpot-predictions' => 'Mozzart Bet Grand Jackpot',
        'shabiki-jackpot-predictions' => 'Shabiki Midweek Jackpot',
        'sportybet-jackpot-predictions' => 'Sporty bet Jackpot',
        'sportybet-daily-jackpot-predictions' => 'Sporty bet Jackpot',
        'betlion-daily-jp-jackpot-predictions' => 'Betlion Daily Jackpot',
        'betika-kitonga-jackpot-tz' => 'Betika Kitonga Tanzania',
        'sportpesa-supa-jackpot-13-predictions-tz' => 'Sportpesa Midweek Tanzania Jackpot',
        'sportpesa-supa-jackpot-17-predictions-tz' => 'Sportpesa Supa Jackpot Tanzania',
        'betway-jackpot-predictions-uganda' => 'Betway Uganda Jackpot',
        'betway-jackpot-predictions-kenya' => 'Betway Kenya Jackpot',
        'betway-jackpot-predictions-tanzania' => 'Betway Tanzania Jackpot',
        'betsafe-daily-jackpot-predictions' => 'Betsafe Daily Jackpot',
        'betsafe-mita-tano-jackpot-predictions' => 'Betsafe Mita Tano Jackpot',
        '22-bet-toto-jackpot-predictions' => '22 Bet Toto Jackpot',
        'bet9ja-super9ja-jackpot-predictions' => 'Bet9ja Supa9ja Jackpot',
        '1xbet-toto-15-jackpot-predictions' => '1XBet Toto 15 Jackpot',
        'merrybet-jackpot-predictions' => 'MerryBet Jackpot',
        'betking-jackpot-predictions' => 'BetKing Jackpot',
    ];
    return $map[$slug] ?? 'Unknown Jackpot';
}

function ajp_jackpot_slug_to_title($slug) {
    static $map = [
        'sportpesa-mega-jackpot-predictions' => 'Sportpesa Mega Jackpot Predictions',
        'sportpesa-midweek-jackpot-predictions' => 'Sportpesa Midweek Jackpot Predictions',
        'sportpesa-supa-jackpot-17-predictions-tz' => 'Sportpesa Tanzania Supa Jackpot 17 Predictions',
        'sportpesa-supa-jackpot-13-predictions-tz' => 'Sportpesa Tanzania Supa Jackpot 13 Predictions',
        'betika-midweek-jackpot-predictions' => 'Betika Midweek Jackpot Predictions',
        'betika-grand-jackpot-predictions' => 'Betika Grand Jackpot Predictions',
        'betika-kitonga-jackpot-tz' => 'Betika Kitonga Jackpot Tanzania',
        'mozzart-super-grand-jackpot-predictions' => 'Mozzart Super Grand Jackpot Predictions',
        'mozzart-super-daily-jackpot-predictions' => 'Mozzart Super Daily Jackpot Predictions',
        'shabiki-jackpot-predictions' => 'Shabiki Jackpot Predictions',
        'odibet-laki-tatu-daily-jackpot-predictions' => 'Odibet Laki Tatu Daily Jackpot Predictions',
        'sportybet-jackpot-predictions' => 'Sportybet Jackpot Predictions',
        'betway-jackpot-predictions-uganda' => 'Betway Jackpot Predictions Uganda',
        'betway-jackpot-predictions-kenya' => 'Betway Jackpot Predictions Kenya',
        'betway-jackpot-predictions-tanzania' => 'Betway Jackpot Predictions Tanzania',
        '22-bet-toto-jackpot-predictions' => '22 Bet Toto Jackpot Predictions',
        'bet9ja-super9ja-jackpot-predictions' => 'Bet9ja Super9ja Jackpot Predictions',
        '1xbet-toto-15-jackpot-predictions' => '1XBet Toto 15 Jackpot Predictions',
        'merrybet-jackpot-predictions' => 'MerryBet Jackpot Predictions',
        'betking-jackpot-predictions' => 'BetKing Jackpot Predictions',
        'betsafe-daily-jackpot-predictions' => 'Betsafe Daily Jackpot Predictions',
        'betsafe-mita-tano-jackpot-predictions' => 'Betsafe Mita Tano Jackpot Predictions',
    ];
    return $map[$slug] ?? ucwords(str_replace('-', ' ', $slug));
}

/** @deprecated commercial HTTP — kept as fallback */
function ajp_jackpot_request($path) {
    $bases = [AJP_JACKPOT_API_BASE];
    if (defined('AJP_JACKPOT_API_FALLBACK') && AJP_JACKPOT_API_FALLBACK !== AJP_JACKPOT_API_BASE) {
        $bases[] = AJP_JACKPOT_API_FALLBACK;
    }
    $headers = [
        'Content-Type: application/json; charset=UTF-8',
        'Origin: ' . AJP_JACKPOT_ORIGIN,
        'X-Jackpot-Client: server',
        'X-Jackpot-Key: ' . AJP_JACKPOT_API_KEY,
        'X-Jackpot-Site: ' . AJP_JACKPOT_ORIGIN,
    ];
    foreach ($bases as $base) {
        $url = rtrim($base, '/') . '/' . ltrim($path, '/');
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER => $headers,
        ]);
        $body = curl_exec($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($code === 200 && $body) {
            $json = json_decode($body, true);
            if (is_array($json)) {
                return $json;
            }
        }
    }
    return null;
}

function ajp_percent($value): float {
    if (is_numeric($value)) {
        return (float) $value;
    }
    return (float) preg_replace('/[^0-9.]/', '', (string) $value);
}

function ajp_tip_code($tip, $homePct, $drawPct, $awayPct): string {
    $tip = trim((string) $tip);
    if ($tip === '1' || strcasecmp($tip, 'Home') === 0) {
        return '1';
    }
    if ($tip === 'X' || strcasecmp($tip, 'Draw') === 0) {
        return 'X';
    }
    if ($tip === '2' || strcasecmp($tip, 'Away') === 0) {
        return '2';
    }
    $h = ajp_percent($homePct);
    $d = ajp_percent($drawPct);
    $a = ajp_percent($awayPct);
    if ($h >= $d && $h >= $a) {
        return '1';
    }
    if ($d >= $h && $d >= $a) {
        return 'X';
    }
    return '2';
}

function ajp_tip_confidence_level(float $pct): string {
    if ($pct >= 60) {
        return 'high';
    }
    if ($pct >= 45) {
        return 'medium';
    }
    return 'low';
}

function ajp_fetch_active_jackpots() {
    // Prefer Bao-style hub from pitchnewdb
    $hub = ajp_curl_api('/api/jackpot-predictions');
    if (is_array($hub) && !empty($hub['jackpots']) && is_array($hub['jackpots'])) {
        $list = [];
        foreach ($hub['jackpots'] as $jp) {
            if (!is_array($jp)) {
                continue;
            }
            $name = (string) ($jp['jackpot_name'] ?? $jp['name'] ?? $jp['label'] ?? '');
            $slug = ajp_jackpot_name_to_slug($name);
            if ($slug === '' && !empty($jp['slug'])) {
                $slug = (string) $jp['slug'];
            }
            $list[] = [
                'jackpot_name' => $name !== '' ? $name : 'Jackpot',
                'slug' => $slug,
                'title' => ($name !== '' ? $name : 'Jackpot') . ' Predictions',
                'total_games' => (int) ($jp['total_games'] ?? $jp['games_count'] ?? $jp['count'] ?? 0),
                'completed_games' => (int) ($jp['completed_games'] ?? 0),
                'confidence_level' => $jp['confidence_level'] ?? $jp['confidence'] ?? null,
                'avg_confidence' => $jp['avg_confidence'] ?? null,
                'total_votes' => $jp['total_votes'] ?? null,
                'start_date_formatted' => $jp['start_date_formatted'] ?? $jp['start_date'] ?? '',
                'end_date_formatted' => $jp['end_date_formatted'] ?? $jp['end_date'] ?? '',
                'start_datetime_formatted' => $jp['start_datetime_formatted'] ?? '',
                'has_started' => !empty($jp['has_started']),
            ];
        }
        if ($list !== []) {
            return $list;
        }
    }

    // Fallback: commercial enhanced endpoint
    $json = ajp_jackpot_request('fetch_active_jackpots_enhanced');
    if (!$json || empty($json['status'])) {
        return [];
    }
    $list = $json['data'] ?? [];
    foreach ($list as &$jp) {
        $jp['slug'] = ajp_jackpot_name_to_slug($jp['jackpot_name'] ?? '') ?: '';
        $jp['title'] = ($jp['jackpot_name'] ?? 'Jackpot') . ' Predictions';
    }
    unset($jp);
    return $list;
}

function ajp_fetch_jackpot_fixtures($dbNameOrSlug) {
    $name = (string) $dbNameOrSlug;
    $slug = ajp_jackpot_name_to_slug($name);
    if ($slug === '' && str_contains($name, '-')) {
        $slug = $name;
        $name = ajp_jackpot_slug_to_db_name($slug);
    }

    // Primary: Bao selections via PageApiService
    if ($slug !== '') {
        $sheet = ajp_fetch_jackpot_sheet($slug);
        if ($sheet['ok'] && $sheet['count'] > 0) {
            return $sheet['fixtures'];
        }
    }

    // Fallback: commercial HTTP
    $json = ajp_jackpot_request('fetch_jackpot_fixtures_by_name?jackpot_name=' . rawurlencode($name));
    if (!$json || empty($json['status'])) {
        return [];
    }
    return $json['data'] ?? [];
}

function ajp_jackpot_status(array $jp) {
    $total = (int) ($jp['total_games'] ?? 0);
    $done = (int) ($jp['completed_games'] ?? 0);
    if ($total > 0 && $done === $total) {
        return 'Completed';
    }
    if (!empty($jp['has_started'])) {
        return 'In Progress';
    }
    $start = $jp['start_datetime_formatted'] ?? '';
    if ($start) {
        $ts = strtotime(str_replace(',', '', $start));
        if ($ts && time() >= $ts) {
            return 'In Progress';
        }
    }
    return 'Not Started';
}

function ajp_tip_label($tip, $homePct, $drawPct, $awayPct) {
    $code = ajp_tip_code($tip, $homePct, $drawPct, $awayPct);
    $map = [
        '1' => ['Home', ajp_percent($homePct)],
        'X' => ['Draw', ajp_percent($drawPct)],
        '2' => ['Away', ajp_percent($awayPct)],
    ];
    return $map[$code];
}
