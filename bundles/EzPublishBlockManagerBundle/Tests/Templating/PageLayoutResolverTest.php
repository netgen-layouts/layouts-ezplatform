<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\Templating;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use Netgen\Bundle\EzPublishBlockManagerBundle\Templating\PageLayoutResolver;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class PageLayoutResolverTest extends \PHPUnit_Framework_TestCase
{
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
        $this->configResolverMock = $this->getMock(ConfigResolverInterface::class);

        $this->requestStackMock = $this->getMock(RequestStack::class);

        $this->resolver = new PageLayoutResolver(
            $this->configResolverMock,
            $this->requestStackMock,
            'viewbaseLayout',
            'defaultPagelayout'
        );
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\PageLayoutResolver::__construct
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\PageLayoutResolver::resolvePageLayout
     */
    public function testResolvePageLayout()
    {
        $request = Request::create('/');

        $this->requestStackMock
            ->expects($this->at(0))
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

        self::assertEquals('resolvedPagelayout', $this->resolver->resolvePageLayout());
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\PageLayoutResolver::resolvePageLayout
     */
    public function testResolvePageLayoutWitNoRequest()
    {
        $this->requestStackMock
            ->expects($this->at(0))
            ->method('getCurrentRequest')
            ->will($this->returnValue(null));

        $this->configResolverMock
            ->expects($this->never())
            ->method('hasParameter');

        $this->configResolverMock
            ->expects($this->never())
            ->method('getParameter');

        self::assertEquals('defaultPagelayout', $this->resolver->resolvePageLayout());
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\PageLayoutResolver::resolvePageLayout
     */
    public function testResolvePageLayoutWithDisabledLayout()
    {
        $request = Request::create('/');
        $request->attributes->set('layout', false);

        $this->requestStackMock
            ->expects($this->at(0))
            ->method('getCurrentRequest')
            ->will($this->returnValue($request));

        $this->configResolverMock
            ->expects($this->never())
            ->method('hasParameter');

        $this->configResolverMock
            ->expects($this->never())
            ->method('getParameter');

        self::assertEquals('viewbaseLayout', $this->resolver->resolvePageLayout());
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Templating\PageLayoutResolver::resolvePageLayout
     */
    public function testResolvePageLayoutNoPageLayoutParam()
    {
        $request = Request::create('/');

        $this->requestStackMock
            ->expects($this->at(0))
            ->method('getCurrentRequest')
            ->will($this->returnValue($request));

        $this->configResolverMock
            ->expects($this->at(0))
            ->method('hasParameter')
            ->with($this->equalTo('pagelayout'))
            ->will($this->returnValue(false));

        $this->configResolverMock
            ->expects($this->never())
            ->method('getParameter');

        self::assertEquals('defaultPagelayout', $this->resolver->resolvePageLayout());
    }
}
