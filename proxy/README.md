# CookSwap Proxy

A bridge layer that implements the CookSwap Ingredient Search API spec and routes requests to retailer adapters. It lets recipe apps use the standard today — via mock or real data — while retailers onboard officially.

## How it works

```
Recipe App
    │
    │  GET /cookswap/v1/search?q=butter&retailer=tesco
    ▼
CookSwap Proxy  ──────────────────────────────────────────────┐
    │                                                          │
    │  Looks up adapter for "tesco"                           │
    │  Calls adapter.search("butter", 5)                      │
    │  Injects affiliate token into every buy_url             │
    │  Returns SearchResponse JSON                             │
    │                                                          │
    ├── MockAdapter (demo / pre-launch)                        │
    ├── TescoAdapter (awaiting official key) ◄────────────────┘
    ├── OcadoAdapter (awaiting official key)
    └── SainsburysAdapter (awaiting official key)
```

## Deploying

Upload the `proxy/` folder to your server. Point `/cookswap/v1/` at `proxy/index.php` using a rewrite rule:

```nginx
# nginx
location /cookswap/v1/ {
    try_files $uri /cookswap/v1/index.php?$query_string;
}
```

```apache
# Apache / cPanel (.htaccess in proxy/)
RewriteEngine On
RewriteRule ^(.*)$ index.php [QSA,L]
```

## Environment variables

| Variable | Description | Default |
|---|---|---|
| `CS_API_KEYS` | Comma-separated valid API keys | `demo-key-12345` |
| `CS_AFFILIATE_PARAM` | Query parameter name to append to buy_url | `cs_ref` |
| `CS_AFFILIATE_ID` | Your affiliate / tracking ID | `cookswap` |
| `TESCO_API_KEY` | Tesco's official API key (when issued) | — |
| `OCADO_API_KEY` | Ocado's official API key (when issued) | — |

## Adding a retailer adapter

1. Create `proxy/adapters/YourRetailerAdapter.php` implementing `RetailerAdapter`
2. Add `require_once __DIR__ . '/adapters/YourRetailerAdapter.php';` to `index.php`
3. Register it: `'your-retailer' => YourRetailerAdapter::class` in the `ADAPTERS` array
4. Done — no changes needed in any recipe app using the standard

## Affiliate injection

Every `buy_url` in every response gets `?cs_ref=cookswap` appended automatically by the proxy before the JSON is returned. Set `CS_AFFILIATE_PARAM` and `CS_AFFILIATE_ID` in your server environment to customise. This is how the proxy self-funds while chasing official retailer adoption.

## Switching from proxy to official implementation

When a retailer ships their own endpoint conforming to the spec, update their adapter to call it directly, or remove them from the proxy's `ADAPTERS` registry and let the recipe app call their endpoint directly. Zero change to the spec, zero change to any recipe app.
