<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Layout\Resolver\Target;

use Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\Target\Content;

class ContentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\Target\Content::getIdentifier
     */
    public function testGetIdentifier()
    {
        $target = new Content();
        self::assertEquals('content', $target->getIdentifier());
    }
}
