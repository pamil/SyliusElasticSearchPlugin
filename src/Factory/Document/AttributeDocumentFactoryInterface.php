<?php

declare(strict_types=1);

namespace Sylius\ElasticSearchPlugin\Factory\Document;

use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Product\Model\ProductAttributeValueInterface;

interface AttributeDocumentFactoryInterface
{

    /**
     * @param string|array $data
     * @param LocaleInterface $locale
     * @param ProductAttributeValueInterface $productAttributeValue
     *
     * @return array
     */
    public function create(
        $data,
        LocaleInterface $locale,
        ProductAttributeValueInterface $productAttributeValue
    ): array;
}
