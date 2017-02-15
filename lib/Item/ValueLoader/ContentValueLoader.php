<?php

namespace Netgen\BlockManager\Ez\Item\ValueLoader;

use Exception;
use eZ\Publish\API\Repository\ContentService;
use Netgen\BlockManager\Exception\InvalidItemException;
use Netgen\BlockManager\Item\ValueLoaderInterface;

class ContentValueLoader implements ValueLoaderInterface
{
    /**
     * @var \eZ\Publish\API\Repository\ContentService
     */
    protected $contentService;

    /**
     * Constructor.
     *
     * @param \eZ\Publish\API\Repository\ContentService $contentService
     */
    public function __construct(ContentService $contentService)
    {
        $this->contentService = $contentService;
    }

    /**
     * Returns the value type this loader loads.
     *
     * @return string
     */
    public function getValueType()
    {
        return 'ezcontent';
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
            $contentInfo = $this->contentService->loadContentInfo($id);
        } catch (Exception $e) {
            throw new InvalidItemException(
                sprintf('Content with ID "%s" could not be loaded.', $id),
                0,
                $e
            );
        }

        if (!$contentInfo->published) {
            throw new InvalidItemException(
                sprintf('Content with ID "%s" is not published and cannot loaded.', $id)
            );
        }

        if ($contentInfo->mainLocationId === null) {
            throw new InvalidItemException(
                sprintf('Content with ID "%s" does not have a main location and cannot loaded.', $id)
            );
        }

        return $contentInfo;
    }
}
