<?php
/**
 * Generic jackpot detail fallback for slugs without a dedicated SEO page in /jackpot-pages/.
 * Prefer /jackpot-pages/{slug}.php when it exists (public URL remains /jackpots/{slug}).
 */
$slug = trim((string) ($_GET['slug'] ?? ''));
require __DIR__ . '/includes/jackpot-page.php';
