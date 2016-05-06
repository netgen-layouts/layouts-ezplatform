<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\LayoutResolver\TargetBuilder\Builder;

use Netgen\Bundle\EzPublishBlockManagerBundle\LayoutResolver\TargetBuilder\Builder\Content;
use Netgen\Bundle\EzPublishBlockManagerBundle\LayoutResolver\Target\Content as ContentTarget;
use Netgen\BlockManager\Traits\RequestStackAwareTrait;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;

class ContentTest extends \PHPUnit_Framework_TestCase
{
    use RequestStackAwareTrait;

    /**
     * @var \Netgen\Bundle\EzPublishBlockManagerBundle\LayoutResolver\TargetBuilder\Builder\Content
     */
    protected $targetBuilder;

    /**
     * Sets up the route target builder tests.
     */
    public function setUp()
    {
        $request = Request::create('/');
        $request->attributes->set('contentId', 42);

        $requestStack = new RequestStack();
        $requestStack->push($request);
        $this->setRequestStack($requestStack);

        $this->targetBuilder = new Content();
        $this->targetBuilder->setRequestStack($this->requestStack);
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\LayoutResolver\TargetBuilder\Builder\Content::buildTarget
     */
    public function testBuildTarget()
    {
        self::assertEquals(new ContentTarget(array(42)), $this->targetBuilder->buildTarget());
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\LayoutResolver\TargetBuilder\Builder\Content::buildTarget
     */
    public function testBuildTargetWithNoRequest()
    {
        // Make sure we have no request
        $this->requestStack->pop();

        self::assertFalse($this->targetBuilder->buildTarget());
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\LayoutResolver\TargetBuilder\Builder\Content::buildTarget
     */
    public function testBuildTargetWithNoContentId()
    {
        // Make sure we have no content ID attribute
        $this->requestStack->getCurrentRequest()->attributes->remove('contentId');

        self::assertFalse($this->targetBuilder->buildTarget());
    }
}
