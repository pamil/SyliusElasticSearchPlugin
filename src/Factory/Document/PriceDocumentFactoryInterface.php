<?php

declare(strict_types=1);

namespace Sylius\ElasticSearchPlugin\Factory\Document;

use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\ElasticSearchPlugin\Document\PriceDocument;

interface PriceDocumentFactoryInterface
{
    public function create(
        ChannelPricingInterface $channelPricing,
        CurrencyInterface $currency
    ): PriceDocument;
}
