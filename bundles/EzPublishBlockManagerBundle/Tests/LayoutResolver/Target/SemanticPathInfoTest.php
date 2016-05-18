<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Layout\Resolver\Target;

use Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\Target\SemanticPathInfo;

class SemanticPathInfoTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\Target\SemanticPathInfo::getIdentifier
     */
    public function testGetIdentifier()
    {
        $target = new SemanticPathInfo();
        self::assertEquals('semantic_path_info', $target->getIdentifier());
    }
}
