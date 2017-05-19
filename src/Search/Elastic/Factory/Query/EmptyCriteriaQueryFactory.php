<?php

namespace Sylius\ElasticSearchPlugin\Search\Elastic\Factory\Query;

use ONGR\ElasticsearchDSL\Query\MatchAllQuery;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.k.e@gmail.com>
 */
final class EmptyCriteriaQueryFactory implements QueryFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function create(array $parameters = [])
    {
        return new MatchAllQuery();
    }
}
