<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Collection\ValueLoader;

use Netgen\BlockManager\Collection\ValueLoaderInterface;
use eZ\Publish\API\Repository\LocationService;

class EzLocationValueLoader implements ValueLoaderInterface
{
    /**
     * @var \eZ\Publish\API\Repository\LocationService
     */
    protected $locationService;

    /**
     * Constructor.
     *
     * @param \eZ\Publish\API\Repository\LocationService $locationService
     */
    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }

    /**
     * Returns the value type this loader loads.
     *
     * @return string
     */
    public function getValueType()
    {
        return 'ezlocation';
    }

    /**
     * Loads the value from provided ID.
     *
     * @param int|string $id
     *
     * @return mixed
     */
    public function load($id)
    {
        return $this->locationService->loadLocation($id);
    }
}
