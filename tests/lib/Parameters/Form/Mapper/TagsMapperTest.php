<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Tests\Parameters\Form\Mapper;

use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\ContentBrowser\Form\Type\ContentBrowserMultipleType;
use Netgen\Layouts\Ez\Parameters\Form\Mapper\TagsMapper;
use Netgen\Layouts\Ez\Parameters\ParameterType\TagsType as ParameterType;
use Netgen\TagsBundle\API\Repository\TagsService;
use PHPUnit\Framework\TestCase;

final class TagsMapperTest extends TestCase
{
    /**
     * @var \Netgen\Layouts\Ez\Parameters\Form\Mapper\TagsMapper
     */
    private $mapper;

    public function setUp(): void
    {
        $this->mapper = new TagsMapper();
    }

    /**
     * @covers \Netgen\Layouts\Ez\Parameters\Form\Mapper\TagsMapper::getFormType
     */
    public function testGetFormType(): void
    {
        self::assertSame(ContentBrowserMultipleType::class, $this->mapper->getFormType());
    }

    /**
     * @covers \Netgen\Layouts\Ez\Parameters\Form\Mapper\TagsMapper::mapOptions
     */
    public function testMapOptions(): void
    {
        self::assertSame(
            [
                'item_type' => 'eztags',
                'min' => 3,
                'max' => 6,
            ],
            $this->mapper->mapOptions(
                ParameterDefinition::fromArray(
                    [
                        'type' => new ParameterType($this->createMock(TagsService::class)),
                        'options' => [
                            'min' => 3,
                            'max' => 6,
                        ],
                    ]
                )
            )
        );
    }
}
