<?php
require_once __DIR__ . '/RetailerAdapter.php';

/**
 * Greenfields — premium organic retailer (fictional demo)
 * Brand: green, organic-focused, higher price point
 */
class GreenfieldAdapter implements RetailerAdapter
{
    private string $retailer;
    private string $locale;

    private const CATALOG = [
        'butter' => [
            ['id'=>'GF-2201','name'=>'Greenfields Organic Unsalted Butter 250g','brand'=>'Greenfields','unit'=>'250g','price'=>2.79,'unit_price'=>'£11.16/kg','in_stock'=>true,'tags'=>['organic','vegetarian']],
            ['id'=>'GF-2202','name'=>'Greenfields Organic Salted Butter 250g','brand'=>'Greenfields','unit'=>'250g','price'=>2.79,'unit_price'=>'£11.16/kg','in_stock'=>true,'tags'=>['organic','vegetarian']],
            ['id'=>'GF-2203','name'=>'Clearwater Grass-Fed Butter 200g','brand'=>'Clearwater','unit'=>'200g','price'=>3.25,'unit_price'=>'£16.25/kg','in_stock'=>true,'tags'=>['organic','vegetarian']],
        ],
        'flour' => [
            ['id'=>'GF-3101','name'=>'Greenfields Organic Plain Flour 1kg','brand'=>'Greenfields','unit'=>'1kg','price'=>2.15,'unit_price'=>'£2.15/kg','in_stock'=>true,'tags'=>['organic','vegan']],
            ['id'=>'GF-3102','name'=>'Greenfields Organic Self-Raising Flour 1kg','brand'=>'Greenfields','unit'=>'1kg','price'=>2.15,'unit_price'=>'£2.15/kg','in_stock'=>true,'tags'=>['organic','vegan']],
            ['id'=>'GF-3103','name'=>'Stoneground Wholemeal Bread Flour 1.5kg','brand'=>'Mill & Stone','unit'=>'1.5kg','price'=>3.49,'unit_price'=>'£2.33/kg','in_stock'=>true,'tags'=>['organic','vegan']],
        ],
        'eggs' => [
            ['id'=>'GF-1001','name'=>'Greenfields Organic Free Range Eggs x6','brand'=>'Greenfields','unit'=>'x6','price'=>2.49,'unit_price'=>'41.5p each','in_stock'=>true,'tags'=>['organic','vegetarian']],
            ['id'=>'GF-1002','name'=>'Greenfields Organic Free Range Eggs x12','brand'=>'Greenfields','unit'=>'x12','price'=>4.49,'unit_price'=>'37.4p each','in_stock'=>true,'tags'=>['organic','vegetarian']],
            ['id'=>'GF-1003','name'=>'Heritage Breed Eggs x6','brand'=>'Henly Farm','unit'=>'x6','price'=>3.25,'unit_price'=>'54.2p each','in_stock'=>false,'tags'=>['organic','vegetarian']],
        ],
        'milk' => [
            ['id'=>'GF-4001','name'=>'Greenfields Organic Whole Milk 2 Pints','brand'=>'Greenfields','unit'=>'1.13L','price'=>1.55,'unit_price'=>'£1.37/L','in_stock'=>true,'tags'=>['organic','vegetarian']],
            ['id'=>'GF-4002','name'=>'Greenfields Organic Semi-Skimmed Milk 4 Pints','brand'=>'Greenfields','unit'=>'2.27L','price'=>2.75,'unit_price'=>'£1.21/L','in_stock'=>true,'tags'=>['organic','vegetarian']],
            ['id'=>'GF-4003','name'=>'Oat Barista Milk 1L','brand'=>'Nordic Oat','unit'=>'1L','price'=>2.20,'unit_price'=>'£2.20/L','in_stock'=>true,'tags'=>['vegan','dairy-free']],
        ],
        'cream' => [
            ['id'=>'GF-4201','name'=>'Greenfields Organic Double Cream 300ml','brand'=>'Greenfields','unit'=>'300ml','price'=>2.10,'unit_price'=>'£7.00/L','in_stock'=>true,'tags'=>['organic','vegetarian']],
            ['id'=>'GF-4202','name'=>'Greenfields Organic Whipping Cream 300ml','brand'=>'Greenfields','unit'=>'300ml','price'=>1.95,'unit_price'=>'£6.50/L','in_stock'=>true,'tags'=>['organic','vegetarian']],
        ],
        'chicken' => [
            ['id'=>'GF-5001','name'=>'Greenfields Organic Whole Chicken 1.5kg','brand'=>'Greenfields','unit'=>'~1.5kg','price'=>9.50,'unit_price'=>'£6.33/kg','in_stock'=>true,'tags'=>['organic']],
            ['id'=>'GF-5002','name'=>'Organic Chicken Breast Fillets 400g','brand'=>'Greenfields','unit'=>'400g','price'=>5.75,'unit_price'=>'£14.38/kg','in_stock'=>true,'tags'=>['organic']],
            ['id'=>'GF-5003','name'=>'Organic Chicken Thighs Bone-In 600g','brand'=>'Greenfields','unit'=>'600g','price'=>4.99,'unit_price'=>'£8.32/kg','in_stock'=>true,'tags'=>['organic']],
        ],
        'onion' => [
            ['id'=>'GF-6001','name'=>'Greenfields Organic Brown Onions 500g','brand'=>'Greenfields','unit'=>'500g','price'=>0.99,'unit_price'=>'£1.98/kg','in_stock'=>true,'tags'=>['organic','vegan']],
            ['id'=>'GF-6002','name'=>'Organic Red Onions 500g','brand'=>'Greenfields','unit'=>'500g','price'=>1.15,'unit_price'=>'£2.30/kg','in_stock'=>true,'tags'=>['organic','vegan']],
        ],
        'garlic' => [
            ['id'=>'GF-6101','name'=>'Organic Garlic Bulb x3','brand'=>'Greenfields','unit'=>'x3 bulbs','price'=>1.25,'unit_price'=>'','in_stock'=>true,'tags'=>['organic','vegan']],
            ['id'=>'GF-6102','name'=>'Smoked Organic Garlic x2','brand'=>'Greenfields','unit'=>'x2 bulbs','price'=>1.75,'unit_price'=>'','in_stock'=>true,'tags'=>['organic','vegan']],
        ],
        'tomato' => [
            ['id'=>'GF-7001','name'=>'Organic Cherry Tomatoes 300g','brand'=>'Greenfields','unit'=>'300g','price'=>2.10,'unit_price'=>'£7.00/kg','in_stock'=>true,'tags'=>['organic','vegan']],
            ['id'=>'GF-7002','name'=>'Organic Vine Tomatoes 500g','brand'=>'Greenfields','unit'=>'500g','price'=>1.99,'unit_price'=>'£3.98/kg','in_stock'=>true,'tags'=>['organic','vegan']],
            ['id'=>'GF-7003','name'=>'Organic Plum Tomatoes 400g Tin','brand'=>'Greenfields','unit'=>'400g','price'=>1.35,'unit_price'=>'£3.38/kg','in_stock'=>true,'tags'=>['organic','vegan']],
        ],
        'olive oil' => [
            ['id'=>'GF-8001','name'=>'Organic Extra Virgin Olive Oil 500ml','brand'=>'Greenfields','unit'=>'500ml','price'=>6.50,'unit_price'=>'£13.00/L','in_stock'=>true,'tags'=>['organic','vegan']],
            ['id'=>'GF-8002','name'=>'Cold-Pressed Olive Oil 750ml','brand'=>'Terra Verde','unit'=>'750ml','price'=>8.99,'unit_price'=>'£11.99/L','in_stock'=>true,'tags'=>['organic','vegan']],
        ],
        'lobster' => [
            ['id'=>'GF-9001','name'=>'Whole Cooked Lobster 400-500g','brand'=>'Ocean Catch','unit'=>'~450g','price'=>18.00,'unit_price'=>'£40.00/kg','in_stock'=>true,'tags'=>[]],
            ['id'=>'GF-9002','name'=>'Lobster Tails x2','brand'=>'Ocean Catch','unit'=>'x2','price'=>14.50,'unit_price'=>'','in_stock'=>false,'tags'=>[]],
        ],
    ];

    public function __construct(string $retailer, string $locale)
    {
        $this->retailer = $retailer;
        $this->locale   = $locale;
    }

    public function search(string $q, int $limit): array
    {
        $key     = strtolower(trim($q));
        $matches = self::CATALOG[$key] ?? $this->fuzzyMatch($key);
        return array_map(fn($p) => $this->format($p), array_slice($matches, 0, $limit));
    }

    private function fuzzyMatch(string $q): array
    {
        foreach (self::CATALOG as $key => $items) {
            if (str_contains($key, $q) || str_contains($q, $key)) return $items;
        }
        // generic fallback
        return [[
            'id'=>'GF-0000','name'=>"Greenfields — no results for \"$q\"",
            'brand'=>'Greenfields','unit'=>'','price'=>0,'unit_price'=>'',
            'in_stock'=>false,'tags'=>[],
        ]];
    }

    private function format(array $p): array
    {
        return [
            'id'        => $p['id'],
            'name'      => $p['name'],
            'brand'     => $p['brand'],
            'unit'      => $p['unit'],
            'price'     => ['amount_minor' => (int)round($p['price'] * 100), 'currency' => 'GBP', 'unit_price' => $p['unit_price']],
            'in_stock'  => $p['in_stock'],
            'tags'      => $p['tags'],
            'buy_url'   => 'https://www.greenfields-demo.com/products/' . strtolower($p['id']),
        ];
    }

    public function addToBasket(string $product_id, int $quantity): ?array
    {
        $token      = bin2hex(random_bytes(16));
        $expires_at = time() + 1800; // 30 min
        return [
            'checkout_url' => "https://www.greenfields-demo.com/checkout?token={$token}&qty={$quantity}&pid=" . urlencode($product_id),
            'basket_id'    => 'gf-bsk-' . strtolower($product_id),
            'expires_at'   => $expires_at,
        ];
    }
}
