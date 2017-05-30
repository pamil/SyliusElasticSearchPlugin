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

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
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
     * @var ViewHandlerInterface
     */
    private $restViewHandler;

    /**
     * @var SearchEngineInterface
     */
    private $searchEngine;

    /**
     * @param ViewHandlerInterface $restViewHandler
     * @param SearchEngineInterface $searchEngine
     */
    public function __construct(ViewHandlerInterface $restViewHandler, SearchEngineInterface $searchEngine)
    {
        $this->restViewHandler = $restViewHandler;
        $this->searchEngine = $searchEngine;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function __invoke(Request $request)
    {
        $content = json_decode($request->getContent(), true);

        if (null === $content) {
            $content = $request->query->all();
        }

        $criteria = Criteria::fromQueryParameters(Product::class, $content);

        $result = $this->searchEngine->match($criteria);
        $page = $result->take($criteria->paginating()->offset(), $criteria->paginating()->itemsPerPage());

        return $this->restViewHandler->handle(View::create($page, Response::HTTP_OK));
    }
}
