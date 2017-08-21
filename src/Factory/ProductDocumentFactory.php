<?php

namespace Sylius\ElasticSearchPlugin\Factory;

use ONGR\ElasticsearchBundle\Collection\Collection;
use Ramsey\Uuid\Uuid;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductTaxonInterface;
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
use Sylius\ElasticSearchPlugin\Document\ProductTaxonDocument;
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
    private $productTaxonDocumentClass;

    /** @var string */
    private $taxonDocumentClass;

    /** @var array */
    private $attributeWhitelist = [];

    public function __construct(
        string $productDocumentClass,
        string $attributeDocumentClass,
        string $imageDocumentClass,
        string $priceDocumentClass,
        string $productTaxonDocumentClass,
        string $taxonDocumentClass,
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

        $this->assertClassExtends($productTaxonDocumentClass, ProductTaxonDocument::class);
        $this->productTaxonDocumentClass = $productTaxonDocumentClass;

        $this->assertClassExtends($taxonDocumentClass, TaxonDocument::class);
        $this->taxonDocumentClass = $taxonDocumentClass;

        $this->attributeWhitelist = $attributeWhitelist;
    }

    /**
     * @param ProductInterface $product
     * @param LocaleInterface $locale
     * @param ChannelInterface $channel
     *
     * @return ProductDocument
     */
    public function createFromSyliusSimpleProductModel(ProductInterface $product, LocaleInterface $locale, ChannelInterface $channel)
    {
        if (!$product->isSimple()) {
            throw new UnsupportedFactoryMethodException(
                __METHOD__,
                sprintf(
                    'Cannot create elastic search model from configurable product "%s".',
                    $product->getCode()
                )
            );
        }

        /** @var ProductVariantInterface $productVariant */
        $productVariant = $product->getVariants()->first();

        /** @var ProductTranslationInterface|TranslationInterface $productTranslation */
        $productTranslation = $product->getTranslation($locale->getCode());
        $channelPrice = $this->getChannelPricingForChannelFromProductVariant($productVariant, $channel);
        $productAttributes = $product->getAttributesByLocale(
            $locale->getCode(),
            $channel->getDefaultLocale()->getCode()
        );

        /** @var ProductDocument $productDocument */
        $productDocument = new $this->productDocumentClass();
        $productDocument->setId(Uuid::uuid4()->toString());
        $productDocument->setEnabled($product->isEnabled());
        $productDocument->setLocaleCode($locale->getCode());
        $productDocument->setSlug($productTranslation->getSlug());
        $productDocument->setName($productTranslation->getName());
        $productDocument->setDescription($productTranslation->getDescription());
        $productDocument->setChannelCode($channel->getCode());
        $productDocument->setCode($product->getCode());
        $productDocument->setCreatedAt($product->getCreatedAt());
        $productDocument->setSynchronisedAt(new \DateTime('now'));
        $productDocument->setAverageReviewRating($product->getAverageRating());

        if (null !== $product->getMainTaxon()) {
            /** @var TaxonDocument $mainTaxonDocument */
            $mainTaxonDocument = new $this->taxonDocumentClass();
            $mainTaxonDocument->setCode($product->getMainTaxon()->getCode());

            /** @var TaxonTranslationInterface $mainTaxonTranslation */
            $mainTaxonTranslation = $product->getMainTaxon()->getTranslation($locale->getCode());
            $mainTaxonDocument->setSlug($mainTaxonTranslation->getSlug());
            $mainTaxonDocument->setDescription($mainTaxonTranslation->getDescription());

            $productDocument->setMainTaxon($mainTaxonDocument);
        }

        /** @var PriceDocument $price */
        $price = new $this->priceDocumentClass();
        $price->setAmount($channelPrice->getPrice());
        $price->setCurrency($channel->getBaseCurrency()->getCode());
        $productDocument->setPrice($price);

        $imageDocuments = [];
        $productImages = $product->getImages();
        foreach ($productImages as $productImage) {
            /** @var ImageDocument $imageDocument */
            $imageDocument = new $this->imageDocumentClass();
            $imageDocument->setPath($productImage->getPath());
            $imageDocument->setCode($productImage->getType());

            $imageDocuments[] = $imageDocument;
        }
        $productDocument->setImages(new Collection($imageDocuments));

        $taxonDocuments = $productTaxonDocuments = [];
        foreach ($product->getProductTaxons() as $syliusProductTaxon) {
            $taxonDocuments[] = $this->createTaxonDocument($syliusProductTaxon->getTaxon(), $locale->getCode());
            $productTaxonDocuments[] = $this->createProductTaxonDocument($syliusProductTaxon, $locale->getCode());

            $syliusProductTaxonAncestors = $this->getAncestorsFromTaxon($syliusProductTaxon->getTaxon());
            foreach ($syliusProductTaxonAncestors as $syliusProductTaxonAncestor) {
                $taxonDocuments[] = $this->createTaxonDocument($syliusProductTaxonAncestor, $locale->getCode());
            }
        }
        $productDocument->setTaxons(new Collection($taxonDocuments));
        $productDocument->setProductTaxons(new Collection($productTaxonDocuments));

        $productAttributeDocuments = [];
        foreach ($productAttributes as $syliusProductAttributeValue) {
            if (in_array($syliusProductAttributeValue->getAttribute()->getCode(), $this->attributeWhitelist, true)) {
                /** @var AttributeDocument $productAttributeDocument */
                $productAttributeDocument = new $this->attributeDocumentClass();
                $productAttributeDocument->setCode($syliusProductAttributeValue->getCode());
                $productAttributeDocument->setValue($syliusProductAttributeValue->getValue());

                /** @var ProductAttributeTranslationInterface $productAttributeTranslation */
                $productAttributeTranslation = $syliusProductAttributeValue->getAttribute()->getTranslation($locale->getCode());
                $productAttributeDocument->setName($productAttributeTranslation->getName());

                $productAttributeDocuments[] = $productAttributeDocument;
            }
        }
        $productDocument->setAttributes(new Collection($productAttributeDocuments));

        return $productDocument;
    }

    /**
     * @param TaxonInterface $taxon
     * @param string $localeCode
     *
     * @return TaxonDocument
     */
    private function createTaxonDocument(TaxonInterface $taxon, $localeCode = null): TaxonDocument
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
     * @return TaxonInterface[]
     */
    private function getAncestorsFromTaxon(TaxonInterface $taxon): array
    {
        $ancestors = [];
        while (null !== $taxon->getParent()) {
            $taxon = $taxon->getParent();

            $ancestors[] = $taxon;
        }

        return $ancestors;
    }

    /**
     * @param ProductTaxonInterface $productTaxon
     * @param string $localeCode
     *
     * @return ProductTaxonDocument
     */
    private function createProductTaxonDocument(ProductTaxonInterface $productTaxon, $localeCode = null): ProductTaxonDocument
    {
        /** @var TaxonInterface $taxon */
        $taxon = $productTaxon->getTaxon();

        /** @var TaxonTranslationInterface $taxonTranslation */
        $taxonTranslation = $taxon->getTranslation($localeCode);

        /** @var ProductTaxonDocument $productTaxonDocument */
        $productTaxonDocument = new $this->productTaxonDocumentClass();
        $productTaxonDocument->setPosition($productTaxon->getPosition());
        $productTaxonDocument->setCode($taxon->getCode());
        $productTaxonDocument->setSlug($taxonTranslation->getSlug());

        return $productTaxonDocument;
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
