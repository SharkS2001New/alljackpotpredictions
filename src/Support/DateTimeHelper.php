<?php
namespace App\Support;

use DateTimeImmutable;
use DateTimeZone;
use Throwable;

/**
 * Kickoff / calendar helpers.
 * Fixture datetimes in pitchnewdb are stored as UTC wall-clock values.
 * Site calendar (today / tomorrow / weekend) follows Africa/Nairobi — same as Pitch Predictions.
 */
final class DateTimeHelper
{
    public const SITE_TZ = 'Africa/Nairobi';
    public const SOURCE_TZ = 'UTC';

    public static function siteNow(): DateTimeImmutable
    {
        return new DateTimeImmutable('now', new DateTimeZone(self::SITE_TZ));
    }

    public static function siteToday(): string
    {
        return self::siteNow()->format('Y-m-d');
    }

    public static function siteDate(string $modifier = 'today'): string
    {
        $now = self::siteNow();
        return match (strtolower($modifier)) {
            'tomorrow' => $now->modify('+1 day')->format('Y-m-d'),
            'yesterday' => $now->modify('-1 day')->format('Y-m-d'),
            default => $now->format('Y-m-d'),
        };
    }

    /**
     * @return array{from:string,to:string}
     */
    public static function siteWeekendRange(): array
    {
        $now = self::siteNow();
        $dow = (int) $now->format('N'); // 1=Mon … 7=Sun
        if ($dow <= 5) {
            $sat = $now->modify('next saturday');
        } elseif ($dow === 6) {
            $sat = $now;
        } else {
            $sat = $now->modify('last saturday');
        }
        $sun = $sat->modify('+1 day');
        return [
            'from' => $sat->format('Y-m-d'),
            'to' => $sun->format('Y-m-d'),
        ];
    }

    /**
     * Convert a stored fixture datetime into site-local display fields + UTC ISO for the browser.
     *
     * @return array{
     *   date:?string,
     *   time:string,
     *   time_clock:string,
     *   kickoff:string,
     *   kickoff_utc:string,
     *   iso:string,
     *   display_tz:string
     * }
     */
    public static function formatKickoff(?string $kickoff, ?string $sourceTz = null): array
    {
        $empty = [
            'date' => null,
            'time' => '',
            'time_clock' => '',
            'date_label' => '',
            'kickoff' => '',
            'kickoff_utc' => '',
            'iso' => '',
            'display_tz' => self::SITE_TZ,
        ];

        $raw = trim((string) $kickoff);
        if ($raw === '' || str_starts_with($raw, '0000-00-00')) {
            return $empty;
        }

        $source = trim((string) ($sourceTz ?: self::SOURCE_TZ));
        if ($source === '' || strcasecmp($source, 'UTC') === 0 || strcasecmp($source, 'GMT') === 0) {
            $source = 'UTC';
        }

        try {
            $normalized = str_replace(' ', 'T', substr($raw, 0, 19));
            if (!preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}/', $normalized)) {
                return $empty;
            }
            if (preg_match('/T\d{2}:\d{2}$/', $normalized)) {
                $normalized .= ':00';
            }

            $from = new DateTimeImmutable($normalized, new DateTimeZone($source));
            $utc = $from->setTimezone(new DateTimeZone('UTC'));
            $local = $utc->setTimezone(new DateTimeZone(self::SITE_TZ));
            $clock = $local->format('g:i A'); // e.g. 2:30 PM
            $siteToday = self::siteToday();
            $localDate = $local->format('Y-m-d');
            // Include weekday + calendar date when not today (jackpots / weekend span days).
            $timeLabel = ($localDate === $siteToday)
                ? $clock
                : ($local->format('D j M') . ' · ' . $clock);

            return [
                'date' => $localDate,
                'time' => $timeLabel,
                'time_clock' => $clock,
                'date_label' => $local->format('D j M'),
                'kickoff' => $local->format('Y-m-d H:i:s'),
                'kickoff_utc' => $utc->format('Y-m-d H:i:s'),
                'iso' => $utc->format('Y-m-d\TH:i:s\Z'),
                'display_tz' => self::SITE_TZ,
            ];
        } catch (Throwable $e) {
            return $empty;
        }
    }

    /**
     * Nairobi calendar date for a stored UTC kickoff (used when filtering day pages).
     */
    public static function siteCalendarDate(?string $kickoff, ?string $sourceTz = null): ?string
    {
        $fmt = self::formatKickoff($kickoff, $sourceTz);
        return $fmt['date'];
    }
}
