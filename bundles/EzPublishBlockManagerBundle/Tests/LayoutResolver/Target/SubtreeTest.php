<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Layout\Resolver\Target;

use Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\Target\Subtree;

class SubtreeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\Target\Subtree::getIdentifier
     */
    public function testGetIdentifier()
    {
        $target = new Subtree();
        self::assertEquals('subtree', $target->getIdentifier());
    }
}
