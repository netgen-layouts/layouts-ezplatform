<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\LayoutResolver\Target;

use Netgen\Bundle\EzPublishBlockManagerBundle\LayoutResolver\Target\Children;

class ChildrenTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\LayoutResolver\Target\Children::getIdentifier
     */
    public function testGetIdentifier()
    {
        $target = new Children();
        self::assertEquals('children', $target->getIdentifier());
    }
}
