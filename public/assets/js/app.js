/* ============================================================
   app.js  -  OWNER: UI lead
   Global helpers and small behaviours wired up with data-* attributes.
   ============================================================ */
window.SC = window.SC || {};

SC.csrf = () => document.querySelector('meta[name="csrf-token"]')?.content || '';

/**
 * fetch wrapper for AJAX endpoints.
 * Expected server response: { success: bool, message: string, data: {} }
 */
SC.request = async function (url, { method = 'GET', body = null } = {}) {
  const options = {
    method,
    headers: { 'X-CSRF-Token': SC.csrf(), 'Accept': 'application/json' },
  };
  if (body instanceof FormData) {
    options.body = body;
  } else if (body) {
    options.headers['Content-Type'] = 'application/json';
    options.body = JSON.stringify(body);
  }
  const res = await fetch(url, options);
  let json;
  try { json = await res.json(); } catch { json = { success: false, message: 'Unexpected server response.' }; }
  if (!res.ok) json.success = false;
  return json;
};

/** Simple toast: SC.toast('Saved!', 'success') */
SC.toast = function (message, type = 'success') {
  let wrap = document.querySelector('.sc-toast-wrap');
  if (!wrap) {
    wrap = document.createElement('div');
    wrap.className = 'sc-toast-wrap';
    wrap.setAttribute('aria-live', 'polite');
    document.body.appendChild(wrap);
  }
  const el = document.createElement('div');
  el.className = `alert alert-${type === 'error' ? 'danger' : type} shadow mb-0`;
  el.textContent = message;
  wrap.appendChild(el);
  setTimeout(() => el.remove(), 3500);
};

/* Panels open on desktop start closed on phones */
document.querySelectorAll('[data-collapse-on-phone]').forEach((panel) => {
  if (window.innerWidth >= 992) return;
  panel.classList.remove('show');
  document.querySelector(`[data-bs-target="#${panel.id}"]`)?.setAttribute('aria-expanded', 'false');
});

/* Show / hide password */
document.addEventListener('click', (event) => {
  const button = event.target.closest('[data-toggle-password]');
  if (!button) return;
  const input = document.querySelector(button.dataset.togglePassword);
  if (!input) return;
  const reveal = input.type === 'password';
  input.type = reveal ? 'text' : 'password';
  button.setAttribute('aria-label', reveal ? 'Hide password' : 'Show password');
  button.setAttribute('aria-pressed', String(reveal));
  button.querySelector('i')?.classList.toggle('bi-eye', !reveal);
  button.querySelector('i')?.classList.toggle('bi-eye-slash', reveal);
});

/* Buttons that only explain themselves, e.g. the Office 365 button */
document.addEventListener('click', (event) => {
  const button = event.target.closest('[data-toast]');
  if (button) SC.toast(button.dataset.toast, 'info');
});

/* Ask before destructive form submits */
document.addEventListener('submit', (event) => {
  const message = event.target.dataset?.confirm;
  if (message && !window.confirm(message)) event.preventDefault();
});

/* Submit a filter as soon as it changes */
document.addEventListener('change', (event) => {
  if (event.target.matches('[data-autosubmit]')) {
    (event.target.form || document.getElementById(event.target.getAttribute('form')))?.requestSubmit();
  }
});

/* Interactive rating stars */
document.addEventListener('click', async (event) => {
  const star = event.target.closest('.sc-rating--interactive .sc-rating__star');
  if (!star) return;
  const group = star.closest('.sc-rating--interactive');
  const value = Number(star.dataset.value);
  const res = await SC.request(group.dataset.ratingUrl, { method: 'POST', body: { value } });
  SC.toast(res.message || 'Could not save your rating.', res.success ? 'success' : 'error');
  if (!res.success) return;
  group.querySelectorAll('.sc-rating__star').forEach((s) => {
    s.classList.toggle('is-active', Number(s.dataset.value) <= value);
  });
  const summary = document.querySelector('[data-rating-summary]');
  if (summary && res.data) {
    summary.textContent = `${Number(res.data.rating).toFixed(1)} (${res.data.rating_count})`;
  }
});

/* Fill the report dialog from the button that opened it */
document.getElementById('flagModal')?.addEventListener('show.bs.modal', (event) => {
  const trigger = event.relatedTarget;
  const modal = event.target;
  modal.querySelector('[data-flag-type-input]').value = trigger.dataset.flagType;
  modal.querySelector('[data-flag-id-input]').value = trigger.dataset.flagId;
});

/* Load more notes */
document.querySelector('[data-load-more]')?.addEventListener('click', async (event) => {
  event.preventDefault();
  const button = event.currentTarget;
  const list = document.querySelector(button.dataset.loadMore);
  const skeleton = document.querySelector('[data-load-skeleton]');
  const next = new URL(button.href);
  const page = Number(next.searchParams.get('page'));
  const last = Number(button.dataset.lastPage);
  next.searchParams.set('partial', '1');

  button.classList.add('disabled');
  skeleton?.removeAttribute('hidden');
  try {
    const res = await fetch(next, { headers: { Accept: 'text/html' } });
    if (!res.ok) throw new Error('load failed');
    list.insertAdjacentHTML('beforeend', await res.text());
    list.dispatchEvent(new CustomEvent('sc:content-added', { bubbles: true }));
    if (page >= last) {
      button.remove();
    } else {
      next.searchParams.delete('partial');
      next.searchParams.set('page', String(page + 1));
      button.href = next.toString();
    }
  } catch {
    SC.toast('Could not load more notes.', 'error');
  } finally {
    button.classList.remove('disabled');
    skeleton?.setAttribute('hidden', '');
  }
});

/* Sign-up password meter and match tick */
(function () {
  const input = document.querySelector('[data-password-meter]');
  if (!input) return;
  const meter = document.querySelector('.sc-pw-meter');
  const fill = meter?.querySelector('.sc-pw-meter__fill');
  const aside = input.closest('.sc-field')?.querySelector('[data-field-aside]');
  const confirm = document.querySelector('[data-password-confirm]');
  const tick = document.querySelector('[data-password-match]');
  const rules = {
    length: (v) => v.length >= 8,
    upper: (v) => /[A-Z]/.test(v),
    number: (v) => /\d/.test(v),
  };
  const labels = ['Weak', 'Weak', 'Fair', 'Strong'];

  function update() {
    const value = input.value;
    let passed = 0;
    Object.entries(rules).forEach(([name, test]) => {
      const ok = test(value);
      if (ok) passed += 1;
      const item = document.querySelector(`[data-rule="${name}"]`);
      item?.classList.toggle('is-met', ok);
      const icon = item?.querySelector('i');
      icon?.classList.toggle('bi-check-circle', ok);
      icon?.classList.toggle('bi-circle', !ok);
    });
    if (fill) fill.style.width = `${value ? Math.max(12, (passed / 3) * 100) : 0}%`;
    if (aside) aside.textContent = value ? labels[passed] : '';
    if (tick && confirm) tick.hidden = !(confirm.value && confirm.value === value);
  }
  input.addEventListener('input', update);
  confirm?.addEventListener('input', update);
  update();
})();

/* "Resend available in" countdown */
(function () {
  const box = document.querySelector('[data-resend-wait]');
  if (!box) return;
  const output = box.querySelector('[data-resend-time]');
  const button = box.querySelector('button');
  let left = Number(box.dataset.resendWait);
  const paint = () => {
    const ready = left <= 0;
    button.disabled = !ready;
    box.querySelector('[data-resend-label]').hidden = ready;
    output.textContent = `00:${String(Math.max(left, 0)).padStart(2, '0')}s`;
  };
  paint();
  const timer = setInterval(() => {
    left -= 1;
    paint();
    if (left <= 0) clearInterval(timer);
  }, 1000);
})();

/* Show the chosen file name in the upload dropzone */
document.querySelectorAll('[data-dropzone]').forEach((zone) => {
  const input = zone.querySelector('input[type="file"]');
  const label = zone.querySelector('[data-dropzone-label]');
  const show = () => { if (input.files[0]) label.textContent = input.files[0].name; };
  input.addEventListener('change', show);
  ['dragenter', 'dragover'].forEach((type) => zone.addEventListener(type, (e) => {
    e.preventDefault();
    zone.classList.add('is-dragover');
  }));
  ['dragleave', 'drop'].forEach((type) => zone.addEventListener(type, () => zone.classList.remove('is-dragover')));
  zone.addEventListener('drop', (e) => {
    e.preventDefault();
    if (e.dataTransfer.files.length) {
      input.files = e.dataTransfer.files;
      show();
    }
  });
});

/* Meeting form: only show place and link fields for the chosen type */
document.querySelectorAll('[data-meeting-mode]').forEach((select) => {
  const paint = () => {
    document.querySelectorAll('[data-mode-field]').forEach((field) => {
      field.hidden = select.value !== '' && !field.dataset.modeField.split(' ').includes(select.value);
    });
  };
  select.addEventListener('change', paint);
  paint();
});

/* Collapsible sidebar. The choice is remembered in this browser; head.php applies it before first paint. */
(function () {
  const root = document.documentElement;
  const toggle = document.querySelector('[data-sidebar-toggle]');
  if (!toggle) return;
  const tips = window.bootstrap
    ? [...document.querySelectorAll('.sc-sidebar [data-sc-tip]')].map((el) => new bootstrap.Tooltip(el, {
        title: el.dataset.scTip, placement: 'right', trigger: 'hover focus', container: 'body',
      }))
    : [];
  const paint = () => {
    const collapsed = root.dataset.sidebar === 'collapsed';
    toggle.setAttribute('aria-expanded', String(!collapsed));
    toggle.setAttribute('aria-label', collapsed ? 'Expand menu' : 'Collapse menu');
    tips.forEach((tip) => { tip.hide(); collapsed ? tip.enable() : tip.disable(); });
  };
  toggle.addEventListener('click', () => {
    root.classList.add('sc-sidebar-animating');
    setTimeout(() => root.classList.remove('sc-sidebar-animating'), 250);
    if (root.dataset.sidebar === 'collapsed') delete root.dataset.sidebar;
    else root.dataset.sidebar = 'collapsed';
    try { localStorage.setItem('sc-sidebar', root.dataset.sidebar || 'open'); } catch (e) { /* private mode */ }
    paint();
  });
  paint();
})();

/* ============================================================
   Live numbers: count-up, growing bars and polling.
   Markup: an element with data-live-root + data-live-url, holding any of
   data-live-value, -tag, -text, -width, -bars, -heat and -bubble.
   The server sends the same payload it used for the first render.
   ============================================================ */
SC.live = (function () {
  const POLL_MS = 20000;
  const calm = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const format = (n) => Number(n).toLocaleString('en-US');

  function countTo(el, to, from = 0) {
    if (calm || from === to) { el.textContent = format(to); return; }
    const started = performance.now();
    const step = (now) => {
      const t = Math.min((now - started) / 900, 1);
      const eased = 1 - Math.pow(1 - t, 3);
      el.textContent = format(Math.round(from + (to - from) * eased));
      if (t < 1) requestAnimationFrame(step);
    };
    requestAnimationFrame(step);
  }

  function flash(el) {
    if (calm) return;
    el.classList.remove('sc-live-flash');
    void el.offsetWidth;
    el.classList.add('sc-live-flash');
  }

  /* Numbers count up and bars grow the first time they scroll into view */
  function animateOnView() {
    if (calm || !('IntersectionObserver' in window)) return;
    const bars = document.querySelectorAll('.sc-bars');
    bars.forEach((b) => b.classList.add('is-waiting', 'is-entering'));
    const numbers = document.querySelectorAll('[data-count], [data-live-value]');
    const show = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) return;
        show.unobserve(entry.target);
        if (entry.target.classList.contains('sc-bars')) {
          entry.target.classList.remove('is-waiting');
          setTimeout(() => entry.target.classList.remove('is-entering'), 1200);
        } else {
          const to = Number(entry.target.textContent.replace(/,/g, ''));
          if (Number.isFinite(to)) countTo(entry.target, to);
        }
      });
    }, { threshold: 0.2 });
    bars.forEach((b) => show.observe(b));
    numbers.forEach((n) => show.observe(n));
  }

  function colMarkup() {
    const col = document.createElement('div');
    col.className = 'sc-bars__col';
    col.tabIndex = 0;
    col.setAttribute('role', 'img');
    col.innerHTML = '<div class="sc-bars__stack"><div class="sc-bars__segments"><span class="sc-bars__top"></span><span class="sc-bars__bottom"></span></div></div>';
    return col;
  }

  /* Sets every bar's height from its real numbers, leaving out any series the legend turned off */
  function scaleBars(chart) {
    const hidden = (chart.dataset.hidden || '').split(' ');
    const showA = !hidden.includes('top');
    const showB = !hidden.includes('bottom');
    const cols = [...chart.querySelectorAll('.sc-bars__col')];
    const parts = cols.map((col) => ({ a: showA ? Number(col.dataset.a) : 0, b: showB ? Number(col.dataset.b) : 0 }));
    const max = Math.max(0, ...parts.map((p) => p.a + p.b));
    const scale = max > 0 ? 90 / max : 0;
    cols.forEach((col, i) => {
      const { a, b } = parts[i];
      const total = a + b;
      col.querySelector('.sc-bars__stack').style.height = `${total * scale}%`;
      col.querySelector('.sc-bars__top').style.height = `${total ? (a / total) * 100 : 0}%`;
      col.querySelector('.sc-bars__bottom').style.height = `${total ? (b / total) * 100 : 0}%`;
    });
    const median = chart.querySelector('.sc-bars__median');
    if (median) {
      const value = Number(chart.dataset.medianValue || 0);
      if (chart.dataset.medianValue !== undefined) median.style.bottom = `${value * scale}%`;
      median.classList.toggle('is-off', hidden.includes('median') || !showA || !showB);
    }
    // The fixed peak label only makes sense with the full totals showing.
    chart.querySelectorAll('.sc-bars__stack > .sc-chart-tip').forEach((tip) => { tip.hidden = !showA || !showB; });
  }

  function updateBars(chart, data) {
    const plot = chart.querySelector('.sc-bars__plot');
    const labelRow = chart.querySelector('.sc-bars__labels');
    let cols = [...chart.querySelectorAll('.sc-bars__col')];
    const rebuilt = cols.length !== data.items.length;
    if (rebuilt) {
      hideFloat(chart);
      cols.forEach((col) => col.remove());
      labelRow.textContent = '';
      data.items.forEach((_, i) => {
        const col = colMarkup();
        col.style.setProperty('--i', i);
        plot.append(col);
        labelRow.append(document.createElement('span'));
      });
      cols = [...chart.querySelectorAll('.sc-bars__col')];
    }
    const labels = labelRow.querySelectorAll('span');
    chart.dataset.medianValue = data.medianValue ?? 0;
    const label = chart.querySelector('.sc-bars__median em');
    if (label) { label.textContent = data.medianLabel || ''; label.hidden = !data.medianLabel; }
    chart.querySelectorAll('.sc-chart-tip').forEach((tip) => tip.remove());
    data.items.forEach((item, i) => {
      const col = cols[i];
      col.dataset.a = item.a;
      col.dataset.b = item.b;
      col.dataset.detail = item.detail || item.label;
      col.setAttribute('aria-label', item.title || item.label);
      col.classList.toggle('is-active', !!item.active);
      labels[i].textContent = item.label;
      labels[i].classList.toggle('is-active', !!item.active);
      if (item.tooltip) {
        const tip = document.createElement('span');
        const edge = i <= 1 ? ' sc-chart-tip--start' : (i >= data.items.length - 2 ? ' sc-chart-tip--end' : '');
        tip.className = `sc-chart-tip${edge}`;
        const dot = document.createElement('span');
        dot.className = 'sc-dot sc-dot--xs';
        const text = document.createElement('span');
        text.textContent = item.tooltip;
        tip.append(dot, text);
        col.querySelector('.sc-bars__stack').prepend(tip);
      }
    });
    if (rebuilt && !calm) {
      // Start from nothing on the next frame, so a new range grows in
      chart.classList.add('is-waiting', 'is-entering');
      requestAnimationFrame(() => requestAnimationFrame(() => {
        chart.classList.remove('is-waiting');
        setTimeout(() => chart.classList.remove('is-entering'), 1200);
      }));
    }
    scaleBars(chart);
  }

  function hideFloat(chart) {
    chart.classList.remove('is-hovering');
    chart.querySelector('.sc-chart-float')?.remove();
    chart.querySelectorAll('.is-focus').forEach((el) => el.classList.remove('is-focus'));
  }

  function showFloat(chart, col) {
    hideFloat(chart);
    const plot = chart.querySelector('.sc-bars__plot');
    const cols = [...chart.querySelectorAll('.sc-bars__col')];
    const index = cols.indexOf(col);
    const hidden = (chart.dataset.hidden || '').split(' ');
    const a = Number(col.dataset.a);
    const b = Number(col.dataset.b);

    const box = document.createElement('div');
    box.className = 'sc-chart-float';
    const title = document.createElement('strong');
    title.textContent = col.dataset.detail;
    box.append(title);
    const row = (name, value, swatch) => {
      const line = document.createElement('div');
      line.className = 'sc-chart-float__row';
      if (swatch) {
        const mark = document.createElement('span');
        mark.className = `sc-chart-float__swatch sc-chart-float__swatch--${swatch}`;
        line.append(mark);
      }
      line.append(name);
      const number = document.createElement('b');
      number.textContent = format(value);
      line.append(number);
      return line;
    };
    if (!hidden.includes('top')) box.append(row(chart.dataset.seriesA || 'Series 1', a, 'a'));
    if (!hidden.includes('bottom')) box.append(row(chart.dataset.seriesB || 'Series 2', b, 'b'));
    if (!hidden.includes('top') && !hidden.includes('bottom')) {
      const total = row('Total', a + b);
      total.classList.add('sc-chart-float__total');
      box.append(total);
    }
    plot.append(box);

    // Sit above the bar, and stay inside the chart at the edges
    const half = box.offsetWidth / 2;
    const centre = col.offsetLeft + col.offsetWidth / 2;
    box.style.left = `${Math.min(Math.max(half, centre), plot.clientWidth - half)}px`;
    const stackHeight = col.querySelector('.sc-bars__stack').offsetHeight;
    box.style.bottom = `${Math.min(stackHeight + 10, plot.clientHeight - box.offsetHeight + 28)}px`;

    chart.classList.add('is-hovering');
    col.classList.add('is-focus');
    chart.querySelectorAll('.sc-bars__labels span')[index]?.classList.add('is-focus');
  }

  /* Hover, tap and keyboard for every chart. Delegated, so rebuilt bars keep working. */
  function wireChart(chart) {
    chart.addEventListener('pointerover', (event) => {
      const col = event.target.closest('.sc-bars__col');
      if (col && !col.classList.contains('is-focus')) showFloat(chart, col);
    });
    chart.addEventListener('pointerleave', () => hideFloat(chart));
    chart.addEventListener('focusin', (event) => {
      const col = event.target.closest('.sc-bars__col');
      if (col) showFloat(chart, col);
    });
    chart.addEventListener('focusout', () => hideFloat(chart));
    chart.addEventListener('keydown', (event) => {
      if (!['ArrowLeft', 'ArrowRight'].includes(event.key)) return;
      const cols = [...chart.querySelectorAll('.sc-bars__col')];
      const next = cols[cols.indexOf(event.target) + (event.key === 'ArrowRight' ? 1 : -1)];
      if (next) { event.preventDefault(); next.focus(); }
    });
  }

  /* Legends switch a series on and off, and hovering one highlights it */
  function wireLegend(list) {
    const chart = document.querySelector(`[data-live-bars="${list.dataset.legendFor}"]`);
    if (!chart) return;
    list.addEventListener('click', (event) => {
      const button = event.target.closest('[data-legend]');
      if (!button) return;
      const on = button.getAttribute('aria-pressed') !== 'true';
      button.setAttribute('aria-pressed', String(on));
      const hidden = new Set((chart.dataset.hidden || '').split(' ').filter(Boolean));
      if (on) hidden.delete(button.dataset.legend); else hidden.add(button.dataset.legend);
      chart.dataset.hidden = [...hidden].join(' ');
      hideFloat(chart);
      scaleBars(chart);
    });
    list.addEventListener('pointerover', (event) => {
      const button = event.target.closest('[data-legend]');
      if (button && button.dataset.legend !== 'median') chart.dataset.emph = button.dataset.legend;
    });
    list.addEventListener('pointerleave', () => delete chart.dataset.emph);
  }

  function apply(root, data) {
    Object.entries(data.values || {}).forEach(([key, value]) => {
      root.querySelectorAll(`[data-live-value="${key}"]`).forEach((el) => {
        const current = Number(el.textContent.replace(/,/g, ''));
        if (current !== value) { countTo(el, value, Number.isFinite(current) ? current : 0); flash(el); }
      });
    });
    Object.entries(data.tags || {}).forEach(([key, tag]) => {
      root.querySelectorAll(`[data-live-tag="${key}"]`).forEach((el) => {
        el.textContent = tag.label;
        if (el.dataset.downClass) el.classList.toggle(el.dataset.downClass, !!tag.down);
      });
    });
    const texts = { ...(data.texts || {}) };
    if (data.bubbles) texts.bubbleNote = data.bubbles.note;
    Object.entries(texts).forEach(([key, value]) => {
      root.querySelectorAll(`[data-live-text="${key}"]`).forEach((el) => { if (el.textContent !== value) el.textContent = value; });
    });
    Object.entries(data.widths || {}).forEach(([key, value]) => {
      root.querySelectorAll(`[data-live-width="${key}"]`).forEach((el) => {
        el.style.width = `${value}%`;
        el.parentElement?.setAttribute('aria-valuenow', value);
      });
    });
    Object.entries(data.bars || {}).forEach(([key, chart]) => {
      root.querySelectorAll(`[data-live-bars="${key}"]`).forEach((el) => updateBars(el, chart));
    });
    if (data.heat) {
      const cells = root.querySelectorAll('[data-live-heat] .sc-heat__cell');
      data.heat.cells.forEach((cell, i) => {
        if (!cells[i]) return;
        cells[i].className = `sc-heat__cell sc-heat__cell--${cell.level}`;
        cells[i].title = cell.title;
      });
      root.querySelectorAll('[data-live-heat-peak]').forEach((el) => { el.textContent = data.heat.peak; });
    }
    if (data.bubbles) {
      root.querySelectorAll('[data-live-bubble]').forEach((el) => {
        const item = data.bubbles.items[Number(el.dataset.liveBubble)];
        el.hidden = !item;
        if (item) {
          el.querySelector('strong').textContent = `${item.percent}%`;
          el.querySelector('span').textContent = item.name;
        }
      });
    }
  }

  function start(root) {
    let range = 'week';
    let last = Date.now();
    let stopped = false;
    let failed = false;

    const paintAge = () => {
      const seconds = Math.round((Date.now() - last) / 1000);
      root.querySelectorAll('[data-live-status]').forEach((badge) => {
        badge.classList.toggle('is-offline', failed);
        badge.querySelector('[data-live-age]').textContent = failed ? ' · reconnecting' : ` · updated ${seconds}s ago`;
      });
    };

    const poll = async () => {
      if (stopped || document.hidden) return;
      try {
        const url = new URL(root.dataset.liveUrl, window.location.href);
        if (range !== 'week') url.searchParams.set('range', range);
        const asked = range;
        const res = await fetch(url, { headers: { Accept: 'application/json' } });
        if (res.status === 401) { stopped = true; return; }
        if (!res.ok) throw new Error('bad response');
        const json = await res.json();
        if (asked !== range) return;   // the range changed while this was loading
        apply(root, json.data);
        last = Date.now();
        failed = false;
      } catch {
        failed = true;   // keep showing the last numbers
      }
      paintAge();
    };

    /* Time range buttons. The choice is remembered in this browser. */
    const buttons = root.querySelectorAll('[data-range]');
    const pick = (value) => {
      range = value;
      buttons.forEach((b) => {
        const on = b.dataset.range === value;
        b.classList.toggle('is-on', on);
        b.setAttribute('aria-pressed', String(on));
      });
      poll();
    };
    buttons.forEach((b) => b.addEventListener('click', () => {
      try { localStorage.setItem('sc-chart-range', b.dataset.range); } catch (e) { /* private mode */ }
      pick(b.dataset.range);
    }));
    if (buttons.length) {
      let saved = null;
      try { saved = localStorage.getItem('sc-chart-range'); } catch (e) { /* private mode */ }
      if (saved && saved !== 'week' && root.querySelector(`[data-range="${saved}"]`)) pick(saved);
    }

    setInterval(poll, POLL_MS);
    setInterval(paintAge, 1000);
    document.addEventListener('visibilitychange', () => { if (!document.hidden) poll(); });
    paintAge();
  }

  document.querySelectorAll('.sc-bars').forEach(wireChart);
  document.querySelectorAll('[data-legend-for]').forEach(wireLegend);
  animateOnView();
  document.querySelectorAll('[data-live-root]').forEach(start);
  return { apply };
})();

/* ============================================================
   Site-wide motion. Adds nothing for reduced motion, so content is never
   left hidden. CSS lives in motion.css.
   ============================================================ */
SC.reveal = function (scope = document) {
  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches || !('IntersectionObserver' in window)) return;
  const selector = '.sc-page-header, .sc-section-head, .landing-metrics__head, .landing-preview, .landing-cta, .sc-panel, .sc-card, .sc-note-card, .sc-thread, .sc-pod, .sc-group, .sc-member, .sc-request, .sc-stat-tile, .sc-table-wrap, .browse-filters__card, .sc-prose';
  const items = [...scope.querySelectorAll(selector)].filter((el) => !el.parentElement.closest(selector));
  if (!SC.revealObserver) {
    SC.revealObserver = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) return;
        const el = entry.target;
        SC.revealObserver.unobserve(el);
        el.classList.add('is-in');
        // Hand transform back to the hover styles once the reveal has finished
        setTimeout(() => {
          el.classList.remove('sc-reveal', 'is-in');
          el.style.removeProperty('--reveal-i');
        }, 900);
      });
    }, { threshold: 0.05, rootMargin: '0px 0px -24px 0px' });
  }
  items.forEach((el) => {
    if (el.classList.contains('sc-reveal') || el.closest('.modal, .collapse:not(.show)')) return;
    const siblings = [...el.parentElement.children];
    el.style.setProperty('--reveal-i', Math.min(siblings.indexOf(el) % 4, 3));
    el.classList.add('sc-reveal');
    SC.revealObserver.observe(el);
  });
};
SC.reveal();
document.addEventListener('sc:content-added', (event) => SC.reveal(event.target));

/* Pointer-following light and art on dark panels */
(function () {
  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
  document.querySelectorAll('[data-spotlight]').forEach((panel) => {
    panel.addEventListener('pointermove', (event) => {
      const box = panel.getBoundingClientRect();
      panel.style.setProperty('--mx', `${event.clientX - box.left}px`);
      panel.style.setProperty('--my', `${event.clientY - box.top}px`);
      if (panel.hasAttribute('data-parallax')) {
        panel.style.setProperty('--px', ((event.clientX - box.left) / box.width - 0.5).toFixed(3));
        panel.style.setProperty('--py', ((event.clientY - box.top) / box.height - 0.5).toFixed(3));
      }
    });
    panel.addEventListener('pointerleave', () => {
      panel.style.setProperty('--px', 0);
      panel.style.setProperty('--py', 0);
    });
  });
})();

/* Public navbar gets a deeper shadow once the page has scrolled */
(function () {
  const header = document.querySelector('.sc-header');
  if (!header) return;
  const paint = () => header.classList.toggle('is-scrolled', window.scrollY > 16);
  window.addEventListener('scroll', paint, { passive: true });
  paint();
})();

/* Light and dark theme. head.php applies the saved choice before first paint. */
(function () {
  const root = document.documentElement;
  const paint = () => {
    const dark = root.dataset.theme === 'dark';
    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
      button.setAttribute('aria-pressed', String(dark));
      button.setAttribute('aria-label', dark ? 'Switch to light theme' : 'Switch to dark theme');
    });
  };
  document.addEventListener('click', (event) => {
    if (!event.target.closest('[data-theme-toggle]')) return;
    const next = root.dataset.theme === 'dark' ? 'light' : 'dark';
    root.classList.add('sc-theme-fading');
    setTimeout(() => root.classList.remove('sc-theme-fading'), 300);
    root.dataset.theme = next;
    root.setAttribute('data-bs-theme', next);
    try { localStorage.setItem('sc-theme', next); } catch (e) { /* private mode */ }
    paint();
  });
  paint();
})();

/* Click a shared image to see it large. Uses the browser's own dialog, so Esc and focus just work. */
document.addEventListener('click', (event) => {
  const link = event.target.closest('[data-lightbox]');
  if (!link) return;
  event.preventDefault();
  const dialog = document.createElement('dialog');
  dialog.className = 'sc-lightbox';
  dialog.setAttribute('aria-label', 'Image preview');
  const image = document.createElement('img');
  image.src = link.href;
  image.alt = link.querySelector('img')?.alt || '';
  const close = document.createElement('button');
  close.type = 'button';
  close.className = 'sc-lightbox__close';
  close.setAttribute('aria-label', 'Close image');
  close.innerHTML = '<i class="bi bi-x-lg" aria-hidden="true"></i>';
  dialog.append(image, close);
  document.body.append(dialog);
  dialog.showModal();
  close.focus();
  dialog.addEventListener('click', (e) => { if (e.target !== image) dialog.close(); });
  dialog.addEventListener('close', () => dialog.remove());
});

/* Profile photo: crop to a small square before upload. This also drops hidden data such as GPS location. */
(function () {
  const input = document.querySelector('[data-photo-input]');
  if (!input || !window.createImageBitmap || !window.DataTransfer) return;
  const SIZE = 512;
  const submit = input.form.querySelector('[type="submit"]');
  let turn = 0;
  input.addEventListener('change', async () => {
    const file = input.files[0];
    if (!file) return;
    const mine = ++turn;
    submit.disabled = true;
    try {
      const bitmap = await createImageBitmap(file);
      const side = Math.min(bitmap.width, bitmap.height);
      const canvas = document.createElement('canvas');
      canvas.width = SIZE;
      canvas.height = SIZE;
      const ctx = canvas.getContext('2d');
      ctx.fillStyle = '#fff';
      ctx.fillRect(0, 0, SIZE, SIZE);
      ctx.drawImage(bitmap, (bitmap.width - side) / 2, (bitmap.height - side) / 2, side, side, 0, 0, SIZE, SIZE);
      const blob = await new Promise((resolve) => canvas.toBlob(resolve, 'image/jpeg', 0.88));
      if (blob && mine === turn) {
        const list = new DataTransfer();
        list.items.add(new File([blob], 'photo.jpg', { type: 'image/jpeg' }));
        input.files = list.files;
      }
    } catch (e) {
      /* Not a readable image: the server explains the problem. */
    }
    if (mine === turn) submit.disabled = false;
  });
})();
