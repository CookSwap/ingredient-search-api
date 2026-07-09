<?php
/**
 * CookSwap Ingredient Search API — Mock demo endpoint
 *
 * Drop this file on any PHP host at /cookswap/v1/ and it returns realistic
 * product data for common ingredients. Useful for showing a working demo
 * before a real retailer has implemented the spec.
 *
 * Endpoint:  GET /cookswap/v1/search?q=butter&limit=3
 * No auth required in demo mode (remove the comment block below to add a key).
 *
 * Upload to: /home/cookswap/public_html/cookswap/v1/search.php
 * (rename to search.php and add a /health.php alongside it)
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('X-RateLimit-Limit: 60');
header('X-RateLimit-Remaining: 59');
header('X-RateLimit-Reset: ' . (time() + 60));

// Optional: uncomment to require a demo key
// $key = $_SERVER['HTTP_X_COOKSWAP_KEY'] ?? '';
// if ($key !== 'demo-key-12345') {
//     http_response_code(401);
//     echo json_encode(['code' => 'UNAUTHORIZED', 'message' => 'Invalid or missing X-CookSwap-Key header']);
//     exit;
// }

$q     = trim(strtolower($_GET['q'] ?? ''));
$limit = max(1, min(20, (int)($_GET['limit'] ?? 5)));

if ($q === '' || strlen($q) > 200) {
    http_response_code(400);
    echo json_encode(['code' => 'INVALID_QUERY', 'message' => 'q parameter is required and must be 1–200 characters']);
    exit;
}

// ── Mock product catalogue ────────────────────────────────────────────────────
// Keys are match patterns (substring of the ingredient query).
// In a real implementation this is replaced by a full-text search against your DB.

$catalogue = [
    'butter' => [
        ['id'=>'SKU-1001','name'=>'Anchor Unsalted Butter 250g','brand'=>'Anchor','unit'=>'250g','price'=>['amount'=>1.89,'currency'=>'GBP','unit_price'=>'£7.56/kg'],'in_stock'=>true,'tags'=>['vegetarian'],'image_url'=>'https://picsum.photos/seed/butter1/120/120','buy_url'=>'https://www.example-retailer.com/products/SKU-1001'],
        ['id'=>'SKU-1002','name'=>'Lurpak Slightly Salted Butter 400g','brand'=>'Lurpak','unit'=>'400g','price'=>['amount'=>2.75,'currency'=>'GBP','unit_price'=>'£6.88/kg'],'in_stock'=>true,'tags'=>['vegetarian'],'image_url'=>'https://picsum.photos/seed/butter2/120/120','buy_url'=>'https://www.example-retailer.com/products/SKU-1002'],
        ['id'=>'SKU-1003','name'=>'Own Label Unsalted Butter 250g','brand'=>null,'unit'=>'250g','price'=>['amount'=>1.35,'currency'=>'GBP','unit_price'=>'£5.40/kg'],'in_stock'=>true,'tags'=>['vegetarian'],'image_url'=>'https://picsum.photos/seed/butter3/120/120','buy_url'=>'https://www.example-retailer.com/products/SKU-1003'],
    ],
    'flour' => [
        ['id'=>'SKU-2001','name'=>'Allinson Plain White Flour 1kg','brand'=>'Allinson','unit'=>'1kg','price'=>['amount'=>1.10,'currency'=>'GBP','unit_price'=>'£1.10/kg'],'in_stock'=>true,'tags'=>['vegan','vegetarian'],'image_url'=>'https://picsum.photos/seed/flour1/120/120','buy_url'=>'https://www.example-retailer.com/products/SKU-2001'],
        ['id'=>'SKU-2002','name'=>'Allinson Self Raising Flour 1kg','brand'=>'Allinson','unit'=>'1kg','price'=>['amount'=>1.10,'currency'=>'GBP','unit_price'=>'£1.10/kg'],'in_stock'=>true,'tags'=>['vegan','vegetarian'],'image_url'=>'https://picsum.photos/seed/flour2/120/120','buy_url'=>'https://www.example-retailer.com/products/SKU-2002'],
        ['id'=>'SKU-2003','name'=>'Own Label Plain Flour 1.5kg','brand'=>null,'unit'=>'1.5kg','price'=>['amount'=>0.85,'currency'=>'GBP','unit_price'=>'£0.57/kg'],'in_stock'=>true,'tags'=>['vegan','vegetarian'],'image_url'=>'https://picsum.photos/seed/flour3/120/120','buy_url'=>'https://www.example-retailer.com/products/SKU-2003'],
    ],
    'egg' => [
        ['id'=>'SKU-3001','name'=>'Free Range Large Eggs 6 Pack','brand'=>null,'unit'=>'6 pack','price'=>['amount'=>2.10,'currency'=>'GBP','unit_price'=>'35p each'],'in_stock'=>true,'tags'=>['vegetarian'],'image_url'=>'https://picsum.photos/seed/eggs1/120/120','buy_url'=>'https://www.example-retailer.com/products/SKU-3001'],
        ['id'=>'SKU-3002','name'=>'Clarence Court Burford Brown Eggs 6 Pack','brand'=>'Clarence Court','unit'=>'6 pack','price'=>['amount'=>3.50,'currency'=>'GBP','unit_price'=>'58p each'],'in_stock'=>true,'tags'=>['vegetarian'],'image_url'=>'https://picsum.photos/seed/eggs2/120/120','buy_url'=>'https://www.example-retailer.com/products/SKU-3002'],
        ['id'=>'SKU-3003','name'=>'Free Range Medium Eggs 12 Pack','brand'=>null,'unit'=>'12 pack','price'=>['amount'=>3.25,'currency'=>'GBP','unit_price'=>'27p each'],'in_stock'=>true,'tags'=>['vegetarian'],'image_url'=>'https://picsum.photos/seed/eggs3/120/120','buy_url'=>'https://www.example-retailer.com/products/SKU-3003'],
    ],
    'cream' => [
        ['id'=>'SKU-4001','name'=>'Elmlea Double Cream 300ml','brand'=>'Elmlea','unit'=>'300ml','price'=>['amount'=>1.40,'currency'=>'GBP','unit_price'=>'£4.67/l'],'in_stock'=>true,'tags'=>['vegetarian'],'image_url'=>'https://picsum.photos/seed/cream1/120/120','buy_url'=>'https://www.example-retailer.com/products/SKU-4001'],
        ['id'=>'SKU-4002','name'=>'Anchor Double Cream 300ml','brand'=>'Anchor','unit'=>'300ml','price'=>['amount'=>1.65,'currency'=>'GBP','unit_price'=>'£5.50/l'],'in_stock'=>true,'tags'=>['vegetarian'],'image_url'=>'https://picsum.photos/seed/cream2/120/120','buy_url'=>'https://www.example-retailer.com/products/SKU-4002'],
        ['id'=>'SKU-4003','name'=>'Own Label Single Cream 300ml','brand'=>null,'unit'=>'300ml','price'=>['amount'=>0.95,'currency'=>'GBP','unit_price'=>'£3.17/l'],'in_stock'=>true,'tags'=>['vegetarian'],'image_url'=>'https://picsum.photos/seed/cream3/120/120','buy_url'=>'https://www.example-retailer.com/products/SKU-4003'],
    ],
    'onion' => [
        ['id'=>'SKU-5001','name'=>'Brown Onions 1kg','brand'=>null,'unit'=>'1kg','price'=>['amount'=>0.79,'currency'=>'GBP','unit_price'=>'79p/kg'],'in_stock'=>true,'tags'=>['vegan','vegetarian'],'image_url'=>'https://picsum.photos/seed/onion1/120/120','buy_url'=>'https://www.example-retailer.com/products/SKU-5001'],
        ['id'=>'SKU-5002','name'=>'Red Onions 3 Pack','brand'=>null,'unit'=>'3 pack','price'=>['amount'=>0.65,'currency'=>'GBP','unit_price'=>'22p each'],'in_stock'=>true,'tags'=>['vegan','vegetarian'],'image_url'=>'https://picsum.photos/seed/onion2/120/120','buy_url'=>'https://www.example-retailer.com/products/SKU-5002'],
        ['id'=>'SKU-5003','name'=>'Spring Onions Bunch','brand'=>null,'unit'=>'bunch','price'=>['amount'=>0.55,'currency'=>'GBP','unit_price'=>'55p each'],'in_stock'=>true,'tags'=>['vegan','vegetarian'],'image_url'=>'https://picsum.photos/seed/onion3/120/120','buy_url'=>'https://www.example-retailer.com/products/SKU-5003'],
    ],
    'chicken' => [
        ['id'=>'SKU-6001','name'=>'British Chicken Breast Fillets 400g','brand'=>null,'unit'=>'400g','price'=>['amount'=>3.50,'currency'=>'GBP','unit_price'=>'£8.75/kg'],'in_stock'=>true,'tags'=>[],'image_url'=>'https://picsum.photos/seed/chicken1/120/120','buy_url'=>'https://www.example-retailer.com/products/SKU-6001'],
        ['id'=>'SKU-6002','name'=>'Organic Chicken Breast Fillets 300g','brand'=>null,'unit'=>'300g','price'=>['amount'=>4.75,'currency'=>'GBP','unit_price'=>'£15.83/kg'],'in_stock'=>true,'tags'=>['organic'],'image_url'=>'https://picsum.photos/seed/chicken2/120/120','buy_url'=>'https://www.example-retailer.com/products/SKU-6002'],
        ['id'=>'SKU-6003','name'=>'British Whole Chicken 1.5kg','brand'=>null,'unit'=>'1.5kg','price'=>['amount'=>4.25,'currency'=>'GBP','unit_price'=>'£2.83/kg'],'in_stock'=>true,'tags'=>[],'image_url'=>'https://picsum.photos/seed/chicken3/120/120','buy_url'=>'https://www.example-retailer.com/products/SKU-6003'],
    ],
    'garlic' => [
        ['id'=>'SKU-7001','name'=>'Garlic Bulb Single','brand'=>null,'unit'=>'each','price'=>['amount'=>0.40,'currency'=>'GBP','unit_price'=>'40p each'],'in_stock'=>true,'tags'=>['vegan','vegetarian'],'image_url'=>'https://picsum.photos/seed/garlic1/120/120','buy_url'=>'https://www.example-retailer.com/products/SKU-7001'],
        ['id'=>'SKU-7002','name'=>'Garlic Bulbs 3 Pack','brand'=>null,'unit'=>'3 pack','price'=>['amount'=>0.89,'currency'=>'GBP','unit_price'=>'30p each'],'in_stock'=>true,'tags'=>['vegan','vegetarian'],'image_url'=>'https://picsum.photos/seed/garlic2/120/120','buy_url'=>'https://www.example-retailer.com/products/SKU-7002'],
        ['id'=>'SKU-7003','name'=>'Lazy Garlic in Oil 290g Jar','brand'=>'Lazy','unit'=>'290g','price'=>['amount'=>1.85,'currency'=>'GBP','unit_price'=>'£6.38/kg'],'in_stock'=>true,'tags'=>['vegan','vegetarian'],'image_url'=>'https://picsum.photos/seed/garlic3/120/120','buy_url'=>'https://www.example-retailer.com/products/SKU-7003'],
    ],
    'olive oil' => [
        ['id'=>'SKU-8001','name'=>'Filippo Berio Extra Virgin Olive Oil 500ml','brand'=>'Filippo Berio','unit'=>'500ml','price'=>['amount'=>4.50,'currency'=>'GBP','unit_price'=>'£9.00/l'],'in_stock'=>true,'tags'=>['vegan','vegetarian'],'image_url'=>'https://picsum.photos/seed/oliveoil1/120/120','buy_url'=>'https://www.example-retailer.com/products/SKU-8001'],
        ['id'=>'SKU-8002','name'=>'Own Label Olive Oil 1L','brand'=>null,'unit'=>'1L','price'=>['amount'=>3.20,'currency'=>'GBP','unit_price'=>'£3.20/l'],'in_stock'=>true,'tags'=>['vegan','vegetarian'],'image_url'=>'https://picsum.photos/seed/oliveoil2/120/120','buy_url'=>'https://www.example-retailer.com/products/SKU-8002'],
    ],
    'tomato' => [
        ['id'=>'SKU-9001','name'=>'Vine Tomatoes 500g','brand'=>null,'unit'=>'500g','price'=>['amount'=>1.10,'currency'=>'GBP','unit_price'=>'£2.20/kg'],'in_stock'=>true,'tags'=>['vegan','vegetarian'],'image_url'=>'https://picsum.photos/seed/tomato1/120/120','buy_url'=>'https://www.example-retailer.com/products/SKU-9001'],
        ['id'=>'SKU-9002','name'=>'Chopped Tomatoes in Juice 400g Tin','brand'=>null,'unit'=>'400g','price'=>['amount'=>0.45,'currency'=>'GBP','unit_price'=>'£1.13/kg'],'in_stock'=>true,'tags'=>['vegan','vegetarian'],'image_url'=>'https://picsum.photos/seed/tomato2/120/120','buy_url'=>'https://www.example-retailer.com/products/SKU-9002'],
        ['id'=>'SKU-9003','name'=>'Cherry Tomatoes 300g','brand'=>null,'unit'=>'300g','price'=>['amount'=>1.25,'currency'=>'GBP','unit_price'=>'£4.17/kg'],'in_stock'=>true,'tags'=>['vegan','vegetarian'],'image_url'=>'https://picsum.photos/seed/tomato3/120/120','buy_url'=>'https://www.example-retailer.com/products/SKU-9003'],
    ],
    'milk' => [
        ['id'=>'SKU-10001','name'=>'Whole Milk 2 Pints','brand'=>null,'unit'=>'1.136L','price'=>['amount'=>1.35,'currency'=>'GBP','unit_price'=>'£1.19/l'],'in_stock'=>true,'tags'=>['vegetarian'],'image_url'=>'https://picsum.photos/seed/milk1/120/120','buy_url'=>'https://www.example-retailer.com/products/SKU-10001'],
        ['id'=>'SKU-10002','name'=>'Semi-Skimmed Milk 4 Pints','brand'=>null,'unit'=>'2.272L','price'=>['amount'=>1.55,'currency'=>'GBP','unit_price'=>'68p/l'],'in_stock'=>true,'tags'=>['vegetarian'],'image_url'=>'https://picsum.photos/seed/milk2/120/120','buy_url'=>'https://www.example-retailer.com/products/SKU-10002'],
        ['id'=>'SKU-10003','name'=>'Oat Milk Barista 1L','brand'=>'Oatly','unit'=>'1L','price'=>['amount'=>2.00,'currency'=>'GBP','unit_price'=>'£2.00/l'],'in_stock'=>true,'tags'=>['vegan','vegetarian','dairy-free'],'image_url'=>'https://picsum.photos/seed/milk3/120/120','buy_url'=>'https://www.example-retailer.com/products/SKU-10003'],
    ],
];

// ── Match query against catalogue ─────────────────────────────────────────────
$results = [];
foreach ($catalogue as $keyword => $products) {
    if (str_contains($q, $keyword) || str_contains($keyword, $q)) {
        $results = $products;
        break;
    }
}

// Fallback: generic results for unknown ingredients
if (empty($results)) {
    $results = [
        ['id'=>'SKU-GEN-1','name'=>ucwords($q) . ' — see all results','brand'=>null,'unit'=>'various','price'=>['amount'=>0.00,'currency'=>'GBP','unit_price'=>null],'in_stock'=>true,'tags'=>[],'image_url'=>'https://picsum.photos/seed/' . urlencode($q) . '/120/120','buy_url'=>'https://www.example-retailer.com/search?q=' . urlencode($q)],
    ];
}

$results = array_slice($results, 0, $limit);

echo json_encode([
    'query'    => $_GET['q'],
    'retailer' => 'demo',
    'total'    => count($results),
    'results'  => $results,
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
