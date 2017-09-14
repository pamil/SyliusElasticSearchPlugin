<?php

declare(strict_types=1);

namespace Sylius\ElasticSearchPlugin\Factory;

use Sylius\Component\Core\Model\ImageInterface;
use Sylius\ElasticSearchPlugin\Document\ImageDocument;

class ImageDocumentFactory implements ImageDocumentFactoryInterface
{
    /** @var string */
    private $imageDocumentClass;

    public function __construct(string $imageDocumentClass)
    {
        $this->imageDocumentClass = $imageDocumentClass;
    }

    public function create(ImageInterface $image): ImageDocument
    {
        /** @var ImageDocument $imageDocument */
        $imageDocument = new $this->imageDocumentClass();
        $imageDocument->setCode($image->getType());
        $imageDocument->setPath($image->getPath());

        return $imageDocument;
    }
}
