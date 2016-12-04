<?php

namespace Netgen\BlockManager\Ez\Tests\Layout\Resolver\Form\TargetType\Mapper;

use Netgen\BlockManager\Ez\Layout\Resolver\Form\TargetType\Mapper\Subtree;
use Netgen\BlockManager\Layout\Resolver\TargetTypeInterface;
use Netgen\ContentBrowser\Form\Type\ContentBrowserType;
use PHPUnit\Framework\TestCase;

class SubtreeTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\Form\TargetType\MapperInterface
     */
    protected $mapper;

    public function setUp()
    {
        $this->mapper = new Subtree();
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\Form\TargetType\Mapper\Subtree::getFormType
     */
    public function testGetFormType()
    {
        $this->assertEquals(ContentBrowserType::class, $this->mapper->getFormType());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\Form\TargetType\Mapper\Subtree::mapOptions
     */
    public function testMapOptions()
    {
        $this->assertEquals(
            array(
                'item_type' => 'ezlocation',
            ),
            $this->mapper->mapOptions(
                $this->createMock(TargetTypeInterface::class)
            )
        );
    }
}
