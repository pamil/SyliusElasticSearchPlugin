<?php

namespace Sylius\ElasticSearchPlugin\Search\Elastic\Factory\Query;

use ONGR\ElasticsearchDSL\Query\Joining\NestedQuery;
use ONGR\ElasticsearchDSL\Query\TermLevel\TermQuery;
use Sylius\ElasticSearchPlugin\Exception\MissingQueryParameterException;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class ProductInChannelQueryFactory implements QueryFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function create(array $parameters = [])
    {
        if (!isset($parameters['channel_code'])) {
            throw new MissingQueryParameterException('channel_code', get_class($this));
        }

        return new NestedQuery('channels', new TermQuery('channels.code', strtolower($parameters['channel_code'])));
    }
}
