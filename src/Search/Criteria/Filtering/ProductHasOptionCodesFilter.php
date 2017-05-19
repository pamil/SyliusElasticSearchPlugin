<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\ElasticSearchPlugin\Search\Criteria\Filtering;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.k.e@gmail.com>
 */
final class ProductHasOptionCodesFilter
{
    /**
     * @var array
     */
    private $codes;

    /**
     * @param array $codes
     */
    public function __construct(array $codes)
    {
        $this->codes = $codes;
    }

    /**
     * @return array
     */
    public function getCodes()
    {
        return $this->codes;
    }
}
