<?php

namespace Netgen\BlockManager\Ez\Collection\QueryType\Handler\Traits;

use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\Parameters\ParameterBuilderInterface;
use Netgen\BlockManager\Parameters\ParameterType;

trait QueryTypeFilterTrait
{
    /**
     * Builds the parameters for selecting a query type.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterBuilderInterface $builder
     * @param array $groups
     */
    private function buildQueryTypeParameters(ParameterBuilderInterface $builder, $groups = array())
    {
        $builder->add(
            'query_type',
            ParameterType\ChoiceType::class,
            array(
                'required' => true,
                'options' => array(
                    'List' => 'list',
                    'Tree' => 'tree',
                ),
                'groups' => $groups,
            )
        );
    }

    /**
     * Returns the criteria used to filter content with one of the supported query types.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Query $query
     * @param \eZ\Publish\API\Repository\Values\Content\Location $parentLocation
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Query\Criterion|null
     */
    private function getQueryTypeFilterCriteria(Query $query, Location $parentLocation)
    {
        if ($query->getParameter('query_type')->getValue() !== 'list') {
            return null;
        }

        return new Criterion\Location\Depth(
            Criterion\Operator::EQ, $parentLocation->depth + 1
        );
    }
}
