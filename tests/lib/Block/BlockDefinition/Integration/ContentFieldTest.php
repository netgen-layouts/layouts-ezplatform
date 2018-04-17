<?php

namespace Netgen\BlockManager\Ez\Tests\Block\BlockDefinition\Integration;

use Netgen\BlockManager\Ez\Block\BlockDefinition\Handler\ContentFieldHandler;
use Netgen\BlockManager\Ez\ContentProvider\ContentProviderInterface;
use Netgen\BlockManager\Tests\Block\BlockDefinition\Integration\BlockTest;

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
        return [
            [
                [],
                [
                    'field_identifier' => null,
                ],
            ],
            [
                [
                    'field_identifier' => null,
                ],
                [
                    'field_identifier' => null,
                ],
            ],
            [
                [
                    'field_identifier' => '',
                ],
                [
                    'field_identifier' => '',
                ],
            ],
            [
                [
                    'field_identifier' => 'title',
                ],
                [
                    'field_identifier' => 'title',
                ],
            ],
            [
                [
                    'unknown' => 'unknown',
                ],
                [],
            ],
        ];
    }

    /**
     * @return array
     */
    public function invalidParametersDataProvider()
    {
        return [
            [
                [
                    'field_identifier' => 42,
                ],
            ],
        ];
    }
}
