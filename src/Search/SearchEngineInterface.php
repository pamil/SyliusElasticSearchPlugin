<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\ElasticSearchPlugin\Search;

use FOS\ElasticaBundle\Paginator\PaginatorAdapterInterface;
use Sylius\ElasticSearchPlugin\Search\Criteria\Criteria;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.k.e@gmail.com>
 */
interface SearchEngineInterface
{
    /**
     * @param Criteria $criteria
     *
     * @return PaginatorAdapterInterface
     */
    public function match(Criteria $criteria);
}
