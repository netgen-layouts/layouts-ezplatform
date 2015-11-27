<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\LayoutResolver\Target;

use Netgen\Bundle\EzPublishBlockManagerBundle\LayoutResolver\Target\Content;

class ContentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\LayoutResolver\Target\Content::getIdentifier
     */
    public function testGetIdentifier()
    {
        $target = new Content();
        self::assertEquals('content', $target->getIdentifier());
    }
}
