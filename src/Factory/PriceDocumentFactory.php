<?php

declare(strict_types=1);

namespace Sylius\ElasticSearchPlugin\Factory;

use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\ElasticSearchPlugin\Document\PriceDocument;

class PriceDocumentFactory implements PriceDocumentFactoryInterface
{

    /**
     * @var string
     */
    protected $priceDocumentClass;

    public function __construct(string $priceDocumentClass)
    {
        $this->priceDocumentClass = $priceDocumentClass;
    }

    public function create(
        ChannelPricingInterface $channelPricing,
        CurrencyInterface $currency
    ): PriceDocument {
        /** @var PriceDocument $price */
        $price = new $this->priceDocumentClass();

        $price->setAmount($channelPricing->getPrice());
        $price->setCurrency($currency->getCode());
        $price->setOriginalAmount($channelPricing->getOriginalPrice() > 0 ?: 0);

        return $price;
    }

}
