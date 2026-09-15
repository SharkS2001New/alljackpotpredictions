/**
 * Show More Matches — fetch next start_index/end_index window from /api/{page} and append.
 */
(function () {
  'use strict';

  function setBusy(btn, busy) {
    btn.disabled = !!busy;
    btn.setAttribute('aria-busy', busy ? 'true' : 'false');
    var label = btn.querySelector('.ajp-load-more-label');
    var wait = btn.querySelector('.ajp-load-more-busy');
    if (label) label.hidden = !!busy;
    if (wait) wait.hidden = !busy;
  }

  function bindNewCards(root) {
    (root || document).querySelectorAll('.pcard').forEach(function (card) {
      if (card.dataset.ajpAccBound === '1') return;
      var row = card.querySelector('.pc-row');
      if (!row) return;
      card.dataset.ajpAccBound = '1';
      row.addEventListener('click', function (e) {
        if (e.target.closest('a') || e.target.closest('button')) {
          /* allow toggle button */
        }
        if (e.target.closest('a')) return;
        var isOpen = card.classList.contains('is-open');
        document.querySelectorAll('.pcard.is-open').forEach(function (c) {
          if (c !== card) c.classList.remove('is-open');
        });
        if (!isOpen) card.classList.add('is-open');
        else card.classList.remove('is-open');
      });
    });
  }

  async function onClick(btn) {
    if (btn.disabled || btn.getAttribute('aria-busy') === 'true') return;

    var api = btn.getAttribute('data-api') || '';
    var start = parseInt(btn.getAttribute('data-start') || '0', 10);
    var chunk = parseInt(btn.getAttribute('data-chunk') || '10', 10);
    var layout = btn.getAttribute('data-layout') || 'pcard';
    var knownTotal = parseInt(btn.getAttribute('data-known-total') || '0', 10);
    if (!api || Number.isNaN(start) || chunk < 1) return;

    var end = start + chunk - 1;
    if (knownTotal > 0) {
      end = Math.min(end, knownTotal - 1);
    }
    if (end < start) {
      var earlyWrap = btn.closest('.ajp-load-more-wrap');
      if (earlyWrap) earlyWrap.remove();
      return;
    }

    var block = btn.closest('[data-ajp-block]') || btn.parentElement;
    var url = api
      + (api.indexOf('?') >= 0 ? '&' : '?')
      + 'start_index=' + encodeURIComponent(String(start))
      + '&end_index=' + encodeURIComponent(String(end))
      + '&format=html'
      + '&layout=' + encodeURIComponent(layout);

    var marketLabel = btn.getAttribute('data-market-label');
    if (marketLabel) url += '&market_label=' + encodeURIComponent(marketLabel);
    var league = btn.getAttribute('data-league');
    if (league) url += '&league=' + encodeURIComponent(league);
    var type = btn.getAttribute('data-type');
    if (type) url += '&type=' + encodeURIComponent(type);
    if (btn.getAttribute('data-exclude-ft') === '1') url += '&exclude_ft=1';
    var marketLine = btn.getAttribute('data-market-line');
    if (marketLine) url += '&market_line=' + encodeURIComponent(marketLine);
    if (btn.getAttribute('data-under') === '1') url += '&under=1';

    setBusy(btn, true);
    try {
      var res = await fetch(url, { headers: { Accept: 'application/json' } });
      if (!res.ok) throw new Error('HTTP ' + res.status);
      var data = await res.json();
      var count = data && typeof data.count === 'number' ? data.count : 0;
      var hasMore = !!(data && data.has_more);

      if (layout === 'pt') {
        var rowsHtml = (data && data.html_rows) || '';
        var cardsHtml = (data && data.html_cards) || '';
        var rowsHost = block && block.querySelector('[data-ajp-matches-rows]');
        var cardsHost = block && block.querySelector('[data-ajp-matches-cards]');
        if (rowsHost && rowsHtml) rowsHost.insertAdjacentHTML('beforeend', rowsHtml);
        if (cardsHost && cardsHtml) cardsHost.insertAdjacentHTML('beforeend', cardsHtml);
        if (!rowsHtml && !cardsHtml) count = 0;
      } else {
        var html = (data && data.html) || '';
        var grid = (block && block.querySelector('[data-ajp-matches]')) || document.querySelector('[data-ajp-matches]');
        if (grid && html) {
          grid.insertAdjacentHTML('beforeend', html);
          bindNewCards(grid);
        }
        if (!html) count = 0;
      }

      if (count < 1 || !hasMore) {
        var wrap = btn.closest('.ajp-load-more-wrap');
        if (wrap) wrap.remove();
        return;
      }

      var next = data.next_start != null ? parseInt(data.next_start, 10) : end + 1;
      btn.setAttribute('data-start', String(next));
      if (typeof data.max === 'number' && data.max > 0) {
        btn.setAttribute('data-known-total', String(data.max));
      }
    } catch (e) {
      btn.setAttribute('data-error', '1');
    } finally {
      setBusy(btn, false);
    }
  }

  function bind(root) {
    (root || document).querySelectorAll('.ajp-load-more').forEach(function (btn) {
      if (btn.dataset.ajpBound === '1') return;
      btn.dataset.ajpBound = '1';
      btn.addEventListener('click', function () {
        onClick(btn);
      });
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function () {
      bind(document);
    });
  } else {
    bind(document);
  }

  window.ajpBindLoadMore = bind;
})();
