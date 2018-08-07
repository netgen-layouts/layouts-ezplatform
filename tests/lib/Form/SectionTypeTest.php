<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Tests\Form;

use eZ\Publish\API\Repository\SectionService;
use eZ\Publish\API\Repository\Values\Content\Section;
use Netgen\BlockManager\Ez\Form\SectionType;
use Netgen\BlockManager\Tests\TestCase\FormTestCase;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SectionTypeTest extends FormTestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $sectionServiceMock;

    /**
     * @covers \Netgen\BlockManager\Ez\Form\SectionType::__construct
     * @covers \Netgen\BlockManager\Ez\Form\SectionType::getSections
     */
    public function testSubmitValidData(): void
    {
        $this->configureSectionService();

        $submittedData = ['media'];

        $form = $this->factory->create(
            SectionType::class,
            null,
            [
                'multiple' => true,
                'sections' => ['media'],
            ]
        );

        $form->submit($submittedData);

        self::assertTrue($form->isSynchronized());
        self::assertSame($submittedData, $form->getData());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Form\SectionType::__construct
     * @covers \Netgen\BlockManager\Ez\Form\SectionType::getSections
     */
    public function testSubmitValidDataWithAllSectionsAllowed(): void
    {
        $this->configureSectionService();

        $submittedData = ['media'];

        $form = $this->factory->create(
            SectionType::class,
            null,
            [
                'multiple' => true,
            ]
        );

        $form->submit($submittedData);

        self::assertTrue($form->isSynchronized());
        self::assertSame($submittedData, $form->getData());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Form\SectionType::__construct
     * @covers \Netgen\BlockManager\Ez\Form\SectionType::getSections
     */
    public function testSubmitNonAllowedSections(): void
    {
        $this->configureSectionService();

        $submittedData = ['media'];

        $form = $this->factory->create(
            SectionType::class,
            null,
            [
                'multiple' => true,
                'sections' => ['standard'],
            ]
        );

        $form->submit($submittedData);

        self::assertFalse($form->isSynchronized());
        self::assertNull($form->getData());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Form\SectionType::getParent
     */
    public function testGetParent(): void
    {
        self::assertSame(ChoiceType::class, $this->formType->getParent());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Form\SectionType::configureOptions
     * @covers \Netgen\BlockManager\Ez\Form\SectionType::getSections
     */
    public function testConfigureOptions(): void
    {
        $this->configureSectionService();

        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $options = $optionsResolver->resolve(
            [
                'sections' => ['media'],
            ]
        );

        self::assertFalse($options['choice_translation_domain']);
        self::assertSame(['Media' => 'media'], $options['choices']);

        if (Kernel::VERSION_ID < 30100) {
            // @deprecated Remove when support for Symfony 2.8 ends
            self::assertTrue($options['choices_as_values']);
        }
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Form\SectionType::configureOptions
     * @covers \Netgen\BlockManager\Ez\Form\SectionType::getSections
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @expectedExceptionMessage The option "sections" with value array is invalid.
     */
    public function testConfigureOptionsWithInvalidSection(): void
    {
        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            [
                'sections' => [42],
            ]
        );
    }

    protected function getMainType(): FormTypeInterface
    {
        $this->sectionServiceMock = $this->createMock(SectionService::class);

        return new SectionType(
            $this->sectionServiceMock
        );
    }

    private function configureSectionService(): void
    {
        $this->sectionServiceMock
            ->expects(self::at(0))
            ->method('loadSections')
            ->will(
                self::returnValue(
                    [
                        new Section(
                            [
                                'identifier' => 'media',
                                'name' => 'Media',
                            ]
                        ),
                    ]
                )
            );
    }
}
