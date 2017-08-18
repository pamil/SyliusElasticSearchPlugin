<?php
declare(strict_types=1);

namespace Sylius\ElasticSearchPlugin\Factory;

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
use Sylius\Component\Product\Model\ProductAttributeValueInterface;
use Sylius\Component\Product\Model\ProductOptionValueInterface;
use Sylius\Component\Resource\Model\TranslationInterface;
use Sylius\Component\Taxonomy\Model\TaxonTranslationInterface;
use Sylius\ElasticSearchPlugin\Document\AttributeDocument;
use Sylius\ElasticSearchPlugin\Document\ImageDocument;
use Sylius\ElasticSearchPlugin\Document\OptionDocument;
use Sylius\ElasticSearchPlugin\Document\PriceDocument;
use Sylius\ElasticSearchPlugin\Document\ProductDocument;
use Sylius\ElasticSearchPlugin\Document\TaxonDocument;
use Sylius\ElasticSearchPlugin\Document\VariantDocument;

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

    /** @var string */
    private $variantDocumentClass;

    /** @var string */
    private $optionDocumentClass;

    /** @var array */
    private $attributeWhitelist = [];

    /**
     * @param string $productDocumentClass
     * @param string $attributeDocumentClass
     * @param string $imageDocumentClass
     * @param string $priceDocumentClass
     * @param string $taxonDocumentClass
     * @param string $variantDocumentClass
     * @param string $optionDocumentClass
     * @param array  $attributeWhitelist
     */
    public function __construct(
        string $productDocumentClass,
        string $attributeDocumentClass,
        string $imageDocumentClass,
        string $priceDocumentClass,
        string $taxonDocumentClass,
        string $variantDocumentClass,
        string $optionDocumentClass,
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
        $this->priceDocumentClass = $priceDocumentClass;

        $this->assertClassExtends($variantDocumentClass, VariantDocument::class);
        $this->variantDocumentClass = $variantDocumentClass;

        $this->assertClassExtends($optionDocumentClass, OptionDocument::class);
        $this->optionDocumentClass = $optionDocumentClass;

        $this->attributeWhitelist = $attributeWhitelist;
    }

    /**
     * @param ProductInterface $syliusProduct
     * @param LocaleInterface  $locale
     * @param ChannelInterface $channel
     *
     * @return ProductDocument
     */
    public function createFromSyliusSimpleProductModel(
        ProductInterface $syliusProduct,
        LocaleInterface $locale,
        ChannelInterface $channel
    ): ProductDocument {

        $minProductChannelPrice = $this->getChannelPricingForChannelFromProductVariant(
            $syliusProduct->getVariants()->first(),
            $channel
        );
        /** @var ProductVariantInterface[] $syliusProductVariants */
        $syliusProductVariants = $syliusProduct->getVariants();

        /**
         * Select minimal product price out of all variants
         */
        $variants = [];
        foreach ($syliusProductVariants as $variant) {
            $channelPrice = $this->getChannelPricingForChannelFromProductVariant($variant, $channel);
            if ($minProductChannelPrice->getPrice() < $channelPrice->getPrice()) {
                $minProductChannelPrice = $channelPrice;
            }
            $variants[] = $this->createVariantDocumentFromSyliusVariant($variant, $channel);
        }

        /** @var ProductTranslationInterface|TranslationInterface $productTranslation */
        $productTranslation      = $syliusProduct->getTranslation($locale->getCode());
        $syliusProductAttributes = $syliusProduct->getAttributesByLocale(
            $locale->getCode(),
            $channel->getDefaultLocale()->getCode()
        );
        $syliusProductTaxons     = $syliusProduct->getProductTaxons();
        $syliusProductImages     = $syliusProduct->getImages();

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
        $product->setVariants(new Collection($variants));

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
        $price->setAmount($minProductChannelPrice->getPrice());
        $price->setCurrency($channel->getBaseCurrency()->getCode());
        $product->setPrice($price);

        $productImages = [];
        foreach ($syliusProductImages as $syliusProductImage) {
            foreach ($syliusProductVariants as $variant) {
                if ($variant->hasImage($syliusProductImage)) {
                    continue 2;
                }
            }
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

            $productTaxons[] = $this->createTaxonDocumentFromSyliusTaxon(
                $syliusProductTaxon->getTaxon(),
                $locale->getCode()
            );

            foreach ($syliusProductTaxonAncestors as $syliusProductTaxonAncestor) {
                $productTaxons[] = $this->createTaxonDocumentFromSyliusTaxon(
                    $syliusProductTaxonAncestor,
                    $locale->getCode()
                );
            }
        }
        $product->setTaxons(new Collection($productTaxons));

        $productAttributes = [];
        foreach ($syliusProductAttributes as $syliusProductAttributeValue) {
            if (in_array(strtolower($syliusProductAttributeValue->getCode()), $this->attributeWhitelist)) {
                $productAttributes = array_merge($productAttributes, $this->createAttributeDocumentFromSyliusProductAttributeValue(
                    $locale,
                    $syliusProductAttributeValue
                ));
            }
        }
        $product->setAttributes(new Collection($productAttributes));

        return $product;
    }

    /**
     * @param TaxonInterface $taxon
     * @param string         $localeCode
     *
     * @return TaxonDocument
     */
    private function createTaxonDocumentFromSyliusTaxon(TaxonInterface $taxon, ?string $localeCode = null)
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
        $syliusTaxonImages   = $taxon->getImages();
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
     * @param ProductVariantInterface $productVariant
     * @param ChannelInterface        $channel
     *
     * @return VariantDocument
     */
    private function createVariantDocumentFromSyliusVariant(
        ProductVariantInterface $productVariant,
        ChannelInterface $channel
    ): VariantDocument {
        /** @var PriceDocument $price */
        $price          = new $this->priceDocumentClass();
        $channelPricing = $this->getChannelPricingForChannelFromProductVariant($productVariant, $channel);
        $price->setAmount($channelPricing->getPrice());
        $price->setCurrency($channel->getBaseCurrency()->getCode());

        $images = [];
        foreach ($productVariant->getImages() as $image) {
            /** @var ImageDocument $productImage */
            $productImage = new $this->imageDocumentClass();
            $productImage->setPath($image->getPath());
            $productImage->setCode($image->getType());
            $images[] = $productImage;
        }

        $options = [];
        foreach ($productVariant->getOptionValues() as $optionValue) {
            $options[] = $this->createOptionDocumentFromSyliusOptionValue($optionValue);
        }

        /** @var VariantDocument $variant */
        $variant = new $this->variantDocumentClass();
        $variant->setCode($productVariant->getCode());
        $variant->setPrice($price);
        $variant->setOnHand($productVariant->getOnHand());
        $variant->setOnHold($productVariant->getOnHold());
        $variant->setImages(new Collection($images));
        $variant->setOptions(new Collection($options));

        return $variant;
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
     * @param ChannelInterface        $channel
     *
     * @return ChannelPricingInterface|null
     */
    private function getChannelPricingForChannelFromProductVariant(
        ProductVariantInterface $productVariant,
        ChannelInterface $channel
    ): ?ChannelPricingInterface
    {
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
    private function assertClassExtends(string $class, string $parentClass)
    {
        if ($class !== $parentClass && !in_array($parentClass, class_parents($class), true)) {
            throw new \InvalidArgumentException(sprintf('Class %s MUST extend class %s!', $class, $parentClass));
        }
    }

    /**
     * @param $optionValue
     *
     * @return OptionDocument
     */
    private function createOptionDocumentFromSyliusOptionValue(ProductOptionValueInterface $optionValue): OptionDocument
    {
        /** @var OptionDocument $option */
        $option = new $this->optionDocumentClass();
        $option->setCode($optionValue->getOptionCode());
        $option->setName($optionValue->getName());
        $option->setValue($optionValue->getValue());

        return $option;
    }

    /**
     * @param LocaleInterface                $locale
     * @param ProductAttributeValueInterface $syliusProductAttributeValue
     *
     * @return AttributeDocument[]
     */
    private function createAttributeDocumentFromSyliusProductAttributeValue(
        LocaleInterface $locale,
        ProductAttributeValueInterface $syliusProductAttributeValue
    ): array {
        $productAttributes = [];

        if (is_array($syliusProductAttributeValue->getValue())) {
            foreach ($syliusProductAttributeValue->getValue() as $value) {
                $productAttributes[] = $this->createSingleAttributeDocumentFromSyliusProductAttributeValue(
                    $value,
                    $syliusProductAttributeValue,
                    $locale
                );
            }
        } else {
            $productAttributes[] = $this->createSingleAttributeDocumentFromSyliusProductAttributeValue(
                $syliusProductAttributeValue->getValue(),
                $syliusProductAttributeValue,
                $locale
            );
        }

        return $productAttributes;
    }

    public function createSingleAttributeDocumentFromSyliusProductAttributeValue(
        string $value,
        ProductAttributeValueInterface $syliusProductAttributeValue,
        LocaleInterface $locale
    ) {
        /** @var AttributeDocument $productAttribute */
        $productAttribute = new $this->attributeDocumentClass();
        $productAttribute->setCode($syliusProductAttributeValue->getCode());
        $productAttribute->setValue($value);
        /** @var ProductAttributeTranslationInterface $syliusProductAttributeTranslation */
        $syliusProductAttributeTranslation = $syliusProductAttributeValue->getAttribute()->getTranslation(
            $locale->getCode()
        )
        ;
        $productAttribute->setName($syliusProductAttributeTranslation->getName());

        return $productAttribute;
    }
}
