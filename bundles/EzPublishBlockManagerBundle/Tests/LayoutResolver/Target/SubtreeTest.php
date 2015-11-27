<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\LayoutResolver\Target;

use Netgen\Bundle\EzPublishBlockManagerBundle\LayoutResolver\Target\Subtree;

class SubtreeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\LayoutResolver\Target\Subtree::getIdentifier
     */
    public function testGetIdentifier()
    {
        $target = new Subtree();
        self::assertEquals('subtree', $target->getIdentifier());
    }
}
