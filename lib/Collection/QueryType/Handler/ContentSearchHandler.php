<?php

namespace Netgen\BlockManager\Ez\Collection\QueryType\Handler;

use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\SearchService;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause;
use eZ\Publish\API\Repository\Values\Content\Search\SearchHit;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\Collection\QueryType\QueryTypeHandlerInterface;
use Netgen\BlockManager\Ez\ContentProvider\ContentProviderInterface;
use Netgen\BlockManager\Ez\Parameters\Parameter as EzParameter;
use Netgen\BlockManager\Parameters\Parameter;
use Exception;

class ContentSearchHandler implements QueryTypeHandlerInterface
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
     * @var \eZ\Publish\API\Repository\SearchService
     */
    protected $searchService;

    /**
     * @var \Netgen\BlockManager\Ez\ContentProvider\ContentProviderInterface
     */
    protected $contentProvider;

    /**
     * @var array
     */
    protected $languages = array();

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
     * @param \eZ\Publish\API\Repository\SearchService $searchService
     * @param \Netgen\BlockManager\Ez\ContentProvider\ContentProviderInterface $contentProvider
     */
    public function __construct(
        LocationService $locationService,
        SearchService $searchService,
        ContentProviderInterface $contentProvider
    ) {
        $this->locationService = $locationService;
        $this->searchService = $searchService;
        $this->contentProvider = $contentProvider;
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
            'use_current_location' => new Parameter\Compound\Boolean(
                array(
                    'parent_location_id' => new EzParameter\Location(),
                ),
                array(
                    'reverse' => true,
                )
            ),
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
            'limit' => new Parameter\Integer(array('min' => 0)),
            'offset' => new Parameter\Integer(array('min' => 0)),
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
                    'content_types' => new EzParameter\ContentType(
                        array(
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
     * @param \Netgen\BlockManager\API\Values\Collection\Query $query
     * @param int $offset
     * @param int $limit
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Location[]
     */
    public function getValues(Query $query, $offset = 0, $limit = null)
    {
        $parentLocation = $this->getParentLocation($query);

        if (!$parentLocation instanceof Location) {
            return array();
        }

        $searchResult = $this->searchService->findLocations(
            $this->buildQuery($parentLocation, $query),
            array('languages' => $this->languages)
        );

        return array_map(
            function (SearchHit $searchHit) {
                return $searchHit->valueObject;
            },
            $searchResult->searchHits
        );
    }

    /**
     * Returns the value count from the query.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Query $query
     *
     * @return int
     */
    public function getCount(Query $query)
    {
        $parentLocation = $this->getParentLocation($query);

        if (!$parentLocation instanceof Location) {
            return 0;
        }

        $searchResult = $this->searchService->findLocations(
            $this->buildQuery($parentLocation, $query, true),
            array('languages' => $this->languages)
        );

        return $searchResult->totalCount;
    }

    /**
     * Returns the limit internal to this query.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Query $query
     *
     * @return int
     */
    public function getInternalLimit(Query $query)
    {
        $limit = $query->getParameter('limit')->getValue();
        if (!is_int($limit)) {
            return null;
        }

        return $limit >= 0 ? $limit : 0;
    }

    /**
     * Returns the parent location to use for the query.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Query $query
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Location
     */
    protected function getParentLocation(Query $query)
    {
        if ($query->getParameter('use_current_location')->getValue() === true) {
            return $this->contentProvider->provideLocation();
        }

        if (empty($query->getParameter('parent_location_id')->getValue())) {
            return;
        }

        try {
            $parentLocation = $this->locationService->loadLocation(
                $query->getParameter('parent_location_id')->getValue()
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
     * @param \Netgen\BlockManager\API\Values\Collection\Query $query
     * @param bool $buildCountQuery
     *
     * @return \eZ\Publish\API\Repository\Values\Content\LocationQuery
     */
    protected function buildQuery(Location $parentLocation, Query $query, $buildCountQuery = false)
    {
        $locationQuery = new LocationQuery();

        $criteria = array(
            new Criterion\Subtree($parentLocation->pathString),
            new Criterion\Visibility(Criterion\Visibility::VISIBLE),
            new Criterion\Location\IsMainLocation(Criterion\Location\IsMainLocation::MAIN),
            new Criterion\LogicalNot(new Criterion\LocationId($parentLocation->id)),
        );

        if ($query->getParameter('query_type')->getValue() === 'list') {
            $criteria[] = new Criterion\Location\Depth(
                Criterion\Operator::EQ, $parentLocation->depth + 1
            );
        }

        if (!empty($query->getParameter('filter_by_content_type')->getValue()) && !empty($query->getParameter('content_types')->getValue())) {
            $contentTypeFilter = new Criterion\ContentTypeIdentifier($query->getParameter('content_types')->getValue());

            if ($query->getParameter('content_types_filter')->getValue() === 'exclude') {
                $contentTypeFilter = new Criterion\LogicalNot($contentTypeFilter);
            }

            $criteria[] = $contentTypeFilter;
        }

        $locationQuery->filter = new Criterion\LogicalAnd($criteria);

        $locationQuery->limit = 0;
        if (!$buildCountQuery) {
            $locationQuery->offset = is_int($query->getParameter('offset')->getValue()) && $query->getParameter('offset')->getValue() >= 0 ?
                $query->getParameter('offset')->getValue() :
                0;

            $locationQuery->limit = is_int($query->getParameter('limit')->getValue()) && $query->getParameter('limit')->getValue() >= 0 ?
                $query->getParameter('limit')->getValue() :
                self::DEFAULT_LIMIT;
        }

        $sortType = 'default';
        if (!empty($query->getParameter('sort_type')->getValue())) {
            $sortType = $query->getParameter('sort_type')->getValue() === 'defined_by_parent' ?
                $parentLocation->sortField :
                $query->getParameter('sort_type')->getValue();
        }

        $sortDirection = LocationQuery::SORT_DESC;
        if (!empty($query->getParameter('sort_direction')->getValue())) {
            $sortDirection = $query->getParameter('sort_type')->getValue() === 'defined_by_parent' ?
                $this->sortDirections[$parentLocation->sortOrder] :
                $query->getParameter('sort_direction')->getValue();
        }

        $locationQuery->sortClauses = array(
            new $this->sortClauses[$sortType]($sortDirection),
        );

        return $locationQuery;
    }
}
