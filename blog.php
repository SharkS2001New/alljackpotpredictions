<?php
require_once __DIR__ . '/config/load-env.php';
require_once __DIR__ . '/src/Support/log.php';
require_once __DIR__ . '/src/Support/Cache.php';
require_once __DIR__ . '/src/Services/BlogService.php';

use App\Services\BlogService;

$page = max(1, (int) ($_GET['page'] ?? 1));
$category = trim((string) ($_GET['category'] ?? 'ALL'));
if ($category === '') {
    $category = 'ALL';
}
$perPage = 6;

$apiPayload = (new BlogService())->list($page, $category, $perPage);
$apiPosts = is_array($apiPayload['data'] ?? null) ? $apiPayload['data'] : [];

$currentPage = (int) ($apiPayload['current_page'] ?? $page);
$lastPage = max(1, (int) ($apiPayload['last_page'] ?? 1));
$total = (int) ($apiPayload['total'] ?? count($apiPosts));

/**
 * @param  array<string,mixed>  $row
 * @return array<string,mixed>|null
 */
function ajp_blog_normalize_card(array $row): ?array
{
    $slug = trim((string) ($row['slug'] ?? ''));
    if ($slug === '' || !preg_match('/^[a-z0-9][a-z0-9\-]{0,190}$/i', $slug)) {
        return null;
    }

    $categoryName = '';
    if (isset($row['category']) && is_array($row['category'])) {
        $categoryName = trim((string) ($row['category']['name'] ?? $row['category']['blogs_category_title'] ?? ''));
    } elseif (isset($row['category_name'])) {
        $categoryName = trim((string) $row['category_name']);
    }

    $author = 'AllJackpotPredictions Desk';
    if (isset($row['user']) && is_array($row['user'])) {
        $author = trim((string) ($row['user']['name'] ?? '')) ?: $author;
    } elseif (!empty($row['author'])) {
        $author = trim((string) $row['author']);
    }

    return [
        'id' => (string) ($row['id'] ?? $slug),
        'title' => (string) ($row['title'] ?? 'Untitled'),
        'slug' => $slug,
        'url' => '/blog/' . rawurlencode($slug),
        'excerpt' => (string) ($row['excerpt'] ?? $row['meta_description'] ?? ''),
        'published_at' => (string) ($row['published_at'] ?? $row['created_at'] ?? ''),
        'category' => $categoryName !== '' ? $categoryName : 'Articles',
        'author' => $author,
        'read_time' => max(1, (int) ($row['read_time'] ?? 5)),
    ];
}

$posts = [];
foreach ($apiPosts as $row) {
    if (!is_array($row)) {
        continue;
    }
    $card = ajp_blog_normalize_card($row);
    if ($card === null) {
        continue;
    }
    $posts[] = $card;
}

function ajp_blog_list_format_date(string $raw): string
{
    $raw = trim($raw);
    if ($raw === '') {
        return '';
    }
    $ts = strtotime($raw);
    if ($ts === false) {
        return $raw;
    }
    return date('j M Y', $ts);
}

function ajp_blog_list_datetime_attr(string $raw): string
{
    $raw = trim($raw);
    if ($raw === '') {
        return '';
    }
    $ts = strtotime($raw);
    if ($ts === false) {
        return '';
    }
    return date('Y-m-d', $ts);
}

function ajp_blog_title_case(string $value): string
{
    $value = trim($value);
    if ($value === '') {
        return '';
    }
    return mb_convert_case(str_replace(['-', '_'], ' ', $value), MB_CASE_TITLE, 'UTF-8');
}

/**
 * @return list<int|string>
 */
function ajp_blog_pagination_window(int $current, int $last): array
{
    if ($last <= 7) {
        return range(1, $last);
    }
    $pages = [1];
    $start = max(2, $current - 1);
    $end = min($last - 1, $current + 1);
    if ($start > 2) {
        $pages[] = '…';
    }
    for ($i = $start; $i <= $end; $i++) {
        $pages[] = $i;
    }
    if ($end < $last - 1) {
        $pages[] = '…';
    }
    $pages[] = $last;
    return $pages;
}

$paginationPages = ajp_blog_pagination_window($currentPage, $lastPage);
$canonical = 'https://www.alljackpotpredictions.com/blog';

function ajp_blog_page_url(int $pageNum, string $category): string
{
    $params = [];
    if ($pageNum > 1) {
        $params['page'] = $pageNum;
    }
    if ($category !== '' && strtoupper($category) !== 'ALL') {
        $params['category'] = $category;
    }
    $qs = http_build_query($params);
    return $qs !== '' ? '/blog?' . $qs : '/blog';
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Jackpot Blog — Acca Strategy &amp; Slip Guides | AllJackpotPredictions</title>
<meta name="description" content="AllJackpotPredictions blog — SportPesa and Betika jackpot strategy, banker selection, accumulator building and responsible betting guides.">
<link rel="canonical" href="<?php echo htmlspecialchars($canonical, ENT_QUOTES, 'UTF-8'); ?>">
<meta name="robots" content="index,follow">
<meta property="og:title" content="Jackpot Blog | AllJackpotPredictions">
<meta property="og:description" content="Jackpot strategy, banker shortlists and accumulator guides from the AllJackpotPredictions desk.">
<meta property="og:url" content="<?php echo htmlspecialchars($canonical, ENT_QUOTES, 'UTF-8'); ?>">
<meta property="og:type" content="website">
<meta property="og:site_name" content="AllJackpotPredictions">
<meta name="theme-color" content="#c9a227">
<link rel="icon" href="/img/favicon.ico" sizes="any">
<link rel="icon" href="/img/favicon.svg" type="image/svg+xml">
<link rel="stylesheet" href="/styles.css">
</head>
<body>
<?php include __DIR__ . '/header.php'; ?>

<main class="blogs-page">
  <div class="wrap" style="max-width:1120px;margin:0 auto;padding:24px 20px 48px">
    <nav class="breadcrumb" aria-label="Breadcrumb" style="margin-bottom:16px;font-size:.85rem;color:var(--muted)">
      <a href="/">Home</a> · <span>Blog</span>
    </nav>

    <header class="blogs-page-title">
      <h1>Jackpot Blog — Acca Strategy &amp; Slip Guides</h1>
    </header>

<?php if ($posts === []): ?>
    <div class="blogs-empty">
      <p>No articles published yet. Check back soon — or browse <a href="/jackpot-picks-today">today's jackpots</a>.</p>
    </div>
<?php else: ?>
    <div class="blog-list-grid">
<?php foreach ($posts as $post): ?>
<?php
  $dt = ajp_blog_list_datetime_attr((string) ($post['published_at'] ?? ''));
  $dateLabel = ajp_blog_list_format_date((string) ($post['published_at'] ?? ''));
  $href = htmlspecialchars((string) $post['url'], ENT_QUOTES, 'UTF-8');
?>
      <article class="blog-card">
        <div class="blog-content">
          <small class="blog-category"><?php echo htmlspecialchars(ajp_blog_title_case((string) $post['category']), ENT_QUOTES, 'UTF-8'); ?></small>
          <a href="<?php echo $href; ?>" class="blog-title"><?php echo htmlspecialchars((string) $post['title'], ENT_QUOTES, 'UTF-8'); ?></a>
          <div class="blog-meta">
            <?php echo htmlspecialchars((string) $post['author'], ENT_QUOTES, 'UTF-8'); ?>
<?php if ($dateLabel !== ''): ?>
            &nbsp;/&nbsp;
            <time datetime="<?php echo htmlspecialchars($dt, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($dateLabel, ENT_QUOTES, 'UTF-8'); ?></time>
<?php endif; ?>
          </div>
<?php if (trim((string) ($post['excerpt'] ?? '')) !== ''): ?>
          <p class="blog-excerpt"><?php echo htmlspecialchars((string) $post['excerpt'], ENT_QUOTES, 'UTF-8'); ?></p>
<?php endif; ?>
        </div>
        <div class="blog-footer">
          <a href="<?php echo $href; ?>" class="read-more-btn" rel="bookmark">Read More →</a>
          <div class="blog-read-time">
            <span><?php echo (int) $post['read_time']; ?> min</span>
          </div>
        </div>
      </article>
<?php endforeach; ?>
    </div>

<?php if ($lastPage > 1): ?>
    <nav class="pagination-container" aria-label="Blog pages">
<?php if ($currentPage > 1): ?>
      <a class="page-btn" href="<?php echo htmlspecialchars(ajp_blog_page_url($currentPage - 1, $category), ENT_QUOTES, 'UTF-8'); ?>">Previous</a>
<?php endif; ?>
<?php foreach ($paginationPages as $p): ?>
<?php if ($p === '…'): ?>
      <span class="page-ellipsis" aria-hidden="true">…</span>
<?php else: ?>
      <a
        class="page-btn<?php echo ((int) $p === $currentPage) ? ' active' : ''; ?>"
        href="<?php echo htmlspecialchars(ajp_blog_page_url((int) $p, $category), ENT_QUOTES, 'UTF-8'); ?>"
        <?php echo ((int) $p === $currentPage) ? 'aria-current="page"' : ''; ?>
      ><?php echo (int) $p; ?></a>
<?php endif; ?>
<?php endforeach; ?>
<?php if ($currentPage < $lastPage): ?>
      <a class="page-btn" href="<?php echo htmlspecialchars(ajp_blog_page_url($currentPage + 1, $category), ENT_QUOTES, 'UTF-8'); ?>">Next</a>
<?php endif; ?>
    </nav>
<?php endif; ?>
<?php endif; ?>
  </div>
</main>

<?php include __DIR__ . '/footer.php'; ?>
<script src="/main.js"></script>
</body>
</html>
