<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Tests\Parameters\Form\Mapper;

use eZ\Publish\API\Repository\Repository;
use Netgen\BlockManager\Ez\Parameters\Form\Mapper\LocationMapper;
use Netgen\BlockManager\Ez\Parameters\ParameterType\LocationType as ParameterType;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\ContentBrowser\Form\Type\ContentBrowserType;
use PHPUnit\Framework\TestCase;

final class LocationMapperTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Ez\Parameters\Form\Mapper\LocationMapper
     */
    private $mapper;

    public function setUp(): void
    {
        $this->mapper = new LocationMapper();
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Parameters\Form\Mapper\LocationMapper::getFormType
     */
    public function testGetFormType(): void
    {
        $this->assertSame(ContentBrowserType::class, $this->mapper->getFormType());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Parameters\Form\Mapper\LocationMapper::mapOptions
     */
    public function testMapOptions(): void
    {
        $this->assertSame(
            [
                'item_type' => 'ezlocation',
            ],
            $this->mapper->mapOptions(new ParameterDefinition(['type' => new ParameterType($this->createMock(Repository::class))]))
        );
    }
}
