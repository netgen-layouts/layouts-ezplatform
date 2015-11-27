<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\LayoutResolver\Target;

use Netgen\Bundle\EzPublishBlockManagerBundle\LayoutResolver\Target\SemanticPathInfo;

class SemanticPathInfoTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\LayoutResolver\Target\SemanticPathInfo::getIdentifier
     */
    public function testGetIdentifier()
    {
        $target = new SemanticPathInfo();
        self::assertEquals('semantic_path_info', $target->getIdentifier());
    }
}
