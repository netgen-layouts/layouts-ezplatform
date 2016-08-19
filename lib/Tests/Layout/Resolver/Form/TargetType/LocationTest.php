<?php

namespace Netgen\BlockManager\Ez\Tests\Layout\Resolver\Form\TargetType;

use eZ\Publish\API\Repository\LocationService;
use Netgen\Bundle\ContentBrowserBundle\Tests\Stubs\Item;
use Netgen\Bundle\ContentBrowserBundle\Item\ItemRepositoryInterface;
use Netgen\BlockManager\Ez\Layout\Resolver\Form\TargetType\Mapper\Location as LocationMapper;
use Netgen\BlockManager\Ez\Layout\Resolver\TargetType\Location;
use Netgen\BlockManager\API\Values\TargetCreateStruct;
use Netgen\BlockManager\Layout\Resolver\Form\TargetType;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;
use Netgen\Bundle\ContentBrowserBundle\Form\Type\ContentBrowserType;

class LocationTest extends FormTestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\TargetTypeInterface
     */
    protected $targetType;

    public function setUp()
    {
        parent::setUp();

        $this->targetType = new Location(
            $this->createMock(LocationService::class)
        );
    }

    /**
     * @return \Symfony\Component\Form\FormTypeInterface
     */
    public function getMainType()
    {
        return new TargetType(
            array(
                'ezlocation' => new LocationMapper(),
            )
        );
    }

    /**
     * @return \Symfony\Component\Form\FormTypeInterface[]
     */
    public function getTypes()
    {
        $itemRepositoryMock = $this->createMock(ItemRepositoryInterface::class);
        $itemRepositoryMock
            ->expects($this->any())
            ->method('loadItem')
            ->with($this->equalTo(24), $this->equalTo('ezlocation'))
            ->will($this->returnValue(new Item()));

        return array(
            new ContentBrowserType(
                $itemRepositoryMock
            ),
        );
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\TargetType::buildForm
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\TargetType\Mapper::getOptions
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\TargetType\Mapper::handleForm
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\Form\TargetType\Mapper\Location::getFormType
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\Form\TargetType\Mapper\Location::getOptions
     */
    public function testSubmitValidData()
    {
        $submittedData = array(
            'value' => 24,
        );

        $updatedStruct = new TargetCreateStruct();
        $updatedStruct->value = 24;

        $form = $this->factory->create(
            TargetType::class,
            new TargetCreateStruct(),
            array('targetType' => $this->targetType)
        );

        $valueFormConfig = $form->get('value')->getConfig();
        $this->assertInstanceOf(ContentBrowserType::class, $valueFormConfig->getType()->getInnerType());

        $form->submit($submittedData);
        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($updatedStruct, $form->getData());

        $this->assertArrayHasKey('value', $form->createView()->children);
    }
}
