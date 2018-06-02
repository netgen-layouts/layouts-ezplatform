<?php

namespace Netgen\BlockManager\Ez\Collection\QueryType\Handler\Traits;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\SPI\Persistence\Content\Type\Handler;
use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\Ez\Parameters\ParameterType as EzParameterType;
use Netgen\BlockManager\Parameters\ParameterBuilderInterface;
use Netgen\BlockManager\Parameters\ParameterType;

trait ContentTypeFilterTrait
{
    /**
     * @var \eZ\Publish\SPI\Persistence\Content\Type\Handler
     */
    private $contentTypeHandler;

    /**
     * Sets the content type handler used by the trait.
     *
     * @param \eZ\Publish\SPI\Persistence\Content\Type\Handler $handler
     */
    private function setContentTypeHandler(Handler $handler)
    {
        $this->contentTypeHandler = $handler;
    }

    /**
     * Builds the parameters for filtering by content types.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterBuilderInterface $builder
     * @param array $groups
     */
    private function buildContentTypeFilterParameters(ParameterBuilderInterface $builder, array $groups = [])
    {
        $builder->add(
            'filter_by_content_type',
            ParameterType\Compound\BooleanType::class,
            [
                'groups' => $groups,
            ]
        );

        $builder->get('filter_by_content_type')->add(
            'content_types',
            EzParameterType\ContentTypeType::class,
            [
                'multiple' => true,
                'groups' => $groups,
            ]
        );

        $builder->get('filter_by_content_type')->add(
            'content_types_filter',
            ParameterType\ChoiceType::class,
            [
                'required' => true,
                'options' => [
                    'Include content types' => 'include',
                    'Exclude content types' => 'exclude',
                ],
                'groups' => $groups,
            ]
        );
    }

    /**
     * Returns the criteria used to filter content by content type.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Query $query
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Query\Criterion|null
     */
    private function getContentTypeFilterCriteria(Query $query)
    {
        if ($query->getParameter('filter_by_content_type')->getValue() !== true) {
            return null;
        }

        $contentTypes = $query->getParameter('content_types')->getValue();
        if (empty($contentTypes)) {
            return null;
        }

        $contentTypeFilter = new Criterion\ContentTypeId(
            $this->getContentTypeIds($contentTypes)
        );

        if ($query->getParameter('content_types_filter')->getValue() === 'exclude') {
            $contentTypeFilter = new Criterion\LogicalNot($contentTypeFilter);
        }

        return $contentTypeFilter;
    }

    /**
     * Returns content type IDs for all provided content type identifiers.
     *
     * @param array $contentTypeIdentifiers
     *
     * @return array
     */
    private function getContentTypeIds(array $contentTypeIdentifiers)
    {
        $idList = [];

        foreach ($contentTypeIdentifiers as $identifier) {
            try {
                $contentType = $this->contentTypeHandler->loadByIdentifier($identifier);
                $idList[] = $contentType->id;
            } catch (NotFoundException $e) {
                continue;
            }
        }

        return $idList;
    }
}
