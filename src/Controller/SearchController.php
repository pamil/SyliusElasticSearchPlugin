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
use ONGR\FilterManagerBundle\Search\FilterManagerInterface;
use Sylius\ElasticSearchPlugin\Factory\ProductListViewFactoryInterface;
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
     * @var ProductListViewFactoryInterface
     */
    private $productListViewFactory;

    /**
     * @var FilterManagerInterface
     */
    private $filterManager;

    /**
     * @param ViewHandlerInterface $restViewHandler
     * @param ProductListViewFactoryInterface $productListViewFactory
     * @param FilterManagerInterface $filterManager
     */
    public function __construct(
        ViewHandlerInterface $restViewHandler,
        ProductListViewFactoryInterface $productListViewFactory,
        FilterManagerInterface $filterManager
    ) {
        $this->restViewHandler = $restViewHandler;
        $this->productListViewFactory = $productListViewFactory;
        $this->filterManager = $filterManager;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function __invoke(Request $request)
    {
        $response = $this->filterManager->handleRequest($request);

        return $this->restViewHandler->handle(
            View::create(
                $this->productListViewFactory->createFromSearchResponse($response),
                Response::HTTP_OK
            )
        );
    }
}
