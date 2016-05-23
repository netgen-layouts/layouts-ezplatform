<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\Layout\Resolver\TargetBuilder;

use Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetBuilder\Content;
use Netgen\BlockManager\Layout\Resolver\Target;
use Netgen\BlockManager\Traits\RequestStackAwareTrait;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;

class ContentTest extends \PHPUnit_Framework_TestCase
{
    use RequestStackAwareTrait;

    /**
     * @var \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetBuilder\Content
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
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetBuilder\Content::buildTarget
     */
    public function testBuildTarget()
    {
        self::assertEquals(
            new Target(array('identifier' => 'content', 'values' => array(42))),
            $this->targetBuilder->buildTarget()
        );
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetBuilder\Content::buildTarget
     */
    public function testBuildTargetWithNoRequest()
    {
        // Make sure we have no request
        $this->requestStack->pop();

        self::assertNull($this->targetBuilder->buildTarget());
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetBuilder\Content::buildTarget
     */
    public function testBuildTargetWithNoContentId()
    {
        // Make sure we have no content ID attribute
        $this->requestStack->getCurrentRequest()->attributes->remove('contentId');

        self::assertNull($this->targetBuilder->buildTarget());
    }
}
