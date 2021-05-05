<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Collection\QueryType\Handler\Traits;

use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use Netgen\Layouts\Parameters\ParameterBuilderInterface;
use Netgen\Layouts\Parameters\ParameterCollectionInterface;
use Netgen\Layouts\Parameters\ParameterType;

trait MainLocationFilterTrait
{
    /**
     * Builds the parameters for filtering content with main location only.
     *
     * @param string[] $groups
     */
    private function buildMainLocationParameters(ParameterBuilderInterface $builder, array $groups = []): void
    {
        $builder->add(
            'only_main_locations',
            ParameterType\BooleanType::class,
            [
                'default_value' => true,
                'groups' => $groups,
            ],
        );
    }

    /**
     * Returns the criteria used to filter content with main location only.
     */
    private function getMainLocationFilterCriteria(ParameterCollectionInterface $parameterCollection): ?Criterion
    {
        if ($parameterCollection->getParameter('only_main_locations')->getValue() !== true) {
            return null;
        }

        return new Criterion\Location\IsMainLocation(
            Criterion\Location\IsMainLocation::MAIN,
        );
    }
}
