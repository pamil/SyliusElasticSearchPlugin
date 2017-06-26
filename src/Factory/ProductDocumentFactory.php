<?php

namespace Sylius\ElasticSearchPlugin\Factory;

use ONGR\ElasticsearchBundle\Collection\Collection;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductTranslationInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Model\TranslationInterface;
use Sylius\ElasticSearchPlugin\Document\AttributeDocument;
use Sylius\ElasticSearchPlugin\Document\AttributeValueDocument;
use Sylius\ElasticSearchPlugin\Document\ImageDocument;
use Sylius\ElasticSearchPlugin\Document\PriceDocument;
use Sylius\ElasticSearchPlugin\Document\ProductDocument;
use Sylius\ElasticSearchPlugin\Document\TaxonDocument;
use Sylius\ElasticSearchPlugin\Exception\UnsupportedFactoryMethodException;

final class ProductDocumentFactory implements ProductDocumentFactoryInterface
{
    /** @var string */
    private $productDocumentClass;

    /** @var string */
    private $attributeDocumentClass;

    /** @var string */
    private $attributeValueDocumentClass;

    /** @var string */
    private $imageDocumentClass;

    /** @var string */
    private $priceDocumentClass;

    /** @var string */
    private $taxonDocumentClass;

    /** @var array */
    private $attributeWhitelist = [];

    /**
     * @param string $productDocumentClass
     * @param string $attributeDocumentClass
     * @param string $attributeValueDocumentClass
     * @param string $imageDocumentClass
     * @param string $priceDocumentClass
     * @param string $taxonDocumentClass
     * @param array $attributeWhitelist
     */
    public function __construct(
        $productDocumentClass,
        $attributeDocumentClass,
        $attributeValueDocumentClass,
        $imageDocumentClass,
        $priceDocumentClass,
        $taxonDocumentClass,
        array $attributeWhitelist
    ) {
        $this->assertClassExtends($productDocumentClass, ProductDocument::class);
        $this->productDocumentClass = $productDocumentClass;

        $this->assertClassExtends($attributeDocumentClass, AttributeDocument::class);
        $this->attributeDocumentClass = $attributeDocumentClass;

        $this->assertClassExtends($attributeValueDocumentClass, AttributeValueDocument::class);
        $this->attributeValueDocumentClass = $attributeValueDocumentClass;

        $this->assertClassExtends($imageDocumentClass, ImageDocument::class);
        $this->imageDocumentClass = $imageDocumentClass;

        $this->assertClassExtends($priceDocumentClass, PriceDocument::class);
        $this->priceDocumentClass = $priceDocumentClass;

        $this->assertClassExtends($taxonDocumentClass, TaxonDocument::class);
        $this->taxonDocumentClass = $taxonDocumentClass;

        $this->attributeWhitelist = $attributeWhitelist;
    }

    /**
     * @param ProductInterface $syliusProduct
     * @param LocaleInterface $locale
     * @param ChannelInterface $channel
     *
     * @return ProductDocument
     */
    public function createFromSyliusSimpleProductModel(ProductInterface $syliusProduct, LocaleInterface $locale, ChannelInterface $channel)
    {
        if (!$syliusProduct->isSimple()) {
            throw new UnsupportedFactoryMethodException(
                __METHOD__,
                sprintf(
                    'Cannot create elastic search model from configurable product "%s".',
                    $syliusProduct->getCode()
                )
            );
        }

        /** @var ProductVariantInterface $productVariant */
        $productVariant = $syliusProduct->getVariants()->first();

        /** @var ProductTranslationInterface|TranslationInterface $productTranslation */
        $productTranslation = $syliusProduct->getTranslation($locale->getCode());
        $channelPrice = $this->getChannelPricingForChannelFromProductVariant($productVariant, $channel);
        $syliusProductAttributes = $syliusProduct->getAttributesByLocale(
            $locale->getCode(),
            $channel->getDefaultLocale()->getCode()
        );
        $syliusProductTaxons = $syliusProduct->getProductTaxons();

        /** @var ProductDocument $product */
        $product = new $this->productDocumentClass();
        $product->setLocaleCode($locale->getCode());
        $product->setSlug($productTranslation->getSlug());
        $product->setName($productTranslation->getName());
        $product->setDescription($productTranslation->getDescription());
        $product->setChannelCode($channel->getCode());
        $product->setCode($syliusProduct->getCode());
        $product->setCreatedAt($syliusProduct->getCreatedAt());

        if (null !== $syliusProduct->getMainTaxon()) {
            /** @var TaxonDocument $mainTaxon */
            $mainTaxon = new $this->taxonDocumentClass();
            $mainTaxon->setCode($syliusProduct->getMainTaxon()->getCode());
            $mainTaxon->setSlug($syliusProduct->getMainTaxon()->getSlug());
            $mainTaxon->setDescription($syliusProduct->getMainTaxon()->getDescription());
            $product->setMainTaxon($mainTaxon);
        }

        /** @var PriceDocument $price */
        $price = new $this->priceDocumentClass();
        $price->setAmount($channelPrice->getPrice());
        $price->setCurrency($channel->getBaseCurrency()->getCode());
        $product->setPrice($price);

        $productImages = [];
        $syliusProductImages = $syliusProduct->getImages();
        foreach ($syliusProductImages as $syliusProductImage) {
            /** @var ImageDocument $productImage */
            $productImage = new $this->imageDocumentClass();
            $productImage->setPath($syliusProductImage->getPath());
            $productImage->setCode($syliusProductImage->getType());
            $productImages[] = $productImage;
        }
        $product->setImages(new Collection($productImages));

        $productTaxons = [];
        foreach ($syliusProductTaxons as $syliusProductTaxon) {
            /** @var TaxonDocument $productTaxon */
            $productTaxon = new $this->taxonDocumentClass();
            $productTaxon->setCode($syliusProductTaxon->getTaxon()->getCode());
            $productTaxon->setSlug($syliusProductTaxon->getTaxon()->getSlug());
            $productTaxon->setPosition($syliusProductTaxon->getTaxon()->getPosition());
            $productTaxon->setDescription($syliusProductTaxon->getTaxon()->getDescription());

            $productTaxonImages = [];
            $syliusTaxonImages = $syliusProductTaxon->getTaxon()->getImages();
            foreach ($syliusTaxonImages as $syliusTaxonImage) {
                /** @var ImageDocument $productTaxonImage */
                $productTaxonImage = new $this->imageDocumentClass();
                $productTaxonImage->setPath($syliusTaxonImage->getPath());
                $productTaxonImage->setCode($syliusTaxonImage->getType());
                $productTaxonImages[] = $productTaxonImage;
            }
            $productTaxon->setImages(new Collection($productTaxonImages));

            $productTaxons[] = $productTaxon;
        }
        $product->setTaxons(new Collection($productTaxons));
        $productAttributeValues = [];
        foreach ($syliusProductAttributes as $syliusProductAttributeValue) {
            if (in_array($syliusProductAttributeValue->getAttribute()->getCode(), $this->attributeWhitelist)) {
                /** @var AttributeValueDocument $productAttributeValue */
                $productAttributeValue = new $this->attributeValueDocumentClass();
                $productAttributeValue->setValue($syliusProductAttributeValue->getValue());

                /** @var AttributeDocument $attribute */
                $attribute = new $this->attributeDocumentClass();
                $attribute->setCode($syliusProductAttributeValue->getAttribute()->getCode());
                $attribute->setName($syliusProductAttributeValue->getAttribute()->getName());
                $productAttributeValue->setAttribute($attribute);

                $productAttributeValues[] = $productAttributeValue;
            }
        }
        $productAttributeValues = new Collection($productAttributeValues);
        $product->setAttributeValues($productAttributeValues);

        return $product;
    }

    /**
     * @param ProductVariantInterface $productVariant
     * @param ChannelInterface $channel
     *
     * @return ChannelPricingInterface|null
     */
    private function getChannelPricingForChannelFromProductVariant(
        ProductVariantInterface $productVariant,
        ChannelInterface $channel
    ) {
        $channelPricings = $productVariant->getChannelPricings();

        foreach ($channelPricings as $channelPricing) {
            if ($channelPricing->getChannelCode() === $channel->getCode()) {
                return $channelPricing;
            }
        }

        return null;
    }

    /**
     * @param string $class
     * @param string $parentClass
     *
     * @throws \InvalidArgumentException
     */
    private function assertClassExtends($class, $parentClass)
    {
        if ($class !== $parentClass && !in_array($parentClass, class_parents($class), true)) {
            throw new \InvalidArgumentException(sprintf('Class %s MUST extend class %s!', $class, $parentClass));
        }
    }
}
