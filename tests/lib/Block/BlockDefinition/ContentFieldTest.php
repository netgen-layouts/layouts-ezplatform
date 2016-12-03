<?php

namespace Netgen\BlockManager\Ez\Tests\Block\BlockDefinition;

use Netgen\BlockManager\Ez\Block\BlockDefinition\Handler\ContentFieldHandler;
use Netgen\BlockManager\Ez\ContentProvider\ContentProviderInterface;
use Netgen\BlockManager\Tests\Block\BlockDefinition\BlockTest;

abstract class ContentFieldTest extends BlockTest
{
    /**
     * @return \Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandlerInterface
     */
    public function createBlockDefinitionHandler()
    {
        return new ContentFieldHandler($this->createMock(ContentProviderInterface::class));
    }

    /**
     * @return array
     */
    public function parametersDataProvider()
    {
        return array(
            array(
                array(),
                array(
                    'field_identifier' => null,
                ),
            ),
            array(
                array(
                    'field_identifier' => null,
                ),
                array(
                    'field_identifier' => null,
                ),
            ),
            array(
                array(
                    'field_identifier' => '',
                ),
                array(
                    'field_identifier' => '',
                ),
            ),
            array(
                array(
                    'field_identifier' => 'title',
                ),
                array(
                    'field_identifier' => 'title',
                ),
            ),
            array(
                array(
                    'unknown' => 'unknown',
                ),
                array(),
            ),
        );
    }

    /**
     * @return array
     */
    public function invalidParametersDataProvider()
    {
        return array(
            array(
                array(
                    'field_identifier' => 42,
                ),
            ),
        );
    }
}
