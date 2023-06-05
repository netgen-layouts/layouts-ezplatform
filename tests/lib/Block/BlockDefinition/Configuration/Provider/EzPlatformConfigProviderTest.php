<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Tests\Block\BlockDefinition\Configuration\Provider;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Block\BlockDefinition\Configuration\ViewType;
use Netgen\Layouts\Ez\Block\BlockDefinition\Configuration\Provider\EzPlatformConfigProvider;
use Netgen\Layouts\Parameters\Parameter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class EzPlatformConfigProviderTest extends TestCase
{
    private MockObject $configResolverMock;

    private EzPlatformConfigProvider $configProvider;

    protected function setUp(): void
    {
        $this->configResolverMock = $this->createMock(ConfigResolverInterface::class);

        $this->configProvider = new EzPlatformConfigProvider(
            $this->configResolverMock,
            [
                'cro' => ['group1', 'group2'],
                'admin' => ['admin_group'],
            ],
            'content_type_identifier',
            'content_view',
        );
    }

    /**
     * @covers \Netgen\Layouts\Ez\Block\BlockDefinition\Configuration\Provider\EzPlatformConfigProvider::__construct
     * @covers \Netgen\Layouts\Ez\Block\BlockDefinition\Configuration\Provider\EzPlatformConfigProvider::buildViewTypes
     * @covers \Netgen\Layouts\Ez\Block\BlockDefinition\Configuration\Provider\EzPlatformConfigProvider::humanize
     * @covers \Netgen\Layouts\Ez\Block\BlockDefinition\Configuration\Provider\EzPlatformConfigProvider::provideViewTypes
     */
    public function testProvideViewTypes(): void
    {
        $blockUuid = Uuid::uuid4();
        $block = Block::fromArray(
            [
                'id' => $blockUuid,
                'parameters' => [
                    'content_type_identifier' => Parameter::fromArray(
                        [
                            'value' => 'foo',
                        ],
                    ),
                ],
            ],
        );

        $this->configResolverMock
            ->method('getParameter')
            ->with(self::identicalTo('content_view'), self::isNull(), self::identicalTo('cro'))
            ->willReturn(
                [
                    'view_style_1' => [
                        'foo' => [
                            'template' => '@templates/foo.html.twig',
                            'match' => [
                                'Identifier\ContentType' => [
                                    'foo',
                                ],
                            ],
                            'params' => [
                                'valid_parameters' => ['param1'],
                            ],
                        ],
                        'foo2' => [
                            'template' => '@templates/foo2.html.twig',
                            'match' => [
                                'Identifier\ContentType' => [
                                    'foo',
                                ],
                            ],
                            'params' => [],
                        ],
                        'foo3' => [
                            'template' => '@templates/foo3.html.twig',
                            'match' => [
                                'Identifier\ContentType' => [
                                    'foo',
                                ],
                            ],
                            'params' => [
                                'valid_parameters' => ['param2'],
                            ],
                        ],
                    ],
                    'view_style_2' => [
                        'foo' => [
                            'template' => '@templates/foo.html.twig',
                            'match' => [
                                'Identifier\ContentType' => [
                                    'foo',
                                ],
                            ],
                            'params' => [],
                        ],
                    ],
                    'view_style_3' => [
                        'foo' => [
                            'template' => '@templates/foo.html.twig',
                            'match' => [
                                'Identifier\Section' => [
                                    'foo',
                                ],
                            ],
                            'params' => [
                                'valid_parameters' => ['param1'],
                            ],
                        ],
                    ],
                    'full' => [
                        'foo' => [
                            'template' => '@templates/foo.html.twig',
                            'match' => [
                                'Identifier\ContentType' => [
                                    'foo',
                                ],
                            ],
                            'params' => [],
                        ],
                    ],
                ],
            );

        $viewTypes = $this->configProvider->provideViewTypes($block);

        self::assertCount(2, $viewTypes);

        self::assertArrayHasKey('view_style_1', $viewTypes);
        self::assertArrayHasKey('view_style_2', $viewTypes);

        self::assertInstanceOf(ViewType::class, $viewTypes['view_style_1']);
        self::assertSame('view_style_1', $viewTypes['view_style_1']->getIdentifier());
        self::assertSame('View Style 1', $viewTypes['view_style_1']->getName());
        self::assertSame(['param1', 'param2'], $viewTypes['view_style_1']->getValidParameters());

        self::assertInstanceOf(ViewType::class, $viewTypes['view_style_2']);
        self::assertSame('view_style_2', $viewTypes['view_style_2']->getIdentifier());
        self::assertSame('View Style 2', $viewTypes['view_style_2']->getName());
        self::assertNull($viewTypes['view_style_2']->getValidParameters());
    }

    /**
     * @covers \Netgen\Layouts\Ez\Block\BlockDefinition\Configuration\Provider\EzPlatformConfigProvider::provideViewTypes
     */
    public function testProvideViewTypesWithoutBlock(): void
    {
        self::assertSame([], $this->configProvider->provideViewTypes());
    }
}
