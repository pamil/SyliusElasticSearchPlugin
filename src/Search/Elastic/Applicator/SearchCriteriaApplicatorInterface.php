<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\ElasticSearchPlugin\Search\Elastic\Applicator;

use ONGR\ElasticsearchDSL\Search;
use Sylius\ElasticSearchPlugin\Search\Criteria\Criteria;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.k.e@gmail.com>
 */
interface SearchCriteriaApplicatorInterface
{
    /**
     * @param Criteria $criteria
     * @param Search $search
     */
    public function apply(Criteria $criteria, Search $search);

    /**
     * @param Criteria $criteria
     *
     * @return bool
     */
    public function supports(Criteria $criteria);
}
