<?php
/**
 * CookSwap Ingredient Search API — Bridge Proxy
 *
 * Implements the open standard spec and routes requests to retailer adapters.
 * While retailers are onboarding officially, adapters can fetch from any source
 * (unofficial endpoints, cached data, mock data). Once a retailer ships an
 * official implementation, swap their adapter to point at it — zero change to
 * any recipe app already using the standard.
 *
 * Deploy at: https://cookswap.com/cookswap/v1/
 *
 * Route:  GET /cookswap/v1/search?q=butter&retailer=tesco&limit=5
 *         GET /cookswap/v1/health
 *
 * Affiliate injection: set CS_AFFILIATE_PARAM and CS_AFFILIATE_ID in env
 * and every buy_url returned will carry your tracking parameter automatically.
 *
 * Add a new retailer: create a class in /adapters/ implementing RetailerAdapter,
 * register it in ADAPTERS below, done.
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('X-Powered-By: CookSwap-Ingredient-API/1.0');

require_once __DIR__ . '/adapters/RetailerAdapter.php';
require_once __DIR__ . '/adapters/MockAdapter.php';
require_once __DIR__ . '/adapters/GreenfieldAdapter.php';
require_once __DIR__ . '/adapters/PriceRightAdapter.php';
require_once __DIR__ . '/adapters/FreshMarketAdapter.php';
// Uncomment as real adapters are built:
// require_once __DIR__ . '/adapters/TescoAdapter.php';
// require_once __DIR__ . '/adapters/OcadoAdapter.php';

// ── Registry ──────────────────────────────────────────────────────────────────
const ADAPTERS = [
    // Fictional demo retailers — full product catalogs
    'greenfields'  => GreenfieldAdapter::class,
    'priceright'   => PriceRightAdapter::class,
    'freshmarket'  => FreshMarketAdapter::class,
    // Generic mock
    'demo'         => MockAdapter::class,
    // Real retailer stubs (mock data until official adapters built)
    'tesco'        => MockAdapter::class,
    'ocado'        => MockAdapter::class,
    'sainsburys'   => MockAdapter::class,
    'asda'         => MockAdapter::class,
    'morrisons'    => MockAdapter::class,
    'waitrose'     => MockAdapter::class,
];

// ── Affiliate config ──────────────────────────────────────────────────────────
// Set these as environment variables on your server, or replace with literals.
// Every buy_url returned will have ?{PARAM}={ID} appended automatically.
define('CS_AFFILIATE_PARAM', getenv('CS_AFFILIATE_PARAM') ?: 'cs_ref');
define('CS_AFFILIATE_ID',    getenv('CS_AFFILIATE_ID')    ?: 'cookswap');

// ── Auth ──────────────────────────────────────────────────────────────────────
define('VALID_KEYS', array_filter(explode(',', getenv('CS_API_KEYS') ?: 'demo-key-12345')));

// ── Routing ───────────────────────────────────────────────────────────────────
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
// Strip optional /cookswap/v1 prefix (for subdirectory deploys) or /v1 prefix
$path = preg_replace('#^(/cookswap/v1|/v1)#', '', $path);
$path = rtrim($path, '/') ?: '/';

if ($path === '/health') {
    echo json_encode(['status' => 'ok', 'version' => '1.0.0', 'adapters' => array_keys(ADAPTERS)]);
    exit;
}

if ($path === '/basket/add') {
    // Auth
    $key = $_SERVER['HTTP_X_COOKSWAP_KEY'] ?? '';
    if (!in_array($key, VALID_KEYS, true)) {
        http_response_code(401);
        echo json_encode(['code' => 'UNAUTHORIZED', 'message' => 'Invalid or missing X-CookSwap-Key header']);
        exit;
    }

    $body      = json_decode(file_get_contents('php://input'), true) ?? [];
    $product_id = trim($body['product_id'] ?? '');
    $quantity   = max(1, min(99, (int)($body['quantity'] ?? 1)));
    $retailer   = strtolower(trim($_GET['retailer'] ?? 'demo'));

    if (!$product_id) {
        http_response_code(400);
        echo json_encode(['code' => 'INVALID_REQUEST', 'message' => 'product_id is required']);
        exit;
    }

    if (!array_key_exists($retailer, ADAPTERS)) {
        http_response_code(400);
        echo json_encode(['code' => 'UNKNOWN_RETAILER', 'message' => "Unknown retailer '$retailer'"]);
        exit;
    }

    try {
        $adapterClass = ADAPTERS[$retailer];
        $adapter      = new $adapterClass($retailer, 'en-GB');
        $result       = $adapter->addToBasket($product_id, $quantity);

        if ($result === null) {
            http_response_code(501);
            echo json_encode([
                'code'    => 'NOT_IMPLEMENTED',
                'message' => 'Basket API not available for this retailer — use buy_url from /search instead',
            ]);
            exit;
        }

        // Inject affiliate param into checkout_url
        if (!empty($result['checkout_url'])) {
            $sep = str_contains($result['checkout_url'], '?') ? '&' : '?';
            $result['checkout_url'] .= $sep . CS_AFFILIATE_PARAM . '=' . urlencode(CS_AFFILIATE_ID);
        }

        echo json_encode($result, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

    } catch (Throwable $e) {
        http_response_code(500);
        echo json_encode(['code' => 'INTERNAL_ERROR', 'message' => 'An unexpected error occurred']);
        error_log('[CookSwap proxy basket] ' . $e->getMessage());
    }
    exit;
}

if ($path !== '/search') {
    http_response_code(404);
    echo json_encode(['code' => 'NOT_FOUND', 'message' => "Unknown path: $path"]);
    exit;
}

// ── Auth check ────────────────────────────────────────────────────────────────
$key = $_SERVER['HTTP_X_COOKSWAP_KEY'] ?? '';
if (!in_array($key, VALID_KEYS, true)) {
    http_response_code(401);
    echo json_encode(['code' => 'UNAUTHORIZED', 'message' => 'Invalid or missing X-CookSwap-Key header']);
    exit;
}

// ── Parse params ─────────────────────────────────────────────────────────────
$q        = trim($_GET['q'] ?? '');
$retailer = strtolower(trim($_GET['retailer'] ?? 'demo'));
$limit    = max(1, min(20, (int)($_GET['limit'] ?? 5)));
$locale   = $_GET['locale'] ?? 'en-GB';

if ($q === '' || strlen($q) > 200) {
    http_response_code(400);
    echo json_encode(['code' => 'INVALID_QUERY', 'message' => 'q parameter is required and must be 1–200 characters']);
    exit;
}

if (!array_key_exists($retailer, ADAPTERS)) {
    http_response_code(400);
    echo json_encode([
        'code'    => 'UNKNOWN_RETAILER',
        'message' => "Unknown retailer '$retailer'. Supported: " . implode(', ', array_keys(ADAPTERS)),
    ]);
    exit;
}

// ── Rate limit headers (add real counter per key in production) ───────────────
header('X-RateLimit-Limit: 60');
header('X-RateLimit-Remaining: 59');
header('X-RateLimit-Reset: ' . (time() + 60));

// ── Call adapter ──────────────────────────────────────────────────────────────
try {
    $adapterClass = ADAPTERS[$retailer];
    /** @var RetailerAdapter $adapter */
    $adapter = new $adapterClass($retailer, $locale);
    $products = $adapter->search(strtolower($q), $limit);

    // ── Inject affiliate token into every buy_url ─────────────────────────────
    foreach ($products as &$product) {
        if (!empty($product['buy_url'])) {
            $sep = str_contains($product['buy_url'], '?') ? '&' : '?';
            $product['buy_url'] .= $sep . CS_AFFILIATE_PARAM . '=' . urlencode(CS_AFFILIATE_ID);
        }
    }
    unset($product);

    echo json_encode([
        'query'    => $_GET['q'],
        'retailer' => $retailer,
        'total'    => count($products),
        'results'  => $products,
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['code' => 'INTERNAL_ERROR', 'message' => 'An unexpected error occurred']);
    error_log('[CookSwap proxy] ' . $e->getMessage());
}
