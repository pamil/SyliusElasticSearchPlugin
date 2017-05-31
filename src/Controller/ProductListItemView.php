<?php

namespace Sylius\ElasticSearchPlugin\Controller;

final class ProductListItemView
{
    /**
     * @var string
     */
    public $code;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $slug;

    /**
     * @var array
     */
    public $taxons;

    /**
     * @var array
     */
    public $variants;

    /**
     * @var array
     */
    public $attributes;

    /**
     * @var array
     */
    public $images;
}
