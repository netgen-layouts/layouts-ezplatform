<?php

namespace Netgen\BlockManager\Ez\ContentProvider;

/**
 * A generic interface used to provide eZ Platform content and location
 * based on current conditions (e.g. current request).
 */
interface ContentProviderInterface
{
    /**
     * Provides the eZ Publish content object.
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Content|null
     */
    public function provideContent();

    /**
     * Provides the eZ Publish location object.
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Location|null
     */
    public function provideLocation();
}
