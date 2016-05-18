<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Layout\Resolver\Target;

use Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\Target\Children;

class ChildrenTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\Target\Children::getIdentifier
     */
    public function testGetIdentifier()
    {
        $target = new Children();
        self::assertEquals('children', $target->getIdentifier());
    }
}
