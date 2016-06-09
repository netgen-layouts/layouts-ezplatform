<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\Layout\Resolver\TargetValueProvider;

use Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetValueProvider\Content;
use Netgen\BlockManager\Traits\RequestStackAwareTrait;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;

class ContentTest extends \PHPUnit\Framework\TestCase
{
    use RequestStackAwareTrait;

    /**
     * @var \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetValueProvider\Content
     */
    protected $targetValueProvider;

    public function setUp()
    {
        $request = Request::create('/');
        $request->attributes->set('contentId', 42);

        $requestStack = new RequestStack();
        $requestStack->push($request);
        $this->setRequestStack($requestStack);

        $this->targetValueProvider = new Content();
        $this->targetValueProvider->setRequestStack($this->requestStack);
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetValueProvider\Content::provideValue
     */
    public function testProvideValue()
    {
        self::assertEquals(
            42,
            $this->targetValueProvider->provideValue()
        );
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetValueProvider\Content::provideValue
     */
    public function testProvideValueWithNoRequest()
    {
        // Make sure we have no request
        $this->requestStack->pop();

        self::assertNull($this->targetValueProvider->provideValue());
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetValueProvider\Content::provideValue
     */
    public function testProvideValueWithNoContentId()
    {
        // Make sure we have no content ID attribute
        $this->requestStack->getCurrentRequest()->attributes->remove('contentId');

        self::assertNull($this->targetValueProvider->provideValue());
    }
}
