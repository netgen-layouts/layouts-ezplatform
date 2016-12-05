<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\Templating;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use Netgen\Bundle\BlockManagerBundle\Templating\PageLayoutResolverInterface;
use Netgen\Bundle\EzPublishBlockManagerBundle\Templating\PageLayoutResolver;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class PageLayoutResolverTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $innerResolverMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $configResolverMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestStackMock;

    /**
     * @var \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\PageLayoutResolver
     */
    protected $resolver;

    public function setUp()
    {
        $this->innerResolverMock = $this->createMock(PageLayoutResolverInterface::class);
        $this->configResolverMock = $this->createMock(ConfigResolverInterface::class);
        $this->requestStackMock = $this->createMock(RequestStack::class);

        $this->resolver = new PageLayoutResolver(
            $this->innerResolverMock,
            $this->configResolverMock,
            'viewbaseLayout'
        );

        $this->resolver->setRequestStack($this->requestStackMock);
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\PageLayoutResolver::__construct
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\PageLayoutResolver::resolvePageLayout
     */
    public function testResolvePageLayout()
    {
        $request = Request::create('/');

        $this->requestStackMock
            ->expects($this->once())
            ->method('getCurrentRequest')
            ->will($this->returnValue($request));

        $this->configResolverMock
            ->expects($this->at(0))
            ->method('hasParameter')
            ->with($this->equalTo('pagelayout'))
            ->will($this->returnValue(true));

        $this->configResolverMock
            ->expects($this->at(1))
            ->method('getParameter')
            ->with($this->equalTo('pagelayout'))
            ->will($this->returnValue('resolvedPagelayout'));

        $this->assertEquals('resolvedPagelayout', $this->resolver->resolvePageLayout());
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\PageLayoutResolver::resolvePageLayout
     */
    public function testResolvePageLayoutWitNoRequest()
    {
        $this->requestStackMock
            ->expects($this->once())
            ->method('getCurrentRequest')
            ->will($this->returnValue(null));

        $this->innerResolverMock
            ->expects($this->once())
            ->method('resolvePageLayout')
            ->will($this->returnValue('defaultPageLayout'));

        $this->configResolverMock
            ->expects($this->never())
            ->method('hasParameter');

        $this->configResolverMock
            ->expects($this->never())
            ->method('getParameter');

        $this->assertEquals('defaultPageLayout', $this->resolver->resolvePageLayout());
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\PageLayoutResolver::resolvePageLayout
     */
    public function testResolvePageLayoutWithDisabledLayout()
    {
        $request = Request::create('/');
        $request->attributes->set('layout', false);

        $this->requestStackMock
            ->expects($this->once())
            ->method('getCurrentRequest')
            ->will($this->returnValue($request));

        $this->innerResolverMock
            ->expects($this->never())
            ->method('resolvePageLayout');

        $this->configResolverMock
            ->expects($this->never())
            ->method('hasParameter');

        $this->configResolverMock
            ->expects($this->never())
            ->method('getParameter');

        $this->assertEquals('viewbaseLayout', $this->resolver->resolvePageLayout());
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\PageLayoutResolver::resolvePageLayout
     */
    public function testResolvePageLayoutNoPageLayoutParam()
    {
        $request = Request::create('/');

        $this->requestStackMock
            ->expects($this->once())
            ->method('getCurrentRequest')
            ->will($this->returnValue($request));

        $this->innerResolverMock
            ->expects($this->once())
            ->method('resolvePageLayout')
            ->will($this->returnValue('defaultPageLayout'));

        $this->configResolverMock
            ->expects($this->at(0))
            ->method('hasParameter')
            ->with($this->equalTo('pagelayout'))
            ->will($this->returnValue(false));

        $this->configResolverMock
            ->expects($this->never())
            ->method('getParameter');

        $this->assertEquals('defaultPageLayout', $this->resolver->resolvePageLayout());
    }
}
