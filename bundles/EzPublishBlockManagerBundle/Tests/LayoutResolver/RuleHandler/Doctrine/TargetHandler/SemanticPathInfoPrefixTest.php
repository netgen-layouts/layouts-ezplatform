<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\LayoutResolver\RuleHandler\Doctrine\TargetHandler;

use Netgen\Bundle\EzPublishBlockManagerBundle\LayoutResolver\RuleHandler\Doctrine\TargetHandler\SemanticPathInfoPrefix;

class SemanticPathInfoPrefixTest extends TargetHandlerTest
{
    /**
     * @covers \Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\TargetHandler\PathInfoPrefix::handleQuery
     */
    public function testLoadSemanticPathInfoPrefixRules()
    {
        $handler = $this->createHandler('semantic_path_info_prefix', $this->getTargetHandler());

        $expected = array(
            array(
                'layout_id' => 11,
                'conditions' => array(),
            ),
        );

        self::assertEquals($expected, $handler->loadRules('semantic_path_info_prefix', array('/the/answer')));
    }

    /**
     * Creates the handler under test.
     *
     * @return \Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\TargetHandler
     */
    protected function getTargetHandler()
    {
        return new SemanticPathInfoPrefix();
    }
}
