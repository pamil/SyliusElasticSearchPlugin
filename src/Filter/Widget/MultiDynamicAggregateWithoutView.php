<?php

declare(strict_types=1);

namespace Sylius\ElasticSearchPlugin\Filter\Widget;

use ONGR\ElasticsearchBundle\Result\DocumentIterator;
use ONGR\FilterManagerBundle\Filter\ViewData;
use ONGR\FilterManagerBundle\Filter\Widget\Dynamic\MultiDynamicAggregate;
use Sylius\ElasticSearchPlugin\Filter\ViewData\EmptyViewData;

final class MultiDynamicAggregateWithoutView extends MultiDynamicAggregate
{
    /**
     * {@inheritdoc}
     */
    public function createViewData()
    {
        return new EmptyViewData();
    }

    /**
     * {@inheritdoc}
     */
    public function getViewData(DocumentIterator $result, ViewData $data): ViewData
    {
        return $data;
    }
}
