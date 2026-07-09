/**
 * CookSwap Ingredient Search API — Node.js / Express reference implementation
 * Install: npm install express
 *
 * Replace the stub search() function with a query against your product DB.
 */

const express = require('express');
const app     = express();

const RETAILER_SLUG = 'your-retailer';
const VALID_KEYS    = new Set([process.env.COOKSWAP_API_KEY]);

// ── Rate-limit headers (add real counters per key in production) ──────────────
app.use((req, res, next) => {
  res.set({
    'X-RateLimit-Limit':     '60',
    'X-RateLimit-Remaining': '59',
    'X-RateLimit-Reset':     String(Math.floor(Date.now() / 1000) + 60),
  });
  next();
});

// ── Auth middleware ───────────────────────────────────────────────────────────
function requireKey(req, res, next) {
  const key = req.headers['x-cookswap-key'];
  if (!key || !VALID_KEYS.has(key)) {
    return res.status(401).json({ code: 'UNAUTHORIZED', message: 'Invalid or missing X-CookSwap-Key header' });
  }
  next();
}

// ── GET /cookswap/v1/search ───────────────────────────────────────────────────
app.get('/cookswap/v1/search', requireKey, async (req, res) => {
  const q     = (req.query.q || '').trim();
  const limit = Math.min(20, Math.max(1, parseInt(req.query.limit) || 5));

  if (!q || q.length > 200) {
    return res.status(400).json({ code: 'INVALID_QUERY', message: 'q parameter is required and must be 1–200 characters' });
  }

  try {
    const products = await search(q.toLowerCase(), limit);
    res.json({ query: q, retailer: RETAILER_SLUG, total: products.length, results: products });
  } catch (err) {
    console.error(err);
    res.status(500).json({ code: 'INTERNAL_ERROR', message: 'An unexpected error occurred' });
  }
});

// ── GET /cookswap/v1/health ───────────────────────────────────────────────────
app.get('/cookswap/v1/health', (req, res) => {
  res.json({ status: 'ok', version: '1.0.0' });
});

app.listen(3000, () => console.log('Listening on :3000'));

// ── Stub — replace with real DB query ────────────────────────────────────────

async function search(q, limit) {
  // Replace with a query against your product catalogue.
  //
  // Example (pg):
  //   const { rows } = await pool.query(
  //     `SELECT * FROM products WHERE to_tsvector('english', name) @@ plainto_tsquery($1) LIMIT $2`,
  //     [q, limit]
  //   );
  //   return rows.map(formatProduct);

  return [
    {
      id:        'SKU-004821',
      name:      'Example Product 250g',
      brand:     'Example Brand',
      unit:      '250g',
      in_stock:  true,
      price:     { amount: 1.89, currency: 'GBP', unit_price: '£7.56/kg' },
      image_url: 'https://cdn.your-retailer.com/SKU-004821.jpg',
      buy_url:   'https://www.your-retailer.com/products/SKU-004821',
      tags:      ['vegetarian'],
    },
  ];
}
