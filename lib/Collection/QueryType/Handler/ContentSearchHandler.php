<?php

namespace Netgen\BlockManager\Ez\Collection\QueryType\Handler;

use Exception;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\SearchService;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause;
use eZ\Publish\API\Repository\Values\Content\Search\SearchHit;
use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\Collection\QueryType\QueryTypeHandlerInterface;
use Netgen\BlockManager\Ez\ContentProvider\ContentProviderInterface;
use Netgen\BlockManager\Ez\Parameters\ParameterType as EzParameterType;
use Netgen\BlockManager\Parameters\ParameterBuilderInterface;
use Netgen\BlockManager\Parameters\ParameterType;

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
     * Builds the parameters by using provided parameter builder.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterBuilderInterface $builder
     */
    public function buildParameters(ParameterBuilderInterface $builder)
    {
        $builder->add(
            'use_current_location',
            ParameterType\Compound\BooleanType::class,
            array(
                'reverse' => true,
            )
        );

        $builder->get('use_current_location')->add(
            'parent_location_id',
            EzParameterType\LocationType::class
        );

        $builder->add(
            'sort_direction',
            ParameterType\ChoiceType::class,
            array(
                'required' => true,
                'options' => array(
                    'Descending' => LocationQuery::SORT_DESC,
                    'Ascending' => LocationQuery::SORT_ASC,
                ),
            )
        );

        $builder->add(
            'sort_type',
            ParameterType\ChoiceType::class,
            array(
                'required' => true,
                'options' => array(
                    'Published' => 'date_published',
                    'Modified' => 'date_modified',
                    'Alphabetical' => 'content_name',
                    'Priority' => 'location_priority',
                    'Defined by parent' => 'defined_by_parent',
                ),
            )
        );

        $builder->add(
            'limit',
            ParameterType\IntegerType::class,
            array(
                'min' => 0,
            )
        );

        $builder->add(
            'offset',
            ParameterType\IntegerType::class,
            array(
                'min' => 0,
            )
        );

        $builder->add(
            'query_type',
            ParameterType\ChoiceType::class,
            array(
                'required' => true,
                'options' => array(
                    'List' => 'list',
                    'Tree' => 'tree',
                ),
            )
        );

        $builder->add(
            'only_main_locations',
            ParameterType\BooleanType::class,
            array(
                'default_value' => true,
            )
        );

        $builder->add(
            'filter_by_content_type',
            ParameterType\Compound\BooleanType::class
        );

        $builder->get('filter_by_content_type')->add(
            'content_types',
            EzParameterType\ContentTypeType::class,
            array(
                'multiple' => true,
            )
        );

        $builder->get('filter_by_content_type')->add(
            'content_types_filter',
            ParameterType\ChoiceType::class,
            array(
                'required' => true,
                'options' => array(
                    'Include content types' => 'include',
                    'Exclude content types' => 'exclude',
                ),
            )
        );
    }

    /**
     * Returns the values from the query.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Query $query
     * @param int $offset
     * @param int $limit
     *
     * @return mixed[]
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
            return self::DEFAULT_LIMIT;
        }

        return $limit >= 0 ? $limit : self::DEFAULT_LIMIT;
    }

    /**
     * Returns the parent location to use for the query.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Query $query
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Location|null
     */
    protected function getParentLocation(Query $query)
    {
        if ($query->getParameter('use_current_location')->getValue()) {
            return $this->contentProvider->provideLocation();
        }

        $parentLocationId = $query->getParameter('parent_location_id')->getValue();
        if (empty($parentLocationId)) {
            return null;
        }

        try {
            $parentLocation = $this->locationService->loadLocation($parentLocationId);

            return $parentLocation->invisible ? null : $parentLocation;
        } catch (Exception $e) {
            return null;
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
            new Criterion\LogicalNot(new Criterion\LocationId($parentLocation->id)),
        );

        if ($query->getParameter('only_main_locations')->getValue()) {
            $criteria[] = new Criterion\Location\IsMainLocation(
                Criterion\Location\IsMainLocation::MAIN
            );
        }

        if ($query->getParameter('query_type')->getValue() === 'list') {
            $criteria[] = new Criterion\Location\Depth(
                Criterion\Operator::EQ, $parentLocation->depth + 1
            );
        }

        if ($query->getParameter('filter_by_content_type')->getValue()) {
            $contentTypes = $query->getParameter('content_types')->getValue();
            if (!empty($contentTypes)) {
                $contentTypeFilter = new Criterion\ContentTypeIdentifier($contentTypes);

                if ($query->getParameter('content_types_filter')->getValue() === 'exclude') {
                    $contentTypeFilter = new Criterion\LogicalNot($contentTypeFilter);
                }

                $criteria[] = $contentTypeFilter;
            }
        }

        $locationQuery->filter = new Criterion\LogicalAnd($criteria);

        $locationQuery->limit = 0;
        if (!$buildCountQuery) {
            $offset = $query->getParameter('offset')->getValue();
            $locationQuery->offset = is_int($offset) && $offset >= 0 ? $offset : 0;

            $limit = $query->getParameter('limit')->getValue();
            $locationQuery->limit = is_int($limit) && $limit >= 0 ? $limit : self::DEFAULT_LIMIT;
        }

        $sortType = $query->getParameter('sort_type')->getValue() ?: 'default';
        $sortDirection = $query->getParameter('sort_direction')->getValue() ?: LocationQuery::SORT_DESC;

        if ($sortType === 'defined_by_parent') {
            $sortType = $parentLocation->sortField;
            $sortDirection = $this->sortDirections[$parentLocation->sortOrder];
        }

        $locationQuery->sortClauses = array(
            new $this->sortClauses[$sortType]($sortDirection),
        );

        return $locationQuery;
    }
}
