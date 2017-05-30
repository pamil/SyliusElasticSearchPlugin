<?php

namespace Sylius\ElasticSearchPlugin\Search\Elastic\Factory\Query;

use ONGR\ElasticsearchDSL\Query\Joining\NestedQuery;
use ONGR\ElasticsearchDSL\Query\TermLevel\TermQuery;
use Sylius\ElasticSearchPlugin\Exception\MissingQueryParameterException;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.k.e@gmail.com>
 */
final class ProductInProductTaxonsQueryFactory implements QueryFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function create(array $parameters = [])
    {
        if (!isset($parameters['taxon_code'])) {
            throw new MissingQueryParameterException('taxon_code', get_class($this));
        }

        return new NestedQuery('productTaxons', new TermQuery('productTaxons.taxon.code', strtolower($parameters['taxon_code'])));
    }
}
