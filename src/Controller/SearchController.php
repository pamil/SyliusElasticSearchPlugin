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
use Sylius\ElasticSearchPlugin\Document\Product;
use Sylius\ElasticSearchPlugin\Search\Criteria\Criteria;
use Sylius\ElasticSearchPlugin\Search\SearchEngineInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
     *
     * @return Response
     */
    public function searchAction(Request $request)
    {
        $content = $request->getContent();
        $criteria = Criteria::fromQueryParameters(Product::class, json_decode($content, true));

        $result = $this->searchEngine->match($criteria);

        $view = View::create($result);

        return $this->restViewHandler->handle($view);
    }
}
