<?php

namespace App\Services;

use App\Support\Cache;
use App\Support\DateTimeHelper;

/**
 * Freeze today's published accumulator tickets so yesterday can show settled results.
 * Prefers Redis/file cache (shared Redis in k3s) with a site-content JSON mirror.
 */
final class AccumulatorSnapshotService
{
    private const CACHE_TTL_SECONDS = 604800; // 7 days

    public static function directory(): string
    {
        return dirname(__DIR__, 2) . '/public/site-content/accumulator-snapshots';
    }

    public static function pathForDate(string $dateYmd): string
    {
        return self::directory() . '/' . $dateYmd . '.json';
    }

    private static function cacheKey(string $dateYmd): string
    {
        return Cache::internalKey('acca_snapshot_' . $dateYmd);
    }

    /**
     * Persist today's tickets once (first non-empty build wins so the board stays stable).
     *
     * @param list<array<string,mixed>> $tickets
     */
    public function rememberForDate(string $dateYmd, array $tickets): void
    {
        if ($tickets === [] || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateYmd)) {
            return;
        }
        if ($this->readRaw($dateYmd) !== null) {
            return;
        }
        $this->write($dateYmd, $tickets);
    }

    /**
     * @param list<array<string,mixed>> $tickets
     */
    public function rememberToday(array $tickets): void
    {
        $this->rememberForDate(DateTimeHelper::siteToday(), $tickets);
    }

    /**
     * @return list<array<string,mixed>>|null
     */
    public function ticketsForDate(string $dateYmd): ?array
    {
        $raw = $this->readRaw($dateYmd);
        if ($raw === null) {
            return null;
        }
        $tickets = $raw['accumulators'] ?? null;
        return is_array($tickets) ? array_values($tickets) : null;
    }

    /**
     * @return list<array<string,mixed>>|null
     */
    public function ticketsForYesterday(): ?array
    {
        return $this->ticketsForDate(DateTimeHelper::siteDate('yesterday'));
    }

    /**
     * @return array{date:string,updated_at:string,accumulators:list<array<string,mixed>>}|null
     */
    private function readRaw(string $dateYmd): ?array
    {
        $cached = Cache::get(self::cacheKey($dateYmd));
        if (is_array($cached) && isset($cached['accumulators']) && is_array($cached['accumulators'])) {
            return $cached;
        }

        $path = self::pathForDate($dateYmd);
        if (!is_file($path)) {
            return null;
        }
        try {
            $decoded = json_decode((string) file_get_contents($path), true);
        } catch (\Throwable $e) {
            return null;
        }
        if (!is_array($decoded) || !isset($decoded['accumulators']) || !is_array($decoded['accumulators'])) {
            return null;
        }

        Cache::put(self::cacheKey($dateYmd), $decoded, self::CACHE_TTL_SECONDS);
        return $decoded;
    }

    /**
     * @param list<array<string,mixed>> $tickets
     */
    private function write(string $dateYmd, array $tickets): void
    {
        $document = [
            'date' => $dateYmd,
            'updated_at' => gmdate('c'),
            'accumulators' => $this->normalizeTicketsForSnapshot($tickets),
        ];

        Cache::put(self::cacheKey($dateYmd), $document, self::CACHE_TTL_SECONDS);

        $dir = self::directory();
        if (!is_dir($dir) && !@mkdir($dir, 0755, true) && !is_dir($dir)) {
            return;
        }
        $encoded = json_encode($document, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if ($encoded === false) {
            return;
        }
        @file_put_contents(self::pathForDate($dateYmd), $encoded . "\n");
    }

    /**
     * Strip live settlement fields so the snapshot stays tip-only; scores are rehydrated later.
     *
     * @param list<array<string,mixed>> $tickets
     * @return list<array<string,mixed>>
     */
    private function normalizeTicketsForSnapshot(array $tickets): array
    {
        $out = [];
        foreach ($tickets as $ticket) {
            if (!is_array($ticket)) {
                continue;
            }
            $picks = [];
            foreach (($ticket['picks'] ?? []) as $pick) {
                if (!is_array($pick)) {
                    continue;
                }
                $picks[] = [
                    'home' => (string) ($pick['home'] ?? ''),
                    'away' => (string) ($pick['away'] ?? ''),
                    'pick' => (string) ($pick['pick'] ?? ''),
                    'pick_code' => $pick['pick_code'] ?? null,
                    'market' => (string) ($pick['market'] ?? '1x2'),
                    'odds' => $pick['odds'] ?? null,
                    'confidence' => isset($pick['confidence']) ? (int) $pick['confidence'] : 0,
                    'league' => (string) ($pick['league'] ?? ''),
                    'fixture_id' => isset($pick['fixture_id']) ? (int) $pick['fixture_id'] : null,
                ];
            }
            if ($picks === []) {
                continue;
            }
            $out[] = [
                'name' => (string) ($ticket['name'] ?? 'Accumulator'),
                'legs' => (int) ($ticket['legs'] ?? count($picks)),
                'combined_odds' => $ticket['combined_odds'] ?? null,
                'blended_confidence' => isset($ticket['blended_confidence']) ? (int) $ticket['blended_confidence'] : 0,
                'picks' => $picks,
            ];
        }
        return $out;
    }
}
