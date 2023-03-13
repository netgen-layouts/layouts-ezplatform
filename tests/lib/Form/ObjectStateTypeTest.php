<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Tests\Form;

use Ibexa\Contracts\Core\Repository\ObjectStateService;
use Ibexa\Core\Repository\Values\ObjectState\ObjectState;
use Ibexa\Core\Repository\Values\ObjectState\ObjectStateGroup;
use Netgen\Layouts\Ibexa\Form\ObjectStateType;
use Netgen\Layouts\Tests\TestCase\FormTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ObjectStateTypeTest extends FormTestCase
{
    private MockObject&ObjectStateService $objectStateServiceMock;

    /**
     * @covers \Netgen\Layouts\Ibexa\Form\ObjectStateType::__construct
     * @covers \Netgen\Layouts\Ibexa\Form\ObjectStateType::getObjectStates
     */
    public function testSubmitValidData(): void
    {
        $this->configureObjectStateService();

        $submittedData = ['ibexa_lock|locked'];

        $form = $this->factory->create(
            ObjectStateType::class,
            null,
            [
                'multiple' => true,
                'states' => [
                    'ibexa_lock' => ['locked'],
                    'third' => false,
                ],
            ],
        );

        $form->submit($submittedData);

        self::assertTrue($form->isSynchronized());
        self::assertSame($submittedData, $form->getData());
    }

    /**
     * @covers \Netgen\Layouts\Ibexa\Form\ObjectStateType::getParent
     */
    public function testGetParent(): void
    {
        self::assertSame(ChoiceType::class, $this->formType->getParent());
    }

    /**
     * @covers \Netgen\Layouts\Ibexa\Form\ObjectStateType::configureOptions
     * @covers \Netgen\Layouts\Ibexa\Form\ObjectStateType::getObjectStates
     */
    public function testConfigureOptions(): void
    {
        $this->configureObjectStateService();

        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $options = $optionsResolver->resolve(
            [
                'states' => [
                    'ibexa_lock' => ['locked'],
                    'third' => false,
                ],
            ],
        );

        self::assertFalse($options['choice_translation_domain']);
        self::assertSame(
            [
                'Lock' => [
                    'Locked' => 'ibexa_lock|locked',
                ],
                'Other' => [
                    'Other' => 'other|other',
                ],
            ],
            $options['choices'],
        );
    }

    protected function getMainType(): FormTypeInterface
    {
        $this->objectStateServiceMock = $this->createMock(ObjectStateService::class);

        return new ObjectStateType(
            $this->objectStateServiceMock,
        );
    }

    private function configureObjectStateService(): void
    {
        $objectStateGroup1 = new ObjectStateGroup(['identifier' => 'ibexa_lock', 'names' => ['eng-GB' => 'Lock'], 'mainLanguageCode' => 'eng-GB']);
        $objectStateGroup2 = new ObjectStateGroup(['identifier' => 'other', 'names' => ['eng-GB' => 'Other'], 'mainLanguageCode' => 'eng-GB']);
        $objectStateGroup3 = new ObjectStateGroup(['identifier' => 'third', 'names' => ['eng-GB' => 'Third'], 'mainLanguageCode' => 'eng-GB']);

        $this->objectStateServiceMock
            ->method('loadObjectStateGroups')
            ->willReturn([$objectStateGroup1, $objectStateGroup2, $objectStateGroup3]);

        $this->objectStateServiceMock
            ->method('loadObjectStates')
            ->willReturnMap(
                [
                    [
                        $objectStateGroup1,
                        [],
                        [
                            new ObjectState(
                                [
                                    'identifier' => 'locked',
                                    'names' => ['eng-GB' => 'Locked'],
                                    'mainLanguageCode' => 'eng-GB',
                                ],
                            ),
                            new ObjectState(
                                [
                                    'identifier' => 'unlocked',
                                    'names' => ['eng-GB' => 'Unlocked'],
                                    'mainLanguageCode' => 'eng-GB',
                                ],
                            ),
                        ],
                    ],
                    [
                        $objectStateGroup2,
                        [],
                        [
                            new ObjectState(
                                [
                                    'identifier' => 'other',
                                    'names' => ['eng-GB' => 'Other'],
                                    'mainLanguageCode' => 'eng-GB',
                                ],
                            ),
                        ],
                    ],
                ],
            );
    }
}
