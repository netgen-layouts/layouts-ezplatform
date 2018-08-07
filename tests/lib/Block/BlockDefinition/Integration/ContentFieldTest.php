<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Tests\Block\BlockDefinition\Integration;

use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandlerInterface;
use Netgen\BlockManager\Ez\Block\BlockDefinition\Handler\ContentFieldHandler;
use Netgen\BlockManager\Ez\ContentProvider\ContentProviderInterface;
use Netgen\BlockManager\Tests\Block\BlockDefinition\Integration\BlockTest;

abstract class ContentFieldTest extends BlockTest
{
    public function parametersDataProvider(): array
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

    public function invalidParametersDataProvider(): array
    {
        return [
            [
                [
                    'field_identifier' => 42,
                ],
            ],
        ];
    }

    protected function createBlockDefinitionHandler(): BlockDefinitionHandlerInterface
    {
        return new ContentFieldHandler($this->createMock(ContentProviderInterface::class));
    }
}
