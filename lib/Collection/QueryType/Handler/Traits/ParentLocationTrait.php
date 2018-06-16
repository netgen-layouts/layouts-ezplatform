<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Collection\QueryType\Handler\Traits;

use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\Content\Location;
use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\Ez\ContentProvider\ContentProviderInterface;
use Netgen\BlockManager\Ez\Parameters\ParameterType as EzParameterType;
use Netgen\BlockManager\Parameters\ParameterBuilderInterface;
use Netgen\BlockManager\Parameters\ParameterType;
use Throwable;

trait ParentLocationTrait
{
    /**
     * @var \Netgen\BlockManager\Ez\ContentProvider\ContentProviderInterface
     */
    private $contentProvider;

    /**
     * @var \eZ\Publish\API\Repository\LocationService
     */
    private $locationService;

    /**
     * Sets the content provider used by the trait.
     */
    private function setContentProvider(ContentProviderInterface $contentProvider): void
    {
        $this->contentProvider = $contentProvider;
    }

    /**
     * Sets the location service used by the trait.
     */
    private function setLocationService(LocationService $locationService): void
    {
        $this->locationService = $locationService;
    }

    /**
     * Builds the parameters for filtering by parent location.
     */
    private function buildParentLocationParameters(ParameterBuilderInterface $builder, array $groups = []): void
    {
        $builder->add(
            'use_current_location',
            ParameterType\Compound\BooleanType::class,
            [
                'reverse' => true,
                'groups' => $groups,
            ]
        );

        $builder->get('use_current_location')->add(
            'parent_location_id',
            EzParameterType\LocationType::class,
            [
                'allow_invalid' => true,
                'groups' => $groups,
            ]
        );
    }

    /**
     * Returns the parent location to use for the query.
     */
    private function getParentLocation(Query $query): ?Location
    {
        if ($query->getParameter('use_current_location')->getValue()) {
            return $this->contentProvider->provideLocation();
        }

        $parentLocationId = $query->getParameter('parent_location_id')->getValue();
        if (empty($parentLocationId)) {
            return null;
        }

        try {
            $parentLocation = $this->locationService->loadLocation($parentLocationId);

            return $parentLocation->invisible ? null : $parentLocation;
        } catch (Throwable $t) {
            return null;
        }
    }
}
