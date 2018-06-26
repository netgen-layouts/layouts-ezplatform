<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Form;

use eZ\Publish\API\Repository\SectionService;
use Netgen\BlockManager\Form\AbstractType;
use Netgen\BlockManager\Form\ChoicesAsValuesTrait;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SectionType extends AbstractType
{
    use ChoicesAsValuesTrait;

    /**
     * @var \eZ\Publish\API\Repository\SectionService
     */
    private $sectionService;

    public function __construct(SectionService $sectionService)
    {
        $this->sectionService = $sectionService;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('sections', []);
        $resolver->setRequired(['sections']);
        $resolver->setAllowedTypes('sections', 'array');

        // @deprecated Replace with "string[]" allowed type when support for Symfony 2.8 ends
        $resolver->setAllowedValues(
            'sections',
            function (array $sections): bool {
                foreach ($sections as $section) {
                    if (!is_string($section)) {
                        return false;
                    }
                }

                return true;
            }
        );

        $resolver->setDefault(
            'choices',
            function (Options $options): array {
                return $this->getSections($options);
            }
        );

        $resolver->setDefault('choice_translation_domain', false);

        $resolver->setDefaults(
            $this->getChoicesAsValuesOption()
        );
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    /**
     * Returns the allowed sections from eZ Platform.
     */
    private function getSections(Options $options): array
    {
        $allSections = [];

        /** @var \eZ\Publish\API\Repository\Values\Content\Section[] $sections */
        $sections = $this->sectionService->loadSections();
        $configuredSections = $options['sections'];

        foreach ($sections as $section) {
            if (!empty($configuredSections) && !in_array($section->identifier, $configuredSections, true)) {
                continue;
            }

            $allSections[$section->name] = $section->identifier;
        }

        return $allSections;
    }
}
