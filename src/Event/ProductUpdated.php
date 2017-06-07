<?php

namespace Sylius\ElasticSearchPlugin\Event;

use Sylius\Component\Core\Model\ProductInterface;

final class ProductUpdated
{
    /**
     * @var ProductInterface
     */
    private $product;

    /**
     * @param ProductInterface $product
     */
    private function __construct(ProductInterface $product)
    {
        $this->product = $product;
    }

    /**
     * @param ProductInterface $product
     *
     * @return self
     */
    public static function occur(ProductInterface $product)
    {
        return new self($product);
    }

    /**
     * @return ProductInterface
     */
    public function product()
    {
        return $this->product;
    }
}
