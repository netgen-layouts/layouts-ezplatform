<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Collection\QueryType;

use Netgen\BlockManager\Collection\QueryType;
use eZ\Publish\API\Repository\ContentTypeService;
use Netgen\BlockManager\Collection\QueryTypeInterface;
use Netgen\BlockManager\Parameters\Parameter;
use eZ\Publish\API\Repository\SearchService;
use Symfony\Component\Validator\Constraints;

class EzContentSearch extends QueryType implements QueryTypeInterface
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
     * Returns the query type.
     *
     * @return string
     */
    public function getType()
    {
        return 'ezcontent_search';
    }

    /**
     * Returns the array specifying query parameters.
     *
     * The keys are parameter identifiers.
     *
     * @return \Netgen\BlockManager\Parameters\Parameter[]
     */
    public function getParameters()
    {
        return array(
            'parent_location_id' => new Parameter\Text(array(), true),
            'content_types' => new Parameter\Select(
                array('options' => $this->getContentTypes(), 'multiple' => true)
            ),
        );
    }

    /**
     * Returns the array specifying query parameter validator constraints.
     *
     * @return array
     */
    public function getParameterConstraints()
    {
        return array(
            'parent_location_id' => array(
                new Constraints\NotBlank(),
            ),
            'content_types' => array(
                new Constraints\Choice(
                    array(
                        'choices' => array_values($this->getContentTypes()),
                        'multiple' => true
                    )
                ),
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
    }

    /**
     * Returns all content types from eZ Publish.
     *
     * @return array
     */
    protected function getContentTypes()
    {
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
    }
}
