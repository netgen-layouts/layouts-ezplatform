<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Form;

use eZ\Publish\API\Repository\ObjectStateService;
use Netgen\BlockManager\Form\AbstractType;
use Netgen\BlockManager\Form\ChoicesAsValuesTrait;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ObjectStateType extends AbstractType
{
    use ChoicesAsValuesTrait;

    /**
     * @var \eZ\Publish\API\Repository\ObjectStateService
     */
    private $objectStateService;

    public function __construct(ObjectStateService $objectStateService)
    {
        $this->objectStateService = $objectStateService;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('states', []);
        $resolver->setRequired(['states']);
        $resolver->setAllowedTypes('states', 'array');

        $resolver->setDefault(
            'choices',
            function (Options $options): array {
                return $this->getObjectStates($options);
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
     * Returns the allowed content states from eZ Platform.
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
                    is_array($configuredGroups[$group->identifier]) &&
                    !in_array($objectState->identifier, $configuredGroups[$group->identifier], true)
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
