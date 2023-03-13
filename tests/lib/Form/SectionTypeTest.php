<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Tests\Form;

use Ibexa\Contracts\Core\Repository\SectionService;
use Ibexa\Contracts\Core\Repository\Values\Content\Section;
use Netgen\Layouts\Ibexa\Form\SectionType;
use Netgen\Layouts\Tests\TestCase\FormTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;

#[CoversClass(SectionType::class)]
final class SectionTypeTest extends FormTestCase
{
    private MockObject&SectionService $sectionServiceMock;

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

        self::assertTrue($form->isSynchronized());
        self::assertSame([], $form->getData());
    }

    public function testGetParent(): void
    {
        self::assertSame(ChoiceType::class, $this->formType->getParent());
    }

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
