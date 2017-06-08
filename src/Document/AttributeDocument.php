<?php

namespace Sylius\ElasticSearchPlugin\Document;

use ONGR\ElasticsearchBundle\Annotation as ElasticSearch;

/**
 * @ElasticSearch\Object
 */
final class AttributeDocument
{
    /**
     * @var string
     *
     * @ElasticSearch\Property(type="keyword")
     */
    private $code;

    /**
     * @var string
     *
     * @ElasticSearch\Property(type="text")
     */
    private $name;

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
}
