<?php

declare(strict_types=1);

namespace Sylius\ElasticSearchPlugin\Factory;

use ONGR\ElasticsearchBundle\Collection\Collection;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Taxonomy\Model\TaxonTranslationInterface;
use Sylius\ElasticSearchPlugin\Document\ImageDocument;
use Sylius\ElasticSearchPlugin\Document\TaxonDocument;

class TaxonDocumentFactory implements TaxonDocumentFactoryInterface
{
    /** @var  string */
    private $taxonDocumentClass;

    /** @var ImageDocumentFactoryInterface */
    private $imageDocumentFactory;

    public function __construct(string $taxonDocumentClass, ImageDocumentFactoryInterface $imageDocumentFactory)
    {
        $this->taxonDocumentClass = $taxonDocumentClass;
        $this->imageDocumentFactory = $imageDocumentFactory;

    }

    public function create(TaxonInterface $taxon, LocaleInterface $localeCode): TaxonDocument
    {
        /** @var TaxonTranslationInterface $taxonTranslation */
        $taxonTranslation = $taxon->getTranslation($localeCode->getCode());

        /** @var TaxonDocument $taxonDocument */
        $taxonDocument = new $this->taxonDocumentClass();
        $taxonDocument->setCode($taxon->getCode());
        $taxonDocument->setSlug($taxonTranslation->getSlug());
        $taxonDocument->setPosition($taxon->getPosition());
        $taxonDocument->setDescription($taxonTranslation->getDescription());

        /** @var ImageDocument[] $images */
        $images = [];
        foreach ($taxon->getImages() as $image) {
            $images[] = $this->imageDocumentFactory->create($image);
        }
        $taxonDocument->setImages(new Collection($images));

        return $taxonDocument;
    }
}
