<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\LayoutResolver\Target;

use Netgen\Bundle\EzPublishBlockManagerBundle\LayoutResolver\Target\SemanticPathInfoPrefix;

class SemanticPathInfoPrefixTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\LayoutResolver\Target\SemanticPathInfoPrefix::getIdentifier
     */
    public function testGetIdentifier()
    {
        $target = new SemanticPathInfoPrefix();
        self::assertEquals('semantic_path_info_prefix', $target->getIdentifier());
    }
}
