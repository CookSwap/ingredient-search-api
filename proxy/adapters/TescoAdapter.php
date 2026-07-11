<?php
require_once __DIR__ . '/RetailerAdapter.php';

/**
 * TescoAdapter — placeholder for Tesco's official implementation.
 *
 * When Tesco ships their endpoint, replace the search() method body
 * with a call to their API. Everything else (auth, affiliate injection,
 * rate limiting) is handled by the proxy router — this class only needs
 * to fetch and normalise product data.
 *
 * Official Tesco contact: developer@tesco.com
 * Their historical API (2009, now deprecated): api.tesco.com
 */
class TescoAdapter implements RetailerAdapter
{
    private string $retailer;
    private string $locale;
    private string $apiKey;

    public function __construct(string $retailer, string $locale)
    {
        $this->retailer = $retailer;
        $this->locale   = $locale;
        $this->apiKey   = getenv('TESCO_API_KEY') ?: '';
    }

    public function search(string $q, int $limit): array
    {
        // ── Replace this block with the real Tesco API call ───────────────────
        //
        // Example (once Tesco issues an official key):
        //
        // $url = "https://api.tesco.com/cookswap/v1/search?"
        //      . http_build_query(['q' => $q, 'limit' => $limit]);
        //
        // $ctx = stream_context_create(['http' => [
        //     'header'  => "X-CookSwap-Key: {$this->apiKey}\r\nAccept: application/json\r\n",
        //     'timeout' => 3,
        // ]]);
        //
        // $raw = @file_get_contents($url, false, $ctx);
        // if (!$raw) throw new RuntimeException('Tesco API unreachable');
        //
        // $data = json_decode($raw, true);
        // return $data['results'] ?? [];
        // ─────────────────────────────────────────────────────────────────────

        throw new RuntimeException('TescoAdapter not yet implemented — awaiting official API key');
    }

    public function addToBasket(string $product_id, int $quantity): ?array
    {
        return null; // basket integration not yet available
    }
}
