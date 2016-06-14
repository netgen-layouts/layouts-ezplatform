<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Collection\QueryType\Handler;

use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Search\SearchHit;
use Netgen\BlockManager\Collection\QueryType\QueryTypeHandlerInterface;
use eZ\Publish\API\Repository\ContentTypeService;
use Netgen\BlockManager\Parameters\Parameter;
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
            'parent_location_id' => new Parameter\Text(array(), true),
            'sort_direction' => new Parameter\Select(
                array(
                    'options' => array(
                        'Descending' => LocationQuery::SORT_DESC,
                        'Ascending' => LocationQuery::SORT_ASC,
                    )
                ),
                true
            ),
            'sort_type' => new Parameter\Select(
                array(
                    'options' => array(
                        'Published' => 'date_published',
                        'Modified' => 'date_modified',
                        'Alphabetical' => 'content_name',
                        'Priority' => 'location_priority',
                        'Defined by parent' => 'parent_location',
                    )
                ),
                true
            ),
            'limit' => new Parameter\Integer(array('min' => 1)),
            'offset' => new Parameter\Integer(array('min' => 0), true),
            'query_type' => new Parameter\Select(
                array(
                    'options' => array(
                        'List' => 'list',
                        'Tree' => 'tree',
                    )
                ),
                true
            ),
            'filter_by_content_type' => new Parameter\Compound\Boolean(
                array(
                    'content_types' => new Parameter\Select(
                        array(
                            'options' => $this->getContentTypes(),
                            'multiple' => true,
                        )
                    ),
                    'content_types_filter' => new Parameter\Select(
                        array(
                            'options' => array(
                                'Include content types' => 'include',
                                'Exclude content types' => 'exclude',
                            )
                        ),
                        true
                    )
                )
            )
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
            return null;
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

        if ($parameters['query_type'] === 'list') {
            $criteria[] = new Criterion\Location\Depth(
                Criterion\Operator::EQ, $parentLocation->depth + 1
            );
        }

        if ($parameters['filter_by_content_type'] && !empty($parameters['content_types'])) {
            $contentTypeFilter = new Criterion\ContentTypeIdentifier($parameters['content_types']);

            if ($parameters['content_types_filter'] === 'exclude') {
                $contentTypeFilter = new Criterion\LogicalNot($contentTypeFilter);
            }

            $criteria[] = $contentTypeFilter;
        }

        $query->filter = new Criterion\LogicalAnd($criteria);

        $query->limit = 0;
        if (!$buildCountQuery) {
            $query->offset = $parameters['offset'];
            $query->limit = is_int($parameters['limit']) ?
                $parameters['limit'] :
                self::DEFAULT_LIMIT;
        }

        return $query;
    }
}
