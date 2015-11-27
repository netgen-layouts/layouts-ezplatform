<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\LayoutResolver\RuleHandler\Doctrine\TargetHandler;

use Netgen\Bundle\EzPublishBlockManagerBundle\LayoutResolver\RuleHandler\Doctrine\TargetHandler\Children;

class ChildrenTest extends TargetHandlerTest
{
    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\LayoutResolver\RuleHandler\Doctrine\TargetHandler\Children::handleQuery
     */
    public function testLoadChildrenRules()
    {
        $handler = $this->createHandler('children', $this->getTargetHandler());

        $expected = array(
            array(
                'layout_id' => 7,
                'conditions' => array(),
            ),
        );

        self::assertEquals($expected, $handler->loadRules('children', array(54)));
    }

    /**
     * Creates the handler under test.
     *
     * @return \Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\TargetHandler
     */
    protected function getTargetHandler()
    {
        return new Children();
    }
}
