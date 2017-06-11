<?php

namespace Sylius\ElasticSearchPlugin\Controller;

class VariantView
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
     * @var PriceView
     */
    public $price;

    /**
     * @var ImageView[]
     */
    public $images = [];
}
