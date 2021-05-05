<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Collection\QueryType\Handler\Traits;

use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\Content\Location;
use Netgen\Layouts\Ez\ContentProvider\ContentProviderInterface;
use Netgen\Layouts\Ez\Parameters\ParameterType as EzParameterType;
use Netgen\Layouts\Parameters\ParameterBuilderInterface;
use Netgen\Layouts\Parameters\ParameterCollectionInterface;
use Netgen\Layouts\Parameters\ParameterType;
use Throwable;

trait ParentLocationTrait
{
    private ContentProviderInterface $contentProvider;

    private LocationService $locationService;

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
     *
     * @param string[] $groups
     */
    private function buildParentLocationParameters(ParameterBuilderInterface $builder, array $groups = []): void
    {
        $builder->add(
            'use_current_location',
            ParameterType\Compound\BooleanType::class,
            [
                'reverse' => true,
                'groups' => $groups,
            ],
        );

        $builder->get('use_current_location')->add(
            'parent_location_id',
            EzParameterType\LocationType::class,
            [
                'allow_invalid' => true,
                'groups' => $groups,
            ],
        );
    }

    /**
     * Returns the parent location to use for the parameter collection.
     */
    private function getParentLocation(ParameterCollectionInterface $parameterCollection): ?Location
    {
        if ($parameterCollection->getParameter('use_current_location')->getValue() === true) {
            return $this->contentProvider->provideLocation();
        }

        $parentLocationId = $parameterCollection->getParameter('parent_location_id')->getValue();
        if ($parentLocationId === null) {
            return null;
        }

        try {
            $parentLocation = $this->locationService->loadLocation((int) $parentLocationId);

            return $parentLocation->invisible ? null : $parentLocation;
        } catch (Throwable $t) {
            return null;
        }
    }
}
