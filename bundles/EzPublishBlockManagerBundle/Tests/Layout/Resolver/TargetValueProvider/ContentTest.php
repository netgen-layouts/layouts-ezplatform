<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\Layout\Resolver\TargetType;

use Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetType\Content;
use Netgen\BlockManager\Traits\RequestStackAwareTrait;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;
use PHPUnit\Framework\TestCase;

class ContentTest extends TestCase
{
    use RequestStackAwareTrait;

    /**
     * @var \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetType\Content
     */
    protected $targetType;

    public function setUp()
    {
        $request = Request::create('/');
        $request->attributes->set('contentId', 42);

        $requestStack = new RequestStack();
        $requestStack->push($request);
        $this->setRequestStack($requestStack);

        $this->targetType = new Content();
        $this->targetType->setRequestStack($this->requestStack);
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetType\Content::provideValue
     */
    public function testProvideValue()
    {
        self::assertEquals(
            42,
            $this->targetType->provideValue()
        );
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetType\Content::provideValue
     */
    public function testProvideValueWithNoRequest()
    {
        // Make sure we have no request
        $this->requestStack->pop();

        self::assertNull($this->targetType->provideValue());
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetType\Content::provideValue
     */
    public function testProvideValueWithNoContentId()
    {
        // Make sure we have no content ID attribute
        $this->requestStack->getCurrentRequest()->attributes->remove('contentId');

        self::assertNull($this->targetType->provideValue());
    }
}
