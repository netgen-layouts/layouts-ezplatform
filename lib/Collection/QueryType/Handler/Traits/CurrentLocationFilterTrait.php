<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Collection\QueryType\Handler\Traits;

use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use Netgen\Layouts\Parameters\ParameterBuilderInterface;
use Netgen\Layouts\Parameters\ParameterCollectionInterface;
use Netgen\Layouts\Parameters\ParameterType;

trait CurrentLocationFilterTrait
{
    /**
     * Builds the parameters for filtering content by excluding the currently displayed location.
     *
     * @param string[] $groups
     */
    private function buildCurrentLocationParameters(ParameterBuilderInterface $builder, array $groups = []): void
    {
        $builder->add(
            'exclude_current_location',
            ParameterType\BooleanType::class,
            [
                'default_value' => true,
                'groups' => $groups,
            ],
        );
    }

    /**
     * Returns the criteria used to filter content by excluding the currently displayed location.
     */
    private function getCurrentLocationFilterCriteria(ParameterCollectionInterface $parameterCollection, Location $location): ?Criterion
    {
        if ($parameterCollection->getParameter('exclude_current_location')->getValue() !== true) {
            return null;
        }

        return new Criterion\LogicalNot(
            new Criterion\LocationId($location->id),
        );
    }
}
