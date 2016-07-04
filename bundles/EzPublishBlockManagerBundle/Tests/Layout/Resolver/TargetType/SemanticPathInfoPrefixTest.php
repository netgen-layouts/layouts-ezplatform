<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\Layout\Resolver\TargetType;

use Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetType\SemanticPathInfoPrefix;

class SemanticPathInfoPrefixTest extends SemanticPathInfoTest
{
    public function setUp()
    {
        parent::setUp();

        $this->targetType = new SemanticPathInfoPrefix();
        $this->targetType->setRequestStack($this->requestStack);
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetType\SemanticPathInfoPrefix::getType
     */
    public function testGetType()
    {
        self::assertEquals('ez_semantic_path_info_prefix', $this->targetType->getType());
    }
}
