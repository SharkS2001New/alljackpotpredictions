<?php
if (!defined('LL_DATA_DIR')) {
  require_once __DIR__ . '/includes/link-functions.php';
}
?>
<!-- FOOTER — AllJackpotPredictions -->
<footer class="ajp-footer" role="contentinfo">
  <div class="ajp-ft-top">
    <div class="ajp-ft-wrap">
      <div class="ajp-ft-brand-block">
        <a class="ajp-ft-brand" href="/">
          <img src="/img/logo-mark.svg" alt="" width="40" height="40" />
          <span>
            <strong>AllJackpot</strong><em>Predictions</em>
          </span>
        </a>
        <p class="ajp-ft-lede">
          Jackpot sheets, banker shortlists and free multi-market tips — built for SportPesa, Betika and everyday accas. Entertainment only.
        </p>
        <div class="ajp-ft-pills">
          <span>Free tips</span>
          <span>Public track record</span>
          <span>18+ only</span>
        </div>
      </div>

      <nav class="ajp-ft-nav" aria-label="Footer">
        <div class="ajp-ft-group">
          <h4>Jackpot desk</h4>
          <a href="/jackpot-picks-today">All Jackpots</a>
          <a href="/must-win-teams-today">Banker Shortlist</a>
          <a href="/jackpots/sportpesa-mega-jackpot-predictions">SportPesa Mega</a>
          <a href="/jackpots/betika-midweek-jackpot-predictions">Betika Midweek</a>
          <a href="/accumulator-tips">Build Acca</a>
          <a href="/predictions-today">Today's Board</a>
          <a href="/predictions-weekend">Weekend Board</a>
        </div>
        <div class="ajp-ft-group">
          <h4>Markets</h4>
          <a href="/1x2-prediction">Match Result</a>
          <a href="/btts-tips">BTTS Legs</a>
          <a href="/over-2-5-goals">Goals O/U</a>
          <a href="/under-2-5-goals">Under 2.5</a>
          <a href="/double-chance-tips">Cover Legs</a>
          <a href="/correct-score-predictions">Correct Score</a>
        </div>
        <div class="ajp-ft-group">
          <h4>Desk</h4>
          <a href="/how-it-works">How it works</a>
          <a href="/track-record">Results Log</a>
          <a href="/blog">Blog</a>
          <a href="/about">About</a>
          <a href="/contact">Contact</a>
          <a href="/partners">Partners</a>
          <a href="/responsible-gambling">Responsible gambling</a>
        </div>
        <div class="ajp-ft-group">
          <h4>Legal</h4>
          <a href="/privacy-policy">Privacy</a>
          <a href="/cookie-policy">Cookies</a>
          <a href="/terms">Terms</a>
          <a href="/disclaimer">Disclaimer</a>
        </div>
      </nav>
    </div>
  </div>

  <div class="ajp-ft-rg">
    <div class="ajp-ft-wrap ajp-ft-rg-row">
      <span class="ajp-ft-age">18+</span>
      <p>
        Gamble responsibly.
        <a href="https://www.begambleaware.org" target="_blank" rel="noopener">BeGambleAware</a>
        ·
        <a href="https://www.gamcare.org.uk" target="_blank" rel="noopener">GamCare</a>
        ·
        <a href="https://www.gamstop.co.uk" target="_blank" rel="noopener">GamStop</a>
        ·
        <a href="/responsible-gambling">Our policy</a>
      </p>
    </div>
  </div>

<?php
    try {
        $ajpFooterRoot = __DIR__;
        $ajpAutoload = $ajpFooterRoot . '/vendor/autoload.php';
        if (is_file($ajpAutoload)) {
            require_once $ajpAutoload;
        }
        if (!class_exists(\App\Services\FooterSponsorsService::class, false)) {
            require_once $ajpFooterRoot . '/src/Services/FooterSponsorsService.php';
        }
        $ajpFooterSponsors = (new \App\Services\FooterSponsorsService())->visibleLinks();
    } catch (Throwable $e) {
        $ajpFooterSponsors = [];
    }
?>
<?php if (!empty($ajpFooterSponsors)): ?>
  <div class="ajp-ft-sponsors">
    <div class="ajp-ft-wrap">
      <p class="ajp-ft-sponsors-label">Our Partners &amp; Sponsors</p>
      <div class="ajp-ft-sponsor-links">
<?php foreach ($ajpFooterSponsors as $sponsor): ?>
<?php
  $sponsorUrl = trim((string) ($sponsor['url'] ?? ''));
  $sponsorUrl = preg_replace('#\./+#', '/', $sponsorUrl) ?? $sponsorUrl;
  $sponsorUrl = rtrim($sponsorUrl, " \t.");
  $sponsorLabel = trim((string) ($sponsor['label'] ?? ''));
  $sponsorLabel = preg_replace('#\./+#', '/', $sponsorLabel) ?? $sponsorLabel;
  $sponsorLabel = rtrim($sponsorLabel, " \t.");
  if ($sponsorLabel === '') {
      $sponsorLabel = $sponsorUrl;
  }
  if ($sponsorLabel === $sponsorUrl || preg_match('#^https?://#i', $sponsorLabel)) {
      $host = parse_url($sponsorUrl !== '' ? $sponsorUrl : $sponsorLabel, PHP_URL_HOST);
      if (is_string($host) && $host !== '') {
          $sponsorLabel = $host;
      }
  }
?>
        <a
          href="<?= htmlspecialchars($sponsorUrl !== '' ? $sponsorUrl : (string) ($sponsor['url'] ?? '#'), ENT_QUOTES, 'UTF-8') ?>"
          rel="<?= htmlspecialchars(implode(' ', $sponsor['rel'] ?? ['noopener', 'noreferrer']), ENT_QUOTES, 'UTF-8') ?>"
          target="_blank"
        ><?= htmlspecialchars($sponsorLabel, ENT_QUOTES, 'UTF-8') ?></a>
<?php endforeach; ?>
      </div>
    </div>
  </div>
<?php endif; ?>

  <div class="ajp-ft-bottom">
    <div class="ajp-ft-wrap">
      <p class="ajp-ft-legal">
        <strong>Entertainment only.</strong>
        Tips on AllJackpotPredictions are not financial or betting advice. You may lose money. Past results do not guarantee future performance. We are not a licensed bookmaker.
      </p>
      <div class="ajp-ft-meta">
        <span>© <?= date('Y') ?> AllJackpotPredictions</span>
      </div>
    </div>
  </div>
</footer>
