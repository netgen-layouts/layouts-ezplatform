<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Collection\QueryType\Handler\Traits;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\SPI\Persistence\Content\Type\Handler;
use Netgen\BlockManager\Parameters\ParameterBuilderInterface;
use Netgen\BlockManager\Parameters\ParameterCollectionInterface;
use Netgen\BlockManager\Parameters\ParameterType;
use Netgen\Layouts\Ez\Parameters\ParameterType as EzParameterType;

trait ContentTypeFilterTrait
{
    /**
     * @var \eZ\Publish\SPI\Persistence\Content\Type\Handler
     */
    private $contentTypeHandler;

    /**
     * Sets the content type handler used by the trait.
     */
    private function setContentTypeHandler(Handler $handler): void
    {
        $this->contentTypeHandler = $handler;
    }

    /**
     * Builds the parameters for filtering by content types.
     */
    private function buildContentTypeFilterParameters(ParameterBuilderInterface $builder, array $groups = []): void
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
     */
    private function getContentTypeFilterCriteria(ParameterCollectionInterface $parameterCollection): ?Criterion
    {
        if ($parameterCollection->getParameter('filter_by_content_type')->getValue() !== true) {
            return null;
        }

        $contentTypes = $parameterCollection->getParameter('content_types')->getValue() ?? [];
        if (count($contentTypes) === 0) {
            return null;
        }

        $contentTypeFilter = new Criterion\ContentTypeId(
            $this->getContentTypeIds($contentTypes)
        );

        if ($parameterCollection->getParameter('content_types_filter')->getValue() === 'exclude') {
            $contentTypeFilter = new Criterion\LogicalNot($contentTypeFilter);
        }

        return $contentTypeFilter;
    }

    /**
     * Returns content type IDs for all provided content type identifiers.
     */
    private function getContentTypeIds(array $contentTypeIdentifiers): array
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
