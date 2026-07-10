<?php
/**
 * RetailerAdapter — interface all retailer adapters must implement.
 *
 * To add a new retailer:
 *   1. Create a class in this folder implementing RetailerAdapter
 *   2. Register it in proxy/index.php ADAPTERS array
 *   3. Done — no changes needed anywhere else
 */
interface RetailerAdapter
{
    public function __construct(string $retailer, string $locale);

    /**
     * Search for products matching an ingredient name.
     *
     * @param  string $q     Normalised (lowercase, trimmed) ingredient name
     * @param  int    $limit Max products to return (1–20)
     * @return array         Array of Product objects conforming to the spec schema
     */
    public function search(string $q, int $limit): array;

    /**
     * Add a product to the user's basket and return a checkout URL.
     *
     * Return null if basket integration is not supported by this retailer —
     * the proxy will respond with 501 Not Implemented automatically.
     *
     * @param  string $product_id  Product identifier from search results
     * @param  int    $quantity    Units to add (1–99)
     * @return array|null          BasketAddResponse shape, or null if unsupported
     */
    public function addToBasket(string $product_id, int $quantity): ?array;
}
