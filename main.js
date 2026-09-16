/* AllJackpotPredictions v6 — main.js
   Includes:
   - Dark / light theme toggle
   - Date pill
   - Back to top
   - Prediction card accordion
   - Newsletter form
   - Tip card filters (league, time, confidence, result)
   - Tip card pagination (.pcard based)
   - Matches table pagination + league filter (predictions-today page)
   - Progress bar animation
   - Mobile drawer overlay close
   - Market strip active state
*/
(function () {
  'use strict';

  /* ══════════════════════════════════════════════
     DARK MODE
  ══════════════════════════════════════════════ */
  var html     = document.documentElement;
  var themeBtn = document.getElementById('themeBtn');

  function setTheme(t) {
    html.setAttribute('data-theme', t);
    localStorage.setItem('to-theme', t);
  }

  var saved = localStorage.getItem('to-theme');
  if (saved) {
    setTheme(saved);
  } else {
    setTheme(window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
  }

  if (themeBtn) {
    themeBtn.addEventListener('click', function () {
      setTheme(html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark');
    });
  }

  /* ══════════════════════════════════════════════
     DATE PILL
  ══════════════════════════════════════════════ */
  var dp = document.getElementById('datePill');
  if (dp) {
    var d    = new Date();
    var days = ['SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT'];
    var mons = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN',
                'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];
    dp.textContent = days[d.getDay()] + ' ' + d.getDate() + ' ' + mons[d.getMonth()];
  }

  /* ══════════════════════════════════════════════
     BACK TO TOP
  ══════════════════════════════════════════════ */
  var btt = document.getElementById('btt');
  if (btt) {
    window.addEventListener('scroll', function () {
      btt.classList.toggle('vis', window.scrollY > 500);
    }, { passive: true });
    btt.addEventListener('click', function () {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  /* ══════════════════════════════════════════════
     PREDICTION CARD ACCORDION
  ══════════════════════════════════════════════ */
  var allCards = document.querySelectorAll('.pcard');
  allCards.forEach(function (card) {
    var row = card.querySelector('.pc-row');
    if (!row) return;
    row.addEventListener('click', function (e) {
      if (e.target.closest('a')) return;
      var isOpen = card.classList.contains('is-open');
      allCards.forEach(function (c) { c.classList.remove('is-open'); });
      if (!isOpen) card.classList.add('is-open');
    });
  });

  /* ══════════════════════════════════════════════
     NEWSLETTER
  ══════════════════════════════════════════════ */
  var nlBtn   = document.getElementById('nlBtn');
  var nlInput = document.getElementById('nlInput');
  if (nlBtn && nlInput) {
    nlBtn.addEventListener('click', function () {
      var v = (nlInput.value || '').trim();
      if (!v || !v.includes('@')) {
        nlInput.style.borderColor = 'var(--pink)';
        nlInput.focus();
        return;
      }
      nlInput.style.borderColor = 'var(--green)';
      nlBtn.textContent  = '✓ Subscribed!';
      nlInput.disabled   = true;
      nlBtn.disabled     = true;
    });
    nlInput.addEventListener('input', function () {
      nlInput.style.borderColor = '';
    });
  }

  /* ══════════════════════════════════════════════
     TIP CARD FILTERS + PAGINATION
     (works on all market pages using .pcard elements)
  ══════════════════════════════════════════════ */
  var CARDS_PER_PAGE = 10;
  var currentCardPage = 1;

  function allPcards() {
    return Array.from(document.querySelectorAll('.pcard'));
  }

  function filteredPcards() {
    return allPcards().filter(function (c) {
      return c.getAttribute('data-pg-hidden') !== '1';
    });
  }

  function showCardPage(page) {
    var pool  = filteredPcards();
    var total = pool.length;
    var pages = Math.max(1, Math.ceil(total / CARDS_PER_PAGE));

    if (page < 1)     page = 1;
    if (page > pages) page = pages;
    currentCardPage = page;

    var start = (page - 1) * CARDS_PER_PAGE;
    var end   = start + CARDS_PER_PAGE;

    allPcards().forEach(function (c) {
      if (c.getAttribute('data-pg-hidden') === '1') {
        c.style.display = 'none';
      } else {
        var idx = pool.indexOf(c);
        c.style.display = (idx >= start && idx < end) ? '' : 'none';
      }
    });

    var info = document.getElementById('pageInfo');
    if (info) {
      if (total === 0) {
        info.textContent = 'No tips match your filters';
      } else {
        var showing = Math.min(end, total);
        info.textContent = 'Showing ' + (start + 1) + '\u2013' + showing + ' of ' + total + ' tips';
      }
    }

    var wrap    = document.getElementById('pageButtons');
    var prevBtn = document.getElementById('prevPage');
    var nextBtn = document.getElementById('nextPage');

    if (wrap && prevBtn && nextBtn) {
      Array.from(wrap.querySelectorAll('.pg-num')).forEach(function (b) { b.remove(); });

      for (var p = 1; p <= pages; p++) {
        var btn = document.createElement('button');
        btn.className = 'pg-btn pg-num' + (p === currentCardPage ? ' pg-active' : '');
        btn.setAttribute('data-page', String(p));
        btn.textContent = String(p);
        btn.addEventListener('click', onCardPageClick);
        wrap.insertBefore(btn, nextBtn);
      }

      prevBtn.disabled = (currentCardPage === 1);
      nextBtn.disabled = (currentCardPage === pages);
    }

    var bar = document.getElementById('paginationWrap');
    if (bar) {
      if (pages <= 1) {
        bar.classList.add('pg-hidden');
      } else {
        bar.classList.remove('pg-hidden');
      }
    }
  }

  function onCardPageClick(e) {
    var p = parseInt(e.currentTarget.getAttribute('data-page'), 10);
    showCardPage(p);
    scrollToTips();
  }

  function scrollToTips() {
    var anchor = document.getElementById('tips');
    if (anchor) anchor.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }

  var prevCardBtn = document.getElementById('prevPage');
  var nextCardBtn = document.getElementById('nextPage');
  if (prevCardBtn) {
    prevCardBtn.addEventListener('click', function () {
      showCardPage(currentCardPage - 1);
      scrollToTips();
    });
  }
  if (nextCardBtn) {
    nextCardBtn.addEventListener('click', function () {
      showCardPage(currentCardPage + 1);
      scrollToTips();
    });
  }

  /* ── filter logic (sets data-pg-hidden, then re-paginates) ── */
  function applyCardFilter() {
    var fL = document.getElementById('fsel-league');
    var fT = document.getElementById('fsel-time');
    var fR = document.getElementById('fsel-result');

    var league = fL ? fL.value : '';
    var time   = fT ? fT.value : '';
    var result = fR ? fR.value : '';

    var activePills = Array.from(document.querySelectorAll('.conf-pill.active'));
    var confs = activePills.length
      ? activePills.map(function (p) {
          return p.classList.contains('hi') ? 'high'
               : p.classList.contains('md') ? 'medium'
               : 'low';
        })
      : null;

    allPcards().forEach(function (c) {
      var ok =
        (!league || c.dataset.league === league) &&
        (!result || c.dataset.result === result) &&
        (time === 'early'   ? +c.dataset.hour < 15   :
         time === 'evening' ? +c.dataset.hour >= 19   : true) &&
        (!confs || confs.includes(c.dataset.conf));

      if (ok) {
        c.removeAttribute('data-pg-hidden');
        c.style.display = '';
      } else {
        c.setAttribute('data-pg-hidden', '1');
        c.style.display = 'none';
      }
    });

    if (document.querySelector('.ajp-load-more')) {
      var info = document.getElementById('pageInfo');
      var visible = filteredPcards().length;
      if (info) {
        info.textContent = visible === 0
          ? 'No tips match your filters'
          : 'Showing ' + visible + ' tip' + (visible === 1 ? '' : 's');
      }
      return;
    }

    showCardPage(1);
  }

  var fL = document.getElementById('fsel-league');
  var fT = document.getElementById('fsel-time');
  var fR = document.getElementById('fsel-result');
  if (fL) fL.addEventListener('change', applyCardFilter);
  if (fT) fT.addEventListener('change', applyCardFilter);
  if (fR) fR.addEventListener('change', applyCardFilter);

  document.querySelectorAll('.conf-pill').forEach(function (pill) {
    pill.addEventListener('click', function () {
      pill.classList.toggle('active');
      setTimeout(applyCardFilter, 0);
    });
  });

  /* init card pagination on pages that have .pcard elements —
     skip when Show More API loading is active (all loaded cards stay visible) */
  if (allPcards().length > 0 && !document.querySelector('.ajp-load-more')) {
    showCardPage(1);
  } else if (document.querySelector('.ajp-load-more')) {
    var bar = document.getElementById('paginationWrap');
    if (bar) bar.classList.add('pg-hidden');
  }

  /* ══════════════════════════════════════════════
     MATCHES TABLE PAGINATION + LEAGUE FILTER
     (predictions-today page only — guards on #pt-matches-table)
  ══════════════════════════════════════════════ */
  (function () {
    var ROWS_PER_PAGE = 10;
    var currentPage   = 1;
    var activeLeague  = '';

    var table      = document.getElementById('pt-matches-table');
    var leagueSel  = document.getElementById('pt-league-filter');
    var pgInfo     = document.getElementById('pt-pg-info');
    var pgControls = document.getElementById('pt-pg-controls');
    var countEl    = document.getElementById('pt-matches-count');
    var pgWrap     = document.getElementById('pt-pagination-wrap');

    if (!table) return; /* not on predictions-today — bail silently */

    var tbody = table.querySelector('tbody');
    if (!tbody) return;

    function allRows() {
      return Array.from(tbody.querySelectorAll('tr'));
    }

    function matchingRows() {
      return allRows().filter(function (r) {
        return !activeLeague || r.getAttribute('data-league') === activeLeague;
      });
    }

    function render() {
      var rows  = matchingRows();
      var total = rows.length;
      var pages = Math.max(1, Math.ceil(total / ROWS_PER_PAGE));

      if (currentPage < 1)     currentPage = 1;
      if (currentPage > pages) currentPage = pages;

      var start = (currentPage - 1) * ROWS_PER_PAGE;
      var end   = start + ROWS_PER_PAGE;

      /* show / hide rows */
      allRows().forEach(function (r) { r.style.display = 'none'; });
      rows.forEach(function (r, i) {
        r.style.display = (i >= start && i < end) ? '' : 'none';
      });

      /* update info text */
      var showing = Math.min(end, total);
      var infoTxt = total === 0
        ? 'No matches for this league'
        : 'Showing ' + (start + 1) + '\u2013' + showing + ' of ' + total + ' matches';
      if (pgInfo)  pgInfo.textContent  = infoTxt;
      if (countEl) countEl.textContent = total + ' matches';

      /* show / hide pagination wrap */
      if (pgWrap) pgWrap.style.display = pages <= 1 ? 'none' : '';

      /* rebuild page buttons */
      if (!pgControls) return;
      pgControls.innerHTML = '';

      /* prev */
      var prev = document.createElement('button');
      prev.className   = 'pg-btn pg-prev';
      prev.textContent = '\u2190 Prev';
      prev.disabled    = (currentPage === 1);
      prev.addEventListener('click', function () { currentPage--; render(); });
      pgControls.appendChild(prev);

      /* numbered — max 5 window */
      var winSize = 5;
      var winHalf = Math.floor(winSize / 2);
      var wStart  = Math.max(1, currentPage - winHalf);
      var wEnd    = Math.min(pages, wStart + winSize - 1);
      if (wEnd - wStart < winSize - 1) wStart = Math.max(1, wEnd - winSize + 1);

      for (var p = wStart; p <= wEnd; p++) {
        (function (pg) {
          var btn = document.createElement('button');
          btn.className   = 'pg-btn pg-num' + (pg === currentPage ? ' pg-active' : '');
          btn.textContent = String(pg);
          btn.setAttribute('data-page', String(pg));
          btn.addEventListener('click', function () {
            currentPage = pg;
            render();
            var wrap = document.querySelector('.pt-table-wrap');
            if (wrap) wrap.scrollIntoView({ behavior: 'smooth', block: 'start' });
          });
          pgControls.appendChild(btn);
        })(p);
      }

      /* next */
      var next = document.createElement('button');
      next.className   = 'pg-btn pg-next';
      next.textContent = 'Next \u2192';
      next.disabled    = (currentPage === pages);
      next.addEventListener('click', function () { currentPage++; render(); });
      pgControls.appendChild(next);
    }

    /* league filter */
    if (leagueSel) {
      leagueSel.addEventListener('change', function () {
        activeLeague = this.value;
        currentPage  = 1;
        render();
      });
    }

    render();
  }());

/* ══════════════════════════════════════════════
   PAGE NAVIGATION PROGRESS (Bao-style thin top bar)
   Shows while navigating same-origin links / form submits.
══════════════════════════════════════════════ */
(function () {
  var loader = document.getElementById('ajp-page-loader');

  function showLoader() {
    if (!loader) return;
    loader.classList.add('is-active');
    loader.setAttribute('aria-busy', 'true');
    loader.setAttribute('aria-hidden', 'false');
  }

  function hideLoader() {
    if (!loader) return;
    loader.classList.remove('is-active');
    loader.setAttribute('aria-busy', 'false');
    loader.setAttribute('aria-hidden', 'true');
  }

  function isModifiedClick(e) {
    return e.metaKey || e.ctrlKey || e.shiftKey || e.altKey || e.button === 1;
  }

  function shouldShowForLink(a) {
    if (!a) return false;
    if (a.target && a.target !== '' && a.target !== '_self') return false;
    if (a.hasAttribute('download')) return false;
    var href = a.getAttribute('href');
    if (!href || href.charAt(0) === '#') return false;
    if (/^(mailto:|tel:|javascript:)/i.test(href)) return false;
    try {
      var url = new URL(href, window.location.href);
      if (url.origin !== window.location.origin) return false;
      if (url.pathname === window.location.pathname && url.search === window.location.search) {
        return false;
      }
    } catch (err) {
      return false;
    }
    return true;
  }

  document.addEventListener('click', function (e) {
    if (isModifiedClick(e)) return;
    var a = e.target.closest && e.target.closest('a[href]');
    if (!a || !shouldShowForLink(a)) return;
    showLoader();
  }, true);

  document.addEventListener('submit', function (e) {
    var form = e.target;
    if (!form || form.tagName !== 'FORM') return;
    if (form.target && form.target !== '' && form.target !== '_self') return;
    showLoader();
  }, true);

  window.addEventListener('pageshow', hideLoader);
  window.addEventListener('load', hideLoader);
  setTimeout(hideLoader, 12000);
}());

  /* ══════════════════════════════════════════════
     PROGRESS BAR ANIMATION on scroll
  ══════════════════════════════════════════════ */
  var bars = document.querySelectorAll('.pmb-fill');
  if (bars.length && 'IntersectionObserver' in window) {
    var barObserver = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.style.width = entry.target.getAttribute('data-width');
          barObserver.unobserve(entry.target);
        }
      });
    }, { threshold: 0.3 });

    bars.forEach(function (bar) {
      var w = bar.style.width;
      bar.setAttribute('data-width', w);
      bar.style.width = '0';
      barObserver.observe(bar);
    });
  }

  /* ══════════════════════════════════════════════
     MOBILE DRAWER — close on overlay click
  ══════════════════════════════════════════════ */
  var mobCk      = document.getElementById('mob-ck');
  var mobOverlay = document.querySelector('.mob-overlay');
  if (mobOverlay && mobCk) {
    mobOverlay.addEventListener('click', function () {
      mobCk.checked = false;
    });
  }

  /* ══════════════════════════════════════════════
     MARKET STRIP ACTIVE STATE
  ══════════════════════════════════════════════ */
  var msTabs = document.querySelectorAll('.ms-tab');
  msTabs.forEach(function (tab) {
    tab.addEventListener('click', function () {
      msTabs.forEach(function (t) { t.classList.remove('active'); });
      tab.classList.add('active');
    });
  });

  /* ══════════════════════════════════════════════
     WEEKEND PAGE — DAY TABS + DUAL TABLE PAGINATION
     Guards on #wkndDayTabs — only runs on predictions-weekend
  ══════════════════════════════════════════════ */
  (function () {
    var tabWrap = document.getElementById('wkndDayTabs');
    if (!tabWrap) return; /* not on weekend page */

    var ROWS_PER_PAGE = 10;

    /* ── day tab switcher ── */
    var tabs   = Array.from(tabWrap.querySelectorAll('.wknd-tab'));
    var panels = {
      sat: document.getElementById('panel-sat'),
      sun: document.getElementById('panel-sun')
    };

    function showDay(day) {
      tabs.forEach(function (t) { t.classList.remove('active'); });
      tabWrap.querySelector('[data-day="' + day + '"]').classList.add('active');

      if (day === 'all') {
        if (panels.sat) panels.sat.classList.remove('wknd-hidden');
        if (panels.sun) panels.sun.classList.remove('wknd-hidden');
      } else {
        Object.keys(panels).forEach(function (k) {
          if (panels[k]) {
            if (k === day) {
              panels[k].classList.remove('wknd-hidden');
            } else {
              panels[k].classList.add('wknd-hidden');
            }
          }
        });
      }
    }

    tabs.forEach(function (tab) {
      tab.addEventListener('click', function () {
        showDay(tab.getAttribute('data-day'));
      });
    });

    /* ── generic table paginator factory ── */
    function makeTablePaginator(cfg) {
      /* cfg: { tableId, leagueSelId, pgInfoId, pgControlsId, countElId, pgWrapId } */
      var table     = document.getElementById(cfg.tableId);
      if (!table) return;
      var tbody     = table.querySelector('tbody');
      if (!tbody) return;

      var leagueSel = document.getElementById(cfg.leagueSelId);
      var pgInfo    = document.getElementById(cfg.pgInfoId);
      var pgCtrls   = document.getElementById(cfg.pgControlsId);
      var countEl   = document.getElementById(cfg.countElId);
      var pgWrap    = document.getElementById(cfg.pgWrapId);

      var currentPage  = 1;
      var activeLeague = '';

      function allRows()     { return Array.from(tbody.querySelectorAll('tr')); }
      function matchingRows() {
        return allRows().filter(function (r) {
          return !activeLeague || r.getAttribute('data-league') === activeLeague;
        });
      }

      function render() {
        var rows  = matchingRows();
        var total = rows.length;
        var pages = Math.max(1, Math.ceil(total / ROWS_PER_PAGE));

        if (currentPage < 1)     currentPage = 1;
        if (currentPage > pages) currentPage = pages;

        var start   = (currentPage - 1) * ROWS_PER_PAGE;
        var end     = start + ROWS_PER_PAGE;
        var showing = Math.min(end, total);

        allRows().forEach(function (r) { r.style.display = 'none'; });
        rows.forEach(function (r, i) {
          r.style.display = (i >= start && i < end) ? '' : 'none';
        });

        var infoTxt = total === 0
          ? 'No matches for this league'
          : 'Showing ' + (start + 1) + '\u2013' + showing + ' of ' + total + ' matches';
        if (pgInfo)  pgInfo.textContent  = infoTxt;
        if (countEl) countEl.textContent = total + ' matches';
        if (pgWrap)  pgWrap.style.display = pages <= 1 ? 'none' : '';

        if (!pgCtrls) return;
        pgCtrls.innerHTML = '';

        /* prev */
        var prev = document.createElement('button');
        prev.className   = 'pg-btn pg-prev';
        prev.textContent = '\u2190 Prev';
        prev.disabled    = (currentPage === 1);
        prev.addEventListener('click', function () { currentPage--; render(); });
        pgCtrls.appendChild(prev);

        /* numbered — max 5 window */
        var winSize = 5;
        var winHalf = Math.floor(winSize / 2);
        var wStart  = Math.max(1, currentPage - winHalf);
        var wEnd    = Math.min(pages, wStart + winSize - 1);
        if (wEnd - wStart < winSize - 1) wStart = Math.max(1, wEnd - winSize + 1);

        for (var p = wStart; p <= wEnd; p++) {
          (function (pg) {
            var btn = document.createElement('button');
            btn.className   = 'pg-btn pg-num' + (pg === currentPage ? ' pg-active' : '');
            btn.textContent = String(pg);
            btn.setAttribute('data-page', String(pg));
            btn.addEventListener('click', function () {
              currentPage = pg;
              render();
              var wrap = table.closest('.pt-table-wrap');
              if (wrap) wrap.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
            pgCtrls.appendChild(btn);
          })(p);
        }

        /* next */
        var next = document.createElement('button');
        next.className   = 'pg-btn pg-next';
        next.textContent = 'Next \u2192';
        next.disabled    = (currentPage === pages);
        next.addEventListener('click', function () { currentPage++; render(); });
        pgCtrls.appendChild(next);
      }

      if (leagueSel) {
        leagueSel.addEventListener('change', function () {
          activeLeague = this.value;
          currentPage  = 1;
          render();
        });
      }

      render();
    }

    /* init Saturday table */
    makeTablePaginator({
      tableId:      'pt-sat-table',
      leagueSelId:  'pt-sat-league-filter',
      pgInfoId:     'pt-sat-pg-info',
      pgControlsId: 'pt-sat-pg-controls',
      countElId:    'pt-sat-count',
      pgWrapId:     'pt-sat-pagination'
    });

    /* init Sunday table */
    makeTablePaginator({
      tableId:      'pt-sun-table',
      leagueSelId:  'pt-sun-league-filter',
      pgInfoId:     'pt-sun-pg-info',
      pgControlsId: 'pt-sun-pg-controls',
      countElId:    'pt-sun-count',
      pgWrapId:     'pt-sun-pagination'
    });

  }());

}());
/* ══════════════════════════════════════════════
     TRACK RECORD PAGE — results filter + pagination
     Guards on #tr-results-table — only runs on track-record
  ══════════════════════════════════════════════ */
  (function () {
    var ROWS_PER_PAGE = 20;
    var currentPage  = 1;

    var table  = document.getElementById('tr-results-table');
    if (!table) return; /* not on track-record — bail silently */

    var tbody  = table.querySelector('tbody');
    var pgWrap = document.getElementById('tr-pagination-wrap');
    var pgInfo = document.getElementById('tr-pg-info');
    var pgCtrl = document.getElementById('tr-pg-controls');
    var countEl= document.getElementById('tr-count');

    function allRows() { return Array.from(tbody.querySelectorAll('tr')); }

    function matchingRows() {
      var rF = document.getElementById('tr-fsel-result');
      var mF = document.getElementById('tr-fsel-market');
      var lF = document.getElementById('tr-fsel-league');
      var r  = rF ? rF.value : '';
      var m  = mF ? mF.value : '';
      var l  = lF ? lF.value : '';
      return allRows().filter(function (row) {
        return (!r || row.getAttribute('data-result')  === r) &&
               (!m || row.getAttribute('data-market')  === m) &&
               (!l || row.getAttribute('data-league')  === l);
      });
    }

    function render() {
      var rows  = matchingRows();
      var total = rows.length;
      var pages = Math.max(1, Math.ceil(total / ROWS_PER_PAGE));

      if (currentPage < 1)     currentPage = 1;
      if (currentPage > pages) currentPage = pages;

      var start   = (currentPage - 1) * ROWS_PER_PAGE;
      var end     = start + ROWS_PER_PAGE;
      var showing = Math.min(end, total);

      allRows().forEach(function (r) { r.style.display = 'none'; });
      rows.forEach(function (r, i) {
        r.style.display = (i >= start && i < end) ? '' : 'none';
      });

      if (pgInfo) {
        pgInfo.textContent = total === 0
          ? 'No results match your filters'
          : 'Showing ' + (start + 1) + '\u2013' + showing + ' of ' + total + ' results';
      }
      if (countEl) countEl.textContent = total + ' results';
      if (pgWrap)  pgWrap.style.display = pages <= 1 ? 'none' : '';

      if (!pgCtrl) return;
      pgCtrl.innerHTML = '';

      /* prev */
      var prev = document.createElement('button');
      prev.className   = 'pg-btn pg-prev';
      prev.textContent = '\u2190 Prev';
      prev.disabled    = (currentPage === 1);
      prev.addEventListener('click', function () { currentPage--; render(); scrollToTable(); });
      pgCtrl.appendChild(prev);

      /* numbered pages — max 5 window */
      var winSize = 5;
      var winHalf = Math.floor(winSize / 2);
      var wStart  = Math.max(1, currentPage - winHalf);
      var wEnd    = Math.min(pages, wStart + winSize - 1);
      if (wEnd - wStart < winSize - 1) wStart = Math.max(1, wEnd - winSize + 1);

      for (var p = wStart; p <= wEnd; p++) {
        (function (pg) {
          var btn = document.createElement('button');
          btn.className   = 'pg-btn pg-num' + (pg === currentPage ? ' pg-active' : '');
          btn.textContent = String(pg);
          btn.addEventListener('click', function () { currentPage = pg; render(); scrollToTable(); });
          pgCtrl.appendChild(btn);
        })(p);
      }

      /* next */
      var next = document.createElement('button');
      next.className   = 'pg-btn pg-next';
      next.textContent = 'Next \u2192';
      next.disabled    = (currentPage === pages);
      next.addEventListener('click', function () { currentPage++; render(); scrollToTable(); });
      pgCtrl.appendChild(next);
    }

    function scrollToTable() {
      var wrap = document.getElementById('tr-results-table-wrap');
      if (wrap) wrap.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    /* filter selects */
    ['tr-fsel-result', 'tr-fsel-market', 'tr-fsel-league'].forEach(function (id) {
      var el = document.getElementById(id);
      if (el) el.addEventListener('change', function () { currentPage = 1; render(); });
    });

    render();
  }());