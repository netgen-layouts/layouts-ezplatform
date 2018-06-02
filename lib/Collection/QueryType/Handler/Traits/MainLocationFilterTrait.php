<?php

namespace Netgen\BlockManager\Ez\Collection\QueryType\Handler\Traits;

use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\Parameters\ParameterBuilderInterface;
use Netgen\BlockManager\Parameters\ParameterType;

trait MainLocationFilterTrait
{
    /**
     * Builds the parameters for filtering content with main location only.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterBuilderInterface $builder
     * @param array $groups
     */
    private function buildMainLocationParameters(ParameterBuilderInterface $builder, $groups = [])
    {
        $builder->add(
            'only_main_locations',
            ParameterType\BooleanType::class,
            [
                'default_value' => true,
                'groups' => $groups,
            ]
        );
    }

    /**
     * Returns the criteria used to filter content with main location only.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Query $query
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Query\Criterion|null
     */
    private function getMainLocationFilterCriteria(Query $query)
    {
        if ($query->getParameter('only_main_locations')->getValue() !== true) {
            return null;
        }

        return new Criterion\Location\IsMainLocation(
            Criterion\Location\IsMainLocation::MAIN
        );
    }
}
