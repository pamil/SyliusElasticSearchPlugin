<?php

namespace Sylius\ElasticSearchPlugin\Document;

use ONGR\ElasticsearchBundle\Annotation as ElasticSearch;

/**
 * @ElasticSearch\Object
 */
final class AttributeValueDocument
{
    /**
     * @var string
     *
     * @ElasticSearch\Property(type="text")
     */
    private $value;

    /**
     * @var AttributeDocument
     *
     * @ElasticSearch\Embedded(class="SyliusElasticSearchPlugin:AttributeDocument")
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
     * @return AttributeDocument
     */
    public function getAttribute()
    {
        return $this->attribute;
    }

    /**
     * @param AttributeDocument $attribute
     */
    public function setAttribute(AttributeDocument $attribute)
    {
        $this->attribute = $attribute;
    }
}
