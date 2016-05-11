<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Collection\ValueLoader;

use Netgen\BlockManager\Collection\ValueLoaderInterface;
use eZ\Publish\API\Repository\ContentService;

class EzContentValueLoader implements ValueLoaderInterface
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
     * @return mixed
     */
    public function load($id)
    {
        return $this->contentService->loadContentInfo($id);
    }
}
