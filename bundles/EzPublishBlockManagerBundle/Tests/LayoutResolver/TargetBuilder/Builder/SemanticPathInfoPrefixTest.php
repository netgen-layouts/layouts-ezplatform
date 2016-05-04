<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\LayoutResolver\TargetBuilder\Builder;

use Netgen\Bundle\EzPublishBlockManagerBundle\LayoutResolver\Target\SemanticPathInfoPrefix as SemanticPathInfoPrefixTarget;
use Netgen\Bundle\EzPublishBlockManagerBundle\LayoutResolver\TargetBuilder\Builder\SemanticPathInfoPrefix;
use Netgen\BlockManager\Traits\RequestStackAwareTrait;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;

class SemanticPathInfoPrefixTest extends \PHPUnit_Framework_TestCase
{
    use RequestStackAwareTrait;

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
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\LayoutResolver\TargetBuilder\Builder\SemanticPathInfoPrefix::buildTarget
     */
    public function testBuildTarget()
    {
        $targetBuilder = new SemanticPathInfoPrefix();
        $targetBuilder->setRequestStack($this->requestStack);

        self::assertEquals(new SemanticPathInfoPrefixTarget(array('/the/answer')), $targetBuilder->buildTarget());
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\LayoutResolver\TargetBuilder\Builder\SemanticPathInfoPrefix::buildTarget
     */
    public function testBuildTargetWithEmptySemanticPathInfo()
    {
        $this->requestStack->getCurrentRequest()->attributes->set('semanticPathinfo', false);

        $targetBuilder = new SemanticPathInfoPrefix();
        $targetBuilder->setRequestStack($this->requestStack);

        self::assertEquals(new SemanticPathInfoPrefixTarget(array('/')), $targetBuilder->buildTarget());
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\LayoutResolver\TargetBuilder\Builder\SemanticPathInfoPrefix::buildTarget
     */
    public function testBuildTargetWithNoRequest()
    {
        // Make sure we have no request
        $this->requestStack->pop();

        $targetBuilder = new SemanticPathInfoPrefix();
        $targetBuilder->setRequestStack($this->requestStack);

        self::assertFalse($targetBuilder->buildTarget());
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\LayoutResolver\TargetBuilder\Builder\SemanticPathInfoPrefix::buildTarget
     */
    public function testBuildTargetWithNoSemanticPathInfo()
    {
        // Make sure we have no semantic path info attribute
        $this->requestStack->getCurrentRequest()->attributes->remove('semanticPathinfo');

        $targetBuilder = new SemanticPathInfoPrefix();
        $targetBuilder->setRequestStack($this->requestStack);

        self::assertFalse($targetBuilder->buildTarget());
    }
}
