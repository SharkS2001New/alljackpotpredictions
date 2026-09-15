<?php
/**
 * One API definition per public tips page.
 * Route becomes: GET /api/{key}
 */
return [

    'homepage' => [
        'title' => 'Homepage predictions',
        'day' => 'today',
        'limit' => 18,
        'market' => 'best',
        'min_confidence' => 55,
        'order' => 'confidence_desc',
    ],

    'football-predictions-today' => [
        'title' => "Today's football predictions",
        'day' => 'today',
        'limit' => 150,
        // Mixed best-market board — distinct from /1x2-predictions (pure match-result).
        'market' => 'best',
        'order' => 'confidence_desc',
    ],

    'football-predictions-tomorrow' => [
        'title' => "Tomorrow's football predictions",
        'day' => 'tomorrow',
        'limit' => 150,
        'market' => '1x2',
        'order' => 'kickoff_asc',
    ],

    'football-predictions-yesterday' => [
        'title' => "Yesterday's results",
        'day' => 'yesterday',
        'limit' => 80,
        'market' => '1x2',
        'status' => 'FT',
        'order' => 'kickoff_asc',
    ],

    'weekend-football-predictions' => [
        'title' => 'Weekend football predictions',
        'range' => 'weekend',
        'limit' => 80,
        'market' => '1x2',
        'order' => 'kickoff_asc',
    ],

    'must-win-teams-today' => [
        'title' => 'Must-win teams today',
        'day' => 'today',
        'limit' => 30,
        // Match-result leans only — distinct from sure-bets (all-market high-confidence band).
        'market' => '1x2',
        'min_confidence' => 75,
        'order' => 'confidence_desc',
    ],

    'sure-bets-today' => [
        'title' => 'Sure bets today',
        'day' => 'today',
        'limit' => 30,
        // Highest-confidence band across 1X2 / O/U / BTTS / DC — not the same list as must-win.
        'market' => 'best',
        'min_confidence' => 78,
        'order' => 'confidence_desc',
    ],

    'betnumbers-tips' => [
        'title' => 'BetNumbers tips',
        'day' => 'today',
        'limit' => 40,
        'market' => 'best',
        'min_confidence' => 58,
        'order' => 'confidence_desc',
    ],

    // Brand-comparison landing: same mixed-market engine as BetNumbers / Today.
    'sunpel-prediction' => [
        'title' => 'Sunpel prediction tips today',
        'day' => 'today',
        'limit' => 40,
        'market' => 'best',
        'min_confidence' => 58,
        'order' => 'confidence_desc',
    ],

    '1x2-predictions' => [
        'title' => '1X2 predictions',
        'day' => 'today',
        'limit' => 150,
        'market' => '1x2',
        'order' => 'confidence_desc',
    ],

    'double-chance-predictions' => [
        'title' => 'Double chance predictions',
        'day' => 'today',
        'limit' => 150,
        'market' => 'double_chance',
        'order' => 'confidence_desc',
    ],

    'over-under-predictions' => [
        'title' => 'Over/Under predictions',
        'day' => 'today',
        'limit' => 150,
        'market' => 'over_under',
        'order' => 'confidence_desc',
    ],

    'btts-predictions' => [
        'title' => 'BTTS predictions',
        'day' => 'today',
        'limit' => 150,
        'market' => 'btts',
        'order' => 'confidence_desc',
    ],

    'live-football-predictions' => [
        'title' => 'Live football predictions',
        'day' => 'today',
        'limit' => 80,
        'market' => 'best',
        'live_only' => true,
        'order' => 'kickoff_asc',
    ],

    'ht-ft-predictions' => [
        'title' => 'HT/FT predictions',
        'day' => 'today',
        'limit' => 120,
        'market' => 'ht_ft',
        'order' => 'confidence_desc',
    ],

    'accumulator-tips' => [
        'title' => 'Accumulator tips',
        'day' => 'today',
        'limit' => 80,
        'market' => 'best',
        'min_confidence' => 58,
        'order' => 'confidence_desc',
        'upcoming_only' => true,
        'extra' => 'accumulators',
    ],

    'results' => [
        'title' => 'Settled results',
        // Distinct from Yesterday (single day): rolling week of settled 1X2 tips.
        'lookback_days' => 7,
        'limit' => 200,
        'market' => '1x2',
        'status' => 'FT',
        'order' => 'kickoff_desc',
    ],

    'jackpot-predictions' => [
        'title' => 'Jackpot predictions hub',
        'source' => 'jackpot_hub',
        'limit' => 20,
    ],

    'sportpesa-mega-jackpot-predictions' => [
        'title' => 'SportPesa Mega Jackpot',
        'source' => 'selections',
        'jackpot' => 'Sportpesa Mega Jackpot',
        'limit' => 20,
        'latest_round' => true,
    ],

    'sportpesa-midweek-jackpot-predictions' => [
        'title' => 'SportPesa Midweek Jackpot',
        'source' => 'selections',
        'jackpot' => 'Sportpesa Midweek Jackpot',
        'limit' => 20,
        'latest_round' => true,
    ],

    'betika-midweek-jackpot-predictions' => [
        'title' => 'Betika Midweek Jackpot',
        'source' => 'selections',
        'jackpot' => 'Betika Midweek Jackpot',
        'limit' => 20,
        'latest_round' => true,
    ],

    'sportybet-daily-jackpot-predictions' => [
        'title' => 'SportyBet Daily Jackpot',
        'source' => 'selections',
        'jackpot' => 'Sporty bet Jackpot',
        'limit' => 20,
        'latest_round' => true,
    ],

    'odibets-laki-tatu-predictions' => [
        'title' => 'Odibets Laki Tatu',
        'source' => 'selections',
        'jackpot' => 'Odibet Laki Tatu Jackpot',
        'limit' => 10,
        'latest_round' => true,
    ],

    'mozzart-super-daily-jackpot-predictions' => [
        'title' => 'Mozzart Super Daily Jackpot',
        'source' => 'selections',
        'jackpot' => 'Mozzart Super Daily Jackpot',
        'limit' => 20,
        'latest_round' => true,
    ],
];
