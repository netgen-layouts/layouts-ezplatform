<?php

namespace Netgen\BlockManager\Ez\Tests\Layout\Resolver\Form\TargetType;

use Netgen\BlockManager\Ez\Layout\Resolver\Form\TargetType\Mapper\SemanticPathInfo as SemanticPathInfoMapper;
use Netgen\BlockManager\Ez\Layout\Resolver\TargetType\SemanticPathInfo;
use Netgen\BlockManager\API\Values\TargetCreateStruct;
use Netgen\BlockManager\Layout\Resolver\Form\TargetType;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class SemanticPathInfoTest extends FormTestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\TargetTypeInterface
     */
    protected $targetType;

    public function setUp()
    {
        parent::setUp();

        $this->targetType = new SemanticPathInfo();
    }

    /**
     * @return \Symfony\Component\Form\FormTypeInterface
     */
    public function getMainType()
    {
        return new TargetType(
            array(
                'ez_semantic_path_info' => new SemanticPathInfoMapper(),
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\TargetType::buildForm
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\TargetType\Mapper::getOptions
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\TargetType\Mapper::handleForm
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\Form\TargetType\Mapper\SemanticPathInfo::getFormType
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\Form\TargetType\Mapper\SemanticPathInfo::getOptions
     */
    public function testSubmitValidData()
    {
        $submittedData = array(
            'value' => '/some/path',
        );

        $updatedStruct = new TargetCreateStruct();
        $updatedStruct->value = '/some/path';

        $form = $this->factory->create(
            TargetType::class,
            new TargetCreateStruct(),
            array('targetType' => $this->targetType)
        );

        $valueFormConfig = $form->get('value')->getConfig();
        $this->assertInstanceOf(TextType::class, $valueFormConfig->getType()->getInnerType());

        $form->submit($submittedData);
        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($updatedStruct, $form->getData());

        $this->assertArrayHasKey('value', $form->createView()->children);
    }
}
