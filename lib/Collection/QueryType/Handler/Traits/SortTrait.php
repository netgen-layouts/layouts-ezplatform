<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Collection\QueryType\Handler\Traits;

use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause;
use Netgen\Layouts\Parameters\ParameterBuilderInterface;
use Netgen\Layouts\Parameters\ParameterCollectionInterface;
use Netgen\Layouts\Parameters\ParameterType;

use function array_intersect;
use function count;

trait SortTrait
{
    /**
     * @var array<string, class-string>
     */
    private static array $sortClauses = [
        'default' => SortClause\DatePublished::class,
        'date_published' => SortClause\DatePublished::class,
        'date_modified' => SortClause\DateModified::class,
        'content_name' => SortClause\ContentName::class,
        'location_priority' => SortClause\Location\Priority::class,
    ];

    /**
     * Builds the parameters for sorting eZ content.
     *
     * @param string[] $groups
     * @param string[] $allowedSortTypes
     */
    private function buildSortParameters(ParameterBuilderInterface $builder, array $groups = [], array $allowedSortTypes = []): void
    {
        $sortTypes = [
            'Published' => 'date_published',
            'Modified' => 'date_modified',
            'Alphabetical' => 'content_name',
            'Priority' => 'location_priority',
            'Defined by parent' => 'defined_by_parent',
        ];

        if (count($allowedSortTypes) > 0) {
            $sortTypes = array_intersect($sortTypes, $allowedSortTypes);
        }

        $builder->add(
            'sort_type',
            ParameterType\ChoiceType::class,
            [
                'required' => true,
                'options' => $sortTypes,
                'groups' => $groups,
            ],
        );

        $builder->add(
            'sort_direction',
            ParameterType\ChoiceType::class,
            [
                'required' => true,
                'options' => [
                    'Descending' => LocationQuery::SORT_DESC,
                    'Ascending' => LocationQuery::SORT_ASC,
                ],
                'groups' => $groups,
            ],
        );
    }

    /**
     * Returns the clauses for sorting eZ content.
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Query\SortClause[]
     */
    private function getSortClauses(ParameterCollectionInterface $parameterCollection, ?Location $parentLocation = null): array
    {
        $sortType = $parameterCollection->getParameter('sort_type')->getValue() ?? 'default';
        $sortDirection = $parameterCollection->getParameter('sort_direction')->getValue() ?? LocationQuery::SORT_DESC;

        if ($sortType === 'defined_by_parent' && $parentLocation !== null) {
            return $parentLocation->getSortClauses();
        }

        /** @var \eZ\Publish\API\Repository\Values\Content\Query\SortClause $sortClause */
        $sortClause = new self::$sortClauses[$sortType]($sortDirection);

        return [$sortClause];
    }
}
