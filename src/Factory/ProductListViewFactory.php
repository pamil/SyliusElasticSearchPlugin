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
use Sylius\ElasticSearchPlugin\Document\AttributeValue;
use Sylius\ElasticSearchPlugin\Document\Image;
use Sylius\ElasticSearchPlugin\Document\Price;
use Sylius\ElasticSearchPlugin\Document\Product;
use Sylius\ElasticSearchPlugin\Document\Taxon;

final class ProductListViewFactory implements ProductListViewFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createFromSearchResponse(SearchResponse $response)
    {
        $result = $response->getResult();
        $productListView = new ProductListView();
        $productListView->filters = $response->getFilters();

        /** @var Product $product */
        foreach ($result as $product) {
            $productListView->items[] = $this->getProductView($product);
        }

        return $productListView;
    }

    /**
     * @param Collection|Image[] $images
     *
     * @return ImageView[]
     */
    private function getImageViews(Collection $images)
    {
        $imageViews = [];
        foreach ($images as $image) {
            $imageView = new ImageView();
            $imageView->code = $image->getCode();
            $imageView->path = $image->getPath();

            $imageViews[] = $imageView;
        }

        return $imageViews;
    }

    /**
     * @param Collection|Taxon[] $taxons
     *
     * @return TaxonView[]
     */
    private function getTaxonViews(Collection $taxons)
    {
        $taxonViews = [];
        foreach ($taxons as $taxon) {
            $taxonViews[] = $this->getTaxonView($taxon);
        }

        return $taxonViews;
    }

    /**
     * @param Collection|AttributeValue[] $attributeValues
     *
     * @return AttributeView[]
     */
    private function getAttributeViews(Collection $attributeValues)
    {
        $attributeValueViews = [];
        foreach ($attributeValues as $attributeValue) {
            $attributeView = new AttributeView();
            $attributeView->value = $attributeValue->getValue();
            $attributeView->code = $attributeValue->getAttribute()->getCode();
            $attributeView->name = $attributeValue->getAttribute()->getName();

            $attributeValueViews[] = $attributeView;
        }

        return $attributeValueViews;
    }

    /**
     * @param Taxon $taxon
     *
     * @return TaxonView
     */
    private function getTaxonView(Taxon $taxon)
    {
        $taxonView = new TaxonView();
        $taxonView->code = $taxon->getCode();
        $taxonView->slug = $taxon->getSlug();
        $taxonView->position = $taxon->getPosition();
        $taxonView->description = $taxon->getDescription();
        $taxonView->images = $this->getImageViews($taxon->getImages());

        return $taxonView;
    }

    /**
     * @param Price $price
     *
     * @return PriceView
     */
    private function getPriceView(Price $price)
    {
        $priceView = new PriceView();
        $priceView->current = $price->getAmount();
        $priceView->currency = $price->getCurrency();

        return $priceView;
    }

    /**
     * @param Product $product
     *
     * @return VariantView
     */
    private function getVariantView(Product $product)
    {
        $variantView = new VariantView();
        $variantView->price = $this->getPriceView($product->getPrice());
        $variantView->code = $product->getCode();
        $variantView->name = $product->getName();
        $variantView->images = $this->getImageViews($product->getImages());

        return $variantView;
    }

    /**
     * @param Product $product
     *
     * @return ProductView
     */
    private function getProductView(Product $product)
    {
        $productView = new ProductView();
        $productView->slug = $product->getSlug();
        $productView->name = $product->getName();
        $productView->code = $product->getCode();
        $productView->localeCode = $product->getLocaleCode();
        $productView->channelCode = $product->getChannelCode();
        $productView->images = $this->getImageViews($product->getImages());
        $productView->taxons = $this->getTaxonViews($product->getTaxons());
        $productView->mainTaxon = $this->getTaxonView($product->getMainTaxon());
        $productView->attributes = $this->getAttributeViews($product->getAttributeValues());
        $productView->variants = [$this->getVariantView($product)];

        return $productView;
    }
}
