<?php
/**
 * Admin — Text Link & Partner Card Manager (JSON file storage, no database)
 * Three tabs:
 *   Footer Text Links   -> sponsor_links.json  (?tab=footer)
 *   Partners Text Links -> partner_links.json  (?tab=partners)
 *   Partner Cards       -> partners.json       (?tab=cards)
 *
 * Wire-up:
 *   1. require_once your existing admin-auth check — keep this page behind login.
 *   2. Make sure /data/ is not publicly reachable (see data/.htaccess).
 */

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/link-functions.php';

if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(24));

/* distinct sender names seen so far, across all three stores, for autocomplete */
$knownSenders = [];
foreach (array_merge(ll_get_all('sponsor'), ll_get_all('partner'), ll_get_all('card')) as $r) {
    if (!empty($r['sender_name'])) $knownSenders[$r['sender_name']] = true;
}
$knownSenders = array_keys($knownSenders);
sort($knownSenders, SORT_FLAG_CASE | SORT_STRING);

$tabParam = $_GET['tab'] ?? 'footer';
$tab = $tabParam === 'partners' ? 'partner' : ($tabParam === 'cards' ? 'card' : 'sponsor');
$tabUrlValue = ['sponsor' => 'footer', 'partner' => 'partners', 'card' => 'cards'][$tab];
$flash = null;

/* ── handle form submissions ── */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf'], $_POST['csrf'] ?? '')) {
        $flash = ['type' => 'error', 'msg' => 'Session expired, please try again.'];
    } else {
        $postTabParam = $_POST['link_type'] ?? 'sponsor';
        $postType = in_array($postTabParam, ['sponsor', 'partner', 'card']) ? $postTabParam : 'sponsor';
        $action   = $_POST['action'] ?? '';

        if ($action === 'delete' && !empty($_POST['id'])) {
            ll_delete($postType, $_POST['id']);
            $flash = ['type' => 'ok', 'msg' => 'Deleted.'];
        } elseif ($action === 'toggle_paid' && !empty($_POST['id'])) {
            ll_toggle_paid($postType, $_POST['id']);
        } elseif ($action === 'save') {
            $data = [
                'anchor_text'   => $_POST['anchor_text'] ?? '',
                'name'          => $_POST['name'] ?? '',
                'url'           => $_POST['url'] ?? '',
                'sender_name'   => $_POST['sender_name'] ?? '',
                'rel_type'      => $_POST['rel_type'] ?? 'nofollow',
                'starts_at'     => !empty($_POST['starts_at']) ? $_POST['starts_at'] : date('Y-m-d H:i:s'),
                'duration'      => $_POST['duration'] ?? '1',
                'custom_expiry' => $_POST['custom_expiry'] ?? null,
                'is_active'     => isset($_POST['is_active']) ? 1 : 0,
                'sort_order'    => $_POST['sort_order'] ?? 0,
                'description'   => $_POST['description'] ?? '',
                'badge_type'    => $_POST['badge_type'] ?? 'network',
                'is_main'       => isset($_POST['is_main']) ? 1 : 0,
                'tags'          => $_POST['tags'] ?? '',
            ];

            $nameField = $postType === 'card' ? $data['name'] : $data['anchor_text'];
            $errors = [];
            if (trim($nameField) === '') $errors[] = $postType === 'card' ? 'Name' : 'Anchor text';
            if (trim($data['url']) === '') $errors[] = 'URL';
            if ($postType !== 'card' && trim($data['sender_name']) === '') $errors[] = 'Sender name';

            if (!empty($errors)) {
                $flash = ['type' => 'error', 'msg' => implode(', ', $errors) . ' required.'];
            } else {
                if (!empty($_POST['id'])) {
                    ll_update($postType, $_POST['id'], $data);
                    $flash = ['type' => 'ok', 'msg' => 'Updated.'];
                } else {
                    ll_create($postType, $data);
                    $flash = ['type' => 'ok', 'msg' => 'Added.'];
                }
            }
        }
        $tab = $postType;
        $tabUrlValue = ['sponsor' => 'footer', 'partner' => 'partners', 'card' => 'cards'][$tab];
    }
}

/* ── data for the current tab ── */
$editId   = $_GET['edit'] ?? null;
$editRow  = $editId ? ll_get_one($tab, $editId) : null;
/* ── auto-purge: delete anything expired 90+ days with no renewal ── */
foreach (['sponsor', 'partner', 'card'] as $t) { ll_purge_stale_expired($t); }

$allRows  = ll_get_all($tab);

$selectedDuration = '1';
if ($editRow) {
    $selectedDuration = (($editRow['duration_type'] ?? 'limited') === 'permanent') ? 'permanent' : 'custom';
}

function h($v) { return htmlspecialchars((string) $v, ENT_QUOTES); }
function is_expired($row) {
    if (($row['duration_type'] ?? 'limited') === 'permanent') return false;
    return strtotime($row['expires_at']) < time();
}

/* ── search ── */
$searchQuery = isset($_GET['q']) ? trim($_GET['q']) : '';
$filteredRows = $allRows;
if ($searchQuery !== '') {
    $needle = mb_strtolower($searchQuery);
    $filteredRows = array_values(array_filter($allRows, function ($row) use ($needle) {
        $haystack = mb_strtolower(
            ($row['name'] ?? '') . ' ' .
            ($row['anchor_text'] ?? '') . ' ' .
            $row['url'] . ' ' .
            ($row['sender_name'] ?? '') . ' ' .
            ($row['description'] ?? '')
        );
        return mb_strpos($haystack, $needle) !== false;
    }));
}

/* ── status filter (all / active / expired) ── */
$statusFilter = isset($_GET['status']) && in_array($_GET['status'], ['active', 'expired']) ? $_GET['status'] : 'all';
if ($statusFilter !== 'all') {
    $filteredRows = array_values(array_filter($filteredRows, function ($row) use ($statusFilter) {
        $expired = is_expired($row);
        if ($statusFilter === 'expired') return $expired;
        return !empty($row['is_active']) && !$expired;
    }));
}

/* ── pagination ── */
$perPage = 15;
$totalFiltered = count($filteredRows);
$totalPages = max(1, (int) ceil($totalFiltered / $perPage));
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
if ($page < 1) $page = 1;
if ($page > $totalPages) $page = $totalPages;
$pageRows = array_slice($filteredRows, ($page - 1) * $perPage, $perPage);
$qParam = $searchQuery !== '' ? '&q=' . urlencode($searchQuery) : '';
$qParam .= $statusFilter !== 'all' ? '&status=' . $statusFilter : '';

/** Compact page-number list with ellipsis gaps for large page counts. */
function ll_page_window($current, $total) {
    $pages = [1, $total, $current];
    if ($current - 1 >= 1) $pages[] = $current - 1;
    if ($current + 1 <= $total) $pages[] = $current + 1;
    if ($total <= 7) { for ($i = 1; $i <= $total; $i++) $pages[] = $i; }
    $pages = array_unique($pages);
    sort($pages);
    return $pages;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin — Link &amp; Partner Manager</title>
<script>
  (function () {
    var saved = localStorage.getItem('to-admin-theme');
    var theme = saved || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
    document.documentElement.setAttribute('data-theme', theme);
  })();
</script>
<style>
  [data-theme="light"] { --navy:#0b3d2e; --pink:#c9a227; --paper:#f6f4ef; --paper2:#ede9e1; --border:#d8d3c8; --body:#3a3f52; --muted:#8a8fa8; --green:#0a8f5a; --red:#c9292a; --card-bg:#fff; --surface2:#fff; }
  [data-theme="dark"]  { --navy:#0b3d2e; --pink:#c9a227; --paper:#0e0f14; --paper2:#161822; --border:#252838; --body:#d8dae2; --muted:#7b80a0; --green:#1fd17c; --red:#ff5252; --card-bg:#161822; --surface2:#1d2030; }
  * { box-sizing:border-box; }
  body { font-family:'Manrope','Avenir Next',sans-serif; background:var(--paper); color:var(--body); margin:0; transition:background .2s, color .2s; }
  .topbar { background:var(--navy); padding:0 32px; height:56px; display:flex; align-items:center; justify-content:space-between; }
  .topbar-title { font-family:system-ui,sans-serif; font-size:.95rem; font-weight:800; color:#fff; }
  .topbar-title a { color:#fff; text-decoration:none; }
  .topbar-title span { color:var(--pink); }
  .topbar-right { display:flex; align-items:center; gap:14px; }
  .topbar-label { font-size:.75rem; color:rgba(255,255,255,.5); font-weight:600; }
  .theme-toggle-btn {
    width:32px; height:32px; border-radius:8px; background:rgba(255,255,255,.08); border:1px solid rgba(255,255,255,.18);
    display:flex; align-items:center; justify-content:center; cursor:pointer; font-size:.95rem; transition:all .18s;
  }
  .theme-toggle-btn:hover { background:rgba(255,255,255,.14); }
  .ti-light { display:none; }
  [data-theme="light"] .ti-dark { display:none; }
  [data-theme="light"] .ti-light { display:inline; }
  .logout-btn { font-size:.75rem; font-weight:700; color:rgba(255,255,255,.7); text-decoration:none; padding:6px 12px; border:1px solid rgba(255,255,255,.18); border-radius:6px; transition:all .18s; }
  .logout-btn:hover { color:#fff; border-color:rgba(255,255,255,.4); background:rgba(255,255,255,.06); }
  .page-wrap { padding:28px 32px; }
  h1 { font-family:system-ui,sans-serif; font-size:1.5rem; color:var(--navy); margin:0 0 20px; }
  [data-theme="dark"] h1 { color:var(--body); }
  .tabs { display:flex; gap:8px; margin-bottom:20px; flex-wrap:wrap; }
  .tab { padding:10px 18px; border-radius:6px 6px 0 0; background:var(--paper2); color:var(--muted); font-weight:700; font-size:.85rem; text-decoration:none; border:1px solid var(--border); border-bottom:none; }
  .tab.active { background:var(--card-bg); color:var(--navy); border-color:var(--navy); }
  [data-theme="dark"] .tab.active { color:var(--body); border-color:var(--pink); }
  .card-box { background:var(--card-bg); border:1px solid var(--border); border-radius:0 8px 8px 8px; padding:22px; margin-bottom:24px; }
  .flash { padding:10px 14px; border-radius:6px; margin-bottom:16px; font-size:.85rem; font-weight:600; }
  .flash.ok { background:rgba(10,143,90,.1); color:var(--green); border:1px solid rgba(10,143,90,.25); }
  .flash.error { background:rgba(201,41,42,.1); color:var(--red); border:1px solid rgba(201,41,42,.25); }
  form.link-form { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
  form.link-form .full { grid-column:1 / -1; }
  label { display:block; font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:var(--muted); margin-bottom:5px; }
  input[type=text], input[type=url], input[type=datetime-local], select, textarea {
    width:100%; padding:9px 11px; background:var(--paper); border:1px solid var(--border); border-radius:5px; font-size:.88rem; font-family:inherit; color:var(--body);
  }
  textarea { resize:vertical; min-height:60px; }
  .rel-choice { display:flex; gap:16px; align-items:center; }
  .rel-choice label { display:flex; align-items:center; gap:6px; font-size:.85rem; text-transform:none; letter-spacing:0; font-weight:500; color:var(--body); margin:0; }
  .actions-row { grid-column:1/-1; display:flex; gap:10px; align-items:center; margin-top:6px; }
  button, .btn { cursor:pointer; border:none; border-radius:6px; padding:10px 20px; font-weight:700; font-size:.85rem; font-family:inherit; }
  .btn-primary { background:var(--pink); color:#fff; }
  .btn-secondary { background:var(--paper2); color:var(--body); border:1px solid var(--border); text-decoration:none; display:inline-flex; align-items:center; }
  table { width:100%; border-collapse:collapse; margin-top:8px; font-size:.85rem; }
  th, td { text-align:left; padding:10px 12px; border-bottom:1px solid var(--border); vertical-align:middle; }
  th { font-size:.68rem; text-transform:uppercase; letter-spacing:.06em; color:var(--muted); }
  .badge { display:inline-block; padding:2px 8px; border-radius:20px; font-size:.68rem; font-weight:700; white-space:nowrap; }
  .badge.active { background:rgba(10,143,90,.1); color:var(--green); }
  .badge.expired { background:rgba(201,41,42,.1); color:var(--red); }
  .badge.inactive { background:var(--paper2); color:var(--muted); }
  .badge.permanent { background:rgba(11,61,46,.08); color:var(--navy); }
  [data-theme="dark"] .badge.permanent { color:#e8d48b; }
  .badge.dofollow { background:rgba(11,61,46,.08); color:var(--navy); }
  [data-theme="dark"] .badge.dofollow { color:#e8d48b; }
  .badge.nofollow { background:var(--paper2); color:var(--muted); }
  .badge.partner { background:rgba(201,162,39,.15); color:#a8841e; }
  .badge.network { background:rgba(11,61,46,.08); color:var(--navy); }
  [data-theme="dark"] .badge.network { color:#e8d48b; }
  .row-actions a, .row-actions button { font-size:.72rem; font-weight:700; margin-right:10px; background:none; border:none; padding:0; color:var(--navy); text-decoration:underline; cursor:pointer; }
  [data-theme="dark"] .row-actions a, [data-theme="dark"] .row-actions button { color:#e8d48b; }
  .row-actions button.del { color:var(--red); }
  .url-cell { max-width:200px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
  .hint { font-size:.72rem; color:var(--muted); margin-top:5px; line-height:1.5; }
  .paid-badge {
    display:inline-flex; align-items:center; gap:4px; font-size:.72rem; font-weight:700;
    color:var(--green); background:rgba(10,143,90,.12); border:1px solid rgba(10,143,90,.3);
    padding:3px 11px; border-radius:20px; cursor:pointer; font-family:inherit;
  }
  .paid-badge:hover { opacity:.8; }
  .mark-paid-btn {
    font-size:.72rem; font-weight:700; color:var(--muted); background:none;
    border:1px dashed var(--border); padding:3px 11px; border-radius:20px;
    cursor:pointer; font-family:inherit; transition:all .18s;
  }
  .mark-paid-btn:hover { color:var(--pink); border-color:var(--pink); }
  .search-row { display:flex; gap:8px; margin-bottom:16px; }
  .search-row input[type=text] { flex:1; }
  .results-info { font-size:.75rem; color:var(--muted); margin-bottom:10px; }
  .pagination-row { display:flex; align-items:center; justify-content:center; gap:6px; margin-top:18px; flex-wrap:wrap; }
  .page-btn {
    display:inline-flex; align-items:center; justify-content:center; min-width:34px; height:34px; padding:0 10px;
    border:1px solid var(--border); border-radius:6px; background:var(--paper); color:var(--muted);
    font-size:.8rem; font-weight:600; text-decoration:none; transition:all .18s;
  }
  .page-btn:hover { border-color:var(--pink); color:var(--pink); }
  .page-btn.active { background:var(--pink); border-color:var(--pink); color:#fff; }
  .page-btn.disabled { opacity:.35; pointer-events:none; }
  .page-ellipsis { color:var(--muted); padding:0 4px; }

  /* ═══ MOBILE RESPONSIVE ═══ */
  @media(max-width:700px) {
    .topbar { padding:10px 16px; flex-wrap:wrap; height:auto; gap:10px; }
    .topbar-right { flex-wrap:wrap; gap:8px; }
    .topbar-label { display:none; }
    .page-wrap { padding:16px; }
    .tabs { flex-wrap:wrap; }
    form.link-form { grid-template-columns:1fr; }
    .search-row { flex-wrap:wrap; }
    .search-row input[type=text] { width:100%; }
    .search-row button, .search-row a.btn { flex:1; text-align:center; }

    table, thead, tbody, th, tr { display:block; width:100%; }
    thead tr { position:absolute; top:-9999px; left:-9999px; }
    table { border:none; }
    tbody tr { border:1px solid var(--border); border-radius:8px; margin-bottom:12px; overflow:hidden; }
    tbody td {
      display:flex; align-items:center; justify-content:space-between; gap:12px;
      padding:10px 14px; border-bottom:1px solid var(--border); text-align:right; white-space:normal;
      width:auto;
    }
    tbody tr td:last-child { border-bottom:none; }
    tbody td::before {
      content: attr(data-label); font-weight:700; font-size:.66rem; text-transform:uppercase;
      letter-spacing:.05em; color:var(--muted); text-align:left; flex-shrink:0;
    }
    .url-cell { max-width:none; white-space:normal; word-break:break-all; }
    td.row-actions { justify-content:flex-end; gap:14px; }
  }
</style>
</head>
<body>

<div class="topbar">
  <div class="topbar-title"><a href="index.php">AllJackpot<span>Predictions</span> Admin</a></div>
  <div class="topbar-right">
    <span class="topbar-label">Signed in as <?= htmlspecialchars(ADMIN_USERNAME) ?></span>
    <button class="theme-toggle-btn" id="themeToggleBtn" title="Toggle light/dark" type="button">
      <span class="ti-dark">🌙</span><span class="ti-light">☀️</span>
    </button>
    <a class="logout-btn" href="logout.php">Log Out</a>
  </div>
</div>

<div class="page-wrap">

<h1>Text Links &amp; Partner Cards</h1>

<div class="tabs">
  <a class="tab <?= $tab === 'sponsor' ? 'active' : '' ?>" href="?tab=footer">Footer Text Links</a>
  <a class="tab <?= $tab === 'partner' ? 'active' : '' ?>" href="?tab=partners">Partners — Text Links</a>
  <a class="tab <?= $tab === 'card' ? 'active' : '' ?>" href="?tab=cards">Partners — Partner Cards</a>
</div>


<div class="card-box">

  <?php if ($flash): ?>
    <div class="flash <?= $flash['type'] ?>"><?= h($flash['msg']) ?></div>
  <?php endif; ?>

  <form class="link-form" method="post" action="?tab=<?= $tabUrlValue ?>">
    <input type="hidden" name="csrf" value="<?= h($_SESSION['csrf']) ?>">
    <input type="hidden" name="link_type" value="<?= $tab ?>">
    <?php if ($editRow): ?><input type="hidden" name="id" value="<?= h($editRow['id']) ?>"><?php endif; ?>
    <input type="hidden" name="action" value="save">

    <?php if ($tab === 'card'): ?>
      <div>
        <label>Anchor Text</label>
        <input type="text" name="name" id="anchorField" required value="<?= h($editRow['name'] ?? '') ?>" placeholder="e.g. 100tip — auto-suggested from URL below if left blank">
      </div>
      <div>
        <label>URL</label>
        <input type="url" name="url" id="urlField" required value="<?= h($editRow['url'] ?? '') ?>" placeholder="https://example.com">
      </div>

      <div>
        <label>Section</label>
        <select name="badge_type" id="badgeTypeSelect" onchange="document.getElementById('isMainWrap').style.display = this.value === 'partner' ? 'block' : 'none';">
          <option value="partner" <?= (($editRow['badge_type'] ?? '') === 'partner') ? 'selected' : '' ?>>Prediction Partners (⭐)</option>
          <option value="network" <?= (($editRow['badge_type'] ?? 'network') === 'network') ? 'selected' : '' ?>>Our Network (🌐)</option>
        </select>
      </div>
      <div id="isMainWrap" style="display:<?= (($editRow['badge_type'] ?? '') === 'partner') ? 'block' : 'none' ?>;">
        <label><input type="checkbox" name="is_main" <?= !empty($editRow['is_main']) ? 'checked' : '' ?>> Main Partner (shows "⭐ Main Partner" instead of "⭐ Partner")</label>
      </div>

      <div class="full">
        <label>Description</label>
        <textarea name="description"><?= h($editRow['description'] ?? '') ?></textarea>
      </div>
      <div class="full">
        <label>Tags (comma-separated)</label>
        <input type="text" name="tags" value="<?= h(!empty($editRow['tags']) ? implode(', ', $editRow['tags']) : '') ?>" placeholder="Free Tips, All Markets, Statistics">
      </div>
    <?php else: ?>
      <div>
        <label>Anchor Text</label>
        <input type="text" name="anchor_text" id="anchorField" required value="<?= h($editRow['anchor_text'] ?? '') ?>" placeholder="e.g. xoilactv — auto-suggested from URL below if left blank">
      </div>
      <div>
        <label>URL</label>
        <input type="url" name="url" id="urlField" required value="<?= h($editRow['url'] ?? '') ?>" placeholder="https://example.com">
      </div>
    <?php endif; ?>

    <div>
      <label>Sender Name<?= $tab === 'card' ? ' (optional)' : '' ?></label>
      <input type="text" name="sender_name" <?= $tab === 'card' ? '' : 'required' ?> list="senderNames" autocomplete="off"
             value="<?= h($editRow['sender_name'] ?? '') ?>" placeholder="Start typing — past senders will suggest">
      <datalist id="senderNames">
        <?php foreach ($knownSenders as $name): ?>
          <option value="<?= h($name) ?>">
        <?php endforeach; ?>
      </datalist>
    </div>
    <div>
      <label>Link Type</label>
      <div class="rel-choice">
        <label><input type="radio" name="rel_type" value="dofollow" <?= (($editRow['rel_type'] ?? '') === 'dofollow') ? 'checked' : '' ?>> Dofollow</label>
        <label><input type="radio" name="rel_type" value="nofollow" <?= (($editRow['rel_type'] ?? 'nofollow') === 'nofollow') ? 'checked' : '' ?>> Nofollow</label>
      </div>
    </div>

    <div>
      <label>Start Date</label>
      <input type="datetime-local" name="starts_at"
        value="<?= h(!empty($editRow['starts_at']) ? date('Y-m-d\TH:i', strtotime($editRow['starts_at'])) : date('Y-m-d\TH:i')) ?>">
    </div>
    <div>
      <label>Duration</label>
      <select name="duration" id="durationSelect" onchange="document.getElementById('customExpiryWrap').style.display = this.value === 'custom' ? 'block' : 'none';">
        <option value="1" <?= $selectedDuration === '1' ? 'selected' : '' ?>>1 Month</option>
        <option value="2">2 Months</option>
        <option value="3">3 Months</option>
        <option value="6">6 Months</option>
        <option value="12">1 Year</option>
        <option value="custom" <?= $selectedDuration === 'custom' ? 'selected' : '' ?>>Custom date</option>
        <?php if ($tab !== 'sponsor'): ?>
          <option value="permanent" <?= $selectedDuration === 'permanent' ? 'selected' : '' ?>>Permanent (no expiry)</option>
        <?php endif; ?>
      </select>
      <?php if ($tab === 'sponsor'): ?>
        <p class="hint">Footer sponsor links always run on a timer — that's the paid-placement model. Use the Partners tabs for permanent listings.</p>
      <?php endif; ?>
    </div>

    <div class="full" id="customExpiryWrap" style="display:<?= $selectedDuration === 'custom' ? 'block' : 'none' ?>;">
      <label>Custom / Current Expiry Date</label>
      <input type="datetime-local" name="custom_expiry" value="<?= h(!empty($editRow['expires_at']) && $selectedDuration === 'custom' ? date('Y-m-d\TH:i', strtotime($editRow['expires_at'])) : '') ?>">
      <p class="hint">On edit, this shows the link's current expiry. Picking "1 Month" etc. above instead recalculates a fresh expiry from the Start Date.</p>
    </div>

    <?php if ($tab === 'partner'): ?>
    <div class="full">
      <label>Description (optional)</label>
      <textarea name="description"><?= h($editRow['description'] ?? '') ?></textarea>
    </div>
    <?php endif; ?>

    <div>
      <label>Display Order (optional)</label>
      <input type="text" name="sort_order" value="<?= h($editRow['sort_order'] ?? 0) ?>">
      <p class="hint">Controls left-to-right order on the site. Lower shows first (0 before 1 before 2...). Leave everyone at 0 to just show newest-added first.</p>
    </div>
    <div>
      <label><input type="checkbox" name="is_active" <?= (!$editRow || $editRow['is_active']) ? 'checked' : '' ?>> Active</label>
    </div>

    <div class="actions-row">
      <button type="submit" class="btn-primary"><?= $editRow ? 'Update' : 'Add' ?></button>
      <?php if ($editRow): ?><a class="btn btn-secondary" href="?tab=<?= $tabUrlValue ?>">Cancel edit</a><?php endif; ?>
    </div>
  </form>

  <script>
    document.getElementById('durationSelect').dispatchEvent(new Event('change'));

    (function () {
      var urlField    = document.getElementById('urlField');
      var anchorField = document.getElementById('anchorField');
      if (!urlField || !anchorField) return;
      urlField.addEventListener('blur', function () {
        if (anchorField.value.trim() !== '') return;
        try {
          var host = new URL(urlField.value).hostname.replace(/^www\./i, '');
          var parts = host.split('.');
          if (parts.length > 1) parts.pop();
          anchorField.value = parts.join('.');
        } catch (e) { /* invalid/incomplete URL — ignore */ }
      });
    })();
  </script>

</div>

<div class="card-box">

  <div class="tabs" style="margin-bottom:16px;">
    <a class="tab <?= $statusFilter === 'all' ? 'active' : '' ?>" href="?tab=<?= $tabUrlValue ?><?= $searchQuery !== '' ? '&q=' . urlencode($searchQuery) : '' ?>">All Links</a>
    <a class="tab <?= $statusFilter === 'active' ? 'active' : '' ?>" href="?tab=<?= $tabUrlValue ?>&status=active<?= $searchQuery !== '' ? '&q=' . urlencode($searchQuery) : '' ?>">Active</a>
    <a class="tab <?= $statusFilter === 'expired' ? 'active' : '' ?>" href="?tab=<?= $tabUrlValue ?>&status=expired<?= $searchQuery !== '' ? '&q=' . urlencode($searchQuery) : '' ?>">Expired</a>
  </div>

  <form method="get" class="search-row">
    <input type="hidden" name="tab" value="<?= $tabUrlValue ?>">
    <?php if ($statusFilter !== 'all'): ?><input type="hidden" name="status" value="<?= $statusFilter ?>"><?php endif; ?>
    <input type="text" name="q" value="<?= h($searchQuery) ?>" placeholder="Search by name/anchor, URL, or sender...">
    <button type="submit" class="btn-primary">Search</button>
    <?php if ($searchQuery !== ''): ?><a class="btn btn-secondary" href="?tab=<?= $tabUrlValue ?><?= $statusFilter !== 'all' ? '&status=' . $statusFilter : '' ?>">Clear</a><?php endif; ?>
  </form>

  <?php if ($searchQuery !== ''): ?>
    <div class="results-info"><?= $totalFiltered ?> result<?= $totalFiltered === 1 ? '' : 's' ?> for "<?= h($searchQuery) ?>"</div>
  <?php endif; ?>

  <table>
    <thead>
      <?php if ($tab === 'card'): ?>
        <tr><th>Name</th><th>Section</th><th>URL</th><th>Rel</th><th>Duration</th><th>Status</th><th>Paid</th><th></th></tr>
      <?php else: ?>
        <tr><th>Anchor</th><th>URL</th><th>Sender</th><th>Rel</th><th>Starts</th><th>Expires</th><th>Status</th><th>Paid</th><th></th></tr>
      <?php endif; ?>
    </thead>
    <tbody>
      <?php if (empty($pageRows)): ?>
        <tr><td colspan="9" style="color:var(--muted)"><?= $searchQuery !== '' ? 'No matches for your search.' : 'Nothing here yet.' ?></td></tr>
      <?php endif; ?>
      <?php foreach ($pageRows as $row): ?>
        <?php
          $expired = is_expired($row);
          $status  = !$row['is_active'] ? 'inactive' : ($expired ? 'expired' : 'active');
          $isPaid  = !empty($row['is_paid']);
        ?>
        <tr>
          <?php if ($tab === 'card'): ?>
            <td data-label="Name"><?= h($row['name']) ?></td>
            <td data-label="Section"><span class="badge <?= h($row['badge_type']) ?>"><?= $row['badge_type'] === 'partner' ? '⭐ Partner' . (!empty($row['is_main']) ? ' (Main)' : '') : '🌐 Network' ?></span></td>
            <td data-label="URL" class="url-cell" title="<?= h($row['url']) ?>"><?= h($row['url']) ?></td>
            <td data-label="Rel"><span class="badge <?= $row['rel_type'] ?>"><?= h($row['rel_type']) ?></span></td>
            <td data-label="Duration">
              <span class="badge <?= ($row['duration_type'] ?? 'limited') === 'permanent' ? 'permanent' : '' ?>">
                <?= ($row['duration_type'] ?? 'limited') === 'permanent' ? 'Permanent' : h(date('d M Y', strtotime($row['expires_at']))) ?>
              </span>
              <?php if (($row['duration_type'] ?? 'limited') !== 'permanent'): ?>
                <br><span class="hint" style="margin:0;"><?= h(ll_days_label($row)) ?></span>
              <?php endif; ?>
            </td>
            <td data-label="Status"><span class="badge <?= $status ?>"><?= ucfirst($status) ?></span></td>
          <?php else: ?>
            <td data-label="Anchor"><?= h($row['anchor_text']) ?></td>
            <td data-label="URL" class="url-cell" title="<?= h($row['url']) ?>"><?= h($row['url']) ?></td>
            <td data-label="Sender"><?= h($row['sender_name']) ?></td>
            <td data-label="Rel"><span class="badge <?= $row['rel_type'] ?>"><?= h($row['rel_type']) ?></span></td>
            <td data-label="Starts"><?= h(date('d M Y', strtotime($row['starts_at']))) ?></td>
            <td data-label="Expires">
              <?= ($row['duration_type'] ?? 'limited') === 'permanent'
                    ? '<span class="badge permanent">Permanent</span>'
                    : h(date('d M Y', strtotime($row['expires_at']))) . '<br><span class="hint" style="margin:0;">' . h(ll_days_label($row)) . '</span>' ?>
            </td>
            <td data-label="Status"><span class="badge <?= $status ?>"><?= ucfirst($status) ?></span></td>
          <?php endif; ?>
          <td data-label="Paid">
            <form method="post" action="?tab=<?= $tabUrlValue ?><?= $qParam ?>&page=<?= $page ?>" style="display:inline" title="<?= $isPaid ? 'Click to mark unpaid' : 'Click to mark paid' ?>">
              <input type="hidden" name="csrf" value="<?= h($_SESSION['csrf']) ?>">
              <input type="hidden" name="link_type" value="<?= $tab ?>">
              <input type="hidden" name="action" value="toggle_paid">
              <input type="hidden" name="id" value="<?= $row['id'] ?>">
              <?php if ($isPaid): ?>
                <button type="submit" class="paid-badge">✓✓ Paid</button>
              <?php else: ?>
                <button type="submit" class="mark-paid-btn">Mark as Paid</button>
              <?php endif; ?>
            </form>
          </td>
          <td class="row-actions" data-label="Actions">
            <a href="?tab=<?= $tabUrlValue ?><?= $qParam ?>&page=<?= $page ?>&edit=<?= $row['id'] ?>">Edit</a>
            <form method="post" action="?tab=<?= $tabUrlValue ?><?= $qParam ?>&page=<?= $page ?>" style="display:inline" onsubmit="return confirm('Delete this entry?');">
              <input type="hidden" name="csrf" value="<?= h($_SESSION['csrf']) ?>">
              <input type="hidden" name="link_type" value="<?= $tab ?>">
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="id" value="<?= $row['id'] ?>">
              <button type="submit" class="del">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <?php if ($totalPages > 1): ?>
    <div class="pagination-row">
      <a class="page-btn <?= $page <= 1 ? 'disabled' : '' ?>" href="?tab=<?= $tabUrlValue ?><?= $qParam ?>&page=<?= max(1, $page - 1) ?>">← Prev</a>
      <?php
        $window = ll_page_window($page, $totalPages);
        $last = 0;
        foreach ($window as $p):
          if ($p - $last > 1): ?>
            <span class="page-ellipsis">…</span>
          <?php endif; ?>
          <a class="page-btn <?= $p === $page ? 'active' : '' ?>" href="?tab=<?= $tabUrlValue ?><?= $qParam ?>&page=<?= $p ?>"><?= $p ?></a>
          <?php $last = $p;
        endforeach;
      ?>
      <a class="page-btn <?= $page >= $totalPages ? 'disabled' : '' ?>" href="?tab=<?= $tabUrlValue ?><?= $qParam ?>&page=<?= min($totalPages, $page + 1) ?>">Next →</a>
    </div>
  <?php endif; ?>

</div>

</div>

<script>
  document.getElementById('themeToggleBtn').addEventListener('click', function () {
    var html = document.documentElement;
    var next = html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
    html.setAttribute('data-theme', next);
    localStorage.setItem('to-admin-theme', next);
  });
</script>
</body>
</html>