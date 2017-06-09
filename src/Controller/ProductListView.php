<?php

namespace Sylius\ElasticSearchPlugin\Controller;

use ONGR\FilterManagerBundle\Filter\ViewData;

class ProductListView
{
    /**
     * @var int
     */
    public $page;

    /**
     * @var int
     */
    public $limit;

    /**
     * @var int
     */
    public $total;

    /**
     * @var int
     */
    public $pages;

    /**
     * @var ProductView[]
     */
    public $items = [];

    /**
     * @var ViewData[]
     */
    public $filters;
}
