<?php

namespace Netgen\BlockManager\Ez\Tests\Parameters\Form\Mapper;

use eZ\Publish\API\Repository\Repository;
use Netgen\BlockManager\Ez\Parameters\Form\Mapper\ContentMapper;
use Netgen\BlockManager\Ez\Parameters\ParameterType\ContentType as ParameterType;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\ContentBrowser\Form\Type\ContentBrowserType;
use PHPUnit\Framework\TestCase;

final class ContentMapperTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Ez\Parameters\Form\Mapper\ContentMapper
     */
    private $mapper;

    public function setUp()
    {
        $this->mapper = new ContentMapper();
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Parameters\Form\Mapper\ContentMapper::getFormType
     */
    public function testGetFormType()
    {
        $this->assertEquals(ContentBrowserType::class, $this->mapper->getFormType());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Parameters\Form\Mapper\ContentMapper::mapOptions
     */
    public function testMapOptions()
    {
        $this->assertEquals(
            array(
                'item_type' => 'ezcontent',
            ),
            $this->mapper->mapOptions(new ParameterDefinition(array('type' => new ParameterType($this->createMock(Repository::class)))))
        );
    }
}
