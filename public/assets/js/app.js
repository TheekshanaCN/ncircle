/* ============================================================
   app.js  -  OWNER: UI lead
   Global helpers only. Feature code goes in assets/js/modules/.
   Needs in the layout head:  <meta name="csrf-token" content="...">
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
    document.body.appendChild(wrap);
  }
  const el = document.createElement('div');
  el.className = `alert alert-${type === 'error' ? 'danger' : type} shadow mb-0`;
  el.textContent = message;
  wrap.appendChild(el);
  setTimeout(() => el.remove(), 3500);
};
