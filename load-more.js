/**
 * Show More — fetch next start_index/end_index window from /api/{page} and append.
 * Updates the "Showing X of Y" label after each successful batch.
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

  function setError(btn, msg) {
    btn.setAttribute('data-error', '1');
    var label = btn.querySelector('.ajp-load-more-label');
    if (label) {
      label.hidden = false;
      label.textContent = msg || 'Couldn’t load — tap to retry';
    }
    var wait = btn.querySelector('.ajp-load-more-busy');
    if (wait) wait.hidden = true;
  }

  function clearError(btn) {
    if (btn.getAttribute('data-error') !== '1') return;
    btn.removeAttribute('data-error');
    var label = btn.querySelector('.ajp-load-more-label');
    if (label) label.textContent = 'Show More Matches';
  }

  function findBlock(btn) {
    return btn.closest('[data-ajp-block]');
  }

  function findGrid(btn) {
    var block = findBlock(btn);
    if (block) {
      if (block.hasAttribute('data-ajp-matches')) return block;
      var inner = block.querySelector('[data-ajp-matches]');
      if (inner) return inner;
    }
    var main = btn.closest('main') || document;
    return main.querySelector('[data-ajp-matches]') || document.querySelector('[data-ajp-matches]');
  }

  function findPtHosts(btn) {
    var block = findBlock(btn);
    var root = block || btn.closest('main') || document;
    return {
      rows: root.querySelector('[data-ajp-matches-rows]'),
      cards: root.querySelector('[data-ajp-matches-cards]'),
    };
  }

  function findProgress(btn) {
    var root = findBlock(btn) || btn.closest('main') || document;
    return (
      root.querySelector('[data-ajp-progress]') ||
      (btn.previousElementSibling && btn.previousElementSibling.querySelector
        ? btn.previousElementSibling.querySelector('.pg-info')
        : null) ||
      (btn.closest('.ajp-load-more-wrap') &&
        btn.closest('.ajp-load-more-wrap').previousElementSibling &&
        btn.closest('.ajp-load-more-wrap').previousElementSibling.querySelector('.pg-info')) ||
      root.querySelector('.pg-info')
    );
  }

  function updateProgress(btn, shown, total, noun) {
    var info = findProgress(btn);
    if (!info) return;
    var word = noun || info.getAttribute('data-noun') || 'tips';
    var max = total > 0 ? total : parseInt(info.getAttribute('data-total') || '0', 10);
    if (max > 0) {
      info.setAttribute('data-total', String(max));
      info.textContent = 'Showing ' + shown + ' of ' + max + ' ' + word;
    } else {
      info.textContent = 'Showing ' + shown + ' ' + word;
    }
  }

  function bindNewCards(root) {
    (root || document).querySelectorAll('.pcard').forEach(function (card) {
      if (card.dataset.ajpAccBound === '1') return;
      var row = card.querySelector('.pc-row');
      if (!row) return;
      card.dataset.ajpAccBound = '1';
      row.addEventListener('click', function (e) {
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

    var url =
      api +
      (api.indexOf('?') >= 0 ? '&' : '?') +
      'start_index=' +
      encodeURIComponent(String(start)) +
      '&end_index=' +
      encodeURIComponent(String(end)) +
      '&format=html' +
      '&layout=' +
      encodeURIComponent(layout);

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

    clearError(btn);
    setBusy(btn, true);
    try {
      var res = await fetch(url, {
        credentials: 'same-origin',
        headers: { Accept: 'application/json' },
      });
      if (!res.ok) throw new Error('HTTP ' + res.status);
      var data = await res.json();
      if (!data || data.ok === false) {
        throw new Error((data && data.error) || 'Bad response');
      }

      var count = typeof data.count === 'number' ? data.count : 0;
      var hasMore = !!data.has_more;
      var total =
        typeof data.max === 'number' && data.max > 0
          ? data.max
          : knownTotal > 0
            ? knownTotal
            : 0;
      var shown = 0;
      var noun = 'tips';

      if (layout === 'pt') {
        noun = 'matches';
        var hosts = findPtHosts(btn);
        var rowsHtml = data.html_rows || '';
        var cardsHtml = data.html_cards || '';
        if (hosts.rows && rowsHtml) hosts.rows.insertAdjacentHTML('beforeend', rowsHtml);
        if (hosts.cards && cardsHtml) hosts.cards.insertAdjacentHTML('beforeend', cardsHtml);
        if (!rowsHtml && !cardsHtml) count = 0;
        shown = hosts.rows
          ? hosts.rows.querySelectorAll('tr').length
          : hosts.cards
            ? hosts.cards.querySelectorAll('.pt-mcard').length
            : start + count;
      } else {
        var html = data.html || '';
        var grid = findGrid(btn);
        if (!grid) throw new Error('No matches container');
        if (html) {
          grid.insertAdjacentHTML('beforeend', html);
          bindNewCards(grid);
        } else {
          count = 0;
        }
        shown = grid.querySelectorAll('.pcard').length;
        noun = 'tips';
      }

      updateProgress(btn, shown, total, noun);

      if (count < 1 || !hasMore) {
        var wrap = btn.closest('.ajp-load-more-wrap');
        if (wrap) wrap.remove();
        return;
      }

      var next = data.next_start != null ? parseInt(data.next_start, 10) : end + 1;
      if (Number.isNaN(next)) next = end + 1;
      btn.setAttribute('data-start', String(next));
      if (total > 0) btn.setAttribute('data-known-total', String(total));
    } catch (e) {
      setError(btn, 'Couldn’t load — tap to retry');
      if (typeof console !== 'undefined' && console.warn) {
        console.warn('[ajp load-more]', e);
      }
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
