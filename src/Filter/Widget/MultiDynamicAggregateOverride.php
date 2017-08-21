<?php
/**
 * Created by PhpStorm.
 * User: psihius
 * Date: 18.08.2017
 * Time: 18:50
 */

namespace Sylius\ElasticSearchPlugin\Filter\Widget;

use ONGR\FilterManagerBundle\Filter\Widget\Dynamic\MultiDynamicAggregate;
use ONGR\ElasticsearchDSL\Aggregation\Bucketing\TermsAggregation;
use ONGR\ElasticsearchDSL\Aggregation\Bucketing\FilterAggregation;
use ONGR\ElasticsearchDSL\Aggregation\Bucketing\NestedAggregation;
use ONGR\ElasticsearchDSL\Query\MatchAllQuery;
use ONGR\ElasticsearchDSL\Search;
use ONGR\FilterManagerBundle\Filter\FilterState;

/**
 * Class MultiDynamicAggregateOverride implements the size option handling in the preProcessSearch method to be able
 * to set the amount of aggregation buckets returned, otherwise Elastic returns only 10 by default and there is not way
 * to change that globally - only via the query options for the aggregation object.
 * All other multi dynamic aggregates are extending from this class in the plugin. Size option is implemented in other
 * types of filters by the FilterManagerBundle
 * 
 * @package Sylius\ElasticSearchPlugin\Filter\Widget
 */
class MultiDynamicAggregateOverride extends MultiDynamicAggregate
{
    /**
     * {@inheritdoc}
     */
    public function preProcessSearch(Search $search, Search $relatedSearch, FilterState $state = null)
    {
        list($path, $field) = explode('>', $this->getDocumentField());
        $filter = !empty($filter = $relatedSearch->getPostFilters()) ? $filter : new MatchAllQuery();
        $aggregation = new NestedAggregation($state->getName(), $path);
        $nameAggregation = new TermsAggregation('name', $this->getNameField());
        $valueAggregation = new TermsAggregation('value', $field);
        $filterAggregation = new FilterAggregation($state->getName() . '-filter');
        $nameAggregation->addAggregation($valueAggregation);
        $aggregation->addAggregation($nameAggregation);
        $filterAggregation->setFilter($filter);

        if ($this->getSortType()) {
            $valueAggregation->addParameter('order', [$this->getSortType() => $this->getSortOrder()]);
        }

        if ($this->getOption('size')) {
            $valueAggregation->addParameter('size',$this->getOption('size'));
        }

        if ($state->isActive()) {
            foreach ($state->getValue() as $key => $term) {
                $terms = $state->getValue();
                unset($terms[$key]);

                $this->addSubFilterAggregation(
                    $filterAggregation,
                    $aggregation,
                    $terms,
                    $key
                );
            }
        }

        $this->addSubFilterAggregation(
            $filterAggregation,
            $aggregation,
            $state->getValue() ? $state->getValue() : [],
            'all-selected'
        );

        $search->addAggregation($filterAggregation);

        if ($this->getShowZeroChoices()) {
            $search->addAggregation($aggregation);
        }
    }
}
