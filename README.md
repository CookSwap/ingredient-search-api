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

**CookSwap does not replace your ecommerce platform. It sends customers directly to your existing product pages.**

---

## Why retailers implement this

This is not a data-sharing agreement. You control exactly what products are returned, how they are ranked, and where the buy link points. The standard defines the interface — your catalogue, your pricing, your pages.

| Benefit | Detail |
|---|---|
| **New customer acquisition** | Recipe users are in active meal-planning mode — high purchase intent, not casual browsing |
| **Recipe discovery traffic** | Your products appear inside the cooking experience, not after a separate shopping trip |
| **Reduced search friction** | Users arrive at your product page with one tap, not via a generic search that may return competitors |
| **Basket conversion** | Ingredient-by-ingredient shopping naturally builds a multi-item basket |
| **Brand-controlled placement** | You decide which products surface — own-label, promoted lines, or full catalogue |
| **No marketplace intermediary** | No commission to a third-party platform. The user is yours from the moment they tap |

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

## Structured ingredient context (v1.1 draft)

A plain text query (`q=butter`) works for simple cases. For better matching, apps may pass structured ingredient context as additional parameters, allowing retailers to rank results more precisely:

```
GET /cookswap/v1/search
  ?q=butter
  &quantity=250
  &unit=g
  &dietary=vegetarian
  &recipe_role=baking
```

This helps resolve ambiguities like:
- `flour` → plain, self-raising, or bread flour? (`recipe_role=baking` hints at plain)
- `cream` → single, double, or whipping? (quantity and recipe role help)
- `chicken breast` → fresh, frozen, organic? (dietary tags help)

These parameters are **optional** in v1.0 and ignored if not understood. The structured vocabulary will be formalised in v1.1. Feedback welcome — open an issue.

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

## Attribution and referral tracking

You may embed your own tracking or referral parameters in `buy_url` — calling apps will not strip or modify them. Commercial arrangements between retailers and app developers are outside the scope of this spec; possible models include:

- **Referral tracking** — tag the URL and measure attributed sales in your own analytics
- **Affiliate commission** — pay a percentage of referred basket value
- **Sponsored placement** — promote specific products in search results
- **Retailer subscription** — a flat fee for premium placement across all adopting apps
- **Analytics service** — aggregate recipe-to-basket data as a commercial product

The spec is commercially neutral. How you monetise the traffic is your decision.

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

## The standards play

This spec is to recipe-to-basket what Open Banking is to financial data — a common interface that lets retailers appear inside third-party experiences without giving up control of their ecommerce platform. One implementation; every adopting app. The alternative is every retailer negotiating bespoke integrations with every recipe app, indefinitely.

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
