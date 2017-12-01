<?php

namespace Netgen\BlockManager\Ez\Collection\QueryType\Handler\Traits;

use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause;
use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\Parameters\ParameterBuilderInterface;
use Netgen\BlockManager\Parameters\ParameterType;

trait SortTrait
{
    /**
     * @var array
     */
    private $sortClauses = array(
        'default' => SortClause\DatePublished::class,
        'date_published' => SortClause\DatePublished::class,
        'date_modified' => SortClause\DateModified::class,
        'content_name' => SortClause\ContentName::class,
        'location_priority' => SortClause\Location\Priority::class,
        Location::SORT_FIELD_PATH => SortClause\Location\Path::class,
        Location::SORT_FIELD_PUBLISHED => SortClause\DatePublished::class,
        Location::SORT_FIELD_MODIFIED => SortClause\DateModified::class,
        Location::SORT_FIELD_SECTION => SortClause\SectionIdentifier::class,
        Location::SORT_FIELD_DEPTH => SortClause\Location\Depth::class,
        Location::SORT_FIELD_PRIORITY => SortClause\Location\Priority::class,
        Location::SORT_FIELD_NAME => SortClause\ContentName::class,
        Location::SORT_FIELD_NODE_ID => SortClause\Location\Id::class,
        Location::SORT_FIELD_CONTENTOBJECT_ID => SortClause\ContentId::class,
    );

    /**
     * @var array
     */
    private $sortDirections = array(
        Location::SORT_ORDER_ASC => LocationQuery::SORT_ASC,
        Location::SORT_ORDER_DESC => LocationQuery::SORT_DESC,
    );

    /**
     * Builds the parameters for sorting eZ content.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterBuilderInterface $builder
     * @param array $groups
     */
    private function buildSortParameters(ParameterBuilderInterface $builder, array $groups = array())
    {
        $builder->add(
            'sort_type',
            ParameterType\ChoiceType::class,
            array(
                'required' => true,
                'options' => array(
                    'Published' => 'date_published',
                    'Modified' => 'date_modified',
                    'Alphabetical' => 'content_name',
                    'Priority' => 'location_priority',
                    'Defined by parent' => 'defined_by_parent',
                ),
                'groups' => $groups,
            )
        );

        $builder->add(
            'sort_direction',
            ParameterType\ChoiceType::class,
            array(
                'required' => true,
                'options' => array(
                    'Descending' => LocationQuery::SORT_DESC,
                    'Ascending' => LocationQuery::SORT_ASC,
                ),
                'groups' => $groups,
            )
        );
    }

    /**
     * Returns the clauses for sorting eZ content.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Query $query
     * @param \eZ\Publish\API\Repository\Values\Content\Location $parentLocation
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Query\SortClause[]
     */
    private function getSortClauses(Query $query, Location $parentLocation = null)
    {
        $sortType = $query->getParameter('sort_type')->getValue() ?: 'default';
        $sortDirection = $query->getParameter('sort_direction')->getValue() ?: LocationQuery::SORT_DESC;

        if ($sortType === 'defined_by_parent' && $parentLocation !== null) {
            $sortType = $parentLocation->sortField;
            $sortDirection = $this->sortDirections[$parentLocation->sortOrder];
        }

        return array(
            new $this->sortClauses[$sortType]($sortDirection),
        );
    }
}
