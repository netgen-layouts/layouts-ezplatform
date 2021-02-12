<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsEzPlatformBundle\Templating;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use Netgen\Bundle\LayoutsBundle\Templating\PageLayoutResolverInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * This is the eZ Platform specific page layout resolver
 * which provides the pagelayout by reading it from the pagelayout
 * configuration of eZ Platform. Meaning, Netgen Layouts will
 * automatically use the pagelayout configured inside eZ Platform.
 */
final class PageLayoutResolver implements PageLayoutResolverInterface
{
    private PageLayoutResolverInterface $innerResolver;

    private ConfigResolverInterface $configResolver;

    private RequestStack $requestStack;

    private string $baseViewLayout;

    public function __construct(
        PageLayoutResolverInterface $innerResolver,
        ConfigResolverInterface $configResolver,
        RequestStack $requestStack,
        string $baseViewLayout
    ) {
        $this->innerResolver = $innerResolver;
        $this->configResolver = $configResolver;
        $this->requestStack = $requestStack;
        $this->baseViewLayout = $baseViewLayout;
    }

    public function resolvePageLayout(): string
    {
        $currentRequest = $this->requestStack->getCurrentRequest();
        if (!$currentRequest instanceof Request) {
            return $this->innerResolver->resolvePageLayout();
        }

        if ($currentRequest->attributes->get('layout') === false) {
            return $this->baseViewLayout;
        }

        return $this->configResolver->hasParameter('page_layout') ?
            $this->configResolver->getParameter('page_layout') :
            $this->configResolver->getParameter('pagelayout');
    }
}
