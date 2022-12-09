<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Form;

use Ibexa\Contracts\Core\Repository\SectionService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function count;
use function in_array;

final class SectionType extends AbstractType
{
    private SectionService $sectionService;

    public function __construct(SectionService $sectionService)
    {
        $this->sectionService = $sectionService;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('translation_domain', 'nglayouts_forms');

        $resolver->setDefault('sections', []);
        $resolver->setRequired(['sections']);
        $resolver->setAllowedTypes('sections', 'string[]');

        $resolver->setDefault(
            'choices',
            fn (Options $options): array => $this->getSections($options),
        );

        $resolver->setDefault('choice_translation_domain', false);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    /**
     * Returns the allowed sections from Ibexa CMS.
     *
     * @return array<string, string>
     */
    private function getSections(Options $options): array
    {
        $allSections = [];

        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Section[] $sections */
        $sections = $this->sectionService->loadSections();
        $configuredSections = $options['sections'];

        foreach ($sections as $section) {
            if (count($configuredSections) > 0 && !in_array($section->identifier, $configuredSections, true)) {
                continue;
            }

            $allSections[$section->name] = $section->identifier;
        }

        return $allSections;
    }
}
