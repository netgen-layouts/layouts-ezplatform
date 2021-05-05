<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Tests\Form;

use eZ\Publish\API\Repository\SectionService;
use eZ\Publish\API\Repository\Values\Content\Section;
use Netgen\Layouts\Ez\Form\SectionType;
use Netgen\Layouts\Tests\TestCase\FormTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SectionTypeTest extends FormTestCase
{
    private MockObject $sectionServiceMock;

    /**
     * @covers \Netgen\Layouts\Ez\Form\SectionType::__construct
     * @covers \Netgen\Layouts\Ez\Form\SectionType::getSections
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
            ],
        );

        $form->submit($submittedData);

        self::assertTrue($form->isSynchronized());
        self::assertSame($submittedData, $form->getData());
    }

    /**
     * @covers \Netgen\Layouts\Ez\Form\SectionType::__construct
     * @covers \Netgen\Layouts\Ez\Form\SectionType::getSections
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
            ],
        );

        $form->submit($submittedData);

        self::assertTrue($form->isSynchronized());
        self::assertSame($submittedData, $form->getData());
    }

    /**
     * @covers \Netgen\Layouts\Ez\Form\SectionType::__construct
     * @covers \Netgen\Layouts\Ez\Form\SectionType::getSections
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
            ],
        );

        $form->submit($submittedData);

        if (Kernel::VERSION_ID >= 50204) {
            self::assertTrue($form->isSynchronized());
            self::assertSame([], $form->getData());

            return;
        }

        // @deprecated Symfony 3.4 behaviour

        self::assertFalse($form->isSynchronized());
        self::assertNull($form->getData());
    }

    /**
     * @covers \Netgen\Layouts\Ez\Form\SectionType::getParent
     */
    public function testGetParent(): void
    {
        self::assertSame(ChoiceType::class, $this->formType->getParent());
    }

    /**
     * @covers \Netgen\Layouts\Ez\Form\SectionType::configureOptions
     * @covers \Netgen\Layouts\Ez\Form\SectionType::getSections
     */
    public function testConfigureOptions(): void
    {
        $this->configureSectionService();

        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $options = $optionsResolver->resolve(
            [
                'sections' => ['media'],
            ],
        );

        self::assertFalse($options['choice_translation_domain']);
        self::assertSame(['Media' => 'media'], $options['choices']);
    }

    /**
     * @covers \Netgen\Layouts\Ez\Form\SectionType::configureOptions
     * @covers \Netgen\Layouts\Ez\Form\SectionType::getSections
     */
    public function testConfigureOptionsWithInvalidSection(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessageMatches('/^The option "sections" with value array is expected to be of type "string\[\]", but one of the elements is of type "int(eger)?".$/');

        $optionsResolver = new OptionsResolver();

        $this->formType->configureOptions($optionsResolver);

        $optionsResolver->resolve(
            [
                'sections' => [42],
            ],
        );
    }

    protected function getMainType(): FormTypeInterface
    {
        $this->sectionServiceMock = $this->createMock(SectionService::class);

        return new SectionType(
            $this->sectionServiceMock,
        );
    }

    private function configureSectionService(): void
    {
        $this->sectionServiceMock
            ->method('loadSections')
            ->willReturn(
                [
                    new Section(
                        [
                            'identifier' => 'media',
                            'name' => 'Media',
                        ],
                    ),
                ],
            );
    }
}
