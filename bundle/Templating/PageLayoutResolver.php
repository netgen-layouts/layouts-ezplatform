<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Templating;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use Netgen\BlockManager\Traits\RequestStackAwareTrait;
use Netgen\Bundle\BlockManagerBundle\Templating\PageLayoutResolverInterface;
use Symfony\Component\HttpFoundation\Request;

class PageLayoutResolver implements PageLayoutResolverInterface
{
    use RequestStackAwareTrait;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Templating\PageLayoutResolverInterface
     */
    protected $innerResolver;

    /**
     * @var \eZ\Publish\Core\MVC\ConfigResolverInterface
     */
    protected $configResolver;

    /**
     * @var string
     */
    protected $viewbaseLayout;

    /**
     * Constructor.
     *
     * @param \Netgen\Bundle\BlockManagerBundle\Templating\PageLayoutResolverInterface $innerResolver
     * @param \eZ\Publish\Core\MVC\ConfigResolverInterface $configResolver
     * @param string $viewbaseLayout
     */
    public function __construct(
        PageLayoutResolverInterface $innerResolver,
        ConfigResolverInterface $configResolver,
        $viewbaseLayout
    ) {
        $this->innerResolver = $innerResolver;
        $this->configResolver = $configResolver;
        $this->viewbaseLayout = $viewbaseLayout;
    }

    /**
     * Resolves the main page layout used to render the page.
     *
     * @return string
     */
    public function resolvePageLayout()
    {
        $currentRequest = $this->requestStack->getCurrentRequest();
        if (!$currentRequest instanceof Request) {
            return $this->innerResolver->resolvePageLayout();
        }

        if ($currentRequest->attributes->get('layout') === false) {
            return $this->viewbaseLayout;
        }

        if (!$this->configResolver->hasParameter('pagelayout')) {
            return $this->innerResolver->resolvePageLayout();
        }

        return $this->configResolver->getParameter('pagelayout');
    }
}
