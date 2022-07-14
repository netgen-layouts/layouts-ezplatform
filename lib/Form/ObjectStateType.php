<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Form;

use eZ\Publish\API\Repository\ObjectStateService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function in_array;
use function is_array;

final class ObjectStateType extends AbstractType
{
    private ObjectStateService $objectStateService;

    public function __construct(ObjectStateService $objectStateService)
    {
        $this->objectStateService = $objectStateService;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('translation_domain', 'nglayouts_forms');

        $resolver->setDefault('states', []);
        $resolver->setRequired(['states']);
        $resolver->setAllowedTypes('states', 'array');

        $resolver->setDefault(
            'choices',
            fn (Options $options): array => $this->getObjectStates($options),
        );

        $resolver->setDefault('choice_translation_domain', false);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    /**
     * Returns the allowed content states from eZ Platform.
     *
     * @return array<string, string[]>
     */
    private function getObjectStates(Options $options): array
    {
        $allObjectStates = [];

        $groups = $this->objectStateService->loadObjectStateGroups();
        $configuredGroups = $options['states'];

        foreach ($groups as $group) {
            $configuredGroups += [$group->identifier => true];
            if ($configuredGroups[$group->identifier] === false) {
                continue;
            }

            $objectStates = $this->objectStateService->loadObjectStates($group);

            foreach ($objectStates as $objectState) {
                if (
                    is_array($configuredGroups[$group->identifier])
                    && !in_array($objectState->identifier, $configuredGroups[$group->identifier], true)
                ) {
                    continue;
                }

                $groupName = $group->getName() ?? $group->identifier;
                $stateName = $objectState->getName() ?? $objectState->identifier;

                $allObjectStates[$groupName][$stateName] = $group->identifier . '|' . $objectState->identifier;
            }
        }

        return $allObjectStates;
    }
}
