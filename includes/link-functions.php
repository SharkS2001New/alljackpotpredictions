<?php
/**
 * Text Link / Partner Card functions — JSON file storage. No database.
 *
 * Three types, three files:
 *   'sponsor' -> sponsor_links.json   (footer text links)
 *   'partner' -> partner_links.json   (partners page — sponsored text-link cards)
 *   'card'    -> partners.json        (partners page — real Prediction Partners
 *                                       + Our Network cards, admin-managed)
 *
 * Reads/writes use flock() so two admin tabs saving at once don't clobber each other.
 */

define('LL_DATA_DIR', __DIR__ . '/../data');
define('LL_FAR_FUTURE', '2099-12-31 23:59:59'); // stand-in "no expiry" date for permanent entries

function ll_file($type) {
    $map = [
        'sponsor' => 'sponsor_links.json',
        'partner' => 'partner_links.json',
        'card'    => 'partners.json',
    ];
    return LL_DATA_DIR . '/' . ($map[$type] ?? 'sponsor_links.json');
}

/** Reads the JSON file into an array. Returns [] if missing/empty/corrupt. */
function ll_read_all($type) {
    $path = ll_file($type);
    if (!file_exists($path)) return [];
    $fp = fopen($path, 'r');
    if (!$fp) return [];
    flock($fp, LOCK_SH);
    $raw = stream_get_contents($fp);
    flock($fp, LOCK_UN);
    fclose($fp);
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

/** Writes the full array back to the JSON file, locked. */
function ll_write_all($type, array $rows) {
    if (!is_dir(LL_DATA_DIR)) mkdir(LL_DATA_DIR, 0755, true);
    $path = ll_file($type);
    $fp = fopen($path, 'c+');
    if (!$fp) return false;
    flock($fp, LOCK_EX);
    ftruncate($fp, 0);
    rewind($fp);
    fwrite($fp, json_encode(array_values($rows), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    fflush($fp);
    flock($fp, LOCK_UN);
    fclose($fp);
    ll_purge_cache();
    return true;
}

/**
 * Tells LiteSpeed's server-side page cache to drop everything for this
 * site the moment admin data changes, so edits show up immediately without
 * a manual "Purge All" in cPanel. Safe no-op on hosts that aren't LiteSpeed.
 */
function ll_purge_cache() {
    if (!headers_sent()) {
        header('X-LiteSpeed-Purge: *');
    }
}

/** Rows to actually display on the live site: active + not expired (or permanent). */
function ll_get_active($type = 'sponsor') {
    $rows = array_filter(ll_read_all($type), function ($r) {
        if (empty($r['is_active'])) return false;
        if (($r['duration_type'] ?? 'limited') === 'permanent') return true;
        return strtotime($r['expires_at']) > time();
    });
    // Uncomment to hard-cap instead of showing everything active:
    // $rows = array_slice($rows, 0, 10);
    usort($rows, function ($a, $b) {
        $so = ($a['sort_order'] ?? 0) <=> ($b['sort_order'] ?? 0);
        return $so !== 0 ? $so : strtotime($b['created_at']) <=> strtotime($a['created_at']);
    });
    return $rows;
}

/** Same as ll_get_active but filtered to one badge_type — only meaningful for type='card'. */
function ll_get_active_by_badge($badgeType) {
    return array_values(array_filter(ll_get_active('card'), function ($r) use ($badgeType) {
        return (isset($r['badge_type']) ? $r['badge_type'] : '') === $badgeType;
    }));
}

/** Everything, for the admin list (including expired/inactive so they can be edited/removed). */
function ll_get_all($type = 'sponsor') {
    $rows = ll_read_all($type);
    usort($rows, function ($a, $b) {
        return strtotime($a['expires_at']) - strtotime($b['expires_at']);
    });
    return $rows;
}

function ll_get_one($type, $id) {
    foreach (ll_read_all($type) as $row) {
        if ((string) $row['id'] === (string) $id) return $row;
    }
    return null;
}

/**
 * Turns a start date + a duration choice into an expiry timestamp.
 * $durationOption: '1','2','3','6','12' (months), 'custom', or 'permanent'.
 */
function ll_calc_expiry($startDate, $durationOption, $customDate = null) {
    if ($durationOption === 'permanent') {
        return LL_FAR_FUTURE;
    }
    $start = new DateTime($startDate ?: 'now');
    if ($durationOption === 'custom' && $customDate) {
        return (new DateTime($customDate))->format('Y-m-d H:i:s');
    }
    $months = (int) $durationOption;
    if ($months <= 0) $months = 1;
    $start->modify("+{$months} months");
    return $start->format('Y-m-d H:i:s');
}

function ll_next_id($rows) {
    $max = 0;
    foreach ($rows as $r) $max = max($max, (int) $r['id']);
    return $max + 1;
}

/** Builds the fields common to every type, from posted admin-form data. */
function ll_base_fields($data) {
    $duration = $data['duration'] ?? '1';
    return [
        'url'           => trim($data['url']),
        'rel_type'      => $data['rel_type'] === 'dofollow' ? 'dofollow' : 'nofollow',
        'starts_at'     => $data['starts_at'] ?: date('Y-m-d H:i:s'),
        'expires_at'    => ll_calc_expiry($data['starts_at'] ?? null, $duration, $data['custom_expiry'] ?? null),
        'duration_type' => $duration === 'permanent' ? 'permanent' : 'limited',
        'is_active'     => !empty($data['is_active']) ? 1 : 0,
        'sort_order'    => (int) ($data['sort_order'] ?? 0),
    ];
}

function ll_create($type, array $data) {
    $rows = ll_read_all($type);
    $now  = date('Y-m-d H:i:s');
    $base = ll_base_fields($data);

    $row = array_merge($base, [
        'id'         => ll_next_id($rows),
        'created_at' => $now,
        'updated_at' => $now,
        'is_paid'    => 0,
    ]);

    if ($type === 'card') {
        $row['name']        = trim($data['name'] ?? $data['anchor_text'] ?? '');
        $row['badge_type']  = in_array($data['badge_type'] ?? '', ['partner', 'network']) ? $data['badge_type'] : 'network';
        $row['is_main']     = !empty($data['is_main']) ? 1 : 0;
        $row['description'] = trim($data['description'] ?? '');
        $row['tags']        = ll_parse_tags($data['tags'] ?? '');
        $row['sender_name'] = trim($data['sender_name'] ?? '');
    } else {
        $row['anchor_text'] = trim($data['anchor_text'] ?? '');
        $row['sender_name'] = trim($data['sender_name'] ?? '');
        if ($type === 'partner') $row['description'] = trim($data['description'] ?? '');
    }

    $rows[] = $row;
    return ll_write_all($type, $rows);
}

function ll_update($type, $id, array $data) {
    $rows  = ll_read_all($type);
    $found = false;
    $base  = ll_base_fields($data);

    foreach ($rows as &$row) {
        if ((string) $row['id'] === (string) $id) {
            $row = array_merge($row, $base, ['updated_at' => date('Y-m-d H:i:s')]);

            if ($type === 'card') {
                $row['name']        = trim($data['name'] ?? $data['anchor_text'] ?? '');
                $row['badge_type']  = in_array($data['badge_type'] ?? '', ['partner', 'network']) ? $data['badge_type'] : 'network';
                $row['is_main']     = !empty($data['is_main']) ? 1 : 0;
                $row['description'] = trim($data['description'] ?? '');
                $row['tags']        = ll_parse_tags($data['tags'] ?? '');
                $row['sender_name'] = trim($data['sender_name'] ?? '');
            } else {
                $row['anchor_text'] = trim($data['anchor_text'] ?? '');
                $row['sender_name'] = trim($data['sender_name'] ?? '');
                if ($type === 'partner') $row['description'] = trim($data['description'] ?? '');
            }
            $found = true;
            break;
        }
    }
    unset($row);

    if (!$found) return false;
    return ll_write_all($type, $rows);
}

function ll_delete($type, $id) {
    $rows = ll_read_all($type);
    $rows = array_filter($rows, function ($r) use ($id) {
        return (string) $r['id'] !== (string) $id;
    });
    return ll_write_all($type, $rows);
}

/**
 * Removes limited-duration links that have been expired for 90+ days
 * with no renewal (renewing a link changes its expires_at, which
 * resets this clock automatically). Permanent links are never touched.
 * Call this once per admin page load — cheap no-op if nothing's stale.
 */
function ll_purge_stale_expired($type) {
    $rows = ll_read_all($type);
    $cutoff = time() - (90 * 86400);
    $kept = array_filter($rows, function ($r) use ($cutoff) {
        if ((isset($r['duration_type']) ? $r['duration_type'] : 'limited') === 'permanent') return true;
        return strtotime($r['expires_at']) >= $cutoff;
    });
    if (count($kept) !== count($rows)) {
        ll_write_all($type, array_values($kept));
    }
}

/** "12 days left" / "Expired 5 days ago" / "Permanent" — for the admin list. */
function ll_days_label($row) {
    if ((isset($row['duration_type']) ? $row['duration_type'] : 'limited') === 'permanent') {
        return 'Permanent';
    }
    $diffDays = (int) floor((strtotime($row['expires_at']) - time()) / 86400);
    if ($diffDays >= 0) {
        return $diffDays . ' day' . ($diffDays === 1 ? '' : 's') . ' left';
    }
    $daysAgo = abs($diffDays);
    return 'Expired ' . $daysAgo . ' day' . ($daysAgo === 1 ? '' : 's') . ' ago';
}

/** Flips is_paid 0<->1 for one row. Never touches any other field. */
function ll_toggle_paid($type, $id) {
    $rows  = ll_read_all($type);
    $found = false;
    foreach ($rows as &$row) {
        if ((string) $row['id'] === (string) $id) {
            $current = !empty($row['is_paid']) ? 1 : 0;
            $row['is_paid'] = $current ? 0 : 1;
            $found = true;
            break;
        }
    }
    unset($row);
    if (!$found) return false;
    return ll_write_all($type, $rows);
}

/** "Nigeria, Free Tips, BTTS" -> ['Nigeria','Free Tips','BTTS'] */
function ll_parse_tags($str) {
    $parts = array_map('trim', explode(',', (string) $str));
    return array_values(array_filter($parts, function ($t) {
        return $t !== '';
    }));
}

/** Renders the anchor tag's rel attribute string based on the stored choice. */
function ll_rel_attr($relType) {
    return $relType === 'dofollow' ? '' : ' rel="nofollow sponsored"';
}

/** "https://www.100tip.com/" -> "100tip". Used as a fallback anchor when none was entered. */
function ll_domain_to_anchor($url) {
    $host = parse_url($url, PHP_URL_HOST) ?: $url;
    $host = preg_replace('/^www\./i', '', $host);
    $parts = explode('.', $host);
    if (count($parts) > 1) array_pop($parts); // drop the TLD (.com, .io, ...)
    return implode('.', $parts);
}