<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\LayoutResolver\RuleHandler\Doctrine\TargetHandler;

use Netgen\Bundle\EzPublishBlockManagerBundle\LayoutResolver\RuleHandler\Doctrine\TargetHandler\Subtree;

class SubtreeTest extends TargetHandlerTest
{
    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\LayoutResolver\RuleHandler\Doctrine\TargetHandler\Subtree::handleQuery
     */
    public function testLoadSubtreeRules()
    {
        $handler = $this->createHandler('subtree', $this->getTargetHandler());

        $expected = array(
            array(
                'layout_id' => 8,
                'conditions' => array(),
            ),
        );

        self::assertEquals($expected, $handler->loadRules('subtree', array(1, 2, 42)));
    }

    /**
     * Creates the handler under test.
     *
     * @return \Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\TargetHandler
     */
    protected function getTargetHandler()
    {
        return new Subtree();
    }
}
