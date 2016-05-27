<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver\TargetValueProvider;

use Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetValueProvider\SemanticPathInfo;
use Netgen\BlockManager\Traits\RequestStackAwareTrait;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;

class SemanticPathInfoTest extends \PHPUnit_Framework_TestCase
{
    use RequestStackAwareTrait;

    /**
     * @var \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetValueProvider\SemanticPathInfo
     */
    protected $targetValueProvider;

    public function setUp()
    {
        $request = Request::create('/the/answer');
        $request->attributes->set('semanticPathinfo', '/the/answer');

        $requestStack = new RequestStack();
        $requestStack->push($request);
        $this->setRequestStack($requestStack);

        $this->targetValueProvider = new SemanticPathInfo();
        $this->targetValueProvider->setRequestStack($this->requestStack);
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetValueProvider\SemanticPathInfo::provideValue
     */
    public function testProvideValue()
    {
        self::assertEquals(
            '/the/answer',
            $this->targetValueProvider->provideValue()
        );
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetValueProvider\SemanticPathInfo::provideValue
     */
    public function testProvideValueWithEmptySemanticPathInfo()
    {
        $this->requestStack->getCurrentRequest()->attributes->set('semanticPathinfo', false);

        self::assertEquals(
            '/',
            $this->targetValueProvider->provideValue()
        );
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetValueProvider\SemanticPathInfo::provideValue
     */
    public function testProvideValueWithNoRequest()
    {
        // Make sure we have no request
        $this->requestStack->pop();

        self::assertNull($this->targetValueProvider->provideValue());
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetValueProvider\SemanticPathInfo::provideValue
     */
    public function testProvideValueWithNoSemanticPathInfo()
    {
        // Make sure we have no semantic path info attribute
        $this->requestStack->getCurrentRequest()->attributes->remove('semanticPathinfo');

        self::assertNull($this->targetValueProvider->provideValue());
    }
}
