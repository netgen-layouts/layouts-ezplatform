<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Collection\QueryType\Handler\Traits;

use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use Netgen\Layouts\Parameters\ParameterBuilderInterface;
use Netgen\Layouts\Parameters\ParameterCollectionInterface;
use Netgen\Layouts\Parameters\ParameterType;

trait QueryTypeFilterTrait
{
    /**
     * Builds the parameters for selecting a query type.
     *
     * @param string[] $groups
     */
    private function buildQueryTypeParameters(ParameterBuilderInterface $builder, array $groups = []): void
    {
        $builder->add(
            'query_type',
            ParameterType\ChoiceType::class,
            [
                'required' => true,
                'options' => [
                    'List' => 'list',
                    'Tree' => 'tree',
                ],
                'groups' => $groups,
            ],
        );
    }

    /**
     * Returns the criteria used to filter content with one of the supported query types.
     */
    private function getQueryTypeFilterCriteria(ParameterCollectionInterface $parameterCollection, Location $parentLocation): ?Criterion
    {
        if ($parameterCollection->getParameter('query_type')->getValue() !== 'list') {
            return null;
        }

        return new Criterion\Location\Depth(
            Criterion\Operator::EQ,
            $parentLocation->depth + 1,
        );
    }
}
