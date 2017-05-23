<?php

namespace Sylius\ElasticSearchPlugin\Search\Elastic;

use ONGR\ElasticsearchBundle\Service\Manager;
use Sylius\ElasticSearchPlugin\Search\Criteria\Criteria;
use Sylius\ElasticSearchPlugin\Search\Elastic\Applicator\SearchCriteriaApplicatorInterface;
use Sylius\ElasticSearchPlugin\Search\SearchEngineInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.k.e@gmail.com>
 */
final class ElasticSearchEngine implements SearchEngineInterface
{
    /**
     * @var Manager
     */
    private $manager;

    /**
     * @var SearchCriteriaApplicatorInterface[]
     */
    private $searchCriteriaApplicators = [];

    /**
     * @var SearchCriteriaApplicatorInterface
     */
    private $sortingApplicator;

    /**
     * @param Manager $manager
     * @param SearchCriteriaApplicatorInterface $sortingApplicator
     */
    public function __construct(Manager $manager, SearchCriteriaApplicatorInterface $sortingApplicator)
    {
        $this->manager = $manager;
        $this->sortingApplicator = $sortingApplicator;
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
        $repository = $this->manager->getRepository($criteria->getResourceAlias());

        $search = $repository->createSearch();

        foreach ($criteria->getFiltering()->getFields() as $filter) {
            if (!is_object($filter)) {
                continue;
            }

            if (isset($this->searchCriteriaApplicators[get_class($filter)])) {
                $this->searchCriteriaApplicators[get_class($filter)]->apply($filter, $search);
            }
        }

        $this->sortingApplicator->applyOrdering($criteria->getOrdering(), $search);

        return $repository->findDocuments($search);
    }
}
