<?php

namespace Sylius\ElasticSearchPlugin\Document;

use Doctrine\Common\Collections\ArrayCollection;
use ONGR\ElasticsearchBundle\Annotation as ElasticSearch;

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
     * @ElasticSearch\Property(type="text", options={"analyzer":"incrementalAnalyzer"})
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
     * @ElasticSearch\Property(type="text", options={"analyzer":"incrementalAnalyzer"})
     */
    private $description;

    /**
     * @var int
     *
     * @ElasticSearch\Embedded(class="ElasticSearchPlugin:Price")
     */
    private $price;

    /**
     * @var string
     *
     * @ElasticSearch\Property(type="keyword")
     */
    private $taxonCode;

    /**
     * @var ArrayCollection
     *
     * @ElasticSearch\Embedded(class="ElasticSearchPlugin:Attribute", multiple=true)
     */
    private $attributes;

    /**
     * @var \DateTime
     *
     * @ElasticSearch\Property(type="date")
     */
    private $createdAt;

    public function __construct()
    {
        $this->attributes = new ArrayCollection();
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
     * @return int
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param int $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * @return string
     */
    public function getTaxonCode()
    {
        return $this->taxonCode;
    }

    /**
     * @param string $taxonCode
     */
    public function setTaxonCode($taxonCode)
    {
        $this->taxonCode = $taxonCode;
    }

    /**
     * @return ArrayCollection
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param ArrayCollection $attributes
     */
    public function setAttributes(ArrayCollection $attributes)
    {
        $this->attributes = $attributes;
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
