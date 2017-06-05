<?php

namespace Sylius\ElasticSearchPlugin\Document;

use ONGR\ElasticsearchBundle\Annotation as ElasticSearch;

/**
 * @ElasticSearch\Object
 */
final class AttributeValue
{
    /**
     * @var string
     *
     * @ElasticSearch\Property(type="text")
     */
    private $value;

    /**
     * @var Attribute
     *
     * @ElasticSearch\Embedded(class="SyliusElasticSearchPlugin:Attribute")
     */
    private $attribute;

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return Attribute
     */
    public function getAttribute()
    {
        return $this->attribute;
    }

    /**
     * @param Attribute $attribute
     */
    public function setAttribute(Attribute $attribute)
    {
        $this->attribute = $attribute;
    }
}
