<?php

namespace Netgen\BlockManager\Ez\Tests\Layout\Resolver\Form\ConditionType\Mapper;

use Netgen\BlockManager\Ez\Form\ContentTypeType;
use Netgen\BlockManager\Ez\Layout\Resolver\Form\ConditionType\Mapper\ContentType;
use Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface;
use PHPUnit\Framework\TestCase;

class ContentTypeTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\Form\ConditionType\MapperInterface
     */
    protected $mapper;

    public function setUp()
    {
        $this->mapper = new ContentType();
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\Form\ConditionType\Mapper\ContentType::getFormType
     */
    public function testGetFormType()
    {
        $this->assertEquals(ContentTypeType::class, $this->mapper->getFormType());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\Form\ConditionType\Mapper\ContentType::mapOptions
     */
    public function testMapOptions()
    {
        $this->assertEquals(
            array(
                'multiple' => true,
            ),
            $this->mapper->mapOptions(
                $this->createMock(ConditionTypeInterface::class)
            )
        );
    }
}
