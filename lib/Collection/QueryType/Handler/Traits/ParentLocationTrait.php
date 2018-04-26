<?php

namespace Netgen\BlockManager\Ez\Collection\QueryType\Handler\Traits;

use Exception;
use eZ\Publish\API\Repository\LocationService;
use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\Ez\ContentProvider\ContentProviderInterface;
use Netgen\BlockManager\Ez\Parameters\ParameterType as EzParameterType;
use Netgen\BlockManager\Parameters\ParameterBuilderInterface;
use Netgen\BlockManager\Parameters\ParameterType;

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
     *
     * @param \Netgen\BlockManager\Ez\ContentProvider\ContentProviderInterface $contentProvider
     */
    private function setContentProvider(ContentProviderInterface $contentProvider)
    {
        $this->contentProvider = $contentProvider;
    }

    /**
     * Sets the location service used by the trait.
     *
     * @param \eZ\Publish\API\Repository\LocationService $locationService
     */
    private function setLocationService(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }

    /**
     * Builds the parameters for filtering by parent location.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterBuilderInterface $builder
     * @param array $groups
     */
    private function buildParentLocationParameters(ParameterBuilderInterface $builder, array $groups = [])
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
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Query $query
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Location|null
     */
    private function getParentLocation(Query $query)
    {
        if ($query->getParameter('use_current_location')->getValue()) {
            return $this->contentProvider->provideLocation();
        }

        $parentLocationId = $query->getParameter('parent_location_id')->getValue();
        if (empty($parentLocationId)) {
            return;
        }

        try {
            $parentLocation = $this->locationService->loadLocation($parentLocationId);

            return $parentLocation->invisible ? null : $parentLocation;
        } catch (Exception $e) {
            return;
        }
    }
}
