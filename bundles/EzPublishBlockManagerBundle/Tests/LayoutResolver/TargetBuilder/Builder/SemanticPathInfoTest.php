<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver\TargetBuilder\Builder;

use Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\Target\SemanticPathInfo as SemanticPathInfoTarget;
use Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetBuilder\Builder\SemanticPathInfo;
use Netgen\BlockManager\Traits\RequestStackAwareTrait;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;

class SemanticPathInfoTest extends \PHPUnit_Framework_TestCase
{
    use RequestStackAwareTrait;

    /**
     * @var \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetBuilder\Builder\SemanticPathInfo
     */
    protected $targetBuilder;

    /**
     * Sets up the route target builder tests.
     */
    public function setUp()
    {
        $request = Request::create('/the/answer');
        $request->attributes->set('semanticPathinfo', '/the/answer');

        $requestStack = new RequestStack();
        $requestStack->push($request);
        $this->setRequestStack($requestStack);

        $this->targetBuilder = new SemanticPathInfo();
        $this->targetBuilder->setRequestStack($this->requestStack);
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetBuilder\Builder\SemanticPathInfo::buildTarget
     */
    public function testBuildTarget()
    {
        self::assertEquals(new SemanticPathInfoTarget(array('/the/answer')), $this->targetBuilder->buildTarget());
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetBuilder\Builder\SemanticPathInfo::buildTarget
     */
    public function testBuildTargetWithEmptySemanticPathInfo()
    {
        $this->requestStack->getCurrentRequest()->attributes->set('semanticPathinfo', false);

        self::assertEquals(new SemanticPathInfoTarget(array('/')), $this->targetBuilder->buildTarget());
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetBuilder\Builder\SemanticPathInfo::buildTarget
     */
    public function testBuildTargetWithNoRequest()
    {
        // Make sure we have no request
        $this->requestStack->pop();

        self::assertFalse($this->targetBuilder->buildTarget());
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetBuilder\Builder\SemanticPathInfo::buildTarget
     */
    public function testBuildTargetWithNoSemanticPathInfo()
    {
        // Make sure we have no semantic path info attribute
        $this->requestStack->getCurrentRequest()->attributes->remove('semanticPathinfo');

        self::assertFalse($this->targetBuilder->buildTarget());
    }
}
