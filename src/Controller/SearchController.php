<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\ElasticSearchPlugin\Controller;

use FOS\RestBundle\View\ConfigurableViewHandlerInterface;
use FOS\RestBundle\View\View;
use Sylius\ElasticSearchPlugin\Form\Type\FilterSetType;
use Sylius\ElasticSearchPlugin\Search\Criteria\Criteria;
use Sylius\ElasticSearchPlugin\Search\Criteria\Filtering\ProductInChannelFilter;
use Sylius\ElasticSearchPlugin\Search\Criteria\Filtering\ProductInTaxonFilter;
use Sylius\ElasticSearchPlugin\Search\SearchEngineInterface;
use Sylius\Component\Core\Context\ShopperContextInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class SearchController
{
    /**
     * @var ConfigurableViewHandlerInterface
     */
    private $restViewHandler;

    /**
     * @var SearchEngineInterface
     */
    private $searchEngine;

    /**
     * @param ConfigurableViewHandlerInterface $restViewHandler
     * @param SearchEngineInterface $searchEngine
     */
    public function __construct(ConfigurableViewHandlerInterface $restViewHandler, SearchEngineInterface $searchEngine)
    {
        $this->restViewHandler = $restViewHandler;
        $this->searchEngine = $searchEngine;
    }

    /**
     * @param Request $request
     */
    public function searchAction(Request $request)
    {

    }
}
