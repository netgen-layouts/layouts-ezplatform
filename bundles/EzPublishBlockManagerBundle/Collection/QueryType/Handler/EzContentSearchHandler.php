<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Collection\QueryType\Handler;

use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Search\SearchHit;
use Netgen\BlockManager\Collection\QueryType\QueryTypeHandlerInterface;
use eZ\Publish\API\Repository\ContentTypeService;
use Netgen\BlockManager\Parameters\Parameter;
use eZ\Publish\API\Repository\SearchService;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;

class EzContentSearchHandler implements QueryTypeHandlerInterface
{
    /**
     * @var array
     */
    protected $contentTypes;

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
     * @param \eZ\Publish\API\Repository\ContentTypeService $contentTypeService
     * @param \eZ\Publish\API\Repository\SearchService $searchService
     */
    public function __construct(ContentTypeService $contentTypeService, SearchService $searchService)
    {
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
            'content_types' => new Parameter\Select(
                array(
                    'options' => $this->getContentTypes(),
                    'multiple' => true,
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
        $query = new Query();

        $query->filter = new Criterion\LogicalAnd(
            array(
                new Criterion\ParentLocationId(71)
            )
        );

        $query->offset = $offset;
        $query->limit = 3;

        $searchResult = $this->searchService->findContentInfo($query);

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
     * @param array $parameters
     *
     * @return int
     */
    public function getCount(array $parameters)
    {
        return 3;
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
}
