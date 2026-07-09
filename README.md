# CookSwap Ingredient Search API

An open standard for ingredient-to-product search. Retailers implement one small API; recipe apps call it to let users buy ingredients directly from a recipe.

**MIT licensed · OpenAPI 3.1 · Language-agnostic**

---

## The problem

Recipe apps want to send users to buy ingredients. Today they rely on hard-coded deep-links to supermarket search pages — fragile, unbranded, and showing zero product data. Retailers have no way to surface their own results in third-party contexts.

## The solution

A single, simple endpoint. Retailers implement it; recipe apps call it. Users see real product names, prices, and images — and land on the retailer's own product page to complete their purchase.

```
GET /cookswap/v1/search?q=unsalted+butter&limit=5
X-CookSwap-Key: <your-key>

→ { results: [{ name, price, image_url, buy_url, ... }] }
```

---

## Endpoints

| Method | Path | Auth | Description |
|--------|------|------|-------------|
| GET | `/cookswap/v1/search` | API key | Search products by ingredient name |
| GET | `/cookswap/v1/health` | None | Health check |

Full spec: [`openapi.yaml`](./openapi.yaml)  
Interactive docs: [cookswap.com/api](https://cookswap.com/api)

---

## Quick start (implementing)

**1. Mount the two endpoints** at `https://your-domain/cookswap/v1/`

**2. Issue an API key** to each client app and validate the `X-CookSwap-Key` header on every `/search` request.

**3. Return well-formed JSON** — the `id`, `name`, and `buy_url` fields are the only required fields on each product object. Everything else is recommended.

**4. Include rate-limit headers** on every response:

```
X-RateLimit-Limit:     60
X-RateLimit-Remaining: 47
X-RateLimit-Reset:     1720523400
```

**5. Respond within 2 seconds** — calling apps time out at 3 s.

See [`examples/`](./examples/) for reference implementations in PHP, Node.js, and Python.

---

## Response example

```json
{
  "query": "unsalted butter",
  "retailer": "your-retailer",
  "total": 14,
  "results": [
    {
      "id": "SKU-004821",
      "name": "Anchor Unsalted Butter 250g",
      "brand": "Anchor",
      "unit": "250g",
      "in_stock": true,
      "price": {
        "amount": 1.89,
        "currency": "GBP",
        "unit_price": "£7.56/kg"
      },
      "image_url": "https://cdn.your-retailer.com/products/SKU-004821.jpg",
      "buy_url": "https://www.your-retailer.com/products/SKU-004821",
      "tags": ["vegetarian"]
    }
  ]
}
```

---

## Authentication

You issue API keys to client apps (one key per app). Send the key in the `X-CookSwap-Key` request header. Keys are revocable per client independently of one another.

---

## Affiliate links

You may embed your own affiliate or tracking parameters in `buy_url` — calling apps will not strip or modify them. If you'd like CookSwap to append its own affiliate tag, agree the parameter name during onboarding.

---

## Implementer checklist

- [ ] Endpoints live at `https://your-domain/cookswap/v1/`
- [ ] `X-CookSwap-Key` validated on every `/search` request
- [ ] `Content-Type: application/json` on all responses
- [ ] All three rate-limit headers present on every response
- [ ] Query normalised before searching (lowercase, trim, strip quantities)
- [ ] All `buy_url` values are HTTPS
- [ ] `/health` returns `200 { "status": "ok" }` with no auth required
- [ ] P95 response time under 2 seconds

---

## Getting listed on CookSwap

Once your implementation is live, open an issue using the **New Retailer** template. We'll validate your endpoint and add you to the retailer selector in the CookSwap app.

Contact: [api@cookswap.com](mailto:api@cookswap.com)

---

## Similar projects & prior art

| Project | What it does | Difference |
|---------|-------------|------------|
| [Open Food Facts](https://openfoodfacts.github.io/openfoodfacts-server/api/) | Open database of food products (barcodes, nutrition, ingredients) | Product intelligence, not retailer shopping — no buy links or live pricing |
| [Kroger Products API](https://developer.kroger.com/reference/) | Kroger's own product search with cart integration | Proprietary, US-only, single retailer |
| [productDNA](https://www.gs1uk.org/services/productdna) | GS1-backed shared product catalogue used by UK supermarkets | Catalogue sync between retailers, not a recipe-context shopping API |
| Tesco API (2009, deprecated) | Tesco's original public product API | No longer available |

**This spec fills the gap**: a lightweight, open, recipe-context standard that any retailer can implement in an afternoon, returning live prices and buy links.

---

## Contributing

See [CONTRIBUTING.md](./CONTRIBUTING.md). All contributions welcome — spec extensions, example implementations, SDKs, validators.

---

## License

MIT © [CookSwap](https://cookswap.com)
