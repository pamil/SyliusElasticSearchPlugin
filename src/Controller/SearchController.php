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
use Sylius\ElasticSearchPlugin\Search\SearchEngineInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.k.e@gmail.com>
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
