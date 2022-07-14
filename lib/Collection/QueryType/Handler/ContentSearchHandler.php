<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Collection\QueryType\Handler;

use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\SearchService;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Search\SearchHit;
use eZ\Publish\API\Repository\Values\ValueObject;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use eZ\Publish\SPI\Persistence\Content\ObjectState\Handler as ObjectStateHandler;
use eZ\Publish\SPI\Persistence\Content\Section\Handler as SectionHandler;
use Netgen\Layouts\API\Values\Collection\Query;
use Netgen\Layouts\Collection\QueryType\QueryTypeHandlerInterface;
use Netgen\Layouts\Ez\ContentProvider\ContentProviderInterface;
use Netgen\Layouts\Parameters\ParameterBuilderInterface;

use function array_filter;
use function array_map;

use const PHP_INT_MAX;

/**
 * Handler for a query which retrieves the eZ locations from the repository
 * based on parameters provided in the query.
 */
final class ContentSearchHandler implements QueryTypeHandlerInterface
{
    use Traits\ContentTypeFilterTrait;
    use Traits\CurrentLocationFilterTrait;
    use Traits\MainLocationFilterTrait;
    use Traits\ObjectStateFilterTrait;
    use Traits\ParentLocationTrait;
    use Traits\QueryTypeFilterTrait;
    use Traits\SectionFilterTrait;
    use Traits\SortTrait;

    private SearchService $searchService;

    private ConfigResolverInterface $configResolver;

    public function __construct(
        LocationService $locationService,
        SearchService $searchService,
        SectionHandler $sectionHandler,
        ObjectStateHandler $objectStateHandler,
        ContentProviderInterface $contentProvider,
        ConfigResolverInterface $configResolver
    ) {
        $this->searchService = $searchService;
        $this->configResolver = $configResolver;

        $this->setSectionHandler($sectionHandler);
        $this->setObjectStateHandler($objectStateHandler);
        $this->setContentProvider($contentProvider);
        $this->setLocationService($locationService);
    }

    public function buildParameters(ParameterBuilderInterface $builder): void
    {
        $advancedGroup = [self::GROUP_ADVANCED];

        $this->buildParentLocationParameters($builder);
        $this->buildSortParameters($builder);
        $this->buildQueryTypeParameters($builder, $advancedGroup);
        $this->buildMainLocationParameters($builder, $advancedGroup);
        $this->buildCurrentLocationParameters($builder, $advancedGroup);
        $this->buildContentTypeFilterParameters($builder, $advancedGroup);
        $this->buildSectionFilterParameters($builder, $advancedGroup);
        $this->buildObjectStateFilterParameters($builder, $advancedGroup);
    }

    public function getValues(Query $query, int $offset = 0, ?int $limit = null): iterable
    {
        $parentLocation = $this->getParentLocation($query);

        if (!$parentLocation instanceof Location) {
            return [];
        }

        $locationQuery = $this->buildLocationQuery($query, $parentLocation);
        $locationQuery->offset = $offset;
        $locationQuery->limit = $limit ?? PHP_INT_MAX;

        // We're disabling query count for performance reasons, however
        // it can only be disabled if limit is not 0
        $locationQuery->performCount = $locationQuery->limit === 0;

        $searchResult = $this->searchService->findLocations(
            $locationQuery,
            ['languages' => $this->configResolver->getParameter('languages')],
        );

        return array_map(
            static fn (SearchHit $searchHit): ValueObject => $searchHit->valueObject,
            $searchResult->searchHits,
        );
    }

    public function getCount(Query $query): int
    {
        $parentLocation = $this->getParentLocation($query);

        if (!$parentLocation instanceof Location) {
            return 0;
        }

        $locationQuery = $this->buildLocationQuery($query, $parentLocation);
        $locationQuery->limit = 0;

        $searchResult = $this->searchService->findLocations(
            $locationQuery,
            ['languages' => $this->configResolver->getParameter('languages')],
        );

        return $searchResult->totalCount ?? 0;
    }

    public function isContextual(Query $query): bool
    {
        return $query->getParameter('use_current_location')->getValue() === true;
    }

    /**
     * Builds the query from current parameters.
     */
    private function buildLocationQuery(Query $query, Location $parentLocation): LocationQuery
    {
        $locationQuery = new LocationQuery();

        $criteria = [
            new Criterion\Subtree($parentLocation->pathString),
            new Criterion\Visibility(Criterion\Visibility::VISIBLE),
            new Criterion\LogicalNot(new Criterion\LocationId($parentLocation->id)),
            $this->getMainLocationFilterCriteria($query),
            $this->getQueryTypeFilterCriteria($query, $parentLocation),
            $this->getContentTypeFilterCriteria($query),
            $this->getSectionFilterCriteria($query),
            $this->getObjectStateFilterCriteria($query),
        ];

        $currentLocation = $this->contentProvider->provideLocation();
        if ($currentLocation instanceof Location) {
            $criteria[] = $this->getCurrentLocationFilterCriteria($query, $currentLocation);
        }

        $criteria = array_filter(
            $criteria,
            static fn (?Criterion $criterion): bool => $criterion instanceof Criterion,
        );

        $locationQuery->filter = new Criterion\LogicalAnd($criteria);
        $locationQuery->sortClauses = $this->getSortClauses($query, $parentLocation);

        return $locationQuery;
    }
}
