<?php
require_once __DIR__ . '/RetailerAdapter.php';

/**
 * The Fresh Market — artisan/local-focus retailer (fictional demo)
 * Brand: warm red, provenance-led, mid-premium
 */
class FreshMarketAdapter implements RetailerAdapter
{
    private string $retailer;
    private string $locale;

    private const CATALOG = [
        'butter' => [
            ['id'=>'FM-D100','name'=>'Cornish Salted Butter 250g','brand'=>'Trevithick Dairy','unit'=>'250g','price'=>2.25,'unit_price'=>'£9.00/kg','in_stock'=>true,'tags'=>['vegetarian']],
            ['id'=>'FM-D101','name'=>'Normandy Unsalted Butter 250g','brand'=>'Isigny Ste Mère','unit'=>'250g','price'=>2.50,'unit_price'=>'£10.00/kg','in_stock'=>true,'tags'=>['vegetarian']],
            ['id'=>'FM-D102','name'=>'Yorkshire Cultured Butter 200g','brand'=>'Wharfe Valley','unit'=>'200g','price'=>3.10,'unit_price'=>'£15.50/kg','in_stock'=>false,'tags'=>['vegetarian']],
        ],
        'flour' => [
            ['id'=>'FM-F100','name'=>'Heritage Wheat Plain Flour 1kg','brand'=>'Shipton Mill','unit'=>'1kg','price'=>2.80,'unit_price'=>'£2.80/kg','in_stock'=>true,'tags'=>['vegan']],
            ['id'=>'FM-F101','name'=>'Spelt Flour 1kg','brand'=>'Doves Farm','unit'=>'1kg','price'=>3.20,'unit_price'=>'£3.20/kg','in_stock'=>true,'tags'=>['vegan']],
            ['id'=>'FM-F102','name'=>'Stoneground White Bread Flour 1.5kg','brand'=>'Shipton Mill','unit'=>'1.5kg','price'=>3.75,'unit_price'=>'£2.50/kg','in_stock'=>true,'tags'=>['vegan']],
        ],
        'eggs' => [
            ['id'=>'FM-E100','name'=>'Burford Brown Eggs x6','brand'=>'Clarence Court','unit'=>'x6','price'=>3.00,'unit_price'=>'50p each','in_stock'=>true,'tags'=>['vegetarian']],
            ['id'=>'FM-E101','name'=>'Old Cotswold Legbar Eggs x6','brand'=>'Clarence Court','unit'=>'x6','price'=>3.50,'unit_price'=>'58.3p each','in_stock'=>true,'tags'=>['vegetarian']],
            ['id'=>'FM-E102','name'=>'Local Free Range Eggs x6','brand'=>'Meadow Farm','unit'=>'x6','price'=>2.20,'unit_price'=>'36.7p each','in_stock'=>true,'tags'=>['vegetarian']],
        ],
        'milk' => [
            ['id'=>'FM-M100','name'=>'Jersey Whole Milk 1L','brand'=>'Ivy House Farm','unit'=>'1L','price'=>1.85,'unit_price'=>'£1.85/L','in_stock'=>true,'tags'=>['vegetarian']],
            ['id'=>'FM-M101','name'=>'Vat Pasteurised Whole Milk 1L','brand'=>'Hook & Son','unit'=>'1L','price'=>2.20,'unit_price'=>'£2.20/L','in_stock'=>true,'tags'=>['vegetarian']],
            ['id'=>'FM-M102','name'=>'Oat Milk Barista 1L','brand'=>'Oatly','unit'=>'1L','price'=>1.80,'unit_price'=>'£1.80/L','in_stock'=>true,'tags'=>['vegan','dairy-free']],
        ],
        'cream' => [
            ['id'=>'FM-C100','name'=>'Clotted Cream 227g','brand'=>'Rodda\'s','unit'=>'227g','price'=>2.85,'unit_price'=>'£12.56/kg','in_stock'=>true,'tags'=>['vegetarian']],
            ['id'=>'FM-C101','name'=>'Jersey Double Cream 300ml','brand'=>'Ivy House Farm','unit'=>'300ml','price'=>2.40,'unit_price'=>'£8.00/L','in_stock'=>true,'tags'=>['vegetarian']],
            ['id'=>'FM-C102','name'=>'Cornish Clotted Cream 170g','brand'=>'Trevithick Dairy','unit'=>'170g','price'=>2.50,'unit_price'=>'£14.71/kg','in_stock'=>true,'tags'=>['vegetarian']],
        ],
        'chicken' => [
            ['id'=>'FM-CH100','name'=>'Free Range Whole Chicken 1.6kg','brand'=>'Fosse Meadows','unit'=>'~1.6kg','price'=>12.00,'unit_price'=>'£7.50/kg','in_stock'=>true,'tags'=>[]],
            ['id'=>'FM-CH101','name'=>'Free Range Chicken Thighs Skin-On x4','brand'=>'Fosse Meadows','unit'=>'x4','price'=>5.50,'unit_price'=>'','in_stock'=>true,'tags'=>[]],
            ['id'=>'FM-CH102','name'=>'Free Range Chicken Breast Fillets x2','brand'=>'Fosse Meadows','unit'=>'x2','price'=>5.00,'unit_price'=>'','in_stock'=>true,'tags'=>[]],
        ],
        'onion' => [
            ['id'=>'FM-O100','name'=>'Red Roscoff Onions 500g','brand'=>'Fresh Market','unit'=>'500g','price'=>1.40,'unit_price'=>'£2.80/kg','in_stock'=>true,'tags'=>['vegan']],
            ['id'=>'FM-O101','name'=>'British Spring Onions x6','brand'=>'Fresh Market','unit'=>'x6','price'=>0.90,'unit_price'=>'','in_stock'=>true,'tags'=>['vegan']],
            ['id'=>'FM-O102','name'=>'Banana Shallots 250g','brand'=>'Fresh Market','unit'=>'250g','price'=>1.20,'unit_price'=>'£4.80/kg','in_stock'=>true,'tags'=>['vegan']],
        ],
        'garlic' => [
            ['id'=>'FM-G100','name'=>'Isle of Wight Garlic Bulb x2','brand'=>'The Garlic Farm','unit'=>'x2','price'=>2.50,'unit_price'=>'','in_stock'=>true,'tags'=>['vegan']],
            ['id'=>'FM-G101','name'=>'Smoked Garlic Bulb','brand'=>'The Garlic Farm','unit'=>'x1','price'=>1.50,'unit_price'=>'','in_stock'=>true,'tags'=>['vegan']],
            ['id'=>'FM-G102','name'=>'Black Garlic Bulb x2','brand'=>'The Garlic Farm','unit'=>'x2','price'=>3.20,'unit_price'=>'','in_stock'=>true,'tags'=>['vegan']],
        ],
        'tomato' => [
            ['id'=>'FM-T100','name'=>'Heritage Tomato Selection 400g','brand'=>'Fresh Market','unit'=>'400g','price'=>3.50,'unit_price'=>'£8.75/kg','in_stock'=>true,'tags'=>['vegan']],
            ['id'=>'FM-T101','name'=>'Isle of Wight Tomatoes 500g','brand'=>'Tomato Stall','unit'=>'500g','price'=>2.80,'unit_price'=>'£5.60/kg','in_stock'=>true,'tags'=>['vegan']],
            ['id'=>'FM-T102','name'=>'San Marzano Tomatoes 400g Tin','brand'=>'Cirio','unit'=>'400g','price'=>1.85,'unit_price'=>'£4.63/kg','in_stock'=>true,'tags'=>['vegan']],
        ],
        'olive oil' => [
            ['id'=>'FM-OO100','name'=>'Greek PDO Extra Virgin Olive Oil 500ml','brand'=>'Mani','unit'=>'500ml','price'=>9.50,'unit_price'=>'£19.00/L','in_stock'=>true,'tags'=>['vegan']],
            ['id'=>'FM-OO101','name'=>'Sicilian Early Harvest Olive Oil 500ml','brand'=>'Planeta','unit'=>'500ml','price'=>12.00,'unit_price'=>'£24.00/L','in_stock'=>true,'tags'=>['vegan']],
            ['id'=>'FM-OO102','name'=>'Spanish EVOO 250ml','brand'=>'Brindisa','unit'=>'250ml','price'=>5.50,'unit_price'=>'£22.00/L','in_stock'=>true,'tags'=>['vegan']],
        ],
        'lobster' => [
            ['id'=>'FM-S100','name'=>'Live Scottish Lobster ~500g','brand'=>'Fresh Market','unit'=>'~500g','price'=>25.00,'unit_price'=>'£50.00/kg','in_stock'=>true,'tags'=>[]],
            ['id'=>'FM-S101','name'=>'Whole Cooked Lobster 400-500g','brand'=>'Fresh Market','unit'=>'~450g','price'=>18.50,'unit_price'=>'£41.11/kg','in_stock'=>true,'tags'=>[]],
            ['id'=>'FM-S102','name'=>'Lobster Bisque 400g','brand'=>'Shippams','unit'=>'400g','price'=>4.50,'unit_price'=>'£11.25/kg','in_stock'=>true,'tags'=>[]],
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
            'id'=>'FM-0000','name'=>"The Fresh Market — no results for \"$q\"",
            'brand'=>'The Fresh Market','unit'=>'','price'=>0,'unit_price'=>'',
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
            'price'    => ['amount_minor' => (int)round($p['price'] * 100), 'currency' => 'GBP', 'unit_price' => $p['unit_price']],
            'in_stock' => $p['in_stock'],
            'tags'     => $p['tags'],
            'buy_url'  => 'https://www.freshmarket-demo.com/products/' . strtolower($p['id']),
        ];
    }

    public function addToBasket(string $product_id, int $quantity): ?array
    {
        $token      = bin2hex(random_bytes(16));
        $expires_at = time() + 3600; // 60 min
        return [
            'checkout_url' => "https://www.freshmarket-demo.com/basket/checkout?ref={$token}&units={$quantity}&sku=" . urlencode($product_id),
            'basket_id'    => 'fm-bsk-' . strtoupper(substr($token, 0, 8)),
            'expires_at'   => $expires_at,
        ];
    }
}
