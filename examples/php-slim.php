<?php
/**
 * CookSwap Ingredient Search API — PHP reference implementation
 * Built on Slim 4. Install: composer require slim/slim slim/psr7
 *
 * Replace the stub search() function with a query against your product DB.
 */

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/vendor/autoload.php';

$app = AppFactory::create();

// ── Auth middleware ───────────────────────────────────────────────────────────
$app->add(function (Request $req, $handler) {
    $path = $req->getUri()->getPath();
    if ($path === '/cookswap/v1/health') {
        return $handler->handle($req);
    }
    $key = $req->getHeaderLine('X-CookSwap-Key');
    if (!validKey($key)) {
        $res = new \Slim\Psr7\Response();
        $res->getBody()->write(json_encode([
            'code'    => 'UNAUTHORIZED',
            'message' => 'Invalid or missing X-CookSwap-Key header',
        ]));
        return $res->withStatus(401)->withHeader('Content-Type', 'application/json');
    }
    return $handler->handle($req);
});

// ── Rate-limit middleware (sliding window per key, APCu) ──────────────────────
// Requires APCu (pecl install apcu). Falls back gracefully if unavailable.
$app->add(function (Request $req, $handler) {
    $key    = $req->getHeaderLine('X-CookSwap-Key');
    $window = 60;
    $limit  = 60;
    $remaining = $limit - 1;

    if (function_exists('apcu_fetch')) {
        $cache_key = 'rl_' . hash('sha256', $key);
        $count     = apcu_fetch($cache_key, $exists);
        if ($exists) {
            $count++;
            apcu_store($cache_key, $count, $window);
        } else {
            $count = 1;
            apcu_store($cache_key, 1, $window);
        }
        $remaining = max(0, $limit - $count);
    }

    $res = $handler->handle($req);
    return $res
        ->withHeader('X-RateLimit-Limit',     (string)$limit)
        ->withHeader('X-RateLimit-Remaining', (string)$remaining)
        ->withHeader('X-RateLimit-Reset',     (string)(time() + $window));
});

// ── GET /cookswap/v1/search ───────────────────────────────────────────────────
$app->get('/cookswap/v1/search', function (Request $req, Response $res) {
    $params = $req->getQueryParams();
    $q      = trim($params['q'] ?? '');
    $limit  = max(1, min(20, (int)($params['limit'] ?? 5)));

    if ($q === '' || strlen($q) > 200) {
        $res->getBody()->write(json_encode([
            'code'    => 'INVALID_QUERY',
            'message' => 'q parameter is required and must be 1–200 characters',
        ]));
        return $res->withStatus(400)->withHeader('Content-Type', 'application/json');
    }

    $products = search(strtolower($q), $limit);

    $res->getBody()->write(json_encode([
        'query'    => $q,
        'retailer' => 'your-retailer',
        'total'    => count($products),
        'results'  => $products,
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

    return $res->withHeader('Content-Type', 'application/json');
});

// ── GET /cookswap/v1/health ───────────────────────────────────────────────────
$app->get('/cookswap/v1/health', function (Request $req, Response $res) {
    $res->getBody()->write(json_encode(['status' => 'ok', 'version' => '1.0.0']));
    return $res->withHeader('Content-Type', 'application/json');
});

$app->run();

// ── Stubs — replace with real DB queries ─────────────────────────────────────

function validKey(string $key): bool
{
    $valid = getenv('COOKSWAP_API_KEY');
    if (!$valid || $key === '') return false;
    return hash_equals($valid, $key);
}

function search(string $q, int $limit): array
{
    // Replace with a query against your product catalogue.
    // The $q string has already been lowercased and trimmed.
    //
    // Example (PDO):
    //   $stmt = $pdo->prepare("SELECT * FROM products WHERE MATCH(name) AGAINST (?) LIMIT ?");
    //   $stmt->execute([$q, $limit]);
    //   return array_map('formatProduct', $stmt->fetchAll());

    return [
        [
            'id'        => 'SKU-004821',
            'name'      => 'Example Product 250g',
            'brand'     => 'Example Brand',
            'unit'      => '250g',
            'in_stock'  => true,
            'price'     => ['amount_minor' => 189, 'currency' => 'GBP', 'unit_price' => '£7.56/kg'],
            'image_url' => 'https://cdn.your-retailer.com/SKU-004821.jpg',
            'buy_url'   => 'https://www.your-retailer.com/products/SKU-004821',
            'tags'      => ['vegetarian'],
        ],
    ];
}
