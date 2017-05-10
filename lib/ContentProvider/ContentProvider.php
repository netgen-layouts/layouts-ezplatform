<?php

namespace Netgen\BlockManager\Ez\ContentProvider;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class ContentProvider implements ContentProviderInterface
{
    /**
     * @var \Netgen\BlockManager\Ez\ContentProvider\ContentExtractorInterface
     */
    protected $contentExtractor;

    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    protected $requestStack;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Ez\ContentProvider\ContentExtractorInterface $contentExtractor
     * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack
     */
    public function __construct(ContentExtractorInterface $contentExtractor, RequestStack $requestStack)
    {
        $this->contentExtractor = $contentExtractor;
        $this->requestStack = $requestStack;
    }

    /**
     * Provides the eZ Publish content value object.
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Content|void
     */
    public function provideContent()
    {
        $currentRequest = $this->requestStack->getCurrentRequest();
        if (!$currentRequest instanceof Request) {
            return;
        }

        return $this->contentExtractor->extractContent($currentRequest);
    }

    /**
     * Provides the eZ Publish location value object.
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Location|void
     */
    public function provideLocation()
    {
        $currentRequest = $this->requestStack->getCurrentRequest();
        if (!$currentRequest instanceof Request) {
            return;
        }

        return $this->contentExtractor->extractLocation($currentRequest);
    }
}
