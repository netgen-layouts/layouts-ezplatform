<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\Layout\Resolver\Form\TargetType;

use Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\Form\TargetType\Mapper\SemanticPathInfoPrefix as SemanticPathInfoPrefixMapper;
use Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetType\SemanticPathInfoPrefix;
use Netgen\BlockManager\API\Values\TargetCreateStruct;
use Netgen\BlockManager\Layout\Resolver\Form\TargetType;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class PathInfoPrefixTest extends FormTestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\TargetTypeInterface
     */
    protected $targetType;

    public function setUp()
    {
        parent::setUp();

        $this->targetType = new SemanticPathInfoPrefix();
    }

    /**
     * @return \Symfony\Component\Form\FormTypeInterface
     */
    public function getMainType()
    {
        return new TargetType(
            array(
                'ez_semantic_path_info_prefix' => new SemanticPathInfoPrefixMapper(),
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\TargetType::buildForm
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\TargetType\Mapper::getOptions
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\TargetType\Mapper::handleForm
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\Form\TargetType\Mapper\SemanticPathInfoPrefix::getFormType
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\Form\TargetType\Mapper\SemanticPathInfoPrefix::getOptions
     */
    public function testSubmitValidData()
    {
        $submittedData = array(
            'value' => '/some/route',
        );

        $updatedStruct = new TargetCreateStruct();
        $updatedStruct->value = '/some/route';

        $form = $this->factory->create(
            TargetType::class,
            new TargetCreateStruct(),
            array('targetType' => $this->targetType)
        );

        $valueFormConfig = $form->get('value')->getConfig();
        self::assertInstanceOf(TextType::class, $valueFormConfig->getType()->getInnerType());

        $form->submit($submittedData);
        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($updatedStruct, $form->getData());

        self::assertArrayHasKey('value', $form->createView()->children);
    }
}
