<?php
/**
 * Dynamic admin-published blog post at /blog/{slug}.
 */
require_once __DIR__ . '/config/load-env.php';
require_once __DIR__ . '/src/Support/log.php';
require_once __DIR__ . '/src/Support/Cache.php';
require_once __DIR__ . '/src/Services/BlogService.php';

use App\Services\BlogService;

$slug = trim((string) ($_GET['slug'] ?? ''));
if ($slug === '' || !preg_match('/^[a-z0-9][a-z0-9\-]{0,190}$/i', $slug)) {
    http_response_code(404);
    include __DIR__ . '/404.php';
    return;
}

$blog = (new BlogService())->post($slug);
if ($blog === null) {
    http_response_code(404);
    include __DIR__ . '/404.php';
    return;
}

$title = trim((string) ($blog['meta_title'] ?? $blog['title'] ?? 'Blog'));
$description = trim((string) ($blog['meta_description'] ?? $blog['excerpt'] ?? ''));
$keywords = trim((string) ($blog['meta_keywords'] ?? ''));
$content = (string) ($blog['content'] ?? '');
$publishedRaw = (string) ($blog['published_at'] ?? $blog['created_at'] ?? '');
$publishedTs = $publishedRaw !== '' ? strtotime($publishedRaw) : false;
$publishedLabel = $publishedTs ? date('j M Y', $publishedTs) : '';
$publishedIso = $publishedTs ? date('Y-m-d', $publishedTs) : '';
$canonical = 'https://www.alljackpotpredictions.com/blog/' . rawurlencode($slug);
$pageTitle = $title . (stripos($title, 'AllJackpotPredictions') === false ? ' | AllJackpotPredictions' : '');
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></title>
<?php if ($description !== ''): ?>
<meta name="description" content="<?php echo htmlspecialchars($description, ENT_QUOTES, 'UTF-8'); ?>">
<?php endif; ?>
<?php if ($keywords !== ''): ?>
<meta name="keywords" content="<?php echo htmlspecialchars($keywords, ENT_QUOTES, 'UTF-8'); ?>">
<?php endif; ?>
<link rel="canonical" href="<?php echo htmlspecialchars($canonical, ENT_QUOTES, 'UTF-8'); ?>">
<meta name="robots" content="index,follow">
<meta property="og:title" content="<?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?>">
<?php if ($description !== ''): ?>
<meta property="og:description" content="<?php echo htmlspecialchars($description, ENT_QUOTES, 'UTF-8'); ?>">
<?php endif; ?>
<meta property="og:url" content="<?php echo htmlspecialchars($canonical, ENT_QUOTES, 'UTF-8'); ?>">
<meta property="og:type" content="article">
<meta property="og:site_name" content="AllJackpotPredictions">
<meta name="theme-color" content="#c9a227">
<link rel="icon" href="/img/favicon.ico" sizes="any">
<link rel="icon" href="/img/favicon.svg" type="image/svg+xml">
<link rel="stylesheet" href="/styles.css">
</head>
<body>
<?php include __DIR__ . '/header.php'; ?>

<main class="blog-post-page">
  <div class="wrap" style="max-width:760px;margin:0 auto;padding:24px 20px 48px">
    <nav class="breadcrumb" aria-label="Breadcrumb" style="margin-bottom:16px;font-size:.85rem;color:var(--muted)">
      <a href="/">Home</a> · <a href="/blog">Blog</a> ·
      <span><?php echo htmlspecialchars((string) ($blog['title'] ?? 'Post'), ENT_QUOTES, 'UTF-8'); ?></span>
    </nav>

    <header class="blog-post-hero">
      <h1><?php echo htmlspecialchars((string) ($blog['title'] ?? 'Post'), ENT_QUOTES, 'UTF-8'); ?></h1>
<?php if ($publishedIso !== ''): ?>
      <p class="blog-post-date"><time datetime="<?php echo htmlspecialchars($publishedIso, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($publishedLabel, ENT_QUOTES, 'UTF-8'); ?></time></p>
<?php endif; ?>
<?php if (trim((string) ($blog['excerpt'] ?? '')) !== ''): ?>
      <p class="blog-post-lede"><?php echo htmlspecialchars((string) $blog['excerpt'], ENT_QUOTES, 'UTF-8'); ?></p>
<?php endif; ?>
    </header>

    <article class="blog-prose">
      <?php
        // Admin-authored HTML — same trust model as Pitch / Bao / Free Winning Tips.
        echo $content;
      ?>
    </article>
  </div>
</main>

<?php include __DIR__ . '/footer.php'; ?>
<script src="/main.js"></script>
</body>
</html>
