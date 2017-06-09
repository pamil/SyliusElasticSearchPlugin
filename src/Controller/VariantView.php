<?php

namespace Sylius\ElasticSearchPlugin\Controller;

final class VariantView
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
    public $price;

    /**
     * @var array
     */
    public $images = [];
}
