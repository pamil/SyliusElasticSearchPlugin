<?php

namespace Sylius\ElasticSearchPlugin\Search\Criteria;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.k.e@gmail.com>
 */
final class Criteria
{
    /**
     * @var string
     */
    private $documentClass;

    /**
     * @var Paginating
     */
    private $paginating;

    /**
     * @var Ordering
     */
    private $ordering;

    /**
     * @var Filtering
     */
    private $filtering;

    /**
     * @param string $documentClass
     * @param Paginating $paginating
     * @param Ordering $ordering
     * @param Filtering $filtering
     */
    private function __construct($documentClass, Paginating $paginating, Ordering $ordering, Filtering $filtering)
    {
        $this->documentClass = $documentClass;
        $this->paginating = $paginating;
        $this->ordering = $ordering;
        $this->filtering = $filtering;
    }

    /**
     * @param string $documentClass
     * @param array $parameters
     *
     * @return Criteria
     */
    public static function fromQueryParameters($documentClass, array $parameters)
    {
        $paginating = Paginating::fromQueryParameters($parameters);
        $ordering = Ordering::fromQueryParameters($parameters);
        $filtering = Filtering::fromQueryParameters($parameters);

        return new self($documentClass, $paginating, $ordering, $filtering);
    }

    /**
     * @return string
     */
    public function documentClass()
    {
        return $this->documentClass;
    }

    /**
     * @return Paginating
     */
    public function paginating()
    {
        return $this->paginating;
    }

    /**
     * @return Ordering
     */
    public function ordering()
    {
        return $this->ordering;
    }

    /**
     * @return Filtering
     */
    public function filtering()
    {
        return $this->filtering;
    }
}
