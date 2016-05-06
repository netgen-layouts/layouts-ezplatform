<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\LayoutResolver\RuleHandler\Doctrine\TargetHandler;

use Netgen\BlockManager\Tests\LayoutResolver\RuleHandler\Doctrine\TargetHandler\TargetHandlerTest;
use Netgen\Bundle\EzPublishBlockManagerBundle\LayoutResolver\RuleHandler\Doctrine\TargetHandler\Location;

class LocationTest extends TargetHandlerTest
{
    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\LayoutResolver\RuleHandler\Doctrine\TargetHandler\Location::handleQuery
     */
    public function testLoadRules()
    {
        $expected = array(
            array(
                'layout_id' => 11,
                'conditions' => array(),
            ),
        );

        self::assertEquals($expected, $this->handler->loadRules($this->getTargetIdentifier(), array(42)));
    }

    /**
     * Returns the target identifier under test.
     *
     * @return \Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\TargetHandler
     */
    protected function getTargetIdentifier()
    {
        return 'location';
    }

    /**
     * Creates the handler under test.
     *
     * @return \Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\TargetHandler
     */
    protected function getTargetHandler()
    {
        return new Location();
    }
}
