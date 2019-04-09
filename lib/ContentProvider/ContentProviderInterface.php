<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\ContentProvider;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Location;

/**
 * A generic interface used to provide eZ Platform content and location
 * based on current conditions (e.g. current request).
 */
interface ContentProviderInterface
{
    /**
     * Provides the eZ Platform content object.
     */
    public function provideContent(): ?Content;

    /**
     * Provides the eZ Platform location object.
     */
    public function provideLocation(): ?Location;
}
