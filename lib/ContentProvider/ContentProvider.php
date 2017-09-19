<?php

namespace Netgen\BlockManager\Ez\ContentProvider;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Provides the eZ Platform content and location objects from the
 * current request.
 */
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

    public function __construct(ContentExtractorInterface $contentExtractor, RequestStack $requestStack)
    {
        $this->contentExtractor = $contentExtractor;
        $this->requestStack = $requestStack;
    }

    public function provideContent()
    {
        $currentRequest = $this->requestStack->getCurrentRequest();
        if (!$currentRequest instanceof Request) {
            return;
        }

        return $this->contentExtractor->extractContent($currentRequest);
    }

    public function provideLocation()
    {
        $currentRequest = $this->requestStack->getCurrentRequest();
        if (!$currentRequest instanceof Request) {
            return;
        }

        return $this->contentExtractor->extractLocation($currentRequest);
    }
}
