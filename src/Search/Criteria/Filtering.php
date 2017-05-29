<?php

namespace Sylius\ElasticSearchPlugin\Search\Criteria;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.k.e@gmail.com>
 */
final class Filtering
{
    /**
     * @var array
     */
    private $fields;

    /**
     * @param array $fields
     */
    private function __construct(array $fields)
    {
        $this->fields = $fields;
    }

    /**
     * @param array $queryParameters
     *
     * @return Filtering
     */
    public static function fromQueryParameters(array $queryParameters)
    {
        $fields = $queryParameters;

        unset($fields['page']);
        unset($fields['limit']);
        unset($fields['sort']);

        return new self($fields);
    }

    /**
     * @return array
     */
    public function fields()
    {
        return $this->fields;
    }
}
