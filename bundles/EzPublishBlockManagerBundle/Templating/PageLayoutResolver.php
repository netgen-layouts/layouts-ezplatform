<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Templating;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use Netgen\Bundle\BlockManagerBundle\Templating\PageLayoutResolver as BasePageLayoutResolver;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class PageLayoutResolver extends BasePageLayoutResolver
{
    /**
     * @var \eZ\Publish\Core\MVC\ConfigResolverInterface
     */
    protected $configResolver;

    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    protected $requestStack;

    /**
     * @var string
     */
    protected $viewbaseLayout;

    /**
     * Constructor.
     *
     * @param \eZ\Publish\Core\MVC\ConfigResolverInterface $configResolver
     * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack
     * @param string $viewbaseLayout
     * @param string $defaultPageLayout
     */
    public function __construct(
        ConfigResolverInterface $configResolver,
        RequestStack $requestStack,
        $viewbaseLayout,
        $defaultPageLayout
    ) {
        $this->configResolver = $configResolver;
        $this->requestStack = $requestStack;
        $this->viewbaseLayout = $viewbaseLayout;

        parent::__construct($defaultPageLayout);
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
            return parent::resolvePageLayout();
        }

        if ($currentRequest->attributes->get('layout') === false) {
            return $this->viewbaseLayout;
        }

        if (!$this->configResolver->hasParameter('pagelayout')) {
            return parent::resolvePageLayout();
        }

        return $this->configResolver->getParameter('pagelayout');
    }
}
