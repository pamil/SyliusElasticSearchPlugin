<?php

declare(strict_types=1);

namespace Sylius\ElasticSearchPlugin\Factory;

use ONGR\ElasticsearchBundle\Collection\Collection;
use ONGR\FilterManagerBundle\Search\SearchResponse;
use Sylius\ElasticSearchPlugin\Controller\ImageView;
use Sylius\ElasticSearchPlugin\Controller\PriceView;
use Sylius\ElasticSearchPlugin\Controller\AttributeView;
use Sylius\ElasticSearchPlugin\Controller\ProductView;
use Sylius\ElasticSearchPlugin\Controller\ProductListView;
use Sylius\ElasticSearchPlugin\Controller\VariantView;
use Sylius\ElasticSearchPlugin\Controller\TaxonView;
use Sylius\ElasticSearchPlugin\Document\AttributeValueDocument;
use Sylius\ElasticSearchPlugin\Document\ImageDocument;
use Sylius\ElasticSearchPlugin\Document\PriceDocument;
use Sylius\ElasticSearchPlugin\Document\ProductDocument;
use Sylius\ElasticSearchPlugin\Document\TaxonDocument;

final class ProductListViewFactory implements ProductListViewFactoryInterface
{
    /** @var string */
    private $productListViewClass;

    /** @var string */
    private $productViewClass;

    /** @var string */
    private $productVariantViewClass;

    /** @var string */
    private $attributeViewClass;

    /** @var string */
    private $imageViewClass;

    /** @var string */
    private $priceViewClass;

    /** @var string */
    private $taxonViewClass;

    public function __construct(
        $productListViewClass,
        $productViewClass,
        $productVariantViewClass,
        $attributeViewClass,
        $imageViewClass,
        $priceViewClass,
        $taxonViewClass
    ) {
        $this->productListViewClass = $productListViewClass;
        $this->productViewClass = $productViewClass;
        $this->productVariantViewClass = $productVariantViewClass;
        $this->attributeViewClass = $attributeViewClass;
        $this->imageViewClass = $imageViewClass;
        $this->priceViewClass = $priceViewClass;
        $this->taxonViewClass = $taxonViewClass;
    }

    /**
     * {@inheritdoc}
     */
    public function createFromSearchResponse(SearchResponse $response)
    {
        $result = $response->getResult();

        /** @var ProductListView $productListView */
        $productListView = new $this->productListViewClass();
        $productListView->filters = $response->getFilters();

        /** @var ProductDocument $product */
        foreach ($result as $product) {
            $productListView->items[] = $this->getProductView($product);
        }

        return $productListView;
    }

    /**
     * @param Collection|ImageDocument[] $images
     *
     * @return ImageView[]
     */
    private function getImageViews(Collection $images)
    {
        $imageViews = [];
        foreach ($images as $image) {
            /** @var ImageView $imageView */
            $imageView = new $this->imageViewClass();
            $imageView->code = $image->getCode();
            $imageView->path = $image->getPath();

            $imageViews[] = $imageView;
        }

        return $imageViews;
    }

    /**
     * @param Collection|TaxonDocument[] $taxons
     * @param TaxonDocument|null $mainTaxonDocument
     *
     * @return TaxonView
     */
    private function getTaxonView(Collection $taxons, ?TaxonDocument $mainTaxonDocument)
    {
        /** @var TaxonView $taxonView */
        $taxonView = new $this->taxonViewClass();

        $taxonView->main = null === $mainTaxonDocument ? null : $mainTaxonDocument->getCode();
        foreach ($taxons as $taxon) {
            $taxonView->others[] = $taxon->getCode();
        }

        return $taxonView;
    }

    /**
     * @param Collection|AttributeValueDocument[] $attributeValues
     *
     * @return AttributeView[]
     */
    private function getAttributeViews(Collection $attributeValues)
    {
        $attributeValueViews = [];
        foreach ($attributeValues as $attributeValue) {
            /** @var AttributeView $attributeView */
            $attributeView = new $this->attributeViewClass();
            $attributeView->value = $attributeValue->getValue();
            $attributeView->code = $attributeValue->getAttribute()->getCode();
            $attributeView->name = $attributeValue->getAttribute()->getName();

            $attributeValueViews[] = $attributeView;
        }

        return $attributeValueViews;
    }

    /**
     * @param PriceDocument $price
     *
     * @return PriceView
     */
    private function getPriceView(PriceDocument $price)
    {
        /** @var PriceView $priceView */
        $priceView = new $this->priceViewClass();
        $priceView->current = $price->getAmount();
        $priceView->currency = $price->getCurrency();

        return $priceView;
    }

    /**
     * @param ProductDocument $product
     *
     * @return VariantView
     */
    private function getVariantView(ProductDocument $product)
    {
        /** @var VariantView $variantView */
        $variantView = new $this->productVariantViewClass();
        $variantView->price = $this->getPriceView($product->getPrice());
        $variantView->code = $product->getCode();
        $variantView->name = $product->getName();
        $variantView->images = $this->getImageViews($product->getImages());

        return $variantView;
    }

    /**
     * @param ProductDocument $product
     *
     * @return ProductView
     */
    private function getProductView(ProductDocument $product)
    {
        /** @var ProductView $productView */
        $productView = new $this->productViewClass();
        $productView->slug = $product->getSlug();
        $productView->name = $product->getName();
        $productView->code = $product->getCode();
        $productView->localeCode = $product->getLocaleCode();
        $productView->channelCode = $product->getChannelCode();
        $productView->images = $this->getImageViews($product->getImages());
        $productView->taxons = $this->getTaxonView($product->getTaxons(), $product->getMainTaxon());
        $productView->attributes = $this->getAttributeViews($product->getAttributeValues());
        $productView->variants = [$this->getVariantView($product)];

        return $productView;
    }
}
