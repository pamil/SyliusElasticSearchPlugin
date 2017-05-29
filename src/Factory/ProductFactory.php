<?php

namespace Sylius\ElasticSearchPlugin\Factory;

use ONGR\ElasticsearchBundle\Collection\Collection;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductTranslationInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Model\TranslationInterface;
use Sylius\ElasticSearchPlugin\Document\Attribute;
use Sylius\ElasticSearchPlugin\Document\AttributeValue;
use Sylius\ElasticSearchPlugin\Document\Price;
use Sylius\ElasticSearchPlugin\Document\Product;
use Sylius\ElasticSearchPlugin\Document\TaxonCode;

final class ProductFactory implements ProductFactoryInterface
{
    /**
     * @return Product
     */
    public function create()
    {
        return new Product();
    }

    /**
     * @param ProductInterface $syliusProduct
     * @param LocaleInterface $locale
     * @param ChannelInterface $channel
     *
     * @return Product
     */
    public function createFromSyliusSimpleProductModel(ProductInterface $syliusProduct, LocaleInterface $locale, ChannelInterface $channel)
    {
        if (!$syliusProduct->isSimple()) {
            throw new \InvalidArgumentException(sprintf(
                'Cannot create elastic search model from configurable product "%s" via this method.',
                $syliusProduct->getCode()
            ));
        }

        /** @var ProductVariantInterface $productVariant */
        $productVariant = $syliusProduct->getVariants()->first();

        /** @var ProductTranslationInterface|TranslationInterface $productTranslation */
        $productTranslation = $syliusProduct->getTranslation($locale->getCode());
        $channelPrice = $productVariant->getChannelPricingForChannel($channel);
        $syliusProductAttributes = $syliusProduct->getAttributesByLocale(
            $locale->getCode(),
            $channel->getDefaultLocale()->getCode()
        );
        $syliusProductTaxons = $syliusProduct->getProductTaxons();

        $product = new Product();
        $price = new Price();
        $taxonCode = new TaxonCode();
        $taxonCode->setValue($syliusProduct->getMainTaxon()->getCode());

        $price->setAmount($channelPrice->getPrice());
        $price->setCurrency($channel->getBaseCurrency()->getCode());

        $product->setLocaleCode($locale->getCode());
        $product->setName($productTranslation->getName());
        $product->setDescription($productTranslation->getDescription());
        $product->setChannelCode($channel->getCode());
        $product->setPrice($price);
        $product->setCode($syliusProduct->getCode());
        $product->setCreatedAt($syliusProduct->getCreatedAt());
        $product->setMainTaxonCode($taxonCode);

        $productTaxonCodes = [];
        foreach ($syliusProductTaxons as $syliusProductTaxon) {
            $productTaxonCode = new TaxonCode();
            $productTaxonCode->setValue($syliusProductTaxon->getTaxon()->getCode());
            $productTaxonCodes[] = $productTaxonCode;
        }
        $product->setTaxonCodes(new Collection($productTaxonCodes));

        $productAttributeValues = [];
        foreach ($syliusProductAttributes as $syliusProductAttributeValue) {
            $productAttributeValue = new AttributeValue();
            $productAttributeValue->setCode($syliusProductAttributeValue->getCode());
            $productAttributeValue->setValue($syliusProductAttributeValue->getValue());

            $attribute = new Attribute();
            $attribute->setCode($syliusProductAttributeValue->getAttribute()->getCode());
            $attribute->setName($syliusProductAttributeValue->getAttribute()->getName());
            $productAttributeValue->setAttribute($attribute);

            $productAttributeValues[] = $productAttributeValue;
        }
        $productAttributeValues = new Collection($productAttributeValues);
        $product->setAttributeValues($productAttributeValues);

        return $product;
    }
}
