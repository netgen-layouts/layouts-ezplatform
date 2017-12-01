<?php

namespace Netgen\BlockManager\Ez\Collection\QueryType\Handler;

use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\SearchService;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Search\SearchHit;
use eZ\Publish\SPI\Persistence\Content\ObjectState\Handler as ObjectStateHandler;
use eZ\Publish\SPI\Persistence\Content\Section\Handler as SectionHandler;
use eZ\Publish\SPI\Persistence\Content\Type\Handler as ContentTypeHandler;
use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\Collection\QueryType\QueryTypeHandlerInterface;
use Netgen\BlockManager\Ez\Collection\QueryType\Handler\Traits;
use Netgen\BlockManager\Ez\ContentProvider\ContentProviderInterface;
use Netgen\BlockManager\Parameters\ParameterBuilderInterface;

/**
 * Handler for a query which retrieves the eZ locations from the repository
 * based on parameters provided in the query.
 *
 * @final
 */
class ContentSearchHandler implements QueryTypeHandlerInterface
{
    use Traits\ParentLocationTrait;
    use Traits\SortTrait;
    use Traits\QueryTypeFilterTrait;
    use Traits\MainLocationFilterTrait;
    use Traits\ContentTypeFilterTrait;
    use Traits\SectionFilterTrait;
    use Traits\ObjectStateFilterTrait;

    /**
     * @var \eZ\Publish\API\Repository\SearchService
     */
    private $searchService;

    /**
     * @var array
     */
    private $languages = array();

    public function __construct(
        LocationService $locationService,
        SearchService $searchService,
        ContentTypeHandler $contentTypeHandler,
        SectionHandler $sectionHandler,
        ObjectStateHandler $objectStateHandler,
        ContentProviderInterface $contentProvider
    ) {
        $this->searchService = $searchService;

        $this->setContentTypeHandler($contentTypeHandler);
        $this->setSectionHandler($sectionHandler);
        $this->setObjectStateHandler($objectStateHandler);
        $this->setContentProvider($contentProvider);
        $this->setLocationService($locationService);
    }

    /**
     * Sets the current siteaccess languages into the handler.
     *
     * @param array $languages
     */
    public function setLanguages(array $languages = null)
    {
        $this->languages = is_array($languages) ? $languages : array();
    }

    public function buildParameters(ParameterBuilderInterface $builder)
    {
        $this->buildParentLocationParameters($builder);
        $this->buildSortParameters($builder);
        $this->buildQueryTypeParameters($builder);
        $this->buildMainLocationParameters($builder);
        $this->buildContentTypeFilterParameters($builder, array(self::GROUP_ADVANCED));
        $this->buildSectionFilterParameters($builder, array(self::GROUP_ADVANCED));
        $this->buildObjectStateFilterParameters($builder, array(self::GROUP_ADVANCED));
    }

    public function getValues(Query $query, $offset = 0, $limit = null)
    {
        $parentLocation = $this->getParentLocation($query);

        if (!$parentLocation instanceof Location) {
            return array();
        }

        $locationQuery = $this->buildQuery($parentLocation, $query);
        $locationQuery->offset = $offset;
        $locationQuery->limit = $limit;

        $searchResult = $this->searchService->findLocations(
            $locationQuery,
            array('languages' => $this->languages)
        );

        return array_map(
            function (SearchHit $searchHit) {
                return $searchHit->valueObject;
            },
            $searchResult->searchHits
        );
    }

    public function getCount(Query $query)
    {
        $parentLocation = $this->getParentLocation($query);

        if (!$parentLocation instanceof Location) {
            return 0;
        }

        $locationQuery = $this->buildQuery($parentLocation, $query);
        $locationQuery->limit = 0;

        $searchResult = $this->searchService->findLocations(
            $locationQuery,
            array('languages' => $this->languages)
        );

        return $searchResult->totalCount;
    }

    public function isContextual(Query $query)
    {
        return $query->getParameter('use_current_location')->getValue() === true;
    }

    /**
     * Builds the query from current parameters.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Location $parentLocation
     * @param \Netgen\BlockManager\API\Values\Collection\Query $query
     *
     * @return \eZ\Publish\API\Repository\Values\Content\LocationQuery
     */
    private function buildQuery(Location $parentLocation, Query $query)
    {
        $locationQuery = new LocationQuery();

        $criteria = array(
            new Criterion\Subtree($parentLocation->pathString),
            new Criterion\Visibility(Criterion\Visibility::VISIBLE),
            new Criterion\LogicalNot(new Criterion\LocationId($parentLocation->id)),
            $this->getMainLocationFilterCriteria($query),
            $this->getQueryTypeFilterCriteria($query, $parentLocation),
            $this->getContentTypeFilterCriteria($query),
            $this->getSectionFilterCriteria($query),
            $this->getObjectStateFilterCriteria($query),
        );

        $locationQuery->filter = new Criterion\LogicalAnd(array_filter($criteria));
        $locationQuery->sortClauses = $this->getSortClauses($query, $parentLocation);

        return $locationQuery;
    }
}
