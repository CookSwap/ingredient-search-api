<?php
require_once __DIR__ . '/RetailerAdapter.php';

/**
 * PriceRight — budget/value retailer (fictional demo)
 * Brand: blue & yellow, own-brand focus, lowest price point
 */
class PriceRightAdapter implements RetailerAdapter
{
    private string $retailer;
    private string $locale;

    private const CATALOG = [
        'butter' => [
            ['id'=>'PR-B001','name'=>'PriceRight Unsalted Butter 250g','brand'=>'PriceRight','unit'=>'250g','price'=>0.89,'unit_price'=>'£3.56/kg','in_stock'=>true,'tags'=>['vegetarian']],
            ['id'=>'PR-B002','name'=>'PriceRight Salted Butter 250g','brand'=>'PriceRight','unit'=>'250g','price'=>0.89,'unit_price'=>'£3.56/kg','in_stock'=>true,'tags'=>['vegetarian']],
            ['id'=>'PR-B003','name'=>'PriceRight Spreadable Butter 500g','brand'=>'PriceRight','unit'=>'500g','price'=>1.49,'unit_price'=>'£2.98/kg','in_stock'=>true,'tags'=>['vegetarian']],
            ['id'=>'PR-B004','name'=>'Anchor Butter 250g','brand'=>'Anchor','unit'=>'250g','price'=>1.55,'unit_price'=>'£6.20/kg','in_stock'=>true,'tags'=>['vegetarian']],
        ],
        'flour' => [
            ['id'=>'PR-F001','name'=>'PriceRight Plain Flour 1.5kg','brand'=>'PriceRight','unit'=>'1.5kg','price'=>0.75,'unit_price'=>'50p/kg','in_stock'=>true,'tags'=>['vegan']],
            ['id'=>'PR-F002','name'=>'PriceRight Self-Raising Flour 1.5kg','brand'=>'PriceRight','unit'=>'1.5kg','price'=>0.75,'unit_price'=>'50p/kg','in_stock'=>true,'tags'=>['vegan']],
            ['id'=>'PR-F003','name'=>'Homepride Plain Flour 1kg','brand'=>'Homepride','unit'=>'1kg','price'=>0.99,'unit_price'=>'99p/kg','in_stock'=>true,'tags'=>['vegan']],
        ],
        'eggs' => [
            ['id'=>'PR-E001','name'=>'PriceRight Medium Free Range Eggs x6','brand'=>'PriceRight','unit'=>'x6','price'=>1.19,'unit_price'=>'19.8p each','in_stock'=>true,'tags'=>['vegetarian']],
            ['id'=>'PR-E002','name'=>'PriceRight Large Free Range Eggs x12','brand'=>'PriceRight','unit'=>'x12','price'=>2.09,'unit_price'=>'17.4p each','in_stock'=>true,'tags'=>['vegetarian']],
            ['id'=>'PR-E003','name'=>'PriceRight Mixed Weight Eggs x15','brand'=>'PriceRight','unit'=>'x15','price'=>2.39,'unit_price'=>'15.9p each','in_stock'=>true,'tags'=>['vegetarian']],
        ],
        'milk' => [
            ['id'=>'PR-M001','name'=>'PriceRight Whole Milk 4 Pints','brand'=>'PriceRight','unit'=>'2.27L','price'=>1.09,'unit_price'=>'48p/L','in_stock'=>true,'tags'=>['vegetarian']],
            ['id'=>'PR-M002','name'=>'PriceRight Semi-Skimmed Milk 6 Pints','brand'=>'PriceRight','unit'=>'3.41L','price'=>1.49,'unit_price'=>'44p/L','in_stock'=>true,'tags'=>['vegetarian']],
            ['id'=>'PR-M003','name'=>'PriceRight Skimmed Milk 4 Pints','brand'=>'PriceRight','unit'=>'2.27L','price'=>0.99,'unit_price'=>'44p/L','in_stock'=>true,'tags'=>['vegetarian']],
        ],
        'cream' => [
            ['id'=>'PR-C001','name'=>'PriceRight Double Cream 300ml','brand'=>'PriceRight','unit'=>'300ml','price'=>0.99,'unit_price'=>'£3.30/L','in_stock'=>true,'tags'=>['vegetarian']],
            ['id'=>'PR-C002','name'=>'PriceRight Whipping Cream 300ml','brand'=>'PriceRight','unit'=>'300ml','price'=>0.89,'unit_price'=>'£2.97/L','in_stock'=>true,'tags'=>['vegetarian']],
            ['id'=>'PR-C003','name'=>'PriceRight Single Cream 150ml','brand'=>'PriceRight','unit'=>'150ml','price'=>0.59,'unit_price'=>'£3.93/L','in_stock'=>true,'tags'=>['vegetarian']],
        ],
        'chicken' => [
            ['id'=>'PR-CH001','name'=>'PriceRight Whole Chicken Medium 1.4kg','brand'=>'PriceRight','unit'=>'~1.4kg','price'=>3.49,'unit_price'=>'£2.49/kg','in_stock'=>true,'tags'=>[]],
            ['id'=>'PR-CH002','name'=>'PriceRight Chicken Breast Fillets 600g','brand'=>'PriceRight','unit'=>'600g','price'=>2.99,'unit_price'=>'£4.98/kg','in_stock'=>true,'tags'=>[]],
            ['id'=>'PR-CH003','name'=>'PriceRight Chicken Drumsticks x8','brand'=>'PriceRight','unit'=>'x8','price'=>1.99,'unit_price'=>'','in_stock'=>true,'tags'=>[]],
            ['id'=>'PR-CH004','name'=>'PriceRight Diced Chicken Breast 400g','brand'=>'PriceRight','unit'=>'400g','price'=>2.49,'unit_price'=>'£6.23/kg','in_stock'=>true,'tags'=>[]],
        ],
        'onion' => [
            ['id'=>'PR-O001','name'=>'PriceRight Brown Onions 1kg','brand'=>'PriceRight','unit'=>'1kg','price'=>0.49,'unit_price'=>'49p/kg','in_stock'=>true,'tags'=>['vegan']],
            ['id'=>'PR-O002','name'=>'PriceRight Red Onions 500g','brand'=>'PriceRight','unit'=>'500g','price'=>0.45,'unit_price'=>'90p/kg','in_stock'=>true,'tags'=>['vegan']],
        ],
        'garlic' => [
            ['id'=>'PR-G001','name'=>'PriceRight Garlic Bulb x2','brand'=>'PriceRight','unit'=>'x2','price'=>0.35,'unit_price'=>'','in_stock'=>true,'tags'=>['vegan']],
            ['id'=>'PR-G002','name'=>'PriceRight Easy Garlic Puree 80g','brand'=>'PriceRight','unit'=>'80g','price'=>0.49,'unit_price'=>'','in_stock'=>true,'tags'=>['vegan']],
        ],
        'tomato' => [
            ['id'=>'PR-T001','name'=>'PriceRight Chopped Tomatoes 400g Tin','brand'=>'PriceRight','unit'=>'400g','price'=>0.29,'unit_price'=>'73p/kg','in_stock'=>true,'tags'=>['vegan']],
            ['id'=>'PR-T002','name'=>'PriceRight Cherry Tomatoes 300g','brand'=>'PriceRight','unit'=>'300g','price'=>0.59,'unit_price'=>'£1.97/kg','in_stock'=>true,'tags'=>['vegan']],
            ['id'=>'PR-T003','name'=>'PriceRight Plum Tomatoes 400g Tin x4','brand'=>'PriceRight','unit'=>'x4 tins','price'=>0.99,'unit_price'=>'£0.62/tin','in_stock'=>true,'tags'=>['vegan']],
        ],
        'olive oil' => [
            ['id'=>'PR-OO001','name'=>'PriceRight Olive Oil 1L','brand'=>'PriceRight','unit'=>'1L','price'=>2.99,'unit_price'=>'£2.99/L','in_stock'=>true,'tags'=>['vegan']],
            ['id'=>'PR-OO002','name'=>'PriceRight Extra Virgin Olive Oil 500ml','brand'=>'PriceRight','unit'=>'500ml','price'=>2.49,'unit_price'=>'£4.98/L','in_stock'=>true,'tags'=>['vegan']],
        ],
        'lobster' => [
            ['id'=>'PR-LB001','name'=>'No results','brand'=>'','unit'=>'','price'=>0,'unit_price'=>'','in_stock'=>false,'tags'=>[]],
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
        return [[
            'id'=>'PR-0000','name'=>"PriceRight — no results for \"$q\"",
            'brand'=>'PriceRight','unit'=>'','price'=>0,'unit_price'=>'',
            'in_stock'=>false,'tags'=>[],
        ]];
    }

    private function format(array $p): array
    {
        return [
            'id'       => $p['id'],
            'name'     => $p['name'],
            'brand'    => $p['brand'],
            'unit'     => $p['unit'],
            'price'    => ['amount' => $p['price'], 'currency' => 'GBP', 'unit_price' => $p['unit_price']],
            'in_stock' => $p['in_stock'],
            'tags'     => $p['tags'],
            'buy_url'  => 'https://www.priceright-demo.com/shop/' . strtolower($p['id']),
        ];
    }

    public function addToBasket(string $product_id, int $quantity): ?array
    {
        $token      = bin2hex(random_bytes(16));
        $expires_at = time() + 900; // 15 min
        return [
            'checkout_url' => "https://www.priceright-demo.com/go/checkout?t={$token}&q={$quantity}&item=" . urlencode($product_id),
            'basket_id'    => 'pr-' . substr(md5($product_id . time()), 0, 10),
            'expires_at'   => $expires_at,
        ];
    }
}
