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
     * @param Manager $manager
     */
    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
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

        return $repository->findDocuments($search);
    }
}
