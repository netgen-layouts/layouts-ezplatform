<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsEzPlatformBundle\Tests\Templating;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use Netgen\Bundle\LayoutsBundle\Templating\PageLayoutResolverInterface;
use Netgen\Bundle\LayoutsEzPlatformBundle\Templating\PageLayoutResolver;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class PageLayoutResolverTest extends TestCase
{
    private MockObject $innerResolverMock;

    private MockObject $configResolverMock;

    private MockObject $requestStackMock;

    private PageLayoutResolver $resolver;

    protected function setUp(): void
    {
        $this->innerResolverMock = $this->createMock(PageLayoutResolverInterface::class);
        $this->configResolverMock = $this->createMock(ConfigResolverInterface::class);
        $this->requestStackMock = $this->createMock(RequestStack::class);

        $this->resolver = new PageLayoutResolver(
            $this->innerResolverMock,
            $this->configResolverMock,
            $this->requestStackMock,
            'fallback_layout.html.twig',
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsEzPlatformBundle\Templating\PageLayoutResolver::__construct
     * @covers \Netgen\Bundle\LayoutsEzPlatformBundle\Templating\PageLayoutResolver::resolvePageLayout
     */
    public function testResolvePageLayout(): void
    {
        $request = Request::create('/');

        $this->requestStackMock
            ->expects(self::once())
            ->method('getCurrentRequest')
            ->willReturn($request);

        $this->configResolverMock
            ->method('hasParameter')
            ->with(self::identicalTo('page_layout'))
            ->willReturn(true);

        $this->configResolverMock
            ->method('getParameter')
            ->with(self::identicalTo('page_layout'))
            ->willReturn('resolved_layout.html.twig');

        self::assertSame('resolved_layout.html.twig', $this->resolver->resolvePageLayout());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsEzPlatformBundle\Templating\PageLayoutResolver::resolvePageLayout
     */
    public function testResolvePageLayoutWithLegacyPagelayout(): void
    {
        $request = Request::create('/');

        $this->requestStackMock
            ->expects(self::once())
            ->method('getCurrentRequest')
            ->willReturn($request);

        $this->configResolverMock
            ->method('hasParameter')
            ->with(self::identicalTo('page_layout'))
            ->willReturn(false);

        $this->configResolverMock
            ->method('getParameter')
            ->with(self::identicalTo('pagelayout'))
            ->willReturn('resolved_layout.html.twig');

        self::assertSame('resolved_layout.html.twig', $this->resolver->resolvePageLayout());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsEzPlatformBundle\Templating\PageLayoutResolver::resolvePageLayout
     */
    public function testResolvePageLayoutWitNoRequest(): void
    {
        $this->requestStackMock
            ->expects(self::once())
            ->method('getCurrentRequest')
            ->willReturn(null);

        $this->innerResolverMock
            ->expects(self::once())
            ->method('resolvePageLayout')
            ->willReturn('default_layout.html.twig');

        $this->configResolverMock
            ->expects(self::never())
            ->method('hasParameter');

        $this->configResolverMock
            ->expects(self::never())
            ->method('getParameter');

        self::assertSame('default_layout.html.twig', $this->resolver->resolvePageLayout());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsEzPlatformBundle\Templating\PageLayoutResolver::resolvePageLayout
     */
    public function testResolvePageLayoutWithDisabledLayout(): void
    {
        $request = Request::create('/');
        $request->attributes->set('layout', false);

        $this->requestStackMock
            ->expects(self::once())
            ->method('getCurrentRequest')
            ->willReturn($request);

        $this->innerResolverMock
            ->expects(self::never())
            ->method('resolvePageLayout');

        $this->configResolverMock
            ->expects(self::never())
            ->method('hasParameter');

        $this->configResolverMock
            ->expects(self::never())
            ->method('getParameter');

        self::assertSame('fallback_layout.html.twig', $this->resolver->resolvePageLayout());
    }
}
