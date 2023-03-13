<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Tests\Block\BlockDefinition\Configuration\Provider;

use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Block\BlockDefinition\Configuration\ViewType;
use Netgen\Layouts\Ibexa\Block\BlockDefinition\Configuration\Provider\IbexaConfigProvider;
use Netgen\Layouts\Parameters\Parameter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class IbexaConfigProviderTest extends TestCase
{
    private MockObject&ConfigResolverInterface $configResolverMock;

    private IbexaConfigProvider $configProvider;

    protected function setUp(): void
    {
        $this->configResolverMock = $this->createMock(ConfigResolverInterface::class);

        $this->configProvider = new IbexaConfigProvider(
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
     * @covers \Netgen\Layouts\Ibexa\Block\BlockDefinition\Configuration\Provider\IbexaConfigProvider::__construct
     * @covers \Netgen\Layouts\Ibexa\Block\BlockDefinition\Configuration\Provider\IbexaConfigProvider::buildViewTypes
     * @covers \Netgen\Layouts\Ibexa\Block\BlockDefinition\Configuration\Provider\IbexaConfigProvider::humanize
     * @covers \Netgen\Layouts\Ibexa\Block\BlockDefinition\Configuration\Provider\IbexaConfigProvider::provideViewTypes
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
            ->expects(self::any())
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
     * @covers \Netgen\Layouts\Ibexa\Block\BlockDefinition\Configuration\Provider\IbexaConfigProvider::provideViewTypes
     */
    public function testProvideViewTypesWithoutBlock(): void
    {
        self::assertSame([], $this->configProvider->provideViewTypes());
    }
}
