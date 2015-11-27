<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\LayoutResolver\RuleHandler\Doctrine\TargetHandler;

use Netgen\Bundle\EzPublishBlockManagerBundle\LayoutResolver\RuleHandler\Doctrine\TargetHandler\Location;

class LocationTest extends TargetHandlerTest
{
    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\LayoutResolver\RuleHandler\Doctrine\TargetHandler\Location::getTargetIdentifier
     */
    public function testGetTargetIdentifier()
    {
        $targetHandler = $this->getTargetHandler();
        self::assertEquals('location', $targetHandler->getTargetIdentifier());
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\LayoutResolver\RuleHandler\Doctrine\TargetHandler\Location::handleQuery
     */
    public function testLoadLocationRules()
    {
        $handler = $this->createHandler();

        $expected = array(
            1 => array(
                'layout_id' => 1,
                'conditions' => array(),
            ),
        );

        self::assertEquals($expected, $handler->loadRules('location', array(42)));
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
