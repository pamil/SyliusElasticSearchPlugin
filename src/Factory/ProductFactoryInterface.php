<?php

namespace Sylius\ElasticSearchPlugin\Factory;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\ElasticSearchPlugin\Document\Product;

interface ProductFactoryInterface
{
    /**
     * @return Product
     */
    public function create();

    /**
     * @param ProductInterface $product
     * @param LocaleInterface $locale
     * @param ChannelInterface $channel
     *
     * @return Product
     */
    public function createFromSyliusSimpleProductModel(
        ProductInterface $product,
        LocaleInterface $locale,
        ChannelInterface $channel
    );
}
