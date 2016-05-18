<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\Layout\Resolver\RuleHandler\Doctrine\TargetHandler;

use Netgen\BlockManager\Tests\Layout\Resolver\RuleHandler\Doctrine\TargetHandler\TargetHandlerTest;
use Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\RuleHandler\Doctrine\TargetHandler\Content;

class ContentTest extends TargetHandlerTest
{
    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\RuleHandler\Doctrine\TargetHandler\Content::handleQuery
     */
    public function testLoadRules()
    {
        $expected = array(
            array(
                'layout_id' => 14,
                'conditions' => array(),
            ),
        );

        self::assertEquals($expected, $this->handler->loadRules($this->getTargetIdentifier(), array(48)));
    }

    /**
     * Returns the target identifier under test.
     *
     * @return \Netgen\BlockManager\Layout\Resolver\RuleHandler\Doctrine\TargetHandler
     */
    protected function getTargetIdentifier()
    {
        return 'content';
    }

    /**
     * Creates the handler under test.
     *
     * @return \Netgen\BlockManager\Layout\Resolver\RuleHandler\Doctrine\TargetHandler
     */
    protected function getTargetHandler()
    {
        return new Content();
    }
}
