<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Tests\Parameters\Form\Mapper;

use Ibexa\Contracts\Core\Repository\Repository;
use Netgen\ContentBrowser\Form\Type\ContentBrowserType;
use Netgen\Layouts\Ibexa\Parameters\Form\Mapper\ContentMapper;
use Netgen\Layouts\Ibexa\Parameters\ParameterType\ContentType as ParameterType;
use Netgen\Layouts\Parameters\ParameterDefinition;
use Netgen\Layouts\Parameters\ValueObjectProviderInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ContentMapper::class)]
final class ContentMapperTest extends TestCase
{
    private ContentMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new ContentMapper();
    }

    public function testGetFormType(): void
    {
        self::assertSame(ContentBrowserType::class, $this->mapper->getFormType());
    }

    public function testMapOptions(): void
    {
        $mappedOptions = $this->mapper->mapOptions(
            ParameterDefinition::fromArray(
                [
                    'type' => new ParameterType(
                        $this->createMock(Repository::class),
                        $this->createMock(ValueObjectProviderInterface::class),
                    ),
                    'options' => [
                        'allowed_types' => ['user', 'image'],
                    ],
                ],
            ),
        );

        self::assertSame(
            [
                'item_type' => 'ibexa_content',
                'block_prefix' => 'ngcb_ibexa_content',
                'custom_params' => [
                    'allowed_content_types' => ['user', 'image'],
                ],
            ],
            $mappedOptions,
        );
    }

    public function testMapOptionsEmptyAllowedTypes(): void
    {
        $mappedOptions = $this->mapper->mapOptions(
            ParameterDefinition::fromArray(
                [
                    'type' => new ParameterType(
                        $this->createMock(Repository::class),
                        $this->createMock(ValueObjectProviderInterface::class),
                    ),
                    'options' => [
                        'allowed_types' => [],
                    ],
                ],
            ),
        );

        self::assertSame(
            [
                'item_type' => 'ibexa_content',
                'block_prefix' => 'ngcb_ibexa_content',
            ],
            $mappedOptions,
        );
    }
}
