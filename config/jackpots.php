<?php
/**
 * Shared jackpot product labels / expected game counts.
 * Live row counts from the selections API always win when present;
 * these expected_* values are fallbacks when a round is empty or offline.
 */
return [
    'sportpesa-mega-jackpot-predictions' => [
        'label' => 'SportPesa Mega Jackpot',
        'schedule' => 'weekend',
        'expected_games' => 17,
        'prize_label' => 'from KES 100,000,000',
    ],
    'sportpesa-midweek-jackpot-predictions' => [
        'label' => 'SportPesa Midweek Jackpot',
        'schedule' => 'midweek',
        // SportPesa Midweek is a 13-game card (not 17 like Mega).
        'expected_games' => 13,
        'prize_label' => 'KES 40,000,000',
    ],
    'betika-midweek-jackpot-predictions' => [
        'label' => 'Betika Midweek Jackpot',
        'schedule' => 'midweek',
        'expected_games' => 15,
        'prize_label' => 'KES 15,000,000',
    ],
    'sportybet-daily-jackpot-predictions' => [
        'label' => 'SportyBet Daily Jackpot',
        'schedule' => 'daily',
        // Live cards are typically 13; strip always counts the fixtures array.
        'expected_games' => 13,
        'prize_label' => 'Shared daily pool',
    ],
    'odibets-laki-tatu-predictions' => [
        'label' => 'Odibets Laki Tatu',
        'schedule' => 'daily',
        'expected_games' => 10,
        'prize_label' => 'up to KES 300,000',
    ],
    'mozzart-super-daily-jackpot-predictions' => [
        'label' => 'Mozzart Super Daily Jackpot',
        'schedule' => 'daily',
        // Competitors consistently describe 16 games / KES 20 / up to KES 20M — confirm live on Mozzartbet.
        'expected_games' => 16,
        'prize_label' => 'up to KES 20,000,000',
    ],
];
