<?php

declare(strict_types=1);

namespace Sylius\ElasticSearchPlugin\Filter\Widget;


use ONGR\ElasticsearchDSL\Query\Joining\NestedQuery;
use ONGR\ElasticsearchDSL\Query\TermLevel\TermQuery;
use ONGR\ElasticsearchDSL\Search;
use ONGR\FilterManagerBundle\Filter\FilterState;
use ONGR\FilterManagerBundle\Filter\Widget\Choice\SingleTermChoice;
use ONGR\FilterManagerBundle\Search\SearchRequest;

/**
 * This class provides single terms choice.
 */
class SingleNestedTermChoice extends SingleTermChoice
{

    /**
     * {@inheritdoc}
     */
    public function modifySearch(Search $search, FilterState $state = null, SearchRequest $request = null)
    {
        list($path, $field) = explode('>', $this->getDocumentField());

        if ($state && $state->isActive()) {
            $search->addPostFilter(
                new NestedQuery(
                    $path,
                    new TermQuery($field, $state->getValue())
                )
            );
        }
    }

}
