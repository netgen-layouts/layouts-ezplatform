<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Context;

use eZ\Publish\API\Repository\Values\Content\Location;
use Netgen\BlockManager\Context\ContextInterface;
use Netgen\BlockManager\Context\ContextProviderInterface;
use Netgen\BlockManager\Ez\ContentProvider\ContentExtractorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class ContextProvider implements ContextProviderInterface
{
    /**
     * @var \Netgen\BlockManager\Ez\ContentProvider\ContentExtractorInterface
     */
    private $contentExtractor;

    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    private $requestStack;

    public function __construct(ContentExtractorInterface $contentExtractor, RequestStack $requestStack)
    {
        $this->contentExtractor = $contentExtractor;
        $this->requestStack = $requestStack;
    }

    public function provideContext(ContextInterface $context): void
    {
        $currentRequest = $this->requestStack->getCurrentRequest();
        if (!$currentRequest instanceof Request) {
            return;
        }

        $location = $this->contentExtractor->extractLocation($currentRequest);

        if ($location instanceof Location) {
            $context->set('ez_location_id', (int) $location->id);
        }
    }
}
