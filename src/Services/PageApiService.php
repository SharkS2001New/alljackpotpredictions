<?php
namespace App\Services;

use App\Support\DateTimeHelper;

/**
 * Resolve page-specific API payloads from config/api-pages.php
 */
final class PageApiService
{
    private GamesService $games;
    /** @var array<string,array<string,mixed>> */
    private array $pages;

    public function __construct(?GamesService $games = null)
    {
        $this->games = $games ?: new GamesService();
        $this->pages = require dirname(__DIR__, 2) . '/config/api-pages.php';
    }

    /** @return list<string> */
    public function pageKeys(): array
    {
        return array_keys($this->pages);
    }

    public function hasPage(string $key): bool
    {
        return isset($this->pages[$key]);
    }

    /**
     * @return array<string,mixed>
     */
    public function payload(string $key, array $overrides = []): array
    {
        if (!$this->hasPage($key)) {
            throw new \InvalidArgumentException('Unknown API page: ' . $key);
        }

        $def = array_merge($this->pages[$key], $overrides);
        $source = strtolower((string) ($def['source'] ?? 'fixtures'));

        $startIndex = array_key_exists('start_index', $overrides) ? max(0, (int) $overrides['start_index']) : null;
        $endIndex = array_key_exists('end_index', $overrides) ? max(0, (int) $overrides['end_index']) : null;

        $filters = $def;
        unset($filters['title'], $filters['extra'], $filters['start_index'], $filters['end_index']);

        $maxLimit = max(1, min(200, (int) ($def['limit'] ?? 50)));

        // Pitch-style window: fetch through end_index (+1 peek for has_more), then slice.
        if ($startIndex !== null && $endIndex !== null) {
            if ($endIndex < $startIndex) {
                $endIndex = $startIndex;
            }
            $endIndex = min($endIndex, $maxLimit - 1);
            $startIndex = min($startIndex, $endIndex);
            $filters['limit'] = min($maxLimit, $endIndex + 2);
        } else {
            $filters['limit'] = $maxLimit;
        }

        $gamesOrHub = $this->games->listGames($filters);

        $payload = [
            'ok' => true,
            'page' => $key,
            'title' => $def['title'] ?? $key,
            'source' => $source,
            'market' => $def['market'] ?? null,
        ];

        if ($source === 'jackpot_hub') {
            $payload['count'] = count($gamesOrHub);
            $payload['jackpots'] = $gamesOrHub;
            $payload['has_more'] = false;
            return $payload;
        }

        $payload['date'] = isset($def['range'])
            ? $this->games->resolveDateRange($def)
            : $this->games->resolveDate($def);

        if ($startIndex !== null && $endIndex !== null) {
            $want = $endIndex - $startIndex + 1;
            $slice = array_slice($gamesOrHub, $startIndex, $want);
            $payload['games'] = $slice;
            $payload['count'] = count($slice);
            $payload['start_index'] = $startIndex;
            $payload['end_index'] = $startIndex + max(0, count($slice) - 1);
            $payload['has_more'] = count($gamesOrHub) > ($endIndex + 1);
            $payload['next_start'] = $payload['has_more'] ? ($endIndex + 1) : null;
            $payload['max'] = $maxLimit;
        } else {
            $payload['count'] = count($gamesOrHub);
            $payload['games'] = $gamesOrHub;
            $payload['has_more'] = false;
            $payload['max'] = $maxLimit;
        }

        if (($def['extra'] ?? '') === 'accumulators') {
            // Accumulators use the full fetched pool (before pagination slice).
            $tickets = $this->games->buildAccumulators($gamesOrHub);
            $snapshots = new AccumulatorSnapshotService();
            $snapshots->rememberToday($tickets);
            $payload['accumulators'] = $tickets;

            $yesterday = DateTimeHelper::siteDate('yesterday');
            $yTickets = $snapshots->ticketsForDate($yesterday);
            if ($yTickets !== null && $yTickets !== []) {
                $payload['yesterday_date'] = $yesterday;
                $payload['yesterday_accumulators'] = $this->games->enrichAccumulatorTickets($yTickets);
                $payload['yesterday_source'] = 'snapshot';
            } else {
                // No frozen snapshot yet — reconstruct from yesterday's tip pool + scores.
                $constructed = $this->games->buildAccumulatorsForDate($yesterday, $def);
                if ($constructed !== []) {
                    $snapshots->rememberForDate($yesterday, $constructed);
                    $payload['yesterday_date'] = $yesterday;
                    $payload['yesterday_accumulators'] = $constructed;
                    $payload['yesterday_source'] = 'constructed';
                }
            }
        }

        if ($source === 'selections'
            && !empty($def['latest_round'])
            && trim((string) ($def['jackpot'] ?? '')) !== ''
        ) {
            $previous = $this->games->listFromSelections([
                'jackpot' => (string) $def['jackpot'],
                'previous_round' => true,
                'limit' => $maxLimit,
            ]);
            if ($previous !== []) {
                $latestIds = [];
                foreach ($gamesOrHub as $g) {
                    $tid = (string) ($g['jackpot_tips_id'] ?? '');
                    if ($tid !== '') {
                        $latestIds[$tid] = true;
                    }
                }
                $prevId = (string) ($previous[0]['jackpot_tips_id'] ?? '');
                if ($prevId === '' || !isset($latestIds[$prevId])) {
                    $payload['previous_games'] = $previous;
                    $payload['previous_count'] = count($previous);
                }
            }
        }

        return $payload;
    }

    /**
     * Flat games list for rendering match cards (not hub).
     *
     * @return list<array<string,mixed>>
     */
    public function gamesForPage(string $key, array $overrides = []): array
    {
        $payload = $this->payload($key, $overrides);
        return $payload['games'] ?? [];
    }
}
