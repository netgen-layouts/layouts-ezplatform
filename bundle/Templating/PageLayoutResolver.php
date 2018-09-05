<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Templating;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use Netgen\Bundle\BlockManagerBundle\Templating\PageLayoutResolverInterface;
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
    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Templating\PageLayoutResolverInterface
     */
    private $innerResolver;

    /**
     * @var \eZ\Publish\Core\MVC\ConfigResolverInterface
     */
    private $configResolver;

    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    private $requestStack;

    /**
     * @var string
     */
    private $viewbaseLayout;

    public function __construct(
        PageLayoutResolverInterface $innerResolver,
        ConfigResolverInterface $configResolver,
        RequestStack $requestStack,
        string $viewbaseLayout
    ) {
        $this->innerResolver = $innerResolver;
        $this->configResolver = $configResolver;
        $this->requestStack = $requestStack;
        $this->viewbaseLayout = $viewbaseLayout;
    }

    public function resolvePageLayout(): string
    {
        $currentRequest = $this->requestStack->getCurrentRequest();
        if (!$currentRequest instanceof Request) {
            return $this->innerResolver->resolvePageLayout();
        }

        if ($currentRequest->attributes->get('layout') === false) {
            return $this->viewbaseLayout;
        }

        return $this->configResolver->getParameter('pagelayout');
    }
}
