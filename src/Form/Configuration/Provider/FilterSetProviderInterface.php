<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\ElasticSearchPlugin\Form\Configuration\Provider;

use Sylius\ElasticSearchPlugin\Exception\FilterSetConfigurationNotFoundException;
use Sylius\ElasticSearchPlugin\Form\Configuration\FilterSet;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.k.e@gmail.com>
 */
interface FilterSetProviderInterface
{
    /**
     * @param string $filterSetName
     *
     * @return FilterSet
     *
     * @throws FilterSetConfigurationNotFoundException
     */
    public function getFilterSetConfiguration($filterSetName);
}
