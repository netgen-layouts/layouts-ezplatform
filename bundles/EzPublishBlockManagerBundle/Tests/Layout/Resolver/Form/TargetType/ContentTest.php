<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\Layout\Resolver\Form\TargetType;

use eZ\Publish\API\Repository\ContentService;
use Netgen\Bundle\ContentBrowserBundle\Tests\Stubs\Item;
use Netgen\Bundle\ContentBrowserBundle\Item\ItemRepositoryInterface;
use Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\Form\TargetType\Mapper\Content as ContentMapper;
use Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetType\Content;
use Netgen\BlockManager\API\Values\TargetCreateStruct;
use Netgen\BlockManager\Layout\Resolver\Form\TargetType;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;
use Netgen\Bundle\ContentBrowserBundle\Form\Type\ContentBrowserType;

class ContentTest extends FormTestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\TargetTypeInterface
     */
    protected $targetType;

    public function setUp()
    {
        parent::setUp();

        $this->targetType = new Content(
            $this->createMock(ContentService::class)
        );
    }

    /**
     * @return \Symfony\Component\Form\FormTypeInterface
     */
    public function getMainType()
    {
        return new TargetType(
            array(
                'ezcontent' => new ContentMapper(),
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
            ->with($this->equalTo(24), $this->equalTo('ezcontent'))
            ->will($this->returnValue(new Item()));

        return array(
            new ContentBrowserType(
                $itemRepositoryMock
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\TargetType::buildForm
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\TargetType\Mapper::getOptions
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\TargetType\Mapper::handleForm
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\Form\TargetType\Mapper\Content::getFormType
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\Form\TargetType\Mapper\Content::getOptions
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
        self::assertInstanceOf(ContentBrowserType::class, $valueFormConfig->getType()->getInnerType());

        $form->submit($submittedData);
        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($updatedStruct, $form->getData());

        self::assertArrayHasKey('value', $form->createView()->children);
    }
}
