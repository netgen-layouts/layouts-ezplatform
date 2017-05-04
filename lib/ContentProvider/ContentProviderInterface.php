<?php

namespace Netgen\BlockManager\Ez\ContentProvider;

interface ContentProviderInterface
{
    /**
     * Provides the eZ Publish content value object.
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Content|void
     */
    public function provideContent();

    /**
     * Provides the eZ Publish location value object.
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Location|void
     */
    public function provideLocation();
}
