<?php

namespace Netgen\BlockManager\Ez\Tests\Layout\Resolver\TargetType;

use Netgen\BlockManager\Ez\Layout\Resolver\TargetType\SemanticPathInfoPrefix;

class SemanticPathInfoPrefixTest extends SemanticPathInfoTest
{
    public function setUp()
    {
        parent::setUp();

        $this->targetType = new SemanticPathInfoPrefix();
        $this->targetType->setRequestStack($this->requestStack);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\TargetType\SemanticPathInfoPrefix::getType
     */
    public function testGetType()
    {
        $this->assertEquals('ez_semantic_path_info_prefix', $this->targetType->getType());
    }
}
