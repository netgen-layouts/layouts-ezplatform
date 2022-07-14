<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Collection\QueryType\Handler\Traits;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Netgen\Layouts\Ibexa\Parameters\ParameterType as IbexaParameterType;
use Netgen\Layouts\Parameters\ParameterBuilderInterface;
use Netgen\Layouts\Parameters\ParameterCollectionInterface;
use Netgen\Layouts\Parameters\ParameterType;

use function count;

trait ContentTypeFilterTrait
{
    /**
     * Builds the parameters for filtering by content types.
     *
     * @param string[] $groups
     */
    private function buildContentTypeFilterParameters(ParameterBuilderInterface $builder, array $groups = []): void
    {
        $builder->add(
            'filter_by_content_type',
            ParameterType\Compound\BooleanType::class,
            [
                'groups' => $groups,
            ],
        );

        $builder->get('filter_by_content_type')->add(
            'content_types',
            IbexaParameterType\ContentTypeType::class,
            [
                'multiple' => true,
                'groups' => $groups,
            ],
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
            ],
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

        $contentTypeFilter = new Criterion\ContentTypeIdentifier($contentTypes);

        if ($parameterCollection->getParameter('content_types_filter')->getValue() === 'exclude') {
            $contentTypeFilter = new Criterion\LogicalNot($contentTypeFilter);
        }

        return $contentTypeFilter;
    }
}
