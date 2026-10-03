<?php

namespace App\Services;

/**
 * Footer / text-link sponsors written by pitchpredictionsadmin (live PUT).
 */
final class FooterSponsorsService
{
    public static function relativePath(): string
    {
        return 'public/site-content/footer-sponsors.json';
    }

    public static function absolutePath(): string
    {
        return dirname(__DIR__, 2) . '/' . self::relativePath();
    }

    /**
     * @return array{updated_at: string, links: list<array<string,mixed>>}
     */
    public function emptyDocument(): array
    {
        return [
            'updated_at' => gmdate('c'),
            'links' => [],
        ];
    }

    /**
     * @return array{updated_at: string, links: list<array<string,mixed>>}
     */
    public function readDocument(): array
    {
        $path = self::absolutePath();
        if (! is_file($path)) {
            return $this->emptyDocument();
        }

        try {
            $raw = json_decode((string) file_get_contents($path), true);
            if (! is_array($raw)) {
                return $this->emptyDocument();
            }

            return $this->normalizeDocument($raw);
        } catch (\Throwable $e) {
            return $this->emptyDocument();
        }
    }

    /**
     * @param  array<string,mixed>  $raw
     * @return array{updated_at: string, links: list<array<string,mixed>>}
     */
    public function writeDocument(array $raw): array
    {
        $document = $this->normalizeDocument($raw);
        $document['updated_at'] = gmdate('c');

        $path = self::absolutePath();
        $dir = dirname($path);
        if (! is_dir($dir) && ! @mkdir($dir, 0755, true) && ! is_dir($dir)) {
            throw new \RuntimeException('Could not create site-content directory.');
        }

        $encoded = json_encode($document, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if ($encoded === false || @file_put_contents($path, $encoded . "\n") === false) {
            throw new \RuntimeException('Could not write footer-sponsors.json.');
        }

        return $document;
    }

    /**
     * @return list<array{id: string, label: string, url: string, rel: list<string>}>
     */
    public function visibleLinks(?\DateTimeInterface $now = null): array
    {
        $document = $this->readDocument();
        $out = [];
        foreach ($document['links'] as $link) {
            if (! $this->isVisible($link, $now)) {
                continue;
            }
            $out[] = [
                'id' => (string) $link['id'],
                'label' => (string) $link['label'],
                'url' => (string) $link['url'],
                'rel' => is_array($link['rel'] ?? null) ? $link['rel'] : ['noopener', 'noreferrer'],
            ];
        }

        return $out;
    }

    /**
     * @param  array<string,mixed>  $raw
     * @return array{updated_at: string, links: list<array<string,mixed>>}
     */
    public function normalizeDocument(array $raw): array
    {
        $links = [];
        $items = is_array($raw['links'] ?? null) ? $raw['links'] : [];
        foreach ($items as $index => $item) {
            if (! is_array($item)) {
                continue;
            }
            $normalized = $this->normalizeLink($item, (int) $index);
            if ($normalized['label'] !== '' && $normalized['url'] !== '') {
                $links[] = $normalized;
            }
        }

        return [
            'updated_at' => trim((string) ($raw['updated_at'] ?? '')) ?: gmdate('c'),
            'links' => $links,
        ];
    }

    /**
     * @param  array<string,mixed>  $raw
     * @return array<string,mixed>
     */
    private function normalizeLink(array $raw, int $index): array
    {
        $label = trim((string) ($raw['label'] ?? ''));
        $url = trim((string) ($raw['url'] ?? ''));
        $id = trim((string) ($raw['id'] ?? ''));
        if ($id === '') {
            $id = $this->slugifyId($label, $url) . '-' . ($index + 1);
        }

        return [
            'id' => $id,
            'label' => $label,
            'url' => $url,
            'starts_at' => $this->parseDateOnly($raw['starts_at'] ?? null),
            'expires_at' => $this->parseDateOnly($raw['expires_at'] ?? null),
            'notes' => trim((string) ($raw['notes'] ?? '')),
            'active' => ($raw['active'] ?? true) === false ? false : true,
            'rel' => $this->normalizeRel($raw['rel'] ?? ($raw['rel_tags'] ?? [])),
            'grace_days' => $this->normalizeGraceDays($raw['grace_days'] ?? null),
        ];
    }

    /**
     * @param  array<string,mixed>  $link
     */
    private function isVisible(array $link, ?\DateTimeInterface $now = null): bool
    {
        if (($link['active'] ?? true) === false) {
            return false;
        }
        $today = $this->nairobiDateString($now ?? new \DateTimeImmutable('now'));
        $starts = $link['starts_at'] ?? null;
        $expires = $link['expires_at'] ?? null;
        if (is_string($starts) && $starts !== '' && strcmp($today, $starts) < 0) {
            return false;
        }
        if (is_string($expires) && $expires !== '' && strcmp($today, $expires) > 0) {
            $grace = $this->normalizeGraceDays($link['grace_days'] ?? 4);
            $graceEnd = $this->addDaysToDateString($expires, $grace);
            if (strcmp($today, $graceEnd) > 0) {
                return false;
            }
        }

        return trim((string) ($link['label'] ?? '')) !== '' && trim((string) ($link['url'] ?? '')) !== '';
    }

    /**
     * @param  mixed  $raw
     * @return list<string>
     */
    private function normalizeRel(mixed $raw): array
    {
        $allowed = ['sponsored' => true, 'nofollow' => true, 'noopener' => true, 'noreferrer' => true];
        $tags = [];
        if (is_string($raw)) {
            $tags = preg_split('/[\s,]+/', $raw) ?: [];
        } elseif (is_array($raw)) {
            $tags = $raw;
        }
        $out = [];
        foreach ($tags as $tag) {
            $t = strtolower(trim((string) $tag));
            if ($t !== '' && isset($allowed[$t]) && ! in_array($t, $out, true)) {
                $out[] = $t;
            }
        }
        if (! in_array('noopener', $out, true)) {
            $out[] = 'noopener';
        }
        if (! in_array('noreferrer', $out, true)) {
            $out[] = 'noreferrer';
        }

        return $out;
    }

    private function normalizeGraceDays(mixed $raw): int
    {
        if ($raw === null || $raw === '') {
            return 4;
        }
        $days = (int) $raw;
        if ($days < 0) {
            return 4;
        }
        if ($days > 365) {
            return 365;
        }

        return $days;
    }

    private function parseDateOnly(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        $raw = trim((string) $value);
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $raw)) {
            return $raw;
        }
        $ts = strtotime($raw);
        if ($ts === false) {
            return null;
        }

        return gmdate('Y-m-d', $ts);
    }

    private function nairobiDateString(\DateTimeInterface $now): string
    {
        // Africa/Nairobi is UTC+3 year-round.
        $ms = ((int) $now->format('U')) + (3 * 3600);

        return gmdate('Y-m-d', $ms);
    }

    private function addDaysToDateString(string $dateStr, int $days): string
    {
        $ts = strtotime($dateStr . ' UTC');
        if ($ts === false) {
            return $dateStr;
        }

        return gmdate('Y-m-d', $ts + ($days * 86400));
    }

    private function slugifyId(string $label, string $url): string
    {
        $base = strtolower($label !== '' ? $label : $url);
        $base = preg_replace('#https?://#', '', $base) ?? $base;
        $base = preg_replace('/[^a-z0-9]+/', '-', $base) ?? $base;
        $base = trim($base, '-');
        $base = substr($base, 0, 48);

        return $base !== '' ? $base : 'link';
    }
}
