<?php
/**
 * Visitor currency from geo headers (Cloudflare / CDN) — no external IP API.
 *
 * Base amounts are local "example stakes" (not live FX conversion of £10).
 * Accumulators multiply odds × stake in the visitor's currency.
 */

/**
 * @return array{country:string,code:string,symbol:string,stake:float,decimals:int,prefix:bool,name:string}
 */
function ajp_visitor_currency(): array
{
    static $cached = null;
    if (is_array($cached)) {
        return $cached;
    }

    $country = ajp_detect_country_code();
    $map = ajp_currency_map();
    $cached = $map[$country] ?? $map['KE']; // Kenya-first defaults for this site
    $cached['country'] = $country;
    return $cached;
}

function ajp_detect_country_code(): string
{
    // Manual override for testing: ?cc=KE or cookie ajp_cc
    $q = strtoupper(trim((string) ($_GET['cc'] ?? '')));
    if (preg_match('/^[A-Z]{2}$/', $q)) {
        return $q;
    }
    $cookie = strtoupper(trim((string) ($_COOKIE['ajp_cc'] ?? '')));
    if (preg_match('/^[A-Z]{2}$/', $cookie)) {
        return $cookie;
    }

    $headers = [
        'HTTP_CF_IPCOUNTRY',           // Cloudflare
        'HTTP_CLOUDFRONT_VIEWER_COUNTRY',
        'HTTP_X_COUNTRY_CODE',
        'HTTP_X_VERCEL_IP_COUNTRY',
        'HTTP_X_APPENGINE_COUNTRY',
        'GEOIP_COUNTRY_CODE',
    ];
    foreach ($headers as $h) {
        $v = strtoupper(trim((string) ($_SERVER[$h] ?? '')));
        if ($v !== '' && $v !== 'XX' && $v !== 'T1' && preg_match('/^[A-Z]{2}$/', $v)) {
            return $v;
        }
    }

    // Fallback: Accept-Language region (weak signal)
    $al = (string) ($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '');
    if (preg_match('/(?:^|,)\s*[a-z]{2}[-_]([A-Za-z]{2})/', $al, $m)) {
        return strtoupper($m[1]);
    }

    return 'KE';
}

/**
 * @return array<string,array{code:string,symbol:string,stake:float,decimals:int,prefix:bool,name:string}>
 */
function ajp_currency_map(): array
{
    // Eurozone share EUR
    $eur = ['code' => 'EUR', 'symbol' => '€', 'stake' => 10.0, 'decimals' => 2, 'prefix' => true, 'name' => 'Euro'];
    $euroCountries = [
        'AT','BE','CY','DE','EE','ES','FI','FR','GR','HR','IE','IT','LT','LU','LV','MT','NL','PT','SI','SK',
        'AD','MC','SM','VA','ME','XK',
    ];

    $map = [
        'KE' => ['code' => 'KES', 'symbol' => 'KES ', 'stake' => 1000.0, 'decimals' => 0, 'prefix' => true, 'name' => 'Kenyan Shilling'],
        'UG' => ['code' => 'UGX', 'symbol' => 'UGX ', 'stake' => 10000.0, 'decimals' => 0, 'prefix' => true, 'name' => 'Ugandan Shilling'],
        'TZ' => ['code' => 'TZS', 'symbol' => 'TZS ', 'stake' => 10000.0, 'decimals' => 0, 'prefix' => true, 'name' => 'Tanzanian Shilling'],
        'NG' => ['code' => 'NGN', 'symbol' => '₦', 'stake' => 5000.0, 'decimals' => 0, 'prefix' => true, 'name' => 'Nigerian Naira'],
        'GH' => ['code' => 'GHS', 'symbol' => 'GH₵', 'stake' => 100.0, 'decimals' => 0, 'prefix' => true, 'name' => 'Ghanaian Cedi'],
        'ZA' => ['code' => 'ZAR', 'symbol' => 'R', 'stake' => 100.0, 'decimals' => 0, 'prefix' => true, 'name' => 'South African Rand'],
        'RW' => ['code' => 'RWF', 'symbol' => 'RWF ', 'stake' => 5000.0, 'decimals' => 0, 'prefix' => true, 'name' => 'Rwandan Franc'],
        'ZM' => ['code' => 'ZMW', 'symbol' => 'ZK', 'stake' => 100.0, 'decimals' => 0, 'prefix' => true, 'name' => 'Zambian Kwacha'],
        'MW' => ['code' => 'MWK', 'symbol' => 'MK', 'stake' => 5000.0, 'decimals' => 0, 'prefix' => true, 'name' => 'Malawian Kwacha'],
        'ET' => ['code' => 'ETB', 'symbol' => 'Br', 'stake' => 500.0, 'decimals' => 0, 'prefix' => true, 'name' => 'Ethiopian Birr'],
        'GB' => ['code' => 'GBP', 'symbol' => '£', 'stake' => 10.0, 'decimals' => 2, 'prefix' => true, 'name' => 'Pound Sterling'],
        'IE' => $eur,
        'US' => ['code' => 'USD', 'symbol' => '$', 'stake' => 10.0, 'decimals' => 2, 'prefix' => true, 'name' => 'US Dollar'],
        'CA' => ['code' => 'CAD', 'symbol' => 'CA$', 'stake' => 10.0, 'decimals' => 2, 'prefix' => true, 'name' => 'Canadian Dollar'],
        'AU' => ['code' => 'AUD', 'symbol' => 'A$', 'stake' => 10.0, 'decimals' => 2, 'prefix' => true, 'name' => 'Australian Dollar'],
        'NZ' => ['code' => 'NZD', 'symbol' => 'NZ$', 'stake' => 10.0, 'decimals' => 2, 'prefix' => true, 'name' => 'New Zealand Dollar'],
        'IN' => ['code' => 'INR', 'symbol' => '₹', 'stake' => 500.0, 'decimals' => 0, 'prefix' => true, 'name' => 'Indian Rupee'],
        'CH' => ['code' => 'CHF', 'symbol' => 'CHF ', 'stake' => 10.0, 'decimals' => 2, 'prefix' => true, 'name' => 'Swiss Franc'],
        'SE' => ['code' => 'SEK', 'symbol' => 'kr', 'stake' => 100.0, 'decimals' => 0, 'prefix' => false, 'name' => 'Swedish Krona'],
        'NO' => ['code' => 'NOK', 'symbol' => 'kr', 'stake' => 100.0, 'decimals' => 0, 'prefix' => false, 'name' => 'Norwegian Krone'],
        'DK' => ['code' => 'DKK', 'symbol' => 'kr', 'stake' => 100.0, 'decimals' => 0, 'prefix' => false, 'name' => 'Danish Krone'],
        'PL' => ['code' => 'PLN', 'symbol' => 'zł', 'stake' => 50.0, 'decimals' => 0, 'prefix' => false, 'name' => 'Polish Złoty'],
        'TR' => ['code' => 'TRY', 'symbol' => '₺', 'stake' => 200.0, 'decimals' => 0, 'prefix' => true, 'name' => 'Turkish Lira'],
        'AE' => ['code' => 'AED', 'symbol' => 'AED ', 'stake' => 50.0, 'decimals' => 0, 'prefix' => true, 'name' => 'UAE Dirham'],
        'SA' => ['code' => 'SAR', 'symbol' => 'SAR ', 'stake' => 50.0, 'decimals' => 0, 'prefix' => true, 'name' => 'Saudi Riyal'],
        'BR' => ['code' => 'BRL', 'symbol' => 'R$', 'stake' => 50.0, 'decimals' => 0, 'prefix' => true, 'name' => 'Brazilian Real'],
        'MX' => ['code' => 'MXN', 'symbol' => 'MX$', 'stake' => 200.0, 'decimals' => 0, 'prefix' => true, 'name' => 'Mexican Peso'],
        'JP' => ['code' => 'JPY', 'symbol' => '¥', 'stake' => 1000.0, 'decimals' => 0, 'prefix' => true, 'name' => 'Japanese Yen'],
        'CN' => ['code' => 'CNY', 'symbol' => '¥', 'stake' => 50.0, 'decimals' => 0, 'prefix' => true, 'name' => 'Chinese Yuan'],
    ];

    foreach ($euroCountries as $cc) {
        if (!isset($map[$cc])) {
            $map[$cc] = $eur;
        }
    }

    return $map;
}

function ajp_format_money(float $amount, ?array $cur = null): string
{
    $cur = $cur ?? ajp_visitor_currency();
    $decimals = (int) ($cur['decimals'] ?? 2);
    $formatted = number_format($amount, $decimals, '.', $decimals === 0 ? ',' : ',');
    $symbol = (string) ($cur['symbol'] ?? '');
    if (!empty($cur['prefix'])) {
        return $symbol . $formatted;
    }
    return $formatted . ' ' . trim($symbol);
}

/** Label like "KES 1,000 returns" */
function ajp_stake_returns_label(?array $cur = null): string
{
    $cur = $cur ?? ajp_visitor_currency();
    return ajp_format_money((float) $cur['stake'], $cur) . ' returns';
}

/** Example return for combined odds × local stake */
function ajp_stake_returns_amount(float $combinedOdds, ?array $cur = null): string
{
    $cur = $cur ?? ajp_visitor_currency();
    $stake = (float) ($cur['stake'] ?? 10);
    return ajp_format_money($combinedOdds * $stake, $cur);
}
