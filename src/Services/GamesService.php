<?php
namespace App\Services;

use App\Database;
use App\Support\DateTimeHelper;
use PDO;

/**
 * Fetch games from pitchnewdb for page-specific APIs.
 * Schema: fixtures, leagues, predictions, predictions_computation, odds, pp_fixtures_selections
 */
final class GamesService
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?: Database::connection();
    }

    /**
     * @param array<string,mixed> $filters
     * @return list<array<string,mixed>>
     */
    public function listGames(array $filters = []): array
    {
        $source = strtolower((string) ($filters['source'] ?? 'fixtures'));
        if ($source === 'selections' || $source === 'tips') {
            return $this->listFromSelections($filters);
        }
        if ($source === 'jackpot_hub') {
            return $this->listJackpotHub($filters);
        }
        return $this->listFromFixtures($filters);
    }

    public function resolveDate(array $filters): string
    {
        $date = trim((string) ($filters['date'] ?? ''));
        if ($date !== '') {
            return $date;
        }
        $day = strtolower((string) ($filters['day'] ?? 'today'));
        return match ($day) {
            'tomorrow' => DateTimeHelper::siteDate('tomorrow'),
            'yesterday' => DateTimeHelper::siteDate('yesterday'),
            default => DateTimeHelper::siteDate('today'),
        };
    }

    /**
     * @param array<string,mixed> $filters
     * @return array{from:string,to:string}
     */
    public function resolveDateRange(array $filters): array
    {
        $range = strtolower((string) ($filters['range'] ?? ''));
        if ($range === 'weekend') {
            return DateTimeHelper::siteWeekendRange();
        }

        // Rolling settled archive (Results page) — end at yesterday so Today stays live-only.
        $lookback = isset($filters['lookback_days']) ? (int) $filters['lookback_days'] : 0;
        if ($lookback > 0) {
            $lookback = max(2, min(30, $lookback));
            $to = DateTimeHelper::siteDate('yesterday');
            $from = (new \DateTimeImmutable($to . ' 12:00:00', new \DateTimeZone(DateTimeHelper::SITE_TZ)))
                ->modify('-' . ($lookback - 1) . ' days')
                ->format('Y-m-d');
            return ['from' => $from, 'to' => $to];
        }

        $date = $this->resolveDate($filters);
        return ['from' => $date, 'to' => $date];
    }

    /**
     * @param array<string,mixed> $filters
     * @return list<array<string,mixed>>
     */
    public function listFromFixtures(array $filters = []): array
    {
        $range = $this->resolveDateRange($filters);
        $limit = max(1, min(500, (int) ($filters['limit'] ?? 50)));
        $leagueId = isset($filters['league_id']) ? (int) $filters['league_id'] : 0;
        $status = trim((string) ($filters['status'] ?? ''));
        $market = strtolower((string) ($filters['market'] ?? '1x2'));
        $minConf = isset($filters['min_confidence']) ? (int) $filters['min_confidence'] : 0;
        $order = (string) ($filters['order'] ?? 'kickoff_asc');

        $sql = <<<SQL
SELECT
  f.fixture_id,
  f.league_id,
  f.home_team_name,
  f.away_team_name,
  f.home_team_logo,
  f.away_team_logo,
  f.date AS kickoff,
  f.timezone,
  f.status_short,
  f.status_long,
  f.goals_home,
  f.goals_away,
  f.ht_goals_home,
  f.ht_goals_away,
  f.option_picked,
  f.prediction_type,
  l.league_name,
  l.country_name,
  COALESCE(l.popular_status, 0) AS popular_status,
  pc.percent_pred_home,
  pc.percent_pred_draw,
  pc.percent_pred_away,
  pc.hf_percent_pred_home,
  pc.hf_percent_pred_draw,
  pc.hf_percent_pred_away,
  pc.both_team_to_score,
  pc.both_teams_percentage_prob,
  pc.pred_group,
  pc.active_status,
  p.advice,
  p.winner_team_name,
  p.under_over,
  p.win_or_draw,
  p.avg_goals,
  o.bookmaker_name,
  o.bets_home,
  o.bets_draw,
  o.bets_away,
  {$this->coalesceOddsColSql('over_2_5')} AS over_2_5,
  {$this->coalesceOddsColSql('under_2_5')} AS under_2_5,
  {$this->coalesceOddsColSql('both_teams_to_score_yes')} AS both_teams_to_score_yes,
  {$this->coalesceOddsColSql('both_teams_to_score_no')} AS both_teams_to_score_no,
  {$this->coalesceOddsColSql('double_chance_home_draw')} AS double_chance_home_draw,
  {$this->coalesceOddsColSql('double_chance_draw_away')} AS double_chance_draw_away,
  {$this->coalesceOddsColSql('double_chance_home_away')} AS double_chance_home_away,
  o.cs_best_score_1,
  o.cs_best_odd_1,
  {$this->coalesceOddsColSql('ht_home')} AS ht_home,
  {$this->coalesceOddsColSql('ht_draw')} AS ht_draw,
  {$this->coalesceOddsColSql('ht_away')} AS ht_away
FROM fixtures f
LEFT JOIN leagues l ON l.league_id = f.league_id
LEFT JOIN predictions_computation pc ON pc.fixture_id = f.fixture_id
LEFT JOIN predictions p ON p.fixture_id = f.fixture_id
LEFT JOIN odds o ON o.id = (
  SELECT o2.id FROM odds o2
  WHERE o2.fixture_id = f.fixture_id
  ORDER BY
    {$this->bookmakerPreferenceSql('o2')},
    o2.id ASC
  LIMIT 1
)
WHERE f.date >= :date_from AND f.date < :date_to
SQL;

        // Nairobi calendar day D → UTC [D 00:00 EAT, D+1 00:00 EAT) = [D-1 21:00 UTC, D 21:00 UTC).
        $fromLocal = new \DateTimeImmutable($range['from'] . ' 00:00:00', new \DateTimeZone(DateTimeHelper::SITE_TZ));
        $toLocalExclusive = (new \DateTimeImmutable($range['to'] . ' 00:00:00', new \DateTimeZone(DateTimeHelper::SITE_TZ)))
            ->modify('+1 day');
        // Live board: widen to yesterday→tomorrow so late/ET matches are not clipped by calendar day.
        if (!empty($filters['live_only'])) {
            $fromLocal = $fromLocal->modify('-1 day');
            $toLocalExclusive = $toLocalExclusive->modify('+1 day');
        }
        $params = [
            ':date_from' => $fromLocal->setTimezone(new \DateTimeZone('UTC'))->format('Y-m-d H:i:s'),
            ':date_to' => $toLocalExclusive->setTimezone(new \DateTimeZone('UTC'))->format('Y-m-d H:i:s'),
        ];

        if ($leagueId > 0) {
            $sql .= ' AND f.league_id = :league_id';
            $params[':league_id'] = $leagueId;
        }
        if ($status !== '') {
            $sql .= ' AND f.status_short = :status';
            $params[':status'] = $status;
        }
        if (!empty($filters['live_only'])) {
            $sql .= " AND UPPER(TRIM(f.status_short)) IN ('1H','2H','HT','ET','BT','P','LIVE','INT','BREAK','SUSP')";
        }
        if (!empty($filters['upcoming_only'])) {
            $sql .= " AND f.status_short NOT IN ('FT','AET','PEN','PST','CANC','ABD','AWD','WO')";
        }

        // Prefer popular leagues, then strongest model lean, so high-chance non-popular
        // games still enter the fetch window when popular slates are thin.
        // Keep the SQL window proportional to the page limit (avoid mapping 200 rows for an 18-card board).
        $confidenceBoard = $order === 'confidence_desc' || $minConf > 0;
        $chronoBoard = $order === 'kickoff_asc' || $order === 'kickoff_desc';
        $fetchLimit = $confidenceBoard
            ? min(1500, max($limit * 6, $limit + 100))
            : min(1000, max($limit * 3, $limit + 40));
        if ($chronoBoard) {
            // Settled / dated boards: fetch by kickoff so the archive window is real.
            // Lookback archives need a wider SQL window — odds/model filters drop many FT rows.
            $fetchLimit = !empty($filters['lookback_days'])
                ? min(2000, max($limit * 6, 600))
                : min(1000, max($limit * 3, $limit + 40));
            $dir = $order === 'kickoff_desc' ? 'DESC' : 'ASC';
            $sql .= " ORDER BY f.date {$dir}, f.fixture_id {$dir} LIMIT " . $fetchLimit;
        } else {
            $sql .= ' ORDER BY COALESCE(l.popular_status, 0) DESC,'
                . ' GREATEST('
                . 'COALESCE(pc.percent_pred_home, 0),'
                . 'COALESCE(pc.percent_pred_draw, 0),'
                . 'COALESCE(pc.percent_pred_away, 0)'
                . ') DESC,'
                . ' f.date ASC, f.fixture_id ASC LIMIT ' . $fetchLimit;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();

        $out = [];
        foreach ($rows as $row) {
            $game = $this->mapFixtureRow($row, $market);
            if ($minConf > 0 && (int) $game['confidence'] < $minConf) {
                continue;
            }
            // Accuracy gate for straight 1X2 pages: skip soft long-priced leans.
            if ($market === '1x2' || $market === '') {
                $conf = (int) ($game['confidence'] ?? 0);
                // Unusable / invented model (ex-Home@50% stub) — never publish.
                if ($conf < 55) {
                    continue;
                }
                $odd = $this->oddFloat($game['odds'] ?? null);
                // No book price yet (common for early CAF / cup weekends) — wait until quoted.
                if ($odd === null) {
                    continue;
                }
                if ($odd >= 3.0) {
                    continue;
                }
                if ($odd >= 2.60 && $conf < 55) {
                    continue;
                }
            }
            // Tip boards need an actionable book price — skip empty odds rows.
            // (best/today/tomorrow included: tomorrow often has fixtures before books post.)
            if (in_array($market, ['double_chance', 'over_under', 'btts', 'ht_ft', 'best'], true)) {
                if ($this->oddFloat($game['odds'] ?? null) === null) {
                    continue;
                }
            }
            // O/U without xG or odds-derived confidence used to stamp every Under at 78%.
            if ($market === 'over_under' && (int) ($game['confidence'] ?? 0) < 55) {
                continue;
            }
            $out[] = $game;
        }

        return $this->selectBoardGames($out, $limit, $order);
    }

    /**
     * @param array<string,mixed> $filters
     * @return list<array<string,mixed>>
     */
    public function listFromSelections(array $filters = []): array
    {
        $limit = max(1, min(200, (int) ($filters['limit'] ?? 50)));
        $category = trim((string) ($filters['category'] ?? ''));
        $jackpot = trim((string) ($filters['jackpot'] ?? ''));
        $latestRound = !empty($filters['latest_round']);
        $previousRound = !empty($filters['previous_round']);
        $explicitTipsId = trim((string) ($filters['jackpot_tips_id'] ?? ''));

        $params = [];
        $sql = <<<SQL
SELECT
  s.id,
  s.fixture_id,
  s.home_team_name,
  s.away_team_name,
  s.fixture_date AS kickoff,
  s.tip,
  s.revised_tip,
  s.research_tip,
  s.odd,
  s.category,
  s.`group` AS tip_group,
  s.jackpot_name,
  s.jackpot_tips_id,
  s.jackpot_position,
  s.prediction_confidence,
  s.research_confidence,
  s.home_prob,
  s.draw_prob,
  s.away_prob,
  s.match_type,
  s.status,
  s.manual_status,
  f.home_team_logo,
  f.away_team_logo,
  f.status_short,
  f.status_long,
  f.goals_home,
  f.goals_away,
  f.timezone,
  l.league_name,
  l.country_name,
  o.bets_home,
  o.bets_draw,
  o.bets_away
FROM pp_fixtures_selections s
LEFT JOIN fixtures f ON f.fixture_id = CAST(s.fixture_id AS UNSIGNED)
LEFT JOIN leagues l ON l.league_id = f.league_id
LEFT JOIN odds o ON o.id = (
  SELECT o2.id FROM odds o2
  WHERE o2.fixture_id = CAST(s.fixture_id AS UNSIGNED)
  ORDER BY
    {$this->bookmakerPreferenceSql('o2')},
    o2.id ASC
  LIMIT 1
)
WHERE s.status = 1
SQL;

        if ($jackpot !== '') {
            $sql .= ' AND s.jackpot_name = :jackpot';
            $params[':jackpot'] = $jackpot;
        }
        if ($category !== '') {
            $sql .= ' AND s.category = :category';
            $params[':category'] = $category;
        }

        if ($explicitTipsId !== '') {
            $sql .= ' AND s.jackpot_tips_id = :tips_id';
            $params[':tips_id'] = $explicitTipsId;
        } elseif ($previousRound && $jackpot !== '') {
            $roundId = $this->previousJackpotTipsId($jackpot);
            if ($roundId !== null) {
                $sql .= ' AND s.jackpot_tips_id = :tips_id';
                $params[':tips_id'] = $roundId;
            } else {
                return [];
            }
        } elseif ($latestRound && $jackpot !== '') {
            $roundId = $this->latestJackpotTipsId($jackpot);
            if ($roundId !== null) {
                $sql .= ' AND s.jackpot_tips_id = :tips_id';
                $params[':tips_id'] = $roundId;
            } else {
                $sql .= ' AND s.fixture_date >= CURDATE() - INTERVAL 2 DAY';
            }
        } else {
            $range = $this->resolveDateRange($filters);
            $sql .= ' AND DATE(s.fixture_date) BETWEEN :date_from AND :date_to';
            $params[':date_from'] = $range['from'];
            $params[':date_to'] = $range['to'];
        }

        $sql .= ' ORDER BY s.jackpot_position ASC, s.fixture_date ASC, s.id ASC LIMIT ' . $limit;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();

        $out = [];
        foreach ($rows as $row) {
            $out[] = $this->mapSelectionRow($row);
        }
        return $out;
    }

    /**
     * Hub cards: latest round per major jackpot product.
     *
     * @param array<string,mixed> $filters
     * @return list<array<string,mixed>>
     */
    public function listJackpotHub(array $filters = []): array
    {
        $names = [
            'Sportpesa Mega Jackpot',
            'Sportpesa Midweek Jackpot',
            'Betika Midweek Jackpot',
            'Sporty bet Jackpot',
            'Odibet Laki Tatu Jackpot',
            'Mozzart Super Daily Jackpot',
        ];

        $out = [];
        foreach ($names as $name) {
            $games = $this->listFromSelections([
                'jackpot' => $name,
                'latest_round' => true,
                'limit' => (int) ($filters['limit'] ?? 20),
            ]);
            $out[] = [
                'jackpot_name' => $name,
                'slug' => $this->jackpotSlug($name),
                'count' => count($games),
                'games' => $games,
            ];
        }
        return $out;
    }

    /**
     * Build 3/5/8-fold accumulator tickets from high-confidence games.
     * Prioritises main leagues and avoids repeating fixtures across tickets.
     *
     * @param list<array<string,mixed>> $games
     * @param array{include_settled?: bool} $opts include_settled=true keeps FT legs (for yesterday results)
     * @return list<array<string,mixed>>
     */
    public function buildAccumulators(array $games, array $opts = []): array
    {
        $includeSettled = !empty($opts['include_settled']);
        $usable = array_values(array_filter($games, function ($g) use ($includeSettled) {
            $odds = isset($g['odds']) ? (float) $g['odds'] : 0;
            if ($odds < 1.20 || $odds > 3.50 || empty($g['pick'])) {
                return false;
            }
            $status = strtoupper((string) ($g['status'] ?? ''));
            if ($includeSettled) {
                // Historical reconstruct: keep FT, drop voids / abandoned.
                if (in_array($status, ['PST', 'CANC', 'ABD'], true)) {
                    return false;
                }
            } elseif (in_array($status, ['FT', 'AET', 'PEN', 'PST', 'CANC', 'ABD', 'AWD', 'WO'], true)) {
                // Live board: skip settled matches — accas are for upcoming / live tips
                return false;
            }
            // Skip reserves / youth / most women's sides for published tickets
            if ($this->isLowTierFixture($g)) {
                return false;
            }
            return true;
        }));

        usort($usable, function (array $a, array $b): int {
            $pa = $this->leaguePriority($a);
            $pb = $this->leaguePriority($b);
            if ($pa !== $pb) {
                return $pb <=> $pa;
            }
            $ca = (int) ($a['confidence'] ?? 0);
            $cb = (int) ($b['confidence'] ?? 0);
            if ($ca !== $cb) {
                return $cb <=> $ca;
            }
            return ((float) ($a['odds'] ?? 99)) <=> ((float) ($b['odds'] ?? 99));
        });

        $pool = $usable;
        $tickets = [];
        foreach ([3, 5, 8] as $legs) {
            $ticket = $this->takeAccumulatorLegs($pool, $legs);
            if ($ticket === null) {
                continue;
            }
            [$picks, $pool] = $ticket;
            $combined = 1.0;
            $confSum = 0;
            foreach ($picks as $g) {
                $combined *= (float) $g['odds'];
                $confSum += (int) $g['confidence'];
            }
            $tickets[] = [
                'name' => $legs . '-Fold Acca',
                'legs' => $legs,
                'combined_odds' => round($combined, 2),
                'blended_confidence' => (int) round($confSum / $legs),
                'picks' => array_map(static function (array $g): array {
                    $market = (string) ($g['market'] ?? '1x2');
                    if (in_array($market, ['best', 'mixed', 'betnumbers', ''], true)) {
                        $market = '1x2';
                    }
                    return [
                        'home' => $g['home'],
                        'away' => $g['away'],
                        'pick' => $g['pick'],
                        'pick_code' => $g['pick_code'] ?? null,
                        'market' => $market,
                        'odds' => $g['odds'],
                        'confidence' => $g['confidence'],
                        'league' => $g['league'] ?? '',
                        'fixture_id' => $g['fixture_id'] ?? null,
                    ];
                }, $picks),
            ];
        }
        return $this->enrichAccumulatorTickets($tickets);
    }

    /**
     * Reconstruct yesterday-style accumulator tickets from a calendar day's fixtures.
     * Prefer a frozen snapshot when present; otherwise build from that day's tips and freeze it.
     *
     * @param array<string,mixed> $pageDef accumulator-tips page filters
     * @return list<array<string,mixed>>
     */
    public function buildAccumulatorsForDate(string $dateYmd, array $pageDef = []): array
    {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateYmd)) {
            return [];
        }
        $games = $this->listGames([
            'date' => $dateYmd,
            'limit' => max(40, min(120, (int) ($pageDef['limit'] ?? 80))),
            'market' => $pageDef['market'] ?? 'best',
            'min_confidence' => $pageDef['min_confidence'] ?? 58,
            'order' => $pageDef['order'] ?? 'confidence_desc',
            // No upcoming_only — we need finished matches for results.
        ]);
        return $this->buildAccumulators($games, ['include_settled' => true]);
    }

    /**
     * Attach live/FT scores and ✅/❌ settlement onto accumulator legs.
     *
     * @param list<array<string,mixed>> $tickets
     * @return list<array<string,mixed>>
     */
    public function enrichAccumulatorTickets(array $tickets): array
    {
        $ids = [];
        foreach ($tickets as $ticket) {
            foreach (($ticket['picks'] ?? []) as $pick) {
                $fid = (int) ($pick['fixture_id'] ?? 0);
                if ($fid > 0) {
                    $ids[$fid] = $fid;
                }
            }
        }
        if ($ids === []) {
            return $tickets;
        }

        $fixtures = $this->fixtureRowsByIds(array_values($ids));
        $out = [];
        foreach ($tickets as $ticket) {
            $picks = [];
            $settled = 0;
            $hits = 0;
            $misses = 0;
            foreach (($ticket['picks'] ?? []) as $pick) {
                if (!is_array($pick)) {
                    continue;
                }
                $fid = (int) ($pick['fixture_id'] ?? 0);
                $row = $fixtures[$fid] ?? null;
                if ($row === null) {
                    $picks[] = $pick;
                    continue;
                }
                $market = (string) ($pick['market'] ?? '1x2');
                if (in_array($market, ['best', 'mixed', 'betnumbers', ''], true)) {
                    $market = '1x2';
                }
                $code = (string) ($pick['pick_code'] ?? $pick['pick'] ?? '');
                $status = strtoupper(trim((string) ($row['status_short'] ?? '')));
                $gh = $row['goals_home'];
                $ga = $row['goals_away'];
                $score = ($gh !== null && $ga !== null) ? ((int) $gh . '-' . (int) $ga) : null;
                $won = null;
                $winning = null;
                if ($gh !== null && $ga !== null) {
                    if (in_array($status, ['FT', 'AET', 'PEN', 'AWD', 'WO'], true)) {
                        $won = $this->tipMatchesScore($code, $market, (int) $gh, (int) $ga, $row);
                    } elseif ($this->isLiveStatus($status)) {
                        $winning = $this->tipMatchesScore($code, $market, (int) $gh, (int) $ga, $row);
                    }
                }
                $pick['score'] = $score;
                $pick['status'] = $status;
                $pick['won'] = $won;
                $pick['winning'] = $winning;
                if ($won === true) {
                    $settled++;
                    $hits++;
                } elseif ($won === false) {
                    $settled++;
                    $misses++;
                }
                $picks[] = $pick;
            }
            $ticket['picks'] = $picks;
            $ticket['legs_settled'] = $settled;
            $ticket['legs_hit'] = $hits;
            $ticket['legs_missed'] = $misses;
            $legCount = count($picks);
            $ticket['won'] = ($legCount > 0 && $settled === $legCount)
                ? ($misses === 0)
                : null;
            $out[] = $ticket;
        }
        return $out;
    }

    /**
     * @param list<int> $fixtureIds
     * @return array<int,array<string,mixed>>
     */
    private function fixtureRowsByIds(array $fixtureIds): array
    {
        $fixtureIds = array_values(array_unique(array_filter(array_map('intval', $fixtureIds))));
        if ($fixtureIds === []) {
            return [];
        }
        $placeholders = implode(',', array_fill(0, count($fixtureIds), '?'));
        $sql = "SELECT fixture_id, status_short, goals_home, goals_away,
                       ht_goals_home, ht_goals_away
                FROM fixtures WHERE fixture_id IN ($placeholders)";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($fixtureIds);
        } catch (\Throwable $e) {
            // Older schemas may lack HT columns — fall back to FT fields only.
            $sql = "SELECT fixture_id, status_short, goals_home, goals_away
                    FROM fixtures WHERE fixture_id IN ($placeholders)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($fixtureIds);
        }
        $map = [];
        foreach ($stmt->fetchAll() as $row) {
            $map[(int) $row['fixture_id']] = $row;
        }
        return $map;
    }

    /**
     * @param array<string,mixed> $g
     */
    private function isLowTierFixture(array $g): bool
    {
        $league = (string) ($g['league'] ?? '');
        $home = (string) ($g['home'] ?? '');
        $away = (string) ($g['away'] ?? '');
        $blob = $league . ' ' . $home . ' ' . $away;
        if (preg_match('/\b(U17|U18|U19|U20|U21|U23|II|III|Reserves?|Youth|Women|Womens|Femenil|Femenina|Feminine|Ladies|WSL)\b/i', $blob)) {
            return true;
        }
        if (preg_match('/\b(3\. Liga|4\. Liga|Esiliiga|III Liga|NB III|Segunda División RFEF|Campeonato De Portugal|Division 2|C League|3\. SNL|2\. SNL)\b/i', $league)) {
            return true;
        }
        // Obscure "Premier League" labels outside big football nations
        if (preg_match('/premier league/i', $league)
            && !in_array(strtolower((string) ($g['country'] ?? '')), ['england', 'scotland', 'wales', 'northern-ireland', 'kenya', 'nigeria', 'egypt', 'ghana', 'south-africa'], true)) {
            return true;
        }
        return false;
    }

    /**
     * Take the next $legs games from $pool (already priority-sorted), remove them from pool.
     * Diversifies tip types so tickets are not all Home Win (or any single market).
     *
     * @param list<array<string,mixed>> $pool
     * @return array{0:list<array<string,mixed>>,1:list<array<string,mixed>>}|null
     */
    private function takeAccumulatorLegs(array $pool, int $legs): ?array
    {
        if (count($pool) < $legs) {
            return null;
        }

        $maxSameCode = max(1, (int) ceil($legs / 2));
        $chosen = [];
        $codeCounts = [];

        // Pass 1: prefer main leagues with tip diversity
        // Pass 2: any remaining with tip diversity
        // Pass 3: fill without diversity if still short
        for ($pass = 1; $pass <= 3; $pass++) {
            foreach ($pool as $g) {
                if (count($chosen) >= $legs) {
                    break 2;
                }
                $id = (int) ($g['fixture_id'] ?? 0);
                foreach ($chosen as $c) {
                    if ($id !== 0 && (int) ($c['fixture_id'] ?? 0) === $id) {
                        continue 2;
                    }
                }

                $prio = $this->leaguePriority($g);
                if ($pass === 1 && $prio < 70) {
                    continue;
                }

                $code = strtoupper((string) ($g['pick_code'] ?? $g['pick'] ?? ''));
                $same = $codeCounts[$code] ?? 0;
                if ($pass < 3 && $same >= $maxSameCode) {
                    continue;
                }

                $chosen[] = $g;
                $codeCounts[$code] = $same + 1;
            }
        }

        if (count($chosen) < $legs) {
            return null;
        }

        $chosenIds = [];
        foreach ($chosen as $g) {
            $chosenIds[(int) ($g['fixture_id'] ?? 0)] = true;
        }
        $remaining = array_values(array_filter(
            $pool,
            static function (array $g) use ($chosenIds): bool {
                $id = (int) ($g['fixture_id'] ?? 0);
                return $id === 0 || !isset($chosenIds[$id]);
            }
        ));

        return [$chosen, $remaining];
    }

    /**
     * Higher = more preferred for accumulator legs.
     *
     * @param array<string,mixed> $g
     */
    private function leaguePriority(array $g): int
    {
        // DB flag from leagues.popular_status
        if ((int) ($g['popular'] ?? 0) > 0) {
            return 100;
        }

        $id = (int) ($g['league_id'] ?? 0);
        $league = strtolower((string) ($g['league'] ?? ''));
        $country = strtolower((string) ($g['country'] ?? ''));

        // Top European + UEFA club competitions + strong domestic leagues
        $eliteIds = [
            2 => 100,   // UEFA Champions League
            3 => 95,    // UEFA Europa League
            848 => 90,  // UEFA Conference League
            39 => 100,  // England Premier League
            140 => 98,  // Spain La Liga
            135 => 97,  // Italy Serie A
            78 => 96,   // Germany Bundesliga
            61 => 95,   // France Ligue 1
            40 => 80,   // England Championship
            88 => 78,   // Netherlands Eredivisie
            94 => 78,   // Portugal Primeira Liga
            203 => 75,  // Turkey Süper Lig
            253 => 72,  // MLS
            71 => 74,   // Brazil Serie A
            128 => 74,  // Argentina Liga Profesional (approx — may vary)
            262 => 73,  // Liga MX
            98 => 72,   // Japan J1
            292 => 70,  // K League 1
            144 => 76,  // Belgium Jupiler (if present)
            179 => 76,  // Scotland Premiership (if present)
        ];
        if (isset($eliteIds[$id])) {
            return $eliteIds[$id];
        }

        // Kenya / East & Southern Africa focus
        if (str_contains($country, 'kenya') || str_contains($league, 'kenya')) {
            return 85;
        }
        if (in_array($country, ['nigeria', 'egypt', 'ghana', 'south-africa', 'south africa', 'uganda', 'tanzania'], true)
            || str_contains($league, 'npfl')
            || str_contains($league, 'premier soccer')) {
            return 70;
        }

        // Name heuristics for major competitions (when IDs differ)
        if (preg_match('/champions league|europa league|conference league/i', $league)) {
            return 92;
        }
        if ($country === 'england' && str_contains($league, 'premier')) {
            return 100;
        }
        if ($country === 'spain' && (str_contains($league, 'la liga') || $league === 'laliga')) {
            return 98;
        }
        if ($country === 'italy' && str_contains($league, 'serie a')) {
            return 97;
        }
        if ($country === 'germany' && $league === 'bundesliga') {
            return 96;
        }
        if ($country === 'france' && str_contains($league, 'ligue 1')) {
            return 95;
        }
        if (in_array($country, ['brazil', 'argentina', 'mexico', 'japan', 'south-korea', 'portugal', 'netherlands', 'turkey', 'usa'], true)
            && preg_match('/serie a|liga mx|j1|k league 1|primeira|eredivisie|süper|super lig|major league soccer|mls|liga profesional/i', $league)) {
            return 72;
        }

        // Generic top-flight names only for notable football countries
        if (preg_match('/^(premier league|primera división|serie a)$/i', trim($league))
            && in_array($country, ['england', 'spain', 'italy', 'scotland', 'belgium', 'portugal', 'netherlands'], true)) {
            return 80;
        }

        return 10;
    }

    private function latestJackpotTipsId(string $jackpot): ?string
    {
        $ids = $this->recentJackpotTipsIds($jackpot, 1);
        return $ids[0] ?? null;
    }

    private function previousJackpotTipsId(string $jackpot): ?string
    {
        $ids = $this->recentJackpotTipsIds($jackpot, 2);
        return $ids[1] ?? null;
    }

    /**
     * Distinct jackpot round ids, newest first (by latest fixture date in the round).
     *
     * @return list<string>
     */
    private function recentJackpotTipsIds(string $jackpot, int $limit = 2): array
    {
        $limit = max(1, min(10, $limit));
        $stmt = $this->db->prepare(
            'SELECT jackpot_tips_id
             FROM pp_fixtures_selections
             WHERE jackpot_name = :j
               AND status = 1
               AND jackpot_tips_id IS NOT NULL
               AND jackpot_tips_id != ""
             GROUP BY jackpot_tips_id
             ORDER BY MAX(fixture_date) DESC, MAX(id) DESC
             LIMIT ' . $limit
        );
        $stmt->execute([':j' => $jackpot]);
        $out = [];
        foreach ($stmt->fetchAll(\PDO::FETCH_COLUMN) as $id) {
            if ($id !== null && $id !== '') {
                $out[] = (string) $id;
            }
        }
        return $out;
    }

    private function jackpotSlug(string $name): string
    {
        return match ($name) {
            'Sportpesa Mega Jackpot' => 'sportpesa-mega-jackpot-predictions',
            'Sportpesa Midweek Jackpot' => 'sportpesa-midweek-jackpot-predictions',
            'Betika Midweek Jackpot' => 'betika-midweek-jackpot-predictions',
            'Sporty bet Jackpot' => 'sportybet-daily-jackpot-predictions',
            'Odibet Laki Tatu Jackpot' => 'odibets-laki-tatu-predictions',
            'Mozzart Super Daily Jackpot' => 'mozzart-super-daily-jackpot-predictions',
            default => strtolower(preg_replace('/[^a-z0-9]+/i', '-', $name) ?? 'jackpot'),
        };
    }

    /**
     * Popular leagues first; if that slate is empty or thin, fill remaining slots
     * with the strongest model leans (home/draw/away %).
     *
     * @param list<array<string,mixed>> $games
     * @return list<array<string,mixed>>
     */
    private function selectBoardGames(array $games, int $limit, string $order): array
    {
        if ($games === [] || $limit <= 0) {
            return [];
        }

        // Chronological boards (Yesterday / Results archive): pure kickoff order, no popular bucketing.
        if ($order === 'kickoff_asc' || $order === 'kickoff_desc') {
            return array_slice($this->sortGamesWithinBucket($games, $order), 0, $limit);
        }

        $popular = [];
        $rest = [];
        foreach ($games as $g) {
            if ((int) ($g['popular'] ?? 0) > 0) {
                $popular[] = $g;
            } else {
                $rest[] = $g;
            }
        }

        // Within each bucket, apply the page order (no hard popular boost here).
        $popular = $this->sortGamesWithinBucket($popular, $order);
        // Fillers always prefer high win-chance when the board ranks by confidence,
        // or when kickoff boards need padding beyond a thin popular slate.
        $fillOrder = $order === 'confidence_desc' ? 'confidence_desc' : $order;
        $rest = $this->sortGamesWithinBucket($rest, $fillOrder);
        if ($order === 'confidence_desc' || count($popular) < $limit) {
            $rest = $this->sortByLeanStrength($rest);
        }

        $out = $popular;
        if (count($out) < $limit) {
            foreach ($rest as $g) {
                $out[] = $g;
                if (count($out) >= $limit) {
                    break;
                }
            }
        }

        return array_slice($out, 0, $limit);
    }

    /**
     * Sort within one league-popularity bucket (popular OR non-popular).
     *
     * @param list<array<string,mixed>> $games
     * @return list<array<string,mixed>>
     */
    private function sortGamesWithinBucket(array $games, string $order): array
    {
        usort($games, static function ($a, $b) use ($order) {
            if ($order === 'confidence_desc') {
                $ca = (int) ($a['confidence'] ?? 0);
                $cb = (int) ($b['confidence'] ?? 0);
                if ($ca !== $cb) {
                    return $cb <=> $ca;
                }
            }
            $ka = (string) ($a['kickoff'] ?? '');
            $kb = (string) ($b['kickoff'] ?? '');
            if ($ka !== $kb) {
                return $order === 'kickoff_desc' ? ($kb <=> $ka) : ($ka <=> $kb);
            }
            $fa = (int) ($a['fixture_id'] ?? 0);
            $fb = (int) ($b['fixture_id'] ?? 0);
            return $order === 'kickoff_desc' ? ($fb <=> $fa) : ($fa <=> $fb);
        });
        return $games;
    }

    /**
     * Strongest 1X2 model share first (home/draw/away %), then published confidence.
     *
     * @param list<array<string,mixed>> $games
     * @return list<array<string,mixed>>
     */
    private function sortByLeanStrength(array $games): array
    {
        usort($games, function ($a, $b) {
            $sa = $this->leanStrength($a);
            $sb = $this->leanStrength($b);
            if ($sa !== $sb) {
                return $sb <=> $sa;
            }
            $ca = (int) ($a['confidence'] ?? 0);
            $cb = (int) ($b['confidence'] ?? 0);
            if ($ca !== $cb) {
                return $cb <=> $ca;
            }
            $ka = (string) ($a['kickoff'] ?? '');
            $kb = (string) ($b['kickoff'] ?? '');
            if ($ka !== $kb) {
                return $ka <=> $kb;
            }
            return ((int) ($a['fixture_id'] ?? 0)) <=> ((int) ($b['fixture_id'] ?? 0));
        });
        return $games;
    }

    /**
     * Best available win chance from model home/draw/away %, else tip confidence.
     *
     * @param array<string,mixed> $g
     */
    private function leanStrength(array $g): int
    {
        $probs = is_array($g['probs'] ?? null) ? $g['probs'] : [];
        $h = (int) ($probs['home'] ?? 0);
        $d = (int) ($probs['draw'] ?? 0);
        $a = (int) ($probs['away'] ?? 0);
        $max = max($h, $d, $a);
        if ($max > 0) {
            return $max;
        }
        return (int) ($g['confidence'] ?? 0);
    }

    /**
     * @param array<string,mixed> $row
     * @return array<string,mixed>
     */
    private function mapFixtureRow(array $row, string $market = '1x2'): array
    {
        $pickMeta = $this->deriveMarketPick($row, $market);
        $resolvedMarket = (string) ($pickMeta['market'] ?? $market);
        if (in_array($resolvedMarket, ['best', 'mixed', 'betnumbers'], true)) {
            $resolvedMarket = '1x2';
        }
        $kickoffRaw = (string) ($row['kickoff'] ?? '');
        $sourceTz = (string) ($row['timezone'] ?? DateTimeHelper::SOURCE_TZ);
        $when = DateTimeHelper::formatKickoff($kickoffRaw, $sourceTz);
        $goalsHome = $row['goals_home'];
        $goalsAway = $row['goals_away'];
        $score = ($goalsHome !== null && $goalsAway !== null)
            ? ((int) $goalsHome . '-' . (int) $goalsAway)
            : null;
        $won = $this->evaluateWon($row, $pickMeta['code'], $resolvedMarket, (string) ($row['status_short'] ?? ''));
        $statusShort = strtoupper(trim((string) ($row['status_short'] ?? '')));
        $isLive = $this->isLiveStatus($statusShort);
        $winning = null;
        if ($won === null && $isLive && $goalsHome !== null && $goalsAway !== null) {
            $winning = $this->tipMatchesScore(
                (string) ($pickMeta['code'] ?? ''),
                $resolvedMarket,
                (int) $goalsHome,
                (int) $goalsAway,
                $row
            );
        }

        return [
            'fixture_id' => (int) $row['fixture_id'],
            'league_id' => (int) ($row['league_id'] ?? 0),
            'league' => (string) ($row['league_name'] ?? 'Football'),
            'country' => (string) ($row['country_name'] ?? ''),
            'popular' => (int) ($row['popular_status'] ?? 0),
            'home' => (string) $row['home_team_name'],
            'away' => (string) $row['away_team_name'],
            'home_logo' => (string) ($row['home_team_logo'] ?? ''),
            'away_logo' => (string) ($row['away_team_logo'] ?? ''),
            'date' => $when['date'],
            'time' => $when['time'],
            'time_clock' => $when['time_clock'],
            'date_label' => $when['date_label'] ?? null,
            'kickoff' => $when['kickoff'] !== '' ? $when['kickoff'] : $kickoffRaw,
            'kickoff_utc' => $when['kickoff_utc'],
            'kickoff_iso' => $when['iso'],
            'timezone' => $when['display_tz'],
            'timezone_source' => $sourceTz !== '' ? $sourceTz : DateTimeHelper::SOURCE_TZ,
            'status' => $statusShort,
            'status_long' => (string) ($row['status_long'] ?? ''),
            'is_live' => $isLive,
            'score' => $score,
            'won' => $won,
            'winning' => $winning,
            'pick' => $pickMeta['pick'],
            'pick_code' => $pickMeta['code'],
            'odds' => $pickMeta['odds'],
            'confidence' => $pickMeta['confidence'],
            'reason' => $this->alignedAdvice($row, $pickMeta, $resolvedMarket),
            'market' => $resolvedMarket,
            'market_label' => $this->marketLabel($resolvedMarket),
            'btts' => $row['both_team_to_score'] ?? null,
            'btts_prob' => isset($row['both_teams_percentage_prob']) ? (int) $row['both_teams_percentage_prob'] : null,
            'probs' => [
                'home' => isset($row['percent_pred_home']) ? (int) $row['percent_pred_home'] : null,
                'draw' => isset($row['percent_pred_draw']) ? (int) $row['percent_pred_draw'] : null,
                'away' => isset($row['percent_pred_away']) ? (int) $row['percent_pred_away'] : null,
            ],
            'bookmaker' => $row['bookmaker_name'] ?? null,
            'source' => 'fixtures',
        ];
    }

    /**
     * Settle tip vs FT score. null = not settled / unknown.
     *
     * @param array<string,mixed> $row
     */
    private function evaluateWon(array $row, string $code, string $market, string $status): ?bool
    {
        $status = strtoupper(trim($status));
        if (!in_array($status, ['FT', 'AET', 'PEN', 'AWD', 'WO'], true)) {
            return null;
        }
        if ($row['goals_home'] === null || $row['goals_away'] === null) {
            return null;
        }

        return $this->tipMatchesScore(
            $code,
            $market,
            (int) $row['goals_home'],
            (int) $row['goals_away'],
            $row
        );
    }

    /**
     * Whether the tip is currently correct for a given scoreline.
     *
     * @param array<string,mixed> $row
     */
    private function tipMatchesScore(string $code, string $market, int $gh, int $ga, array $row = []): ?bool
    {
        $code = $this->normalizePickCode($code, $market);

        return match ($market) {
            'double_chance' => $this->wonDoubleChance($code, $gh, $ga),
            'over_under' => $this->wonOverUnder($code, $gh, $ga),
            'btts' => $this->wonBtts($code, $gh, $ga),
            'correct_score' => $this->wonCorrectScore($code, $gh, $ga),
            'ht_ft' => $this->wonHtFt($code, $row, $gh, $ga),
            default => $this->won1x2($code, $gh, $ga),
        };
    }

    private function normalizePickCode(string $code, string $market = '1x2'): string
    {
        $c = strtoupper(trim($code));
        $c = str_replace(['_', '-'], ' ', $c);
        $c = preg_replace('/\s+/', ' ', $c) ?? $c;

        if (in_array($c, ['1', 'HOME', 'HOME WIN', 'W1', 'HW'], true) || str_starts_with($c, 'HOME WIN')) {
            return '1';
        }
        if (in_array($c, ['2', 'AWAY', 'AWAY WIN', 'W2', 'AW'], true) || str_starts_with($c, 'AWAY WIN')) {
            return '2';
        }
        if (in_array($c, ['X', 'DRAW', 'D'], true) || $c === 'DRAW') {
            return 'X';
        }
        if (in_array($c, ['1X', 'X2', '12'], true)) {
            return $c;
        }
        if (in_array($c, ['O2.5', 'OVER 2.5', 'OVER 2.5 GOALS', 'OVER'], true) || str_starts_with($c, 'O2')) {
            return 'O2.5';
        }
        if (in_array($c, ['U2.5', 'UNDER 2.5', 'UNDER 2.5 GOALS', 'UNDER'], true) || str_starts_with($c, 'U2')) {
            return 'U2.5';
        }
        if (str_contains($c, 'BTTS') && (str_contains($c, 'YES') || str_ends_with($c, 'Y'))) {
            return 'BTTS_YES';
        }
        if (str_contains($c, 'BTTS') && (str_contains($c, 'NO') || str_ends_with($c, 'N'))) {
            return 'BTTS_NO';
        }

        return strtoupper(trim($code));
    }

    private function isLiveStatus(string $status): bool
    {
        return in_array($status, ['1H', '2H', 'HT', 'ET', 'BT', 'P', 'LIVE', 'INT', 'BREAK', 'SUSP'], true);
    }

    private function won1x2(string $code, int $gh, int $ga): bool
    {
        $code = $this->normalizePickCode($code, '1x2');
        $actual = $gh > $ga ? '1' : ($gh < $ga ? '2' : 'X');
        return $code === $actual;
    }

    private function wonDoubleChance(string $code, int $gh, int $ga): bool
    {
        $code = $this->normalizePickCode($code, 'double_chance');
        $actual = $gh > $ga ? '1' : ($gh < $ga ? '2' : 'X');
        return match ($code) {
            '1X' => $actual === '1' || $actual === 'X',
            'X2' => $actual === 'X' || $actual === '2',
            '12' => $actual === '1' || $actual === '2',
            // Plain 1X2 codes sometimes land on DC pages — treat as exact result.
            '1', '2', 'X' => $code === $actual,
            default => false,
        };
    }

    private function wonOverUnder(string $code, int $gh, int $ga): bool
    {
        $code = $this->normalizePickCode($code, 'over_under');
        $total = $gh + $ga;
        if ($code === 'O2.5' || str_starts_with($code, 'O')) {
            return $total > 2;
        }
        return $total < 3;
    }

    private function wonBtts(string $code, int $gh, int $ga): bool
    {
        $code = $this->normalizePickCode($code, 'btts');
        $both = $gh > 0 && $ga > 0;
        if ($code === 'BTTS_YES' || str_contains($code, 'YES')) {
            return $both;
        }
        return !$both;
    }

    private function wonCorrectScore(string $code, int $gh, int $ga): bool
    {
        $expected = preg_replace('/^CS_/', '', $code) ?? '';
        return $expected === ($gh . '-' . $ga);
    }

    /** @param array<string,mixed> $row */
    private function wonHtFt(string $code, array $row, int $gh, int $ga): ?bool
    {
        if ($row['ht_goals_home'] === null || $row['ht_goals_away'] === null) {
            return null;
        }
        $hh = (int) $row['ht_goals_home'];
        $ha = (int) $row['ht_goals_away'];
        $ht = $hh > $ha ? '1' : ($hh < $ha ? '2' : 'X');
        $ft = $gh > $ga ? '1' : ($gh < $ga ? '2' : 'X');
        return $code === ($ht . '/' . $ft);
    }

    /**
     * @param array<string,mixed> $row
     * @return array{pick:string,code:string,odds:?string,confidence:int,market?:string}
     */
    private function deriveMarketPick(array $row, string $market): array
    {
        return match ($market) {
            'best', 'mixed', 'betnumbers' => $this->deriveBestMarketPick($row),
            'double_chance' => $this->deriveDoubleChance($row) + ['market' => 'double_chance'],
            'over_under' => $this->deriveOverUnder($row) + ['market' => 'over_under'],
            'btts' => $this->deriveBtts($row) + ['market' => 'btts'],
            'correct_score' => $this->deriveCorrectScore($row) + ['market' => 'correct_score'],
            'ht_ft' => $this->deriveHtFt($row) + ['market' => 'ht_ft'],
            default => $this->derive1x2($row) + ['market' => '1x2'],
        };
    }

    /**
     * Pick the strongest lean across 1X2, BTTS, Over/Under 2.5, and Double Chance.
     * Accuracy-first: use model home/draw/away % + odds fit; prefer shorter prices
     * that the model actually supports (avoid home-win @ 3.0 style traps).
     *
     * @param array<string,mixed> $row
     * @return array{pick:string,code:string,odds:?string,confidence:int,market:string}
     */
    private function deriveBestMarketPick(array $row): array
    {
        $candidates = [
            $this->derive1x2($row) + ['market' => '1x2'],
            $this->deriveBtts($row) + ['market' => 'btts'],
            $this->deriveOverUnder($row) + ['market' => 'over_under'],
            $this->deriveDoubleChance($row) + ['market' => 'double_chance'],
        ];

        $scored = [];
        foreach ($candidates as $c) {
            $odd = $this->oddFloat($c['odds'] ?? null);
            if ($odd === null || $odd < 1.18 || $odd > 3.10) {
                continue;
            }

            $modelPct = $this->modelWinPercent($row, $c);
            if ($modelPct < 52) {
                continue;
            }

            // Long prices need a clear model majority — otherwise prefer DC / O/U / BTTS.
            if ($odd >= 2.50 && $modelPct < 58) {
                continue;
            }
            if ($odd >= 2.80 && $modelPct < 62) {
                continue;
            }
            if ($odd >= 3.0 && $c['market'] === '1x2') {
                continue;
            }

            $fair = $modelPct > 0 ? (100.0 / $modelPct) : 99.0;
            // Book much longer than the model → poor accuracy candidate.
            if ($odd > $fair * 1.28 && $odd >= 2.15) {
                continue;
            }

            // Hit-rate score: model chance first, then prefer shorter fitting odds.
            $score = (float) $modelPct;
            $score += max(0.0, (2.35 - min($odd, 2.35)) * 7.0);
            $score -= max(0.0, ($odd - $fair) * 18.0);

            // Demote soft long 1X2; boost strong short-priced favourites.
            if ($c['market'] === '1x2' && $odd >= 2.40) {
                $score -= 10.0;
            }
            if ($c['market'] === '1x2' && $odd <= 2.05 && $modelPct >= 60) {
                $score += 8.0;
            }
            // Ultra-short 1X2 bankers belong on Must-Win / Today — nudge mixed boards away from them.
            if ($c['market'] === '1x2' && $odd < 1.28) {
                $score -= 5.0;
            }

            // Ultra-short DC pads hit rate but crowds out clearer singles / goals tips.
            if ($c['market'] === 'double_chance') {
                $score -= 6.0;
                if ($odd < 1.28) {
                    $score -= 8.0;
                }
            }

            // Goals markets: promote when price is stakeable and avg-goals lean is solid.
            if ($c['market'] === 'over_under' && $odd <= 1.95 && $modelPct >= 58) {
                $score += 9.0;
            }
            if ($c['market'] === 'btts' && $odd <= 2.05 && $modelPct >= 60) {
                $score += 5.0;
            }
            // Prefer a competitive non-1X2 lean so BetNumbers / Sure Bets diverge from the 1X2 boards.
            if ($c['market'] !== '1x2') {
                $score += 4.0;
            }

            // Publish tempered model lean (not a win-rate promise). Cap below the old
            // 88 ceiling so Double Chance cards do not all stamp the same number.
            $c['confidence'] = $this->publishConfidence($modelPct, (string) ($c['market'] ?? ''));

            $scored[] = [
                'pick' => $c,
                'score' => $score,
                'model' => $modelPct,
                'odd' => $odd,
                'fit' => abs($odd - $fair),
            ];
        }

        if ($scored === []) {
            return $this->saferFallbackPick($row);
        }

        usort($scored, static function (array $a, array $b): int {
            if (abs($a['score'] - $b['score']) > 0.35) {
                return $b['score'] <=> $a['score'];
            }
            if ($a['model'] !== $b['model']) {
                return $b['model'] <=> $a['model'];
            }
            if (abs($a['fit'] - $b['fit']) > 0.05) {
                return $a['fit'] <=> $b['fit'];
            }
            return $a['odd'] <=> $b['odd'];
        });

        /** @var array{pick:string,code:string,odds:?string,confidence:int,market:string} $best */
        $best = $scored[0]['pick'];
        return $best;
    }

    /**
     * Model win % for a candidate from predictions_computation home/draw/away (and peers).
     *
     * @param array<string,mixed> $row
     * @param array{code?:string,market?:string,confidence?:int} $c
     */
    private function modelWinPercent(array $row, array $c): int
    {
        $h = (int) ($row['percent_pred_home'] ?? 0);
        $d = (int) ($row['percent_pred_draw'] ?? 0);
        $a = (int) ($row['percent_pred_away'] ?? 0);
        $code = strtoupper((string) ($c['code'] ?? ''));
        $market = (string) ($c['market'] ?? '');

        return match ($market) {
            '1x2' => match ($code) {
                '1' => $h,
                '2' => $a,
                'X' => $d,
                default => (int) ($c['confidence'] ?? 0),
            },
            // Do not publish home+draw sums (they pile up at a fake ~88% ceiling).
            'double_chance' => match ($code) {
                '1X' => $this->doubleChanceLeanPercent($h, $d, $a),
                'X2' => $this->doubleChanceLeanPercent($d, $a, $h),
                '12' => $this->doubleChanceLeanPercent($h, $a, $d),
                default => (int) ($c['confidence'] ?? 0),
            },
            'btts', 'over_under' => (int) ($c['confidence'] ?? 0),
            default => (int) ($c['confidence'] ?? 0),
        };
    }

    /**
     * Realistic DC lean for cards/ranking.
     * Do not publish raw home+draw sums (fake ~88% pile-up), and do not hard-cap so
     * many strong covers at one identical ceiling % — both read as placeholders.
     */
    private function doubleChanceLeanPercent(int $coveredA, int $coveredB, int $excluded): int
    {
        $primary = max($coveredA, $coveredB);
        $secondary = min($coveredA, $coveredB);
        $covered = min(95, $coveredA + $coveredB);

        // Scale the two-way cover down — DC tips are safer than 1X2, not near-certain.
        $lean = (int) round($covered * 0.78);

        // Stronger primary vs second cover pushes the dial up a little.
        $dominance = $primary - $secondary;
        if ($dominance > 20) {
            $lean += (int) round(min(5, ($dominance - 20) * 0.12));
        }

        // Uncovered outcome still live → soft pull-down.
        if ($excluded >= 28) {
            $lean -= (int) round(min(4, ($excluded - 28) * 0.2));
        }

        return max(58, min(80, $lean));
    }

    /**
     * Card-facing % band — honest lean, not a near-certainty stamp.
     */
    private function publishConfidence(int $modelPct, string $market): int
    {
        $cap = match ($market) {
            'double_chance' => 80,
            'over_under', 'btts' => 78,
            'ht_ft', 'correct_score' => 72,
            default => 82,
        };

        return $this->clampPublishedConfidence(max(50, min($cap, $modelPct)));
    }

    /**
     * Never publish 100% (or near-certainty) on tips cards — that reads as a guarantee
     * and contradicts the site RG disclaimer. Upstream prediction_confidence can be 100.
     */
    private function clampPublishedConfidence(int $pct): int
    {
        return max(40, min(85, $pct));
    }

    /**
     * When no candidate clears the accuracy gates, prefer short DC / O/U over long 1X2.
     *
     * @param array<string,mixed> $row
     * @return array{pick:string,code:string,odds:?string,confidence:int,market:string}
     */
    private function saferFallbackPick(array $row): array
    {
        $pool = [
            $this->deriveDoubleChance($row) + ['market' => 'double_chance'],
            $this->deriveOverUnder($row) + ['market' => 'over_under'],
            $this->deriveBtts($row) + ['market' => 'btts'],
            $this->derive1x2($row) + ['market' => '1x2'],
        ];

        $best = null;
        $bestScore = -1.0;
        foreach ($pool as $c) {
            $odd = $this->oddFloat($c['odds'] ?? null);
            if ($odd === null || $odd < 1.18 || $odd > 2.60) {
                continue;
            }
            $model = $this->modelWinPercent($row, $c);
            $score = $model - ($odd * 8.0);
            if ($score > $bestScore) {
                $bestScore = $score;
                $c['confidence'] = $this->publishConfidence($model, (string) ($c['market'] ?? ''));
                $best = $c;
            }
        }

        if ($best !== null) {
            return $best;
        }

        // Last resort still needs a real book price — never publish a bare DC with null odds.
        $fallback = $this->derive1x2($row) + ['market' => '1x2'];
        if ($this->oddFloat($fallback['odds'] ?? null) !== null) {
            $fallback['confidence'] = $this->publishConfidence(
                $this->modelWinPercent($row, $fallback),
                '1x2'
            );
            return $fallback;
        }

        $dc = $this->deriveDoubleChance($row) + ['market' => 'double_chance'];
        $dc['confidence'] = 0;
        return $dc;
    }

    private function oddFloat(mixed $odds): ?float
    {
        if ($odds === null || $odds === '' || $odds === '-') {
            return null;
        }
        $n = (float) $odds;
        return $n > 1.0 ? $n : null;
    }

    /** Book preference for odds rows (Bet365 → 10Bet → William Hill → others). */
    private function bookmakerPreferenceSql(string $alias): string
    {
        return "CASE {$alias}.bookmaker_name"
            . " WHEN 'Bet365' THEN 0"
            . " WHEN '10Bet' THEN 1"
            . " WHEN 'William Hill' THEN 2"
            . ' ELSE 9 END';
    }

    /**
     * Prefer the joined book's market price; if that column is empty, take the same
     * market from any other book for this fixture (same preference order).
     * Partial feeds often ship 1X2 without BTTS/O/U/DC on the preferred row.
     */
    private function coalesceOddsColSql(string $column): string
    {
        $allowed = [
            'over_2_5', 'under_2_5',
            'both_teams_to_score_yes', 'both_teams_to_score_no',
            'double_chance_home_draw', 'double_chance_draw_away', 'double_chance_home_away',
            'ht_home', 'ht_draw', 'ht_away',
        ];
        if (!in_array($column, $allowed, true)) {
            throw new \InvalidArgumentException('Unsupported odds column: ' . $column);
        }
        $usable = static function (string $expr) use ($column): string {
            return "({$expr} IS NOT NULL AND {$expr} NOT IN ('', '-') AND CAST({$expr} AS DECIMAL(10,2)) > 1.01)";
        };
        $pref = $usable("o.{$column}");
        $alt = $usable("o3.{$column}");
        return "COALESCE("
            . "CASE WHEN {$pref} THEN o.{$column} END,"
            . "(SELECT o3.{$column} FROM odds o3"
            . " WHERE o3.fixture_id = f.fixture_id AND {$alt}"
            . " ORDER BY {$this->bookmakerPreferenceSql('o3')}, o3.id ASC LIMIT 1)"
            . ')';
    }

    /**
     * Market tips without a book price must not publish — zero confidence so boards skip them.
     *
     * @param array{pick:string,code:string,odds:?string,confidence:int} $meta
     * @return array{pick:string,code:string,odds:?string,confidence:int}
     */
    private function requireMarketOdds(array $meta): array
    {
        if ($this->oddFloat($meta['odds'] ?? null) === null) {
            $meta['confidence'] = 0;
            $meta['odds'] = null;
        }
        return $meta;
    }

    /**
     * Align the tipped outcome to published confidence without breaking the three-way sum
     * (e.g. lean 45→published 45 with draw 32 + reverse 30 was summing to 107%).
     *
     * @return array{0:int,1:int,2:int} home, draw, away
     */
    private function alignProbsToPublished(int $h, int $d, int $a, string $code, int $published): array
    {
        if ($published <= 0) {
            return [$h, $d, $a];
        }
        $code = strtoupper(trim($code));
        if ($code === '1') {
            $rest = max(0, 100 - $published);
            $sum = $d + $a;
            if ($sum <= 0) {
                return [$published, $d, $a];
            }
            $d2 = (int) round($rest * ($d / $sum));
            return [$published, $d2, $rest - $d2];
        }
        if ($code === '2') {
            $rest = max(0, 100 - $published);
            $sum = $h + $d;
            if ($sum <= 0) {
                return [$h, $d, $published];
            }
            $h2 = (int) round($rest * ($h / $sum));
            return [$h2, $rest - $h2, $published];
        }
        if ($code === 'X') {
            $rest = max(0, 100 - $published);
            $sum = $h + $a;
            if ($sum <= 0) {
                return [$h, $published, $a];
            }
            $h2 = (int) round($rest * ($h / $sum));
            return [$h2, $published, $rest - $h2];
        }
        return [$h, $d, $a];
    }

    private function marketLabel(string $market): string
    {
        return match ($market) {
            'double_chance' => 'DC',
            'over_under' => 'O/U',
            'btts' => 'BTTS',
            'ht_ft' => 'HT/FT',
            'correct_score' => 'CS',
            default => '1X2',
        };
    }

    /**
     * One-line tip reason: soft YMYL tone + varied templates so cards do not all read the same.
     *
     * @param array<string,mixed> $row
     * @param array{pick?:string,code?:string,odds?:?string,confidence?:int} $pickMeta
     */
    private function alignedAdvice(array $row, array $pickMeta, string $market): string
    {
        $home = trim((string) ($row['home_team_name'] ?? 'Home'));
        $away = trim((string) ($row['away_team_name'] ?? 'Away'));
        $code = strtoupper(trim((string) ($pickMeta['code'] ?? '')));
        $odd = $this->oddFloat($pickMeta['odds'] ?? null);
        $h = (int) ($row['percent_pred_home'] ?? 0);
        $d = (int) ($row['percent_pred_draw'] ?? 0);
        $a = (int) ($row['percent_pred_away'] ?? 0);
        // Keep pill + reason lean % aligned, but rescale the other two so the split still sums to ~100.
        $published = (int) ($pickMeta['confidence'] ?? 0);
        [$h, $d, $a] = $this->alignProbsToPublished($h, $d, $a, $code, $published);
        $avg = isset($row['avg_goals']) ? (float) $row['avg_goals'] : 0.0;
        $bttsProb = isset($row['both_teams_percentage_prob']) ? (int) $row['both_teams_percentage_prob'] : 0;
        $league = trim((string) ($row['league_name'] ?? ''));
        $price = $odd !== null ? number_format($odd, 2) : null;
        $seed = (int) ($row['fixture_id'] ?? 0)
            + strlen($home) * 7
            + strlen($away) * 13
            + ord($code[0] ?? '1');

        $line = match ($market) {
            'double_chance' => $this->doubleChanceAdviceLine($code, $home, $away, $h, $d, $a, $price, $seed, $league),
            'over_under' => $this->overUnderAdviceLine(
                $code,
                $home,
                $away,
                $avg,
                $price,
                $seed,
                $published,
                $league
            ),
            'btts' => $this->bttsAdviceLine($code, $bttsProb, $price, $seed, $home, $away, $league),
            'ht_ft' => $this->htFtAdviceLine(
                $code,
                $home,
                $away,
                (int) ($row['hf_percent_pred_home'] ?? 0),
                (int) ($row['hf_percent_pred_draw'] ?? 0),
                (int) ($row['hf_percent_pred_away'] ?? 0),
                $h,
                $d,
                $a,
                $price,
                $seed,
                $published,
                $league
            ),
            'correct_score' => trim((string) ($pickMeta['pick'] ?? '')),
            default => match ($code) {
                '1' => $this->winnerAdviceLine($home, 'home', $h, $d, $a, $price, $seed, $away, $league),
                '2' => $this->winnerAdviceLine($away, 'away', $a, $d, $h, $price, $seed, $home, $league),
                'X' => $this->drawAdviceLine($h, $d, $a, $price, $seed, $home, $away, $league),
                default => trim((string) ($pickMeta['pick'] ?? '')),
            },
        };

        $line = trim(preg_replace('/\s+/', ' ', $line) ?? $line);
        if (strlen($line) > 170) {
            $line = rtrim(substr($line, 0, 167)) . '…';
        }
        return $line;
    }

    /** Stable 0..$n-1 pick from fixture seed (same tip keeps same wording). */
    private function adviceVariant(int $seed, int $n): int
    {
        if ($n <= 1) {
            return 0;
        }
        return abs($seed) % $n;
    }

    private function priceClause(?string $price, int $variant): string
    {
        if ($price === null || ($variant % 5) === 0) {
            return '';
        }
        return match ($variant % 3) {
            1 => 'Price near ' . $price . '.',
            2 => 'Shortlist around ' . $price . '.',
            default => 'About ' . $price . ' on the board.',
        };
    }

    private function cautionClause(int $variant): string
    {
        // Only append on some cards — repeating the same disclaimer on every tip is itself a template signal.
        return match ($variant % 5) {
            1 => 'Opinion only.',
            2 => 'Can flip on the day.',
            default => '',
        };
    }

    /**
     * Join core reasoning + price/caveat clauses with real sentence punctuation.
     * Prevents "…(75% model share) About 1.70…" glued fragments.
     */
    private function sealAdvice(string $core, ?string $price, int $seed, string ...$extras): string
    {
        $segments = [trim($core)];
        $segments[] = $this->priceClause($price, $seed);
        $segments[] = $this->cautionClause($seed + 1);
        foreach ($extras as $extra) {
            $segments[] = trim($extra);
        }
        $out = '';
        foreach ($segments as $seg) {
            $seg = trim($seg);
            if ($seg === '') {
                continue;
            }
            if (!preg_match('/[.!?…]$/u', $seg)) {
                $seg .= '.';
            }
            $out = $out === '' ? $seg : ($out . ' ' . $seg);
        }
        return $out;
    }

    private function leagueCue(string $league, int $seed): string
    {
        $league = trim($league);
        if ($league === '' || ($seed % 3) === 0) {
            return '';
        }
        return $league . ': ';
    }

    /**
     * Soft 1X2 winner line — structurally varied so cards do not share one skeleton.
     */
    private function winnerAdviceLine(
        string $team,
        string $side,
        int $leanPct,
        int $drawPct,
        int $otherPct,
        ?string $price,
        int $seed,
        string $opponent = '',
        string $league = ''
    ): string {
        $sideLabel = $side === 'away' ? 'away' : 'home';
        $gap = $leanPct - max($drawPct, $otherPct);
        $v = $this->adviceVariant($seed, 8);
        $cue = $this->leagueCue($league, $seed);
        $vs = $opponent !== '' ? (' vs ' . $opponent) : '';

        if ($leanPct >= 62 && $gap >= 12) {
            $core = match ($v) {
                1 => sprintf('%s%s look clear enough %s (%d%% model share)', $cue, $team, $sideLabel, $leanPct),
                2 => sprintf('%sFavouring %s on the %s side (%d%%) — draw %d%% / other %d%%', $cue, $team, $sideLabel, $leanPct, $drawPct, $otherPct),
                3 => sprintf('%sOur strongest read here is %s (%s, %d%%)', $cue, $team, $sideLabel, $leanPct),
                4 => sprintf('%s%s%s: edge sits with the %s side at %d%%', $cue, $team, $vs, $sideLabel, $leanPct),
                5 => sprintf('%sBanker-ish lean on %s — model %d%%, draw only %d%%', $cue, $team, $leanPct, $drawPct),
                6 => sprintf('%s%s should control this %s fixture more often than not (%d%%)', $cue, $team, $sideLabel, $leanPct),
                7 => sprintf('%sClear model preference for %s; reverse outcome only %d%%', $cue, $team, $otherPct),
                default => sprintf('%s%s (%s) carry the published lean at %d%%', $cue, $team, $sideLabel, $leanPct),
            };
            return $this->sealAdvice($core, $price, $seed);
        }

        if ($leanPct >= 55) {
            $core = match ($v) {
                1 => sprintf('%sMild edge to %s %s (%d%%) — draw still %d%%', $cue, $team, $sideLabel, $leanPct, $drawPct),
                2 => sprintf('%s%s get the nod%s, but it is not a separation you stake heavily (%d%%)', $cue, $team, $vs, $leanPct),
                3 => sprintf('%sWorking pick: %s %s (%d%%) — draw %d%% / other %d%%', $cue, $team, $sideLabel, $leanPct, $drawPct, $otherPct),
                4 => sprintf('%sLean %s without calling it settled — model %d%%, other side %d%%', $cue, $team, $leanPct, $otherPct),
                5 => sprintf('%s%s at %s feels like the better side of a close game (%d%%)', $cue, $team, $sideLabel, $leanPct),
                6 => sprintf('%sSlight tilt to %s; keep the draw (%d%%) in mind if you stack this', $cue, $team, $drawPct),
                7 => sprintf('%s%s%s is a soft favourite in our sheet at %d%%', $cue, $team, $vs, $leanPct),
                default => sprintf('%sPublished lean is %s (%s, %d%%)', $cue, $team, $sideLabel, $leanPct),
            };
            return $this->sealAdvice($core, $price, $seed);
        }

        $core = match ($v) {
            1 => sprintf('%sTight call, slight nod to %s %s (%d%%) with draw %d%%', $cue, $team, $sideLabel, $leanPct, $drawPct),
            2 => sprintf('%s%s only shade it%s — treat as fragile at %d%%', $cue, $team, $vs, $leanPct),
            3 => sprintf('%sProvisional lean: %s. Margins are thin (draw %d%% / reverse %d%%)', $cue, $team, $drawPct, $otherPct),
            4 => sprintf('%sIf you need a side, %s %s is ours — barely (%d%%)', $cue, $team, $sideLabel, $leanPct),
            5 => sprintf('%sCoin-flip territory; %s get a soft %s lean at %d%%', $cue, $team, $sideLabel, $leanPct),
            6 => sprintf('%s%s%s: published tip is thin — better as an acca filler than a single', $cue, $team, $vs),
            7 => sprintf('%sSmall model preference for %s over a live draw risk (%d%%)', $cue, $team, $drawPct),
            default => sprintf('%sNarrow lean on %s (%s, %d%%)', $cue, $team, $sideLabel, $leanPct),
        };
        return $this->sealAdvice($core, $price, $seed);
    }

    private function drawAdviceLine(
        int $h,
        int $d,
        int $a,
        ?string $price,
        int $seed,
        string $home = '',
        string $away = '',
        string $league = ''
    ): string {
        $v = $this->adviceVariant($seed, 6);
        $cue = $this->leagueCue($league, $seed);
        $pair = ($home !== '' && $away !== '') ? ($home . ' vs ' . $away) : 'this fixture';

        $core = match ($v) {
            1 => sprintf('%s%s looks level enough for a draw lean (%d%%)', $cue, $pair, $d),
            2 => sprintf('%sNeither side separates cleanly — draw is the published pick at %d%%', $cue, $d),
            3 => sprintf('%sWorking lean is the draw; home %d%% and away %d%% stay close', $cue, $h, $a),
            4 => sprintf('%s%s: stalemate profile in the model (draw %d%%)', $cue, $pair, $d),
            5 => sprintf('%sBalanced match-up — we take the draw rather than force a winner', $cue),
            default => sprintf('%sDraw lean at %d%% model share for %s', $cue, $d, $pair),
        };
        return $this->sealAdvice($core, $price, $seed);
    }

    private function doubleChanceAdviceLine(
        string $code,
        string $home,
        string $away,
        int $h,
        int $d,
        int $a,
        ?string $price,
        int $seed,
        string $league = ''
    ): string {
        $v = $this->adviceVariant($seed, 4);
        $cue = $this->leagueCue($league, $seed);

        if ($code === '1X') {
            $core = match ($v) {
                1 => sprintf('%sSafer ticket is %s or draw — away win is the outcome we are least keen on (%d%%)', $cue, $home, $a),
                2 => sprintf('%s%s / draw cover while the reverse result stays soft', $cue, $home),
                3 => sprintf('%sDouble chance on the home side of %s vs %s', $cue, $home, $away),
                default => sprintf('%s1X lean: protect against %s failing to win outright', $cue, $home),
            };
            return $this->sealAdvice($core, $price, $seed, 'Cover only.');
        }
        if ($code === 'X2') {
            $core = match ($v) {
                1 => sprintf('%sCover draw or %s — home win is the softer share (%d%%)', $cue, $away, $h),
                2 => sprintf('%sX2 on %s while we stay wary of a home result', $cue, $away),
                3 => sprintf('%sTwo-way cover against %s winning this one', $cue, $home),
                default => sprintf('%sWorking cover: draw / %s', $cue, $away),
            };
            return $this->sealAdvice($core, $price, $seed, 'Cover only.');
        }
        if ($code === '12') {
            $core = match ($v) {
                1 => sprintf('%sExpect a winner either way — draw share only %d%%', $cue, $d),
                2 => sprintf('%s%s vs %s: 12 cover while the stalemate looks less likely', $cue, $home, $away),
                3 => sprintf('%sPrefer a decisive result over the draw in this match-up', $cue),
                default => sprintf('%s12 lean — model keeps the draw as the softer path (%d%%)', $cue, $d),
            };
            return $this->sealAdvice($core, $price, $seed, 'Cover only.');
        }
        return $this->sealAdvice('Double-chance lean', $price, $seed, 'Cover only.');
    }

    private function overUnderAdviceLine(
        string $code,
        string $home,
        string $away,
        float $avg,
        ?string $price,
        int $seed,
        int $conf = 0,
        string $league = ''
    ): string {
        $v = $this->adviceVariant($seed + (int) round($avg * 10) + $conf, 6);
        $cue = $this->leagueCue($league, $seed);
        $pair = $home . ' vs ' . $away;
        $confBit = $conf > 0 ? sprintf(' (%d%% confidence)', $conf) : '';

        if ($code === 'O2.5') {
            if ($avg >= 0.8) {
                $core = match ($v) {
                    1 => sprintf('%s%s should see enough chances — projected total ~%.1f backs Over 2.5%s', $cue, $pair, $avg, $confBit),
                    2 => sprintf('%sOpen-game lean for %s: Over 2.5 with expected goals near %.1f', $cue, $pair, $avg),
                    3 => sprintf('%sOver the line in %s — model total %.1f%s', $cue, $pair, $avg, $confBit),
                    4 => sprintf('%sBoth sides look capable of contributing in %s; Over 2.5 is the pick', $cue, $pair),
                    5 => sprintf('%sGoals market: Over 2.5 for %s (xg ~%.1f)', $cue, $pair, $avg),
                    default => sprintf('%sPublished goals lean is Over 2.5 in %s%s', $cue, $pair, $confBit),
                };
                return $this->sealAdvice($core, $price, $seed);
            }
            $core = match ($v) {
                1 => sprintf('%s%s: Over 2.5 lean from the goals price%s', $cue, $pair, $confBit),
                2 => sprintf('%sBook price favours Over 2.5 in %s%s', $cue, $pair, $confBit),
                3 => sprintf('%sOver the line is the published lean for %s — no full xG total on file%s', $cue, $pair, $confBit),
                default => sprintf('%sOver 2.5 shortlist for %s%s', $cue, $pair, $confBit),
            };
            return $this->sealAdvice($core, $price, $seed);
        }

        if ($avg >= 0.8) {
            $core = match ($v) {
                1 => sprintf('%s%s looks tighter — projected total ~%.1f backs Under 2.5%s', $cue, $pair, $avg, $confBit),
                2 => sprintf('%sLower-event lean for %s: Under 2.5 with expected goals near %.1f', $cue, $pair, $avg),
                3 => sprintf('%sUnder the line in %s — model total %.1f%s', $cue, $pair, $avg, $confBit),
                4 => sprintf('%sFewer clear chances in %s; Under 2.5 is the pick', $cue, $pair),
                5 => sprintf('%sGoals market: Under 2.5 for %s (xg ~%.1f)', $cue, $pair, $avg),
                default => sprintf('%sPublished goals lean is Under 2.5 in %s%s', $cue, $pair, $confBit),
            };
            return $this->sealAdvice($core, $price, $seed);
        }
        $core = match ($v) {
            1 => sprintf('%s%s: Under 2.5 lean from the goals price%s', $cue, $pair, $confBit),
            2 => sprintf('%sBook price favours Under 2.5 in %s%s', $cue, $pair, $confBit),
            3 => sprintf('%sUnder the line is the published lean for %s — no full xG total on file%s', $cue, $pair, $confBit),
            default => sprintf('%sUnder 2.5 shortlist for %s%s', $cue, $pair, $confBit),
        };
        return $this->sealAdvice($core, $price, $seed);
    }

    private function bttsAdviceLine(
        string $code,
        int $bttsProb,
        ?string $price,
        int $seed,
        string $home = '',
        string $away = '',
        string $league = ''
    ): string {
        $v = $this->adviceVariant($seed, 5);
        $cue = $this->leagueCue($league, $seed);
        $pair = ($home !== '' && $away !== '') ? ($home . ' vs ' . $away) : 'this match';

        if ($code === 'BTTS_YES') {
            if ($bttsProb > 0) {
                $core = match ($v) {
                    1 => sprintf('%sBoth sides to score in %s — model BTTS near %d%%', $cue, $pair, $bttsProb),
                    2 => sprintf('%sBTTS Yes is the lean; neither defence looks airtight here (~%d%%)', $cue, $bttsProb),
                    3 => sprintf('%s%s: expect goals at both ends (BTTS ~%d%%)', $cue, $pair, $bttsProb),
                    4 => sprintf('%sWorking pick is both teams to score — probability around %d%%', $cue, $bttsProb),
                    default => sprintf('%sBTTS Yes lean for %s', $cue, $pair),
                };
                return $this->sealAdvice($core, $price, $seed);
            }
            return $this->sealAdvice($cue . 'Both teams to score Yes for ' . $pair, $price, $seed);
        }

        if ($bttsProb > 0) {
            $core = match ($v) {
                1 => sprintf('%sLean against both scoring in %s (BTTS still ~%d%%)', $cue, $pair, $bttsProb),
                2 => sprintf('%sBTTS No — one side looks likelier to blank (~%d%% both-score chance)', $cue, $bttsProb),
                3 => sprintf('%s%s: cleaner-sheet path is the published lean', $cue, $pair),
                4 => sprintf('%sWorking pick is BTTS No while both-score chance sits near %d%%', $cue, $bttsProb),
                default => sprintf('%sBTTS No lean for %s', $cue, $pair),
            };
            return $this->sealAdvice($core, $price, $seed);
        }
        return $this->sealAdvice($cue . 'Both teams to score No for ' . $pair, $price, $seed);
    }

    /**
     * HT/FT reason: name the fixture and the half-time → full-time path (not a pick echo).
     * Price shown is the first-half 1X2 book quote for the HT leg — true HT/FT combo odds
     * are not in the feed.
     */
    private function htFtAdviceLine(
        string $code,
        string $home,
        string $away,
        int $hh,
        int $hd,
        int $ha,
        int $fh,
        int $fd,
        int $fa,
        ?string $price,
        int $seed,
        int $published,
        string $league = ''
    ): string {
        $parts = explode('/', strtoupper(str_replace(' ', '', $code)));
        $ht = $parts[0] ?? 'X';
        $ft = $parts[1] ?? '1';
        $pair = ($home !== '' && $away !== '') ? ($home . ' vs ' . $away) : 'this match';
        $cue = $this->leagueCue($league, $seed);
        $v = $this->adviceVariant($seed, 4);
        $confBit = $published > 0 ? sprintf(' (%d%% confidence)', $published) : '';
        $htLabel = match ($ht) {
            '1' => $home !== '' ? $home : 'home',
            '2' => $away !== '' ? $away : 'away',
            default => 'a draw',
        };
        $ftLabel = match ($ft) {
            '1' => $home !== '' ? $home : 'home',
            '2' => $away !== '' ? $away : 'away',
            default => 'a draw',
        };
        $htPct = match ($ht) {
            '1' => $hh,
            '2' => $ha,
            default => $hd,
        };
        $ftPct = match ($ft) {
            '1' => $fh,
            '2' => $fa,
            default => $fd,
        };

        $path = $ht . '/' . $ft;
        $core = match ($path) {
            'X/1' => match ($v) {
                1 => sprintf(
                    '%s%s: slow-start path — drawish first half then %s to control late%s',
                    $cue,
                    $pair,
                    $home !== '' ? $home : 'the home side',
                    $confBit
                ),
                2 => sprintf(
                    '%sFavourite finishes strongly in %s: HT draw lean (~%d%%) into home FT (~%d%%)',
                    $cue,
                    $pair,
                    max(1, $htPct),
                    max(1, $ftPct)
                ),
                3 => sprintf(
                    '%sX/1 for %s — expect a cagey opening, then %s take over',
                    $cue,
                    $pair,
                    $home !== '' ? $home : 'home'
                ),
                default => sprintf(
                    '%s%s maps to Draw/Home: first-half stalemate, second-half home push%s',
                    $cue,
                    $pair,
                    $confBit
                ),
            },
            'X/2' => match ($v) {
                1 => sprintf(
                    '%s%s: away side finishes stronger — HT draw into %s at FT%s',
                    $cue,
                    $pair,
                    $away !== '' ? $away : 'away',
                    $confBit
                ),
                2 => sprintf(
                    '%sLate-away lean in %s: first half open (~%d%% draw), then %s (~%d%% FT)',
                    $cue,
                    $pair,
                    max(1, $htPct),
                    $away !== '' ? $away : 'away',
                    max(1, $ftPct)
                ),
                3 => sprintf(
                    '%sX/2 shortlist for %s — cagey HT, %s stronger after the break',
                    $cue,
                    $pair,
                    $away !== '' ? $away : 'the away side'
                ),
                default => sprintf(
                    '%s%s: Draw/Away path — slow start, away control late%s',
                    $cue,
                    $pair,
                    $confBit
                ),
            },
            '1/1' => match ($v) {
                1 => sprintf(
                    '%s%s: %s favoured to lead at HT and hold at FT (HT ~%d%%, FT ~%d%%)',
                    $cue,
                    $pair,
                    $htLabel,
                    max(1, $htPct),
                    max(1, $ftPct)
                ),
                2 => sprintf(
                    '%sHome/Home lean for %s — early control from %s looks durable%s',
                    $cue,
                    $pair,
                    $home !== '' ? $home : 'home',
                    $confBit
                ),
                3 => sprintf(
                    '%s%s: both-halves home path — start on the front foot and close it out',
                    $cue,
                    $pair
                ),
                default => sprintf(
                    '%sWorking HT/FT is Home/Home in %s%s',
                    $cue,
                    $pair,
                    $confBit
                ),
            },
            '2/2' => match ($v) {
                1 => sprintf(
                    '%s%s: %s to lead at the break and finish the job (HT ~%d%%, FT ~%d%%)',
                    $cue,
                    $pair,
                    $htLabel,
                    max(1, $htPct),
                    max(1, $ftPct)
                ),
                2 => sprintf(
                    '%sAway/Away lean for %s — early away edge that should travel%s',
                    $cue,
                    $pair,
                    $confBit
                ),
                3 => sprintf(
                    '%s%s: both-halves away path for %s',
                    $cue,
                    $pair,
                    $away !== '' ? $away : 'the visitors'
                ),
                default => sprintf(
                    '%sWorking HT/FT is Away/Away in %s%s',
                    $cue,
                    $pair,
                    $confBit
                ),
            },
            '1/2' => sprintf(
                '%s%s: lead-change path — %s ahead at HT, %s favoured by FT%s',
                $cue,
                $pair,
                $htLabel,
                $ftLabel,
                $confBit
            ),
            '2/1' => sprintf(
                '%s%s: lead-change path — %s ahead at HT, %s favoured by FT%s',
                $cue,
                $pair,
                $htLabel,
                $ftLabel,
                $confBit
            ),
            '1/X' => sprintf(
                '%s%s: %s front-run into a shared point — HT home lean (~%d%%), FT drawish%s',
                $cue,
                $pair,
                $home !== '' ? $home : 'Home',
                max(1, $htPct),
                $confBit
            ),
            '2/X' => sprintf(
                '%s%s: %s front-run into a shared point — HT away lean (~%d%%), FT drawish%s',
                $cue,
                $pair,
                $away !== '' ? $away : 'Away',
                max(1, $htPct),
                $confBit
            ),
            'X/X' => sprintf(
                '%s%s: stalemate both halves — drawish HT (~%d%%) and FT (~%d%%)%s',
                $cue,
                $pair,
                max(1, $htPct),
                max(1, $ftPct),
                $confBit
            ),
            default => sprintf(
                '%s%s: HT/FT path %s → %s at half-time, %s at full-time%s',
                $cue,
                $pair,
                $path,
                $htLabel,
                $ftLabel,
                $confBit
            ),
        };

        // Price is first-half 1X2 for the HT leg — say so when we quote it.
        if ($price !== null) {
            $priceNote = match ($v % 3) {
                1 => 'First-half price near ' . $price,
                2 => 'HT leg around ' . $price . ' (combo market not quoted)',
                default => 'About ' . $price . ' on the HT result',
            };
            return $this->sealAdvice($core, null, $seed, $priceNote);
        }
        return $this->sealAdvice($core, null, $seed);
    }

    /**
     * @param array<string,mixed> $row
     * @return array{pick:string,code:string,odds:?string,confidence:int}
     */
    private function derive1x2(array $row): array
    {
        $h = (int) ($row['percent_pred_home'] ?? 0);
        $d = (int) ($row['percent_pred_draw'] ?? 0);
        $a = (int) ($row['percent_pred_away'] ?? 0);

        // No usable three-way model — do not invent Home@50% (polluted FA Cup / cup replays).
        if (!$this->hasUsable1x2Model($h, $d, $a)) {
            return [
                'code' => '1',
                'pick' => 'Home Win',
                'odds' => null,
                'confidence' => 0,
            ];
        }

        $max = max($h, $d, $a);
        if ($h === $max && $h >= $d && $h >= $a) {
            $code = '1';
            $conf = $h;
        } elseif ($a === $max && $a >= $h && $a >= $d) {
            $code = '2';
            $conf = $a;
        } else {
            $code = 'X';
            $conf = $d;
        }

        // Align with predictions.winner_team_name only when percents agree.
        $winner = trim((string) ($row['winner_team_name'] ?? ''));
        $home = trim((string) ($row['home_team_name'] ?? ''));
        $away = trim((string) ($row['away_team_name'] ?? ''));
        if ($winner !== '' && $home !== '' && strcasecmp($winner, $home) === 0 && $h >= $d && $h >= $a) {
            $code = '1';
            $conf = $h;
        } elseif ($winner !== '' && $away !== '' && strcasecmp($winner, $away) === 0 && $a >= $d && $a >= $h) {
            $code = '2';
            $conf = $a;
        }

        // Soft / three-way coin flips → temper confidence but keep publishable when model is real.
        $second = $code === '1' ? max($d, $a) : ($code === '2' ? max($h, $d) : max($h, $a));
        if ($max - $second < 4) {
            $conf = min($conf, 58);
        }

        $oddsMap = [
            '1' => $row['bets_home'] ?? null,
            'X' => $row['bets_draw'] ?? null,
            '2' => $row['bets_away'] ?? null,
        ];
        $oddStr = $this->oddStr($oddsMap[$code] ?? null);
        $odd = $this->oddFloat($oddStr);

        // Accuracy guard: long 1X2 prices need a real model majority.
        if ($odd !== null) {
            if ($odd >= 3.0 && $conf < 62) {
                $conf = min($conf, 45);
            } elseif ($odd >= 2.70 && $conf < 55) {
                $conf = min($conf, 48);
            } elseif ($odd >= 2.40 && $conf < 52) {
                $conf = min($conf, 50);
            }
        }

        // conf may have been demoted below publish floor by long-odds guards — treat as unusable.
        if ($conf < 55) {
            return [
                'code' => $code,
                'pick' => $this->tipLabel($code),
                'odds' => $oddStr,
                'confidence' => 0,
            ];
        }

        return [
            'code' => $code,
            'pick' => $this->tipLabel($code),
            'odds' => $oddStr,
            'confidence' => max(55, min(85, $conf)),
        ];
    }

    /**
     * Real 1X2 model split: all three outcomes present and sum near 100.
     * Rejects null/zero stubs that used to publish as Home Win @ 50%.
     */
    private function hasUsable1x2Model(int $h, int $d, int $a): bool
    {
        if ($h <= 0 || $d <= 0 || $a <= 0) {
            return false;
        }
        $sum = $h + $d + $a;
        if ($sum < 70 || $sum > 130) {
            return false;
        }
        return max($h, $d, $a) >= 55;
    }

    /**
     * @param array<string,mixed> $row
     * @return array{pick:string,code:string,odds:?string,confidence:int}
     */
    private function deriveDoubleChance(array $row): array
    {
        $h = (int) ($row['percent_pred_home'] ?? 0);
        $d = (int) ($row['percent_pred_draw'] ?? 0);
        $a = (int) ($row['percent_pred_away'] ?? 0);

        if ($a < $h && $a < $d) {
            $code = '1X';
            $odds = $row['double_chance_home_draw'] ?? null;
            $conf = $this->doubleChanceLeanPercent($h, $d, $a);
        } elseif ($h < $a && $h < $d) {
            $code = 'X2';
            $odds = $row['double_chance_draw_away'] ?? null;
            $conf = $this->doubleChanceLeanPercent($d, $a, $h);
        } else {
            $code = '12';
            $odds = $row['double_chance_home_away'] ?? null;
            $conf = $this->doubleChanceLeanPercent($h, $a, $d);
        }

        return $this->requireMarketOdds([
            'code' => $code,
            'pick' => $this->tipLabel($code),
            'odds' => $this->oddStr($odds),
            'confidence' => $conf,
        ]);
    }

    /**
     * @param array<string,mixed> $row
     * @return array{pick:string,code:string,odds:?string,confidence:int}
     */
    private function deriveOverUnder(array $row): array
    {
        $underOver = strtolower(trim((string) ($row['under_over'] ?? '')));
        $avg = isset($row['avg_goals']) ? (float) $row['avg_goals'] : 0.0;
        $overOdd = $this->oddFloat($row['over_2_5'] ?? null);
        $underOdd = $this->oddFloat($row['under_2_5'] ?? null);
        $hasAvg = $avg >= 0.8;

        $leanOver = null;
        if (str_contains($underOver, 'over')) {
            $leanOver = true;
        } elseif (str_contains($underOver, 'under')) {
            $leanOver = false;
        } elseif ($hasAvg) {
            $leanOver = $avg >= 2.5;
        } elseif ($overOdd !== null && $underOdd !== null) {
            // No xG — lean with the shorter goals price (book favourite side of 2.5).
            $leanOver = $overOdd <= $underOdd;
        }

        if ($leanOver === null) {
            // No projected total and no two-way goals price — do not invent Under@78.
            return $this->requireMarketOdds([
                'code' => 'U2.5',
                'pick' => 'Under 2.5 Goals',
                'odds' => $this->oddStr($row['under_2_5'] ?? null),
                'confidence' => 0,
            ]);
        }

        if ($leanOver) {
            $conf = $hasAvg
                ? (int) round(55 + max(0.0, min(2.0, $avg - 2.5)) * 11.0)
                : $this->goalsOddsConfidence($overOdd, $underOdd, true);
            return $this->requireMarketOdds([
                'code' => 'O2.5',
                'pick' => 'Over 2.5 Goals',
                'odds' => $this->oddStr($row['over_2_5'] ?? null),
                'confidence' => $conf > 0 ? max(55, min(78, $conf)) : 0,
            ]);
        }

        $conf = $hasAvg
            ? (int) round(55 + max(0.0, min(2.0, 2.5 - $avg)) * 11.0)
            : $this->goalsOddsConfidence($overOdd, $underOdd, false);
        return $this->requireMarketOdds([
            'code' => 'U2.5',
            'pick' => 'Under 2.5 Goals',
            'odds' => $this->oddStr($row['under_2_5'] ?? null),
            'confidence' => $conf > 0 ? max(55, min(78, $conf)) : 0,
        ]);
    }

    /**
     * Confidence from over/under prices when avg_goals is missing.
     * Returns 0 when prices are unusable (caller should not publish).
     */
    private function goalsOddsConfidence(?float $overOdd, ?float $underOdd, bool $leanOver): int
    {
        $pickOdd = $leanOver ? $overOdd : $underOdd;
        $otherOdd = $leanOver ? $underOdd : $overOdd;
        if ($pickOdd === null || $pickOdd < 1.05) {
            return 0;
        }
        if ($otherOdd !== null && $otherOdd > 1.05) {
            $impPick = 1.0 / $pickOdd;
            $impOther = 1.0 / $otherOdd;
            $share = $impPick / max(0.01, $impPick + $impOther);
            return (int) round(56 + ($share * 20.0)); // roughly 56–76
        }
        // Single price only — shorter implies a modestly stronger lean.
        return (int) round(max(56, min(72, 88 - ($pickOdd * 14.0))));
    }

    /**
     * @param array<string,mixed> $row
     * @return array{pick:string,code:string,odds:?string,confidence:int}
     */
    private function deriveBtts(array $row): array
    {
        $raw = strtolower(trim((string) ($row['both_team_to_score'] ?? '')));
        $prob = (int) ($row['both_teams_percentage_prob'] ?? 50);
        $yes = str_starts_with($raw, 'y') || $raw === '1';
        if ($raw === '' && $prob >= 55) {
            $yes = true;
        }
        if ($yes) {
            return $this->requireMarketOdds([
                'code' => 'BTTS_YES',
                'pick' => 'BTTS Yes',
                'odds' => $this->oddStr($row['both_teams_to_score_yes'] ?? null),
                'confidence' => max(50, min(85, $prob)),
            ]);
        }
        return $this->requireMarketOdds([
            'code' => 'BTTS_NO',
            'pick' => 'BTTS No',
            'odds' => $this->oddStr($row['both_teams_to_score_no'] ?? null),
            'confidence' => max(50, min(85, 100 - $prob)),
        ]);
    }

    /**
     * @param array<string,mixed> $row
     * @return array{pick:string,code:string,odds:?string,confidence:int}
     */
    private function deriveCorrectScore(array $row): array
    {
        $score = trim((string) ($row['cs_best_score_1'] ?? ''));
        $odd = $row['cs_best_odd_1'] ?? null;
        $base = $this->derive1x2($row);
        if ($score === '') {
            // Fallback from 1X2 lean
            $score = match ($base['code']) {
                '2' => '0-1',
                'X' => '1-1',
                default => '1-0',
            };
        }
        return [
            'code' => 'CS_' . $score,
            'pick' => 'Correct Score ' . $score,
            'odds' => $this->oddStr($odd),
            'confidence' => max(40, min(70, (int) $base['confidence'] - 15)),
        ];
    }

    /**
     * @param array<string,mixed> $row
     * @return array{pick:string,code:string,odds:?string,confidence:int}
     */
    private function deriveHtFt(array $row): array
    {
        $hh = (int) ($row['hf_percent_pred_home'] ?? 0);
        $hd = (int) ($row['hf_percent_pred_draw'] ?? 0);
        $ha = (int) ($row['hf_percent_pred_away'] ?? 0);
        $ft = $this->derive1x2($row);

        $htMax = max($hh, $hd, $ha);
        // No usable first-half model — do not invent a path.
        if ($htMax <= 0) {
            return $this->requireMarketOdds([
                'code' => 'X/1',
                'pick' => 'HT/FT Draw/Home',
                'odds' => null,
                'confidence' => 0,
            ]);
        }

        if ($hh === $htMax && $hh >= $hd && $hh >= $ha) {
            $ht = '1';
        } elseif ($ha === $htMax && $ha >= $hh && $ha >= $hd) {
            $ht = '2';
        } else {
            $ht = 'X';
        }
        // Classic slow-starter favourite → X/1 when FT is home and HT drawish
        if ($ft['code'] === '1' && $hd >= $hh) {
            $ht = 'X';
        }
        // Mirror for away favourites that start slowly
        if ($ft['code'] === '2' && $hd >= $ha) {
            $ht = 'X';
        }

        $code = $ht . '/' . $ft['code'];
        $htOdd = match ($ht) {
            '1' => $row['ht_home'] ?? null,
            '2' => $row['ht_away'] ?? null,
            default => $row['ht_draw'] ?? null,
        };
        $label = 'HT/FT ' . str_replace(
            ['1', '2', 'X'],
            ['Home', 'Away', 'Draw'],
            $code
        );

        return $this->requireMarketOdds([
            'code' => $code,
            'pick' => $label,
            'odds' => $this->oddStr($htOdd),
            // Feed has first-half 1X2 prices only — not true HT/FT combo odds.
            'confidence' => max(50, min(72, (int) round(($htMax + (int) $ft['confidence']) / 2))),
        ]);
    }

    /**
     * @param array<string,mixed> $row
     * @return array<string,mixed>
     */
    private function mapSelectionRow(array $row): array
    {
        $isJackpot = trim((string) ($row['jackpot_name'] ?? '')) !== '';
        // Jackpot sheets must publish pp_fixtures_selections.tip (not revised_tip overrides).
        if ($isJackpot) {
            $tip = trim((string) ($row['tip'] ?? ''));
            if ($tip === '') {
                $tip = trim((string) ($row['revised_tip'] ?? ''));
            }
        } else {
            $tip = trim((string) ($row['revised_tip'] ?: $row['tip'] ?: ''));
        }

        [$code, $market] = $this->parseSelectionTip($tip !== '' ? $tip : '1');

        // Jackpot coupons: always expose both 1X2 + Double Chance (Pitch-style).
        $pickDcCode = null;
        $pick1x2Code = null;
        if ($isJackpot) {
            if ($market === 'double_chance') {
                $pickDcCode = $code;
                $pick1x2Code = $this->jackpotOneX2FromRow($row, $tip);
                $code = $pick1x2Code;
                $market = '1x2';
            } else {
                $pick1x2Code = $code;
                $pickDcCode = $this->jackpotDoubleChanceCode($code, $row);
            }
        }

        $hProb = (int) ($row['home_prob'] ?? 0);
        $dProb = (int) ($row['draw_prob'] ?? 0);
        $aProb = (int) ($row['away_prob'] ?? 0);
        // Tip-aligned model share — same figure on the card pill and in the reason line.
        // Prefer this over prediction_confidence: upstream often stores a bogus 100 ceiling
        // that reads as a guarantee (RG / trust risk on jackpot pages).
        $leanShare = match ($code) {
            '1' => $hProb,
            '2' => $aProb,
            'X' => $dProb,
            '1X' => max($hProb, $dProb),
            'X2' => max($dProb, $aProb),
            '12' => max($hProb, $aProb),
            default => 0,
        };
        $rawStored = $row['research_confidence'] ?? null;
        if ($rawStored === null || $rawStored === '') {
            $rawStored = $row['prediction_confidence'] ?? null;
        }
        $conf = $leanShare > 0 ? $leanShare : (int) ($rawStored ?? 50);

        $kickoffRaw = (string) ($row['kickoff'] ?? '');
        $when = DateTimeHelper::formatKickoff($kickoffRaw, (string) ($row['timezone'] ?? DateTimeHelper::SOURCE_TZ));
        $goalsHome = $row['goals_home'] ?? null;
        $goalsAway = $row['goals_away'] ?? null;
        $score = ($goalsHome !== null && $goalsAway !== null)
            ? ((int) $goalsHome . '-' . (int) $goalsAway)
            : null;
        $oddsHome = $this->oddStr($row['bets_home'] ?? null);
        $oddsDraw = $this->oddStr($row['bets_draw'] ?? null);
        $oddsAway = $this->oddStr($row['bets_away'] ?? null);

        $odd = (string) ($row['odd'] ?? '');
        if ($odd === '-' || $odd === '') {
            $odd = null;
        }
        // Jackpot selections often store odd as "-" — use book 1X2 for the published pick.
        if ($odd === null) {
            $pickBookOdd = match ($code) {
                '1' => $oddsHome,
                'X' => $oddsDraw,
                '2' => $oddsAway,
                '1X' => $oddsHome !== null && $oddsDraw !== null
                    ? $this->oddStr(min((float) $oddsHome, (float) $oddsDraw))
                    : ($oddsHome ?? $oddsDraw),
                'X2' => $oddsDraw !== null && $oddsAway !== null
                    ? $this->oddStr(min((float) $oddsDraw, (float) $oddsAway))
                    : ($oddsDraw ?? $oddsAway),
                '12' => $oddsHome !== null && $oddsAway !== null
                    ? $this->oddStr(min((float) $oddsHome, (float) $oddsAway))
                    : ($oddsHome ?? $oddsAway),
                default => null,
            };
            $odd = $pickBookOdd;
        }

        $pos = isset($row['jackpot_position']) ? (int) $row['jackpot_position'] : 0;
        $league = (string) ($row['league_name'] ?? '');
        if ($pos > 0) {
            $league = '#' . $pos . ($league !== '' ? ' · ' . $league : '');
        } elseif ($league === '') {
            $league = (string) ($row['jackpot_name'] ?? 'Football');
        }

        $statusShort = strtoupper(trim((string) ($row['status_short'] ?? '')));
        $status = $statusShort !== '' ? $statusShort : strtoupper(trim((string) ($row['manual_status'] ?? '')));
        $isLive = $this->isLiveStatus($statusShort);
        $won = null;
        $winning = null;
        $wonDc = null;
        $winningDc = null;
        if ($goalsHome !== null && $goalsAway !== null) {
            $gh = (int) $goalsHome;
            $ga = (int) $goalsAway;
            if (in_array($statusShort, ['FT', 'AET', 'PEN', 'AWD', 'WO'], true)) {
                $won = $this->tipMatchesScore($code, $market, $gh, $ga, $row);
                if ($pickDcCode !== null) {
                    $wonDc = $this->tipMatchesScore($pickDcCode, 'double_chance', $gh, $ga, $row);
                }
            } elseif ($isLive) {
                $winning = $this->tipMatchesScore($code, $market, $gh, $ga, $row);
                if ($pickDcCode !== null) {
                    $winningDc = $this->tipMatchesScore($pickDcCode, 'double_chance', $gh, $ga, $row);
                }
            }
        }

        $out = [
            'id' => (int) $row['id'],
            'fixture_id' => (int) $row['fixture_id'],
            'league' => $league,
            'country' => (string) ($row['country_name'] ?? ''),
            'home' => (string) $row['home_team_name'],
            'away' => (string) $row['away_team_name'],
            'home_logo' => (string) ($row['home_team_logo'] ?? ''),
            'away_logo' => (string) ($row['away_team_logo'] ?? ''),
            'date' => $when['date'],
            'time' => $when['time'],
            'time_clock' => $when['time_clock'],
            'date_label' => $when['date_label'] ?? null,
            'kickoff' => $when['kickoff'] !== '' ? $when['kickoff'] : $kickoffRaw,
            'kickoff_utc' => $when['kickoff_utc'],
            'kickoff_iso' => $when['iso'],
            'timezone' => $when['display_tz'],
            'status' => $status,
            'status_long' => (string) ($row['status_long'] ?? ''),
            'is_live' => $isLive,
            'score' => $score,
            'won' => $won,
            'winning' => $winning,
            'pick' => $this->tipLabel($code),
            'pick_code' => $code,
            'odds' => $odd,
            'odds_home' => $oddsHome,
            'odds_draw' => $oddsDraw,
            'odds_away' => $oddsAway,
            'home_prob' => $hProb > 0 ? $hProb : null,
            'draw_prob' => $dProb > 0 ? $dProb : null,
            'away_prob' => $aProb > 0 ? $aProb : null,
            'confidence' => $this->clampPublishedConfidence($conf),
            'reason' => $this->selectionAdviceLine($row, $code, $market, $odd, $pos, $this->clampPublishedConfidence($conf)),
            'category' => (string) ($row['category'] ?? ''),
            'jackpot_name' => (string) ($row['jackpot_name'] ?? ''),
            'jackpot_tips_id' => (string) ($row['jackpot_tips_id'] ?? ''),
            'market' => $market,
            'market_label' => $this->marketLabel($market),
            'source' => 'selections',
        ];

        if ($pickDcCode !== null) {
            $dcLean = match ($pickDcCode) {
                '1X' => max($hProb, $dProb),
                'X2' => max($dProb, $aProb),
                '12' => max($hProb, $aProb),
                default => 0,
            };
            $out['pick_dc'] = $this->tipLabel($pickDcCode);
            $out['pick_dc_code'] = $pickDcCode;
            $out['pick_dc_short'] = $pickDcCode;
            $out['won_dc'] = $wonDc;
            $out['winning_dc'] = $winningDc;
            $out['confidence_dc'] = $this->clampPublishedConfidence(
                $dcLean > 0 ? $dcLean : max(50, (int) round($conf * 0.92))
            );
        }
        if ($pick1x2Code !== null) {
            $out['pick_1x2_code'] = $pick1x2Code;
        }

        return $out;
    }

    /**
     * @return array{0:string,1:string} [code, market]
     */
    private function parseSelectionTip(string $tip): array
    {
        $upper = strtoupper(str_replace([' ', '-'], '', $tip));
        if (preg_match('/^DC?(1X|X2|12)$/', $upper, $m)) {
            return [$m[1], 'double_chance'];
        }
        if (in_array($upper, ['1X', 'X2', '12'], true)) {
            return [$upper, 'double_chance'];
        }
        $code = $this->normalizePickCode($tip !== '' ? $tip : '1', '1x2');
        $market = in_array($code, ['1X', 'X2', '12'], true) ? 'double_chance' : '1x2';
        return [$code, $market];
    }

    /**
     * Companion DC for a jackpot 1X2 tip (1→1X, 2→X2, X→stronger side cover).
     * Prefer an explicit DC in revised_tip / research_tip when present.
     *
     * @param array<string,mixed> $row
     */
    private function jackpotDoubleChanceCode(string $oneX2Code, array $row): string
    {
        foreach (['revised_tip', 'research_tip'] as $key) {
            $raw = trim((string) ($row[$key] ?? ''));
            if ($raw === '') {
                continue;
            }
            [$code, $market] = $this->parseSelectionTip($raw);
            if ($market === 'double_chance') {
                return $code;
            }
        }

        $code = strtoupper(trim($oneX2Code));
        if ($code === '1') {
            return '1X';
        }
        if ($code === '2') {
            return 'X2';
        }
        // Draw tip: cover draw + the stronger side.
        $h = (int) ($row['home_prob'] ?? 0);
        $a = (int) ($row['away_prob'] ?? 0);
        return $h >= $a ? '1X' : 'X2';
    }

    /**
     * When the stored tip is already DC, recover a 1X2 lean from tip/probs.
     *
     * @param array<string,mixed> $row
     */
    private function jackpotOneX2FromRow(array $row, string $fallbackTip): string
    {
        $tip = trim((string) ($row['tip'] ?? ''));
        if ($tip !== '') {
            [$code, $market] = $this->parseSelectionTip($tip);
            if ($market === '1x2' && in_array($code, ['1', 'X', '2'], true)) {
                return $code;
            }
        }
        $h = (int) ($row['home_prob'] ?? 0);
        $d = (int) ($row['draw_prob'] ?? 0);
        $a = (int) ($row['away_prob'] ?? 0);
        if ($h >= $d && $h >= $a) {
            return '1';
        }
        if ($a >= $d && $a >= $h) {
            return '2';
        }
        if ($d > 0) {
            return 'X';
        }
        [$code] = $this->parseSelectionTip($fallbackTip !== '' ? $fallbackTip : '1');
        return in_array($code, ['1', 'X', '2'], true) ? $code : '1';
    }

    /**
     * Jackpot/selection card reason — aligned to published tip, never upstream
     * "revised / research vs system" winner_reason strings.
     *
     * $publishedConfidence is the same % shown on the card pill (tip-aligned share).
     */
    private function selectionAdviceLine(
        array $row,
        string $code,
        string $market,
        ?string $odd,
        int $pos,
        int $publishedConfidence = 0
    ): string {
        $home = trim((string) ($row['home_team_name'] ?? 'Home'));
        $away = trim((string) ($row['away_team_name'] ?? 'Away'));
        $h = (int) ($row['home_prob'] ?? 0);
        $d = (int) ($row['draw_prob'] ?? 0);
        $a = (int) ($row['away_prob'] ?? 0);
        // Keep relative draw/other context, but force the lean % to match the card pill.
        if ($publishedConfidence > 0) {
            if ($code === '1') {
                $h = $publishedConfidence;
            } elseif ($code === '2') {
                $a = $publishedConfidence;
            } elseif ($code === 'X') {
                $d = $publishedConfidence;
            }
        }
        $price = $this->oddFloat($odd) !== null ? number_format((float) $odd, 2) : null;
        $seed = (int) ($row['fixture_id'] ?? 0) + $pos * 17 + ord($code[0] ?? '1');
        $league = trim((string) ($row['league_name'] ?? ''));

        if ($market === 'double_chance') {
            return $this->doubleChanceAdviceLine($code, $home, $away, $h, $d, $a, $price, $seed, $league);
        }

        if ($h + $d + $a >= 50 || $publishedConfidence >= 40) {
            return match ($code) {
                '1' => $this->winnerAdviceLine($home, 'home', $h, $d, $a, $price, $seed, $away, $league),
                '2' => $this->winnerAdviceLine($away, 'away', $a, $d, $h, $price, $seed, $home, $league),
                'X' => $this->drawAdviceLine($h, $d, $a, $price, $seed, $home, $away, $league),
                default => $this->jackpotFallbackAdvice($code, $home, $away, $price, $pos, $seed),
            };
        }

        return $this->jackpotFallbackAdvice($code, $home, $away, $price, $pos, $seed);
    }

    private function jackpotFallbackAdvice(
        string $code,
        string $home,
        string $away,
        ?string $price,
        int $pos,
        int $seed
    ): string {
        $v = $this->adviceVariant($seed, 3);
        $game = $pos > 0 ? ('Game ' . $pos) : 'This row';

        $core = match ($code) {
            '1' => match ($v) {
                1 => sprintf('%s sheet lean is %s at home', $game, $home),
                2 => sprintf('Jackpot lean: favour %s at home on %s', $home, strtolower($game)),
                default => sprintf('%s: model lean Home Win — %s on the sheet', $game, $home),
            },
            '2' => match ($v) {
                1 => sprintf('%s sheet lean is %s away', $game, $away),
                2 => sprintf('Jackpot lean: favour %s away on %s', $away, strtolower($game)),
                default => sprintf('%s: model lean Away Win — %s on the sheet', $game, $away),
            },
            'X' => match ($v) {
                1 => sprintf('%s sheet lean is a draw between %s and %s', $game, $home, $away),
                2 => sprintf('Jackpot lean: draw on %s (%s vs %s)', strtolower($game), $home, $away),
                default => sprintf('%s: model lean Draw — %s vs %s', $game, $home, $away),
            },
            '1X' => sprintf('%s: double-chance cover %s or draw', $game, $home),
            'X2' => sprintf('%s: double-chance cover draw or %s', $game, $away),
            '12' => sprintf('%s: either side to win preferred over a draw', $game),
            default => sprintf('%s: published jackpot lean', $game),
        };
        return $this->sealAdvice($core, $price, $seed);
    }

    private function oddStr(mixed $odds): ?string
    {
        if ($odds === null || $odds === '' || $odds === '-') {
            return null;
        }
        return (string) $odds;
    }

    private function tipLabel(string $code): string
    {
        $code = strtoupper(trim($code));
        return match ($code) {
            '1' => 'Home Win',
            'X' => 'Draw',
            '2' => 'Away Win',
            '1X' => 'Double Chance 1X',
            'X2' => 'Double Chance X2',
            '12' => 'Double Chance 12',
            default => $code !== '' ? $code : 'Home Win',
        };
    }
}
