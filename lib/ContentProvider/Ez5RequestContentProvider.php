<?php

namespace Netgen\BlockManager\Ez\ContentProvider;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Location;
use Netgen\BlockManager\Traits\RequestStackAwareTrait;
use Symfony\Component\HttpFoundation\Request;

/**
 * @deprecated Class used to provide content and location from current request
 * in eZ Publish 5
 */
class Ez5RequestContentProvider implements ContentProviderInterface
{
    use RequestStackAwareTrait;

    /**
     * Provides the eZ Publish content value object.
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Content
     */
    public function provideContent()
    {
        $currentRequest = $this->requestStack->getCurrentRequest();
        if (!$currentRequest instanceof Request) {
            return;
        }

        $content = $currentRequest->attributes->get('content');

        return $content instanceof Content ? $content : null;
    }

    /**
     * Provides the eZ Publish location value object.
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Location
     */
    public function provideLocation()
    {
        $currentRequest = $this->requestStack->getCurrentRequest();
        if (!$currentRequest instanceof Request) {
            return;
        }

        $location = $currentRequest->attributes->get('location');

        return $location instanceof Location ? $location : null;
    }
}
