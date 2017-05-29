<?php

namespace Sylius\ElasticSearchPlugin\Document;

use ONGR\ElasticsearchBundle\Annotation as ElasticSearch;
use ONGR\ElasticsearchBundle\Collection\Collection;

/**
 * @ElasticSearch\Document(type="product")
 */
final class Product
{
    /**
     * @var string
     *
     * @ElasticSearch\Id()
     */
    private $code;

    /**
     * @var string
     *
     * @ElasticSearch\Property(
     *    type="text",
     *    name="name",
     *    options={
     *        "analyzer"="standard",
     *        "fields"={
     *            "raw"={"type"="keyword"},
     *            "standard"={"type"="text", "analyzer"="standard"}
     *        }
     *    }
     * )
     */
    private $name;

    /**
     * @var string
     *
     * @ElasticSearch\Property(type="keyword")
     */
    private $channelCode;

    /**
     * @var string
     *
     * @ElasticSearch\Property(type="keyword")
     */
    private $localeCode;

    /**
     * @var string
     *
     * @ElasticSearch\Property(type="text")
     */
    private $description;

    /**
     * @var Price
     *
     * @ElasticSearch\Embedded(class="SyliusElasticSearchPlugin:Price")
     */
    private $price;

    /**
     * @var TaxonCode
     *
     * @ElasticSearch\Embedded(class="SyliusElasticSearchPlugin:TaxonCode")
     */
    private $mainTaxonCode;

    /**
     * @var Collection
     *
     * @ElasticSearch\Embedded(class="SyliusElasticSearchPlugin:TaxonCode", multiple=true)
     */
    private $taxonCodes;

    /**
     * @var Collection
     *
     * @ElasticSearch\Embedded(class="SyliusElasticSearchPlugin:AttributeValue", multiple=true)
     */
    private $attributeValues;

    /**
     * @var \DateTime
     *
     * @ElasticSearch\Property(type="date")
     */
    private $createdAt;

    public function __construct()
    {
        $this->attributeValues = new Collection();
        $this->taxonCodes = new Collection();
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getChannelCode()
    {
        return $this->channelCode;
    }

    /**
     * @param string $channelCode
     */
    public function setChannelCode($channelCode)
    {
        $this->channelCode = $channelCode;
    }

    /**
     * @return string
     */
    public function getLocaleCode()
    {
        return $this->localeCode;
    }

    /**
     * @param string $localeCode
     */
    public function setLocaleCode($localeCode)
    {
        $this->localeCode = $localeCode;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return Price
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param Price $price
     */
    public function setPrice(Price $price)
    {
        $this->price = $price;
    }

    /**
     * @return TaxonCode
     */
    public function getMainTaxonCode()
    {
        return $this->mainTaxonCode;
    }

    /**
     * @param TaxonCode $mainTaxonCode
     */
    public function setMainTaxonCode(TaxonCode $mainTaxonCode)
    {
        $this->mainTaxonCode = $mainTaxonCode;
    }

    /**
     * @return Collection
     */
    public function getTaxonCodes()
    {
        return $this->taxonCodes;
    }

    /**
     * @param Collection $taxonCodes
     */
    public function setTaxonCodes(Collection $taxonCodes)
    {
        $this->taxonCodes = $taxonCodes;
    }

    /**
     * @return Collection
     */
    public function getAttributeValues()
    {
        return $this->attributeValues;
    }

    /**
     * @param Collection $attributeValues
     */
    public function setAttributeValues(Collection $attributeValues)
    {
        $this->attributeValues = $attributeValues;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }
}
