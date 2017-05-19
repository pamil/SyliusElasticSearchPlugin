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
final class ProductInPriceRangeFilter
{
    /**
     * @var int
     */
    private $graterThan;

    /**
     * @var int
     */
    private $lessThan;

    /**
     * @param int $graterThan
     * @param int $lessThan
     */
    public function __construct($graterThan, $lessThan)
    {
        $this->graterThan = $graterThan;
        $this->lessThan = $lessThan;
    }

    /**
     * @return int
     */
    public function getGraterThan()
    {
        return $this->graterThan;
    }

    /**
     * @return int
     */
    public function getLessThan()
    {
        return $this->lessThan;
    }
}
