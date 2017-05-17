<?php

namespace Sylius\ElasticSearchPlugin\Search\Elastic;

use FOS\ElasticaBundle\Manager\RepositoryManagerInterface;
use FOS\ElasticaBundle\Repository;
use Sylius\ElasticSearchPlugin\Search\Criteria\Criteria;
use Sylius\ElasticSearchPlugin\Search\Elastic\Applicator\SearchCriteriaApplicatorInterface;
use Sylius\ElasticSearchPlugin\Search\Elastic\Factory\Search\SearchFactoryInterface;
use Sylius\ElasticSearchPlugin\Search\SearchEngineInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class ElasticSearchEngine implements SearchEngineInterface
{
    /**
     * @var RepositoryManagerInterface
     */
    private $repositoryManager;

    /**
     * @var SearchFactoryInterface
     */
    private $searchFactory;

    /**
     * @var SearchCriteriaApplicatorInterface[]
     */
    private $searchCriteriaApplicators = [];

    /**
     * @param RepositoryManagerInterface $repositoryManager
     * @param SearchFactoryInterface $searchFactory
     */
    public function __construct(RepositoryManagerInterface $repositoryManager, SearchFactoryInterface $searchFactory)
    {
        $this->repositoryManager = $repositoryManager;
        $this->searchFactory = $searchFactory;
    }

    /**
     * @param SearchCriteriaApplicatorInterface $searchCriteriaApplicator
     * @param string $criteriaClass
     */
    public function addSearchCriteriaApplicator(
        SearchCriteriaApplicatorInterface $searchCriteriaApplicator,
        $criteriaClass
    ) {
        $this->searchCriteriaApplicators[$criteriaClass] = $searchCriteriaApplicator;
    }

    /**
     * {@inheritdoc}
     */
    public function match(Criteria $criteria)
    {
        $search = $this->searchFactory->create();
        $filters = array_merge($criteria->getFiltering()->getFields(), [$criteria->getOrdering()]);

        foreach ($filters as $filter) {
            if (!is_object($filter)) {
                continue;
            }

            if (isset($this->searchCriteriaApplicators[get_class($filter)])) {
                $this->searchCriteriaApplicators[get_class($filter)]->apply($filter, $search);
            }
        }

        /** @var Repository $repository */
        $repository = $this->repositoryManager->getRepository($criteria->getResourceAlias());

        return $repository->createPaginatorAdapter($search->toArray());
    }
}
