<?php

declare(strict_types=1);

namespace Sylius\ElasticSearchPlugin\Factory;

use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\ElasticSearchPlugin\Document\TaxonDocument;

interface TaxonDocumentFactoryInterface
{
    public function create(TaxonInterface $taxon, LocaleInterface $localeCode): TaxonDocument;
}
