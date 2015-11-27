<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\LayoutResolver\RuleHandler\Doctrine\TargetHandler;

use Netgen\Bundle\EzPublishBlockManagerBundle\LayoutResolver\RuleHandler\Doctrine\TargetHandler\Content;

class ContentTest extends TargetHandlerTest
{
    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\LayoutResolver\RuleHandler\Doctrine\TargetHandler\Content::handleQuery
     */
    public function testLoadContentRules()
    {
        $handler = $this->createHandler('content', $this->getTargetHandler());

        $expected = array(
            array(
                'layout_id' => 4,
                'conditions' => array(),
            ),
        );

        self::assertEquals($expected, $handler->loadRules('content', array(48)));
    }

    /**
     * Creates the handler under test.
     *
     * @return \Netgen\BlockManager\LayoutResolver\RuleHandler\Doctrine\TargetHandler
     */
    protected function getTargetHandler()
    {
        return new Content();
    }
}
