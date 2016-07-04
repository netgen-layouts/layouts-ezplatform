<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\Layout\Resolver\Form\ConditionType;

use Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\Form\ConditionType\Mapper\SiteAccess as SiteAccessMapper;
use Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\ConditionType\SiteAccess;
use Netgen\BlockManager\API\Values\ConditionCreateStruct;
use Netgen\BlockManager\Layout\Resolver\Form\ConditionType;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class SiteAccessTest extends FormTestCase
{
    /**
     * @var array
     */
    protected $siteAccessList;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface
     */
    protected $conditionType;

    public function setUp()
    {
        parent::setUp();

        $this->conditionType = new SiteAccess(
            $this->siteAccessList
        );
    }

    /**
     * @return \Symfony\Component\Form\FormTypeInterface
     */
    public function getMainType()
    {
        $this->siteAccessList = array('cro', 'eng', 'admin');

        return new ConditionType(
            array(
                'ez_site_access' => new SiteAccessMapper(
                    $this->siteAccessList
                ),
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\ConditionType::buildForm
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\ConditionType\Mapper::getOptions
     * @covers \Netgen\BlockManager\Layout\Resolver\Form\ConditionType\Mapper::handleForm
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\Form\ConditionType\Mapper\SiteAccess::__construct
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\Form\ConditionType\Mapper\SiteAccess::getFormType
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\Form\ConditionType\Mapper\SiteAccess::getOptions
     */
    public function testSubmitValidData()
    {
        $submittedData = array(
            'value' => array('cro', 'eng'),
        );

        $updatedStruct = new ConditionCreateStruct();
        $updatedStruct->value = array('cro', 'eng');

        $form = $this->factory->create(
            ConditionType::class,
            new ConditionCreateStruct(),
            array('conditionType' => $this->conditionType)
        );

        $valueFormConfig = $form->get('value')->getConfig();
        self::assertInstanceOf(ChoiceType::class, $valueFormConfig->getType()->getInnerType());

        $form->submit($submittedData);
        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($updatedStruct, $form->getData());

        self::assertArrayHasKey('value', $form->createView()->children);
    }
}
