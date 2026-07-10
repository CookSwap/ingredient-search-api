<?php
// Simple password gate — not public-facing
$pass = $_POST['pass'] ?? $_COOKIE['cs_test_auth'] ?? '';
$ok   = ($pass === 'cookswap2024');
if ($_POST['pass'] ?? false) {
    if ($ok) setcookie('cs_test_auth', $pass, time() + 86400 * 7, '/', '', true, true);
    else $auth_error = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>CookSwap API Test</title>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: system-ui, sans-serif; background: #f5f4f1; color: #222; min-height: 100vh; }

  .gate {
    display: flex; align-items: center; justify-content: center; min-height: 100vh;
  }
  .gate-box {
    background: white; border-radius: 12px; padding: 2rem; width: 320px;
    box-shadow: 0 2px 16px rgba(0,0,0,0.1);
  }
  .gate-box h1 { font-size: 1.1rem; margin-bottom: 1rem; color: #e17a58; }
  .gate-box input {
    width: 100%; padding: 9px 12px; border: 1px solid #ddd; border-radius: 8px;
    font-size: 0.9rem; margin-bottom: 0.75rem;
  }
  .gate-box button {
    width: 100%; padding: 10px; background: #e17a58; color: white; border: none;
    border-radius: 8px; font-size: 0.9rem; font-weight: 600; cursor: pointer;
  }
  .gate-error { color: #c0392b; font-size: 0.82rem; margin-top: 0.5rem; }

  header {
    background: #e17a58; color: white; padding: 1rem 1.5rem;
    display: flex; align-items: center; gap: 1rem;
  }
  header h1 { font-size: 1rem; font-weight: 700; }
  header span { font-size: 0.8rem; opacity: 0.8; }

  .layout { display: grid; grid-template-columns: 320px 1fr; height: calc(100vh - 52px); }

  .sidebar {
    background: white; border-right: 1px solid #eee;
    padding: 1.25rem; overflow-y: auto; display: flex; flex-direction: column; gap: 1rem;
  }

  .field label {
    display: block; font-size: 0.72rem; font-weight: 700; color: #888;
    letter-spacing: 0.08em; text-transform: uppercase; margin-bottom: 4px;
  }
  .field input, .field select {
    width: 100%; padding: 8px 10px; border: 1px solid #ddd; border-radius: 7px;
    font-size: 0.88rem; font-family: inherit; color: #222; background: white;
  }
  .field input:focus, .field select:focus { outline: none; border-color: #e17a58; }

  .btn-run {
    padding: 11px; background: #e17a58; color: white; border: none;
    border-radius: 8px; font-size: 0.95rem; font-weight: 700; cursor: pointer;
    font-family: inherit; width: 100%;
  }
  .btn-run:hover { background: #c9623f; }
  .btn-run:disabled { background: #ccc; cursor: default; }

  .divider { border: none; border-top: 1px solid #eee; }

  .preset-label {
    font-size: 0.72rem; font-weight: 700; color: #aaa;
    letter-spacing: 0.08em; text-transform: uppercase; margin-bottom: 6px;
  }
  .presets { display: flex; flex-wrap: wrap; gap: 5px; }
  .preset {
    padding: 4px 10px; background: #f5f4f1; border: 1px solid #e0ded9;
    border-radius: 20px; font-size: 0.78rem; cursor: pointer; color: #555;
  }
  .preset:hover { background: #fde8e0; border-color: #e17a58; color: #c9623f; }

  .main { display: flex; flex-direction: column; overflow: hidden; }

  .meta-bar {
    padding: 0.6rem 1.25rem; background: #fafaf8; border-bottom: 1px solid #eee;
    display: flex; gap: 1.5rem; align-items: center; font-size: 0.8rem; color: #888;
    min-height: 36px;
  }
  .meta-bar .status-ok   { color: #27ae60; font-weight: 700; }
  .meta-bar .status-err  { color: #c0392b; font-weight: 700; }
  .meta-bar .timing      { margin-left: auto; }

  .output-wrap { flex: 1; overflow: auto; padding: 1.25rem; }

  .results-grid {
    display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 12px;
    margin-bottom: 1.5rem;
  }

  .result-card {
    background: white; border-radius: 10px; padding: 0.9rem;
    border: 1px solid #eee; font-size: 0.82rem;
  }
  .result-card .rc-name  { font-weight: 700; color: #222; margin-bottom: 4px; line-height: 1.3; }
  .result-card .rc-brand { color: #aaa; font-size: 0.75rem; margin-bottom: 6px; }
  .result-card .rc-price { font-size: 1rem; font-weight: 700; color: #e17a58; margin-bottom: 4px; }
  .result-card .rc-unit  { color: #aaa; font-size: 0.75rem; margin-bottom: 8px; }
  .result-card .rc-stock { font-size: 0.75rem; margin-bottom: 8px; }
  .rc-stock.in  { color: #27ae60; }
  .rc-stock.out { color: #c0392b; }
  .result-card a {
    display: inline-block; padding: 5px 12px; background: #e17a58; color: white;
    border-radius: 20px; font-size: 0.75rem; font-weight: 600; text-decoration: none;
  }
  .result-card a:hover { background: #c9623f; }
  .result-card .rc-id { color: #ccc; font-size: 0.7rem; margin-top: 6px; }

  .raw-toggle {
    font-size: 0.78rem; color: #aaa; cursor: pointer; text-decoration: underline;
    margin-bottom: 0.5rem; display: inline-block;
  }
  .raw-json {
    background: #1e1e1e; color: #d4d4d4; border-radius: 8px; padding: 1rem;
    font-size: 0.78rem; font-family: 'Consolas', monospace; overflow-x: auto;
    white-space: pre; display: none;
  }

  .empty { color: #aaa; font-size: 0.9rem; padding: 2rem 0; text-align: center; }
</style>
</head>
<body>

<?php if (!$ok): ?>
<div class="gate">
  <div class="gate-box">
    <h1>CookSwap API Test</h1>
    <form method="post">
      <input type="password" name="pass" placeholder="Password" autofocus>
      <button type="submit">Enter</button>
      <?php if (isset($auth_error)): ?>
        <p class="gate-error">Incorrect password.</p>
      <?php endif; ?>
    </form>
  </div>
</div>
<?php else: ?>

<header>
  <h1>CookSwap API Test</h1>
  <span>api.cookswap.com — private</span>
</header>

<div class="layout">
  <div class="sidebar">

    <div class="field">
      <label>Ingredient</label>
      <input type="text" id="q" value="butter" placeholder="e.g. unsalted butter">
    </div>

    <div class="field">
      <label>Retailer</label>
      <select id="retailer">
        <optgroup label="── Demo retailers ──">
          <option value="greenfields">Greenfields (organic/premium)</option>
          <option value="priceright">PriceRight (budget/value)</option>
          <option value="freshmarket">The Fresh Market (artisan/local)</option>
        </optgroup>
        <optgroup label="── Real retailers (mock data) ──">
          <option value="tesco">Tesco</option>
          <option value="ocado">Ocado</option>
          <option value="sainsburys">Sainsbury's</option>
          <option value="asda">ASDA</option>
          <option value="morrisons">Morrisons</option>
          <option value="waitrose">Waitrose</option>
        </optgroup>
      </select>
    </div>

    <div class="field">
      <label>Limit</label>
      <input type="number" id="limit" value="5" min="1" max="20">
    </div>

    <div class="field">
      <label>API Key</label>
      <input type="text" id="apikey" value="demo-key-12345">
    </div>

    <button class="btn-run" id="runBtn" onclick="runTest()">Run</button>

    <hr class="divider">

    <div>
      <div class="preset-label">Quick tests</div>
      <div class="presets">
        <?php
        $presets = ['butter','flour','eggs','cream','onion','chicken','garlic','tomato','milk','olive oil','lobster','unknown ingredient'];
        foreach ($presets as $p): ?>
          <span class="preset" onclick="setQ('<?= $p ?>')"><?= $p ?></span>
        <?php endforeach; ?>
      </div>
    </div>

    <hr class="divider">

    <div>
      <div class="preset-label">Error tests</div>
      <div class="presets">
        <span class="preset" onclick="runRaw('/health','')">GET /health</span>
        <span class="preset" onclick="runBadKey()">Bad API key</span>
        <span class="preset" onclick="runNoQ()">Missing q</span>
        <span class="preset" onclick="runBadRetailer()">Bad retailer</span>
      </div>
    </div>

  </div>

  <div class="main">
    <div class="meta-bar" id="metaBar">
      <span>Run a query to see results.</span>
    </div>
    <div class="output-wrap" id="outputWrap">
      <div class="empty">Results will appear here.</div>
    </div>
  </div>
</div>

<script>
const BASE = 'https://api.cookswap.com';

const RETAILER_META = {
  greenfields: {
    label: 'Greenfields',
    tagline: 'Naturally better',
    color: '#2d7d5a',
    bg: '#edf7f2',
    logo: `<svg width="110" height="32" viewBox="0 0 110 32" xmlns="http://www.w3.org/2000/svg">
      <circle cx="16" cy="16" r="14" fill="#2d7d5a"/>
      <text x="16" y="21" text-anchor="middle" fill="white" font-family="system-ui" font-size="14" font-weight="700">G</text>
      <text x="36" y="21" fill="#2d7d5a" font-family="system-ui" font-size="14" font-weight="700">Greenfields</text>
    </svg>`,
  },
  priceright: {
    label: 'PriceRight',
    tagline: 'Great value, every day',
    color: '#1a3a8f',
    bg: '#eef2fc',
    logo: `<svg width="110" height="32" viewBox="0 0 110 32" xmlns="http://www.w3.org/2000/svg">
      <rect x="1" y="1" width="30" height="30" rx="4" fill="#1a3a8f"/>
      <text x="16" y="22" text-anchor="middle" fill="#f5c800" font-family="system-ui" font-size="13" font-weight="900">PR</text>
      <text x="38" y="21" fill="#1a3a8f" font-family="system-ui" font-size="13" font-weight="700">PriceRight</text>
    </svg>`,
  },
  freshmarket: {
    label: 'The Fresh Market',
    tagline: 'Local. Fresh. Yours.',
    color: '#b83232',
    bg: '#fdf2f2',
    logo: `<svg width="130" height="32" viewBox="0 0 130 32" xmlns="http://www.w3.org/2000/svg">
      <rect x="1" y="1" width="30" height="30" rx="15" fill="#b83232"/>
      <text x="16" y="22" text-anchor="middle" fill="white" font-family="system-ui" font-size="13" font-weight="700">FM</text>
      <text x="38" y="14" fill="#b83232" font-family="system-ui" font-size="10" font-weight="700">THE FRESH</text>
      <text x="38" y="26" fill="#b83232" font-family="system-ui" font-size="10" font-weight="700">MARKET</text>
    </svg>`,
  },
};

function setQ(val) {
  document.getElementById('q').value = val;
  runTest();
}

async function runTest() {
  const q       = document.getElementById('q').value.trim();
  const retailer= document.getElementById('retailer').value;
  const limit   = document.getElementById('limit').value;
  const key     = document.getElementById('apikey').value.trim();
  const url     = `${BASE}/search?q=${encodeURIComponent(q)}&retailer=${retailer}&limit=${limit}`;
  await runRaw(url, key);
}

async function runBadKey()      { await runRaw(`${BASE}/search?q=butter&retailer=demo`, 'wrong-key'); }
async function runNoQ()         { await runRaw(`${BASE}/search?retailer=demo`, document.getElementById('apikey').value); }
async function runBadRetailer() { await runRaw(`${BASE}/search?q=butter&retailer=lidl`, document.getElementById('apikey').value); }

async function runRaw(url, key) {
  const btn = document.getElementById('runBtn');
  btn.disabled = true; btn.textContent = 'Running…';
  const meta = document.getElementById('metaBar');
  meta.innerHTML = '<span>Fetching…</span>';

  const t0 = performance.now();
  try {
    const headers = key ? { 'X-CookSwap-Key': key } : {};
    const res  = await fetch(url, { headers });
    const ms   = Math.round(performance.now() - t0);
    const json = await res.json();
    renderResult(res.status, ms, url, json);
  } catch (e) {
    const ms = Math.round(performance.now() - t0);
    renderError(ms, url, e.message);
  } finally {
    btn.disabled = false; btn.textContent = 'Run';
  }
}

function renderResult(status, ms, url, json) {
  const meta = document.getElementById('metaBar');
  const ok   = status >= 200 && status < 300;
  meta.innerHTML = `
    <span class="${ok ? 'status-ok' : 'status-err'}">${status}</span>
    <span>${url}</span>
    <span class="timing">${ms}ms</span>`;

  const wrap = document.getElementById('outputWrap');

  if (json.results) {
    const meta = RETAILER_META[json.retailer];
    const banner = meta ? `
      <div style="display:flex;align-items:center;gap:1rem;padding:0.9rem 1rem;background:${meta.bg};border-radius:10px;margin-bottom:1rem;border:1px solid ${meta.color}22;">
        ${meta.logo}
        <div>
          <div style="font-size:0.75rem;color:${meta.color};font-weight:700;letter-spacing:0.05em;text-transform:uppercase;">Partner retailer · demo feed</div>
          <div style="font-size:0.8rem;color:#888;font-style:italic;">${meta.tagline}</div>
        </div>
        <div style="margin-left:auto;font-size:0.78rem;color:#aaa;">${json.total} result${json.total!==1?'s':''} for <strong style="color:#555">${esc(json.query)}</strong></div>
      </div>` : `<div style="margin-bottom:1rem;font-size:0.82rem;color:#888;">${json.total} result${json.total!==1?'s':''} for <strong>${esc(json.query)}</strong> · ${esc(json.retailer)}</div>`;

    const cards = json.results.map(r => {
      const accentColor = meta ? meta.color : '#e17a58';
      const tags = (r.tags||[]).map(t=>`<span style="font-size:0.68rem;padding:2px 7px;background:#f0f0f0;border-radius:10px;color:#666;">${t}</span>`).join(' ');
      return `
      <div class="result-card">
        <div class="rc-name">${esc(r.name)}</div>
        <div class="rc-brand">${esc(r.brand || '')}</div>
        ${r.price && r.price.amount ? `<div class="rc-price" style="color:${accentColor}">£${Number(r.price.amount).toFixed(2)}</div>` : ''}
        <div class="rc-unit">${esc(r.unit || '')}${r.price&&r.price.unit_price?' · '+esc(r.price.unit_price):''}</div>
        <div class="rc-stock ${r.in_stock ? 'in' : 'out'}">${r.in_stock ? '✓ In stock' : '✗ Out of stock'}</div>
        ${tags ? `<div style="margin-bottom:8px;display:flex;flex-wrap:wrap;gap:3px;">${tags}</div>` : ''}
        <div style="display:flex;gap:6px;flex-wrap:wrap;align-items:center;">
          <a href="${esc(r.buy_url)}" target="_blank" rel="noopener" style="background:${accentColor}">View →</a>
          ${r.in_stock ? `<button onclick="addToBasket('${esc(r.id)}','${esc(json.retailer)}',this)"
            style="padding:5px 12px;background:white;color:${accentColor};border:1.5px solid ${accentColor};
            border-radius:20px;font-size:0.75rem;font-weight:600;cursor:pointer;font-family:inherit;">
            🛒 Go to checkout
          </button>` : ''}
        </div>
        <div class="rc-id">${esc(r.id)}</div>
      </div>`;
    }).join('');

    wrap.innerHTML = `
      ${banner}
      <div class="results-grid">${cards || '<div class="empty">No results returned.</div>'}</div>
      <span class="raw-toggle" onclick="toggleRaw(this)">Show raw JSON</span>
      <pre class="raw-json">${esc(JSON.stringify(json, null, 2))}</pre>`;
  } else {
    wrap.innerHTML = `
      <pre class="raw-json" style="display:block">${esc(JSON.stringify(json, null, 2))}</pre>`;
  }
}

function renderError(ms, url, msg) {
  document.getElementById('metaBar').innerHTML =
    `<span class="status-err">Network error</span><span>${url}</span><span class="timing">${ms}ms</span>`;
  document.getElementById('outputWrap').innerHTML =
    `<pre class="raw-json" style="display:block">${esc(msg)}</pre>`;
}

function toggleRaw(el) {
  const pre = el.nextElementSibling;
  const show = pre.style.display === 'none' || pre.style.display === '';
  pre.style.display = show ? 'block' : 'none';
  el.textContent = show ? 'Hide raw JSON' : 'Show raw JSON';
}

async function addToBasket(productId, retailer, btn) {
  const key = document.getElementById('apikey').value.trim();
  const orig = btn.innerHTML;
  btn.disabled = true;
  btn.innerHTML = 'Adding…';

  try {
    const res  = await fetch(`${BASE}/basket/add?retailer=${retailer}`, {
      method:  'POST',
      headers: { 'X-CookSwap-Key': key, 'Content-Type': 'application/json' },
      body:    JSON.stringify({ product_id: productId, quantity: 1 }),
    });
    const json = await res.json();

    if (json.checkout_url) {
      btn.innerHTML = '✓ Opening checkout…';
      setTimeout(() => { btn.disabled = false; btn.innerHTML = orig; }, 3000);
      window.open(json.checkout_url, '_blank', 'noopener');
      // Also show in meta bar
      document.getElementById('metaBar').innerHTML =
        `<span class="status-ok">${res.status}</span>
         <span>basket/add · ${esc(productId)}</span>
         <span style="margin-left:auto;font-size:0.75rem;color:#aaa;">basket: ${esc(json.basket_id||'')} · expires ${new Date(json.expires_at*1000).toLocaleTimeString()}</span>`;
    } else if (json.code === 'NOT_IMPLEMENTED') {
      btn.innerHTML = '✗ Not supported';
      setTimeout(() => { btn.disabled = false; btn.innerHTML = orig; }, 2500);
    } else {
      btn.innerHTML = '✗ Error';
      setTimeout(() => { btn.disabled = false; btn.innerHTML = orig; }, 2500);
    }
  } catch {
    btn.innerHTML = '✗ Network error';
    setTimeout(() => { btn.disabled = false; btn.innerHTML = orig; }, 2500);
  }
}

function esc(s) {
  return String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
</script>

<?php endif; ?>
</body>
</html>
