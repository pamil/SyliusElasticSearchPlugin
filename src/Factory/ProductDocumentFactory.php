<?php

namespace Sylius\ElasticSearchPlugin\Factory;

use Doctrine\ORM\Id\UuidGenerator;
use ONGR\ElasticsearchBundle\Collection\Collection;
use Ramsey\Uuid\Uuid;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductTranslationInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Product\Model\ProductAttributeTranslationInterface;
use Sylius\Component\Resource\Model\TranslationInterface;
use Sylius\Component\Taxonomy\Model\TaxonTranslationInterface;
use Sylius\ElasticSearchPlugin\Document\AttributeDocument;
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
     * @param string $imageDocumentClass
     * @param string $priceDocumentClass
     * @param string $taxonDocumentClass
     * @param array $attributeWhitelist
     */
    public function __construct(
        $productDocumentClass,
        $attributeDocumentClass,
        $imageDocumentClass,
        $priceDocumentClass,
        $taxonDocumentClass,
        array $attributeWhitelist
    ) {
        $this->assertClassExtends($productDocumentClass, ProductDocument::class);
        $this->productDocumentClass = $productDocumentClass;

        $this->assertClassExtends($attributeDocumentClass, AttributeDocument::class);
        $this->attributeDocumentClass = $attributeDocumentClass;

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
        $product->setId(Uuid::uuid4()->toString());
        $product->setEnabled($syliusProduct->isEnabled());
        $product->setLocaleCode($locale->getCode());
        $product->setSlug($productTranslation->getSlug());
        $product->setName($productTranslation->getName());
        $product->setDescription($productTranslation->getDescription());
        $product->setChannelCode($channel->getCode());
        $product->setCode($syliusProduct->getCode());
        $product->setCreatedAt($syliusProduct->getCreatedAt());
        $product->setSynchronisedAt(new \DateTime('now'));
        $product->setAverageReviewRating($syliusProduct->getAverageRating());

        if (null !== $syliusProduct->getMainTaxon()) {
            /** @var TaxonDocument $mainTaxonDocument */
            $mainTaxonDocument = new $this->taxonDocumentClass();
            /** @var TaxonTranslationInterface $mainTaxonTranslation */
            $mainTaxonTranslation = $syliusProduct->getMainTaxon()->getTranslation($locale->getCode());

            $mainTaxonDocument->setCode($syliusProduct->getMainTaxon()->getCode());
            $mainTaxonDocument->setSlug($mainTaxonTranslation->getSlug());
            $mainTaxonDocument->setDescription($mainTaxonTranslation->getDescription());
            $product->setMainTaxon($mainTaxonDocument);
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
            $syliusProductTaxonAncestors = $this->getAncestorsFromTaxon($syliusProductTaxon->getTaxon());

            $productTaxons[] = $this->createTaxonDocumentFromSyliusTaxon($syliusProductTaxon->getTaxon(), $locale->getCode());

            foreach ($syliusProductTaxonAncestors as $syliusProductTaxonAncestor) {
                $productTaxons[] = $this->createTaxonDocumentFromSyliusTaxon($syliusProductTaxonAncestor, $locale->getCode());
            }
        }
        $product->setTaxons(new Collection($productTaxons));
        $productAttributes = [];
        foreach ($syliusProductAttributes as $syliusProductAttributeValue) {
            if (in_array($syliusProductAttributeValue->getAttribute()->getCode(), $this->attributeWhitelist)) {
                /** @var AttributeDocument $productAttribute */
                $productAttribute = new $this->attributeDocumentClass();
                $productAttribute->setCode($syliusProductAttributeValue->getCode());
                $productAttribute->setValue($syliusProductAttributeValue->getValue());
                /** @var ProductAttributeTranslationInterface $syliusProductAttributeTranslation */
                $syliusProductAttributeTranslation = $syliusProductAttributeValue->getAttribute()->getTranslation($locale->getCode());
                $productAttribute->setName($syliusProductAttributeTranslation->getName());

                $productAttributes[] = $productAttribute;
            }
        }
        $productAttributes = new Collection($productAttributes);
        $product->setAttributes($productAttributes);

        return $product;
    }

    /**
     * @param TaxonInterface $taxon
     * @param string $localeCode
     *
     * @return TaxonDocument
     */
    private function createTaxonDocumentFromSyliusTaxon(TaxonInterface $taxon, $localeCode = null)
    {
        /** @var TaxonTranslationInterface $taxonTranslation */
        $taxonTranslation = $taxon->getTranslation($localeCode);

        /** @var TaxonDocument $taxonDocument */
        $taxonDocument = new $this->taxonDocumentClass();
        $taxonDocument->setCode($taxon->getCode());
        $taxonDocument->setSlug($taxonTranslation->getSlug());
        $taxonDocument->setPosition($taxon->getPosition());
        $taxonDocument->setDescription($taxonTranslation->getDescription());

        $taxonDocumentImages = [];
        $syliusTaxonImages = $taxon->getImages();
        foreach ($syliusTaxonImages as $syliusTaxonImage) {
            /** @var ImageDocument $taxonDocumentImage */
            $taxonDocumentImage = new $this->imageDocumentClass();
            $taxonDocumentImage->setPath($syliusTaxonImage->getPath());
            $taxonDocumentImage->setCode($syliusTaxonImage->getType());
            $taxonDocumentImages[] = $taxonDocumentImage;
        }
        $taxonDocument->setImages(new Collection($taxonDocumentImages));

        return $taxonDocument;
    }

    /**
     * @param TaxonInterface $taxon
     *
     * @return array
     */
    private function getAncestorsFromTaxon(TaxonInterface $taxon)
    {
        $ancestors = [];
        while (null !== $taxon->getParent()) {
            $taxon = $taxon->getParent();

            $ancestors[] = $taxon;
        }

        return $ancestors;
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
