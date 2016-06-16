<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Collection\QueryType\Handler;

use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause;
use eZ\Publish\API\Repository\Values\Content\Search\SearchHit;
use Netgen\BlockManager\Collection\QueryType\QueryTypeHandlerInterface;
use eZ\Publish\API\Repository\ContentTypeService;
use Netgen\BlockManager\Parameters\Parameter;
use Netgen\Bundle\EzPublishBlockManagerBundle\Parameters\Parameter as EzParameter;
use eZ\Publish\API\Repository\SearchService;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use Exception;

class EzContentSearchHandler implements QueryTypeHandlerInterface
{
    /**
     * @const int
     */
    const DEFAULT_LIMIT = 25;

    /**
     * @var array
     */
    protected $contentTypes;

    /**
     * @var \eZ\Publish\API\Repository\LocationService
     */
    protected $locationService;

    /**
     * @var \eZ\Publish\API\Repository\ContentTypeService
     */
    protected $contentTypeService;

    /**
     * @var \eZ\Publish\API\Repository\SearchService
     */
    protected $searchService;

    /**
     * @var array
     */
    protected $sortClauses = array(
        'default' => SortClause\DatePublished::class,
        'date_published' => SortClause\DatePublished::class,
        'date_modified' => SortClause\DateModified::class,
        'content_name' => SortClause\ContentName::class,
        'location_priority' => SortClause\Location\Priority::class,
        Location::SORT_FIELD_PATH => SortClause\Location\Path::class,
        Location::SORT_FIELD_PUBLISHED => SortClause\DatePublished::class,
        Location::SORT_FIELD_MODIFIED => SortClause\DateModified::class,
        Location::SORT_FIELD_SECTION => SortClause\SectionIdentifier::class,
        Location::SORT_FIELD_DEPTH => SortClause\Location\Depth::class,
        Location::SORT_FIELD_PRIORITY => SortClause\Location\Priority::class,
        Location::SORT_FIELD_NAME => SortClause\ContentName::class,
        Location::SORT_FIELD_NODE_ID => SortClause\Location\Id::class,
        Location::SORT_FIELD_CONTENTOBJECT_ID => SortClause\ContentId::class,
    );

    /**
     * @var array
     */
    protected $sortDirections = array(
        Location::SORT_ORDER_ASC => LocationQuery::SORT_ASC,
        Location::SORT_ORDER_DESC => LocationQuery::SORT_DESC,
    );

    /**
     * Constructor.
     *
     * @param \eZ\Publish\API\Repository\LocationService $locationService
     * @param \eZ\Publish\API\Repository\ContentTypeService $contentTypeService
     * @param \eZ\Publish\API\Repository\SearchService $searchService
     */
    public function __construct(
        LocationService $locationService,
        ContentTypeService $contentTypeService,
        SearchService $searchService
    ) {
        $this->locationService = $locationService;
        $this->contentTypeService = $contentTypeService;
        $this->searchService = $searchService;
    }

    /**
     * Returns the array specifying query parameters.
     *
     * The keys are parameter identifiers.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterInterface[]
     */
    public function getParameters()
    {
        return array(
            'parent_location_id' => new EzParameter\EzLocation(array(), true),
            'sort_direction' => new Parameter\Choice(
                array(
                    'options' => array(
                        'Descending' => LocationQuery::SORT_DESC,
                        'Ascending' => LocationQuery::SORT_ASC,
                    ),
                ),
                true
            ),
            'sort_type' => new Parameter\Choice(
                array(
                    'options' => array(
                        'Published' => 'date_published',
                        'Modified' => 'date_modified',
                        'Alphabetical' => 'content_name',
                        'Priority' => 'location_priority',
                        'Defined by parent' => 'defined_by_parent',
                    ),
                ),
                true
            ),
            'limit' => new Parameter\Integer(array('min' => 1)),
            'offset' => new Parameter\Integer(array('min' => 0), true),
            'query_type' => new Parameter\Choice(
                array(
                    'options' => array(
                        'List' => 'list',
                        'Tree' => 'tree',
                    ),
                ),
                true
            ),
            'filter_by_content_type' => new Parameter\Compound\Boolean(
                array(
                    'content_types' => new Parameter\Choice(
                        array(
                            'options' => $this->getContentTypes(),
                            'multiple' => true,
                        )
                    ),
                    'content_types_filter' => new Parameter\Choice(
                        array(
                            'options' => array(
                                'Include content types' => 'include',
                                'Exclude content types' => 'exclude',
                            ),
                        ),
                        true
                    ),
                )
            ),
        );
    }

    /**
     * Returns the values from the query.
     *
     * @param array $parameters
     * @param int $offset
     * @param int $limit
     *
     * @return \eZ\Publish\API\Repository\Values\Content\ContentInfo[]
     */
    public function getValues(array $parameters, $offset = 0, $limit = null)
    {
        $parentLocation = $this->getParentLocation(
            $parameters['parent_location_id']
        );

        if (!$parentLocation instanceof Location) {
            return array();
        }

        $searchResult = $this->searchService->findLocations(
            $this->buildQuery($parentLocation, $parameters)
        );

        return array_map(
            function (SearchHit $searchHit) {
                return $searchHit->valueObject->contentInfo;
            },
            $searchResult->searchHits
        );
    }

    /**
     * Returns the value count from the query.
     *
     * @param array $parameters
     *
     * @return int
     */
    public function getCount(array $parameters)
    {
        $parentLocation = $this->getParentLocation(
            $parameters['parent_location_id']
        );

        if (!$parentLocation instanceof Location) {
            return 0;
        }

        $searchResult = $this->searchService->findLocations(
            $this->buildQuery($parentLocation, $parameters, true)
        );

        return $searchResult->totalCount;
    }

    /**
     * Returns all content types from eZ Publish.
     * Uses closure to make sure content types are fetched only when used.
     *
     * @return \Closure
     */
    protected function getContentTypes()
    {
        return function () {
            if ($this->contentTypes === null) {
                $groups = $this->contentTypeService->loadContentTypeGroups();
                foreach ($groups as $group) {
                    $contentTypes = $this->contentTypeService->loadContentTypes($group);
                    foreach ($contentTypes as $contentType) {
                        $contentTypeNames = array_values($contentType->getNames());
                        $this->contentTypes[$contentTypeNames[0]] = $contentType->identifier;
                    }
                }
            }

            return $this->contentTypes;
        };
    }

    /**
     * Returns the parent location by its ID.
     *
     * @param int $parentLocationId
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Location
     */
    protected function getParentLocation($parentLocationId)
    {
        try {
            $parentLocation = $this->locationService->loadLocation(
                $parentLocationId
            );

            return $parentLocation->invisible ? null : $parentLocation;
        } catch (Exception $e) {
            return;
        }
    }

    /**
     * Builds the query from current parameters.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Location $parentLocation
     * @param array $parameters
     * @param bool $buildCountQuery
     *
     * @return \eZ\Publish\API\Repository\Values\Content\LocationQuery
     */
    protected function buildQuery(Location $parentLocation, array $parameters, $buildCountQuery = false)
    {
        $query = new LocationQuery();

        $criteria = array(
            new Criterion\Subtree($parentLocation->pathString),
            new Criterion\Visibility(Criterion\Visibility::VISIBLE),
            new Criterion\LogicalNot(new Criterion\LocationId($parentLocation->id)),
        );

        if (!isset($parameters['query_type']) || $parameters['query_type'] === 'list') {
            $criteria[] = new Criterion\Location\Depth(
                Criterion\Operator::EQ, $parentLocation->depth + 1
            );
        }

        if (!empty($parameters['filter_by_content_type']) && !empty($parameters['content_types'])) {
            $contentTypeFilter = new Criterion\ContentTypeIdentifier($parameters['content_types']);

            if (isset($parameters['content_types_filter']) && $parameters['content_types_filter'] === 'exclude') {
                $contentTypeFilter = new Criterion\LogicalNot($contentTypeFilter);
            }

            $criteria[] = $contentTypeFilter;
        }

        $query->filter = new Criterion\LogicalAnd($criteria);

        $query->limit = 0;
        if (!$buildCountQuery) {
            $query->offset = isset($parameters['offset']) && is_int($parameters['offset']) ?
                $parameters['offset'] :
                0;

            $query->limit = isset($parameters['limit']) && is_int($parameters['limit']) ?
                $parameters['limit'] :
                self::DEFAULT_LIMIT;
        }

        $sortType = 'default';
        if (!empty($parameters['sort_type'])) {
            $sortType = $parameters['sort_type'] === 'defined_by_parent' ?
                $parentLocation->sortField :
                $parameters['sort_type'];
        }

        $sortDirection = LocationQuery::SORT_DESC;
        if (!empty($parameters['sort_direction'])) {
            $sortDirection = isset($parameters['sort_type']) && $parameters['sort_type'] === 'defined_by_parent' ?
                $this->sortDirections[$parentLocation->sortOrder] :
                $parameters['sort_direction'];
        }

        $query->sortClauses = array(
            new $this->sortClauses[$sortType]($sortDirection),
        );

        return $query;
    }
}
