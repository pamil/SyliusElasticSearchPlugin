<?php

declare(strict_types=1);

namespace Sylius\ElasticSearchPlugin\Factory;

use Sylius\Component\Core\Model\ImageInterface;
use Sylius\ElasticSearchPlugin\Document\ImageDocument;

interface ImageDocumentFactoryInterface
{
    public function create(ImageInterface $image): ImageDocument;
}
