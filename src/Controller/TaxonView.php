<?php

namespace Sylius\ElasticSearchPlugin\Controller;

final class TaxonView
{
    /**
     * @var string
     */
    public $code;

    /**
     * @var string
     */
    public $slug;

    /**
     * @var int
     */
    public $position;

    /**
     * @var ImageView[]
     */
    public $images = [];

    /**
     * @var string
     */
    public $description;
}
