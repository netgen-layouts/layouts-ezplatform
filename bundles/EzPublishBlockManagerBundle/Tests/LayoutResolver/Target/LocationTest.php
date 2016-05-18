<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Layout\Resolver\Target;

use Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\Target\Location;

class LocationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\Target\Location::getIdentifier
     */
    public function testGetIdentifier()
    {
        $target = new Location();
        self::assertEquals('location', $target->getIdentifier());
    }
}
