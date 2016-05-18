<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\Layout\Resolver\TargetBuilder\Builder;

use Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\Target\SemanticPathInfoPrefix as SemanticPathInfoPrefixTarget;
use Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetBuilder\Builder\SemanticPathInfoPrefix;
use Netgen\BlockManager\Traits\RequestStackAwareTrait;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;

class SemanticPathInfoPrefixTest extends \PHPUnit_Framework_TestCase
{
    use RequestStackAwareTrait;

    /**
     * @var \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetBuilder\Builder\SemanticPathInfoPrefix
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

        $this->targetBuilder = new SemanticPathInfoPrefix();
        $this->targetBuilder->setRequestStack($this->requestStack);
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetBuilder\Builder\SemanticPathInfoPrefix::buildTarget
     */
    public function testBuildTarget()
    {
        self::assertEquals(new SemanticPathInfoPrefixTarget(array('/the/answer')), $this->targetBuilder->buildTarget());
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetBuilder\Builder\SemanticPathInfoPrefix::buildTarget
     */
    public function testBuildTargetWithEmptySemanticPathInfo()
    {
        $this->requestStack->getCurrentRequest()->attributes->set('semanticPathinfo', false);

        self::assertEquals(new SemanticPathInfoPrefixTarget(array('/')), $this->targetBuilder->buildTarget());
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetBuilder\Builder\SemanticPathInfoPrefix::buildTarget
     */
    public function testBuildTargetWithNoRequest()
    {
        // Make sure we have no request
        $this->requestStack->pop();

        self::assertFalse($this->targetBuilder->buildTarget());
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetBuilder\Builder\SemanticPathInfoPrefix::buildTarget
     */
    public function testBuildTargetWithNoSemanticPathInfo()
    {
        // Make sure we have no semantic path info attribute
        $this->requestStack->getCurrentRequest()->attributes->remove('semanticPathinfo');

        self::assertFalse($this->targetBuilder->buildTarget());
    }
}
