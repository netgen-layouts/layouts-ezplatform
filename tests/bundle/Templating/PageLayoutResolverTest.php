<?php

declare(strict_types=1);

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\Templating;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use Netgen\Bundle\BlockManagerBundle\Templating\PageLayoutResolverInterface;
use Netgen\Bundle\EzPublishBlockManagerBundle\Templating\PageLayoutResolver;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class PageLayoutResolverTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $innerResolverMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $configResolverMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $requestStackMock;

    /**
     * @var \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\PageLayoutResolver
     */
    private $resolver;

    public function setUp(): void
    {
        $this->innerResolverMock = $this->createMock(PageLayoutResolverInterface::class);
        $this->configResolverMock = $this->createMock(ConfigResolverInterface::class);
        $this->requestStackMock = $this->createMock(RequestStack::class);

        $this->resolver = new PageLayoutResolver(
            $this->innerResolverMock,
            $this->configResolverMock,
            $this->requestStackMock,
            'viewbaseLayout'
        );
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\PageLayoutResolver::__construct
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\PageLayoutResolver::resolvePageLayout
     */
    public function testResolvePageLayout(): void
    {
        $request = Request::create('/');

        $this->requestStackMock
            ->expects(self::once())
            ->method('getCurrentRequest')
            ->willReturn($request);

        $this->configResolverMock
            ->expects(self::at(0))
            ->method('getParameter')
            ->with(self::identicalTo('pagelayout'))
            ->willReturn('resolvedPagelayout');

        self::assertSame('resolvedPagelayout', $this->resolver->resolvePageLayout());
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\PageLayoutResolver::resolvePageLayout
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
            ->willReturn('defaultPageLayout');

        $this->configResolverMock
            ->expects(self::never())
            ->method('hasParameter');

        $this->configResolverMock
            ->expects(self::never())
            ->method('getParameter');

        self::assertSame('defaultPageLayout', $this->resolver->resolvePageLayout());
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\PageLayoutResolver::resolvePageLayout
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

        self::assertSame('viewbaseLayout', $this->resolver->resolvePageLayout());
    }
}
