<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\LayoutResolver\RuleHandler\Doctrine\TargetHandler;

use Netgen\Bundle\EzPublishBlockManagerBundle\LayoutResolver\RuleHandler\Doctrine\TargetHandler\SemanticPathInfo;

class SemanticPathInfoTest extends TargetHandlerTest
{
    /**
     * @covers \Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\TargetHandler\PathInfo::handleQuery
     */
    public function testLoadSemanticPathInfoRules()
    {
        $handler = $this->createHandler('semantic_path_info', $this->getTargetHandler());

        $expected = array(
            array(
                'layout_id' => 10,
                'conditions' => array(),
            ),
        );

        self::assertEquals($expected, $handler->loadRules('semantic_path_info', array('/the/answer')));
    }

    /**
     * Creates the handler under test.
     *
     * @return \Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\TargetHandler
     */
    protected function getTargetHandler()
    {
        return new SemanticPathInfo();
    }
}
