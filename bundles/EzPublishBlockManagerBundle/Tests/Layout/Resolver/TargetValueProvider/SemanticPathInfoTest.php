<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver\TargetType;

use Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetType\SemanticPathInfo;
use Netgen\BlockManager\Traits\RequestStackAwareTrait;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;
use PHPUnit\Framework\TestCase;

class SemanticPathInfoTest extends TestCase
{
    use RequestStackAwareTrait;

    /**
     * @var \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetType\SemanticPathInfo
     */
    protected $targetType;

    public function setUp()
    {
        $request = Request::create('/the/answer');
        $request->attributes->set('semanticPathinfo', '/the/answer');

        $requestStack = new RequestStack();
        $requestStack->push($request);
        $this->setRequestStack($requestStack);

        $this->targetType = new SemanticPathInfo();
        $this->targetType->setRequestStack($this->requestStack);
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetType\SemanticPathInfo::provideValue
     */
    public function testProvideValue()
    {
        self::assertEquals(
            '/the/answer',
            $this->targetType->provideValue()
        );
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetType\SemanticPathInfo::provideValue
     */
    public function testProvideValueWithEmptySemanticPathInfo()
    {
        $this->requestStack->getCurrentRequest()->attributes->set('semanticPathinfo', false);

        self::assertEquals(
            '/',
            $this->targetType->provideValue()
        );
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetType\SemanticPathInfo::provideValue
     */
    public function testProvideValueWithNoRequest()
    {
        // Make sure we have no request
        $this->requestStack->pop();

        self::assertNull($this->targetType->provideValue());
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetType\SemanticPathInfo::provideValue
     */
    public function testProvideValueWithNoSemanticPathInfo()
    {
        // Make sure we have no semantic path info attribute
        $this->requestStack->getCurrentRequest()->attributes->remove('semanticPathinfo');

        self::assertNull($this->targetType->provideValue());
    }
}
