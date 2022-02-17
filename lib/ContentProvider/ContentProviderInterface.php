<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\ContentProvider;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;

/**
 * A generic interface used to provide Ibexa Platform content and location
 * based on current conditions (e.g. current request).
 */
interface ContentProviderInterface
{
    /**
     * Provides the Ibexa Platform content object.
     */
    public function provideContent(): ?Content;

    /**
     * Provides the Ibexa Platform location object.
     */
    public function provideLocation(): ?Location;
}
