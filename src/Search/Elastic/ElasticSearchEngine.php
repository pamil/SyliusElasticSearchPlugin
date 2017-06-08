<?php

namespace Sylius\ElasticSearchPlugin\Search\Elastic;

use ONGR\ElasticsearchBundle\Service\Manager;
use Porpaginas\Arrays\ArrayResult;
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
     * @param SearchCriteriaApplicatorInterface $sortingApplicator
     */
    public function __construct(Manager $manager, SearchCriteriaApplicatorInterface $sortingApplicator)
    {
        $this->manager = $manager;
    }

    /**
     * @param SearchCriteriaApplicatorInterface $searchCriteriaApplicator
     */
    public function addSearchCriteriaApplicator(SearchCriteriaApplicatorInterface $searchCriteriaApplicator)
    {
        $this->searchCriteriaApplicators[] = $searchCriteriaApplicator;
    }

    /**
     * {@inheritdoc}
     */
    public function match(Criteria $criteria)
    {
        $repository = $this->manager->getRepository($criteria->documentClass());

        $search = $repository->createSearch();
        foreach ($this->searchCriteriaApplicators as $searchCriteriaApplicator) {
            if ($searchCriteriaApplicator->supports($criteria)) {
                $searchCriteriaApplicator->apply($criteria, $search);
            }
        }

        $search->setSize($criteria->paginating()->itemsPerPage());
        $search->setFrom($criteria->paginating()->offset());

        return $repository->findDocuments($search);
    }
}
