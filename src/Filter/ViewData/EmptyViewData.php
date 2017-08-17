<?php

declare(strict_types=1);

namespace Sylius\ElasticSearchPlugin\Filter\ViewData;

use ONGR\FilterManagerBundle\Filter\ViewData;

final class EmptyViewData extends ViewData
{
    /**
     * {@inheritdoc}
     */
    public function getSerializableData(): array
    {
        return [];
    }
}
