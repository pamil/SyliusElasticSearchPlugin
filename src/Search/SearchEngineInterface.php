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

use ONGR\ElasticsearchBundle\Result\DocumentIterator;
use Porpaginas\Result;
use Sylius\ElasticSearchPlugin\Search\Criteria\Criteria;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.k.e@gmail.com>
 */
interface SearchEngineInterface
{
    /**
     * @param Criteria $criteria
     *
     * @return Result
     */
    public function match(Criteria $criteria);
}
