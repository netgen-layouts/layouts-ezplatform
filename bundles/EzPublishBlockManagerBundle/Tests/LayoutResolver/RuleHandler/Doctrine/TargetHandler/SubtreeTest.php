<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\Layout\Resolver\RuleHandler\Doctrine\TargetHandler;

use Netgen\BlockManager\Tests\Layout\Resolver\RuleHandler\Doctrine\TargetHandler\TargetHandlerTest;
use Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\RuleHandler\Doctrine\TargetHandler\Subtree;

class SubtreeTest extends TargetHandlerTest
{
    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\RuleHandler\Doctrine\TargetHandler\Subtree::handleQuery
     */
    public function testLoadRules()
    {
        $expected = array(
            array(
                'layout_id' => 18,
                'conditions' => array(),
            ),
        );

        self::assertEquals($expected, $this->handler->loadRules($this->getTargetIdentifier(), array(1, 2, 42)));
    }

    /**
     * Returns the target identifier under test.
     *
     * @return \Netgen\BlockManager\Layout\Resolver\RuleHandler\Doctrine\TargetHandler
     */
    protected function getTargetIdentifier()
    {
        return 'subtree';
    }

    /**
     * Creates the handler under test.
     *
     * @return \Netgen\BlockManager\Layout\Resolver\RuleHandler\Doctrine\TargetHandler
     */
    protected function getTargetHandler()
    {
        return new Subtree();
    }
}
