<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Item\ValueLoader;

use Netgen\BlockManager\Exception\InvalidItemException;
use Netgen\BlockManager\Item\ValueLoaderInterface;
use eZ\Publish\API\Repository\LocationService;
use Exception;

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
     * @throws \Netgen\BlockManager\Exception\InvalidItemException If value cannot be loaded
     *
     * @return mixed
     */
    public function load($id)
    {
        try {
            return $this->locationService->loadLocation($id);
        } catch (Exception $e) {
            throw new InvalidItemException('Value with ID ' . $id . ' could not be loaded.');
        }
    }
}
