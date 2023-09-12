<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Tests\Block\BlockDefinition\Integration;

use Netgen\Layouts\Block\BlockDefinition\BlockDefinitionHandlerInterface;
use Netgen\Layouts\Ez\Block\BlockDefinition\Handler\ContentFieldHandler;
use Netgen\Layouts\Ez\ContentProvider\ContentProviderInterface;
use Netgen\Layouts\Tests\Block\BlockDefinition\Integration\BlockTestCase;

abstract class ContentFieldTestBase extends BlockTestCase
{
    public static function parametersDataProvider(): iterable
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

    public static function invalidParametersDataProvider(): iterable
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
