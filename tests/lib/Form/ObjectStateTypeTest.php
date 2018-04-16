<?php

namespace Netgen\BlockManager\Ez\Tests\Form;

use eZ\Publish\API\Repository\ObjectStateService;
use eZ\Publish\Core\Repository\Values\ObjectState\ObjectState;
use eZ\Publish\Core\Repository\Values\ObjectState\ObjectStateGroup;
use Netgen\BlockManager\Ez\Form\ObjectStateType;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ObjectStateTypeTest extends FormTestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $objectStateServiceMock;

    /**
     * @return \Symfony\Component\Form\FormTypeInterface
     */
    public function getMainType()
    {
        $this->objectStateServiceMock = $this->createMock(ObjectStateService::class);

        return new ObjectStateType(
            $this->objectStateServiceMock
        );
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Form\ObjectStateType::__construct
     * @covers \Netgen\BlockManager\Ez\Form\ObjectStateType::getObjectStates
     */
    public function testSubmitValidData()
    {
        $this->configureObjectStateService();

        $submittedData = array('ez_lock|locked');

        $form = $this->factory->create(
            ObjectStateType::class,
            null,
            array(
                'multiple' => true,
                'states' => array(
                    'ez_lock' => array('locked'),
                    'third' => false,
                ),
            )
        );

        $form->submit($submittedData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($submittedData, $form->getData());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Form\ObjectStateType::getParent
     */
    public function testGetParent()
    {
        $this->assertEquals(ChoiceType::class, $this->formType->getParent());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Form\ObjectStateType::configureOptions
     * @covers \Netgen\BlockManager\Ez\Form\ObjectStateType::getObjectStates
     */
    public function testConfigureOptions()
    {
        $this->configureObjectStateService();

        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $options = $optionsResolver->resolve(
            array(
                'states' => array(
                    'ez_lock' => array('locked'),
                    'third' => false,
                ),
            )
        );

        $this->assertFalse($options['choice_translation_domain']);
        $this->assertEquals(
            array(
                'Lock' => array(
                    'Locked' => 'ez_lock|locked',
                ),
                'Other' => array(
                    'Other' => 'other|other',
                ),
            ),
            $options['choices']
        );

        if (Kernel::VERSION_ID < 30100) {
            // @deprecated Remove when support for Symfony 2.8 ends
            $this->assertTrue($options['choices_as_values']);
        }
    }

    private function configureObjectStateService()
    {
        $objectStateGroup1 = new ObjectStateGroup(array('identifier' => 'ez_lock', 'names' => array('eng-GB' => 'Lock')));
        $objectStateGroup2 = new ObjectStateGroup(array('identifier' => 'other', 'names' => array('eng-GB' => 'Other')));
        $objectStateGroup3 = new ObjectStateGroup(array('identifier' => 'third', 'names' => array('eng-GB' => 'Third')));

        $this->objectStateServiceMock
            ->expects($this->at(0))
            ->method('loadObjectStateGroups')
            ->will($this->returnValue(array($objectStateGroup1, $objectStateGroup2, $objectStateGroup3)));

        $this->objectStateServiceMock
            ->expects($this->at(1))
            ->method('loadObjectStates')
            ->with($this->equalTo($objectStateGroup1))
            ->will(
                $this->returnValue(
                    array(
                        new ObjectState(
                            array(
                                'identifier' => 'locked',
                                'names' => array(
                                    'eng-GB' => 'Locked',
                                ),
                            )
                        ),
                        new ObjectState(
                            array(
                                'identifier' => 'unlocked',
                                'names' => array(
                                    'eng-GB' => 'Unlocked',
                                ),
                            )
                        ),
                    )
                )
            );

        $this->objectStateServiceMock
            ->expects($this->at(2))
            ->method('loadObjectStates')
            ->with($this->equalTo($objectStateGroup2))
            ->will(
                $this->returnValue(
                    array(
                        new ObjectState(
                            array(
                                'identifier' => 'other',
                                'names' => array(
                                    'eng-GB' => 'Other',
                                ),
                            )
                        ),
                    )
                )
            );
    }
}
