<?php

namespace Sylius\ElasticSearchPlugin\Search\Elastic\Factory\Sort;

use Sylius\ElasticSearchPlugin\Search\Criteria\Ordering;
use ONGR\ElasticsearchDSL\Sort\FieldSort;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.k.e@gmail.com>
 */
final class SortByFieldQueryFactory implements SortFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function create(Ordering $ordering)
    {
        return new FieldSort($ordering->field(), $ordering->direction());
    }
}
