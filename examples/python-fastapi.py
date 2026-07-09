"""
CookSwap Ingredient Search API — Python / FastAPI reference implementation
Install: pip install fastapi uvicorn

Run: uvicorn python-fastapi:app --reload

Replace the stub search() function with a query against your product DB.
"""

import os
import time
from typing import Optional
from fastapi import FastAPI, Header, HTTPException, Query, Response
from pydantic import BaseModel, HttpUrl

app = FastAPI(title="CookSwap Ingredient Search API", version="1.0.0")

RETAILER_SLUG = "your-retailer"
VALID_KEYS    = {os.environ.get("COOKSWAP_API_KEY")}


# ── Schemas ───────────────────────────────────────────────────────────────────

class Price(BaseModel):
    amount:     float
    currency:   str
    unit_price: Optional[str] = None

class Product(BaseModel):
    id:        str
    name:      str
    buy_url:   HttpUrl
    brand:     Optional[str]      = None
    price:     Optional[Price]    = None
    unit:      Optional[str]      = None
    image_url: Optional[HttpUrl]  = None
    in_stock:  Optional[bool]     = None
    tags:      list[str]          = []

class SearchResponse(BaseModel):
    query:    str
    retailer: str
    total:    int
    results:  list[Product]

class HealthResponse(BaseModel):
    status:  str
    version: str


# ── Auth helper ───────────────────────────────────────────────────────────────

def require_key(x_cookswap_key: Optional[str] = Header(default=None)):
    if not x_cookswap_key or x_cookswap_key not in VALID_KEYS:
        raise HTTPException(
            status_code=401,
            detail={"code": "UNAUTHORIZED", "message": "Invalid or missing X-CookSwap-Key header"},
        )


# ── Endpoints ─────────────────────────────────────────────────────────────────

@app.get("/cookswap/v1/search", response_model=SearchResponse)
async def search_products(
    response: Response,
    q:     str = Query(..., min_length=1, max_length=200),
    limit: int = Query(default=5, ge=1, le=20),
    x_cookswap_key: Optional[str] = Header(default=None),
):
    require_key(x_cookswap_key)

    response.headers["X-RateLimit-Limit"]     = "60"
    response.headers["X-RateLimit-Remaining"] = "59"   # replace with real counter
    response.headers["X-RateLimit-Reset"]     = str(int(time.time()) + 60)

    products = await search(q.lower().strip(), limit)
    return SearchResponse(query=q, retailer=RETAILER_SLUG, total=len(products), results=products)


@app.get("/cookswap/v1/health", response_model=HealthResponse)
async def health_check():
    return HealthResponse(status="ok", version="1.0.0")


# ── Stub — replace with real DB query ────────────────────────────────────────

async def search(q: str, limit: int) -> list[Product]:
    """
    Replace with a query against your product catalogue.

    Example (asyncpg):
        rows = await conn.fetch(
            "SELECT * FROM products WHERE to_tsvector('english', name) @@ plainto_tsquery($1) LIMIT $2",
            q, limit
        )
        return [Product(**format_product(r)) for r in rows]
    """
    return [
        Product(
            id        = "SKU-004821",
            name      = "Example Product 250g",
            brand     = "Example Brand",
            unit      = "250g",
            in_stock  = True,
            price     = Price(amount=1.89, currency="GBP", unit_price="£7.56/kg"),
            image_url = "https://cdn.your-retailer.com/SKU-004821.jpg",
            buy_url   = "https://www.your-retailer.com/products/SKU-004821",
            tags      = ["vegetarian"],
        )
    ]
