<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\LayoutResolver\Target;

use Netgen\Bundle\EzPublishBlockManagerBundle\LayoutResolver\Target\Location;

class LocationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\LayoutResolver\Target\Location::getIdentifier
     */
    public function testGetIdentifier()
    {
        $target = new Location();
        self::assertEquals('location', $target->getIdentifier());
    }
}
