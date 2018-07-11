<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Form;

use eZ\Publish\API\Repository\ObjectStateService;
use eZ\Publish\API\Repository\Values\ObjectState\ObjectState;
use eZ\Publish\API\Repository\Values\ObjectState\ObjectStateGroup;
use eZ\Publish\Core\Repository\Values\MultiLanguageNameTrait;
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

                $allObjectStates[$this->getGroupName($group)][$this->getStateName($objectState)] = $group->identifier . '|' . $objectState->identifier;
            }
        }

        return $allObjectStates;
    }

    /**
     * @deprecated BC layer for eZ Publish 5 to fetch object state name.
     *
     * Remove when support for eZ Publish 5 ends.
     */
    private function getStateName(ObjectState $state): string
    {
        if (trait_exists(MultiLanguageNameTrait::class)) {
            return $state->getName() ?? '';
        }

        $stateNames = array_values($state->getNames());

        return $stateNames[0] ?? '';
    }

    /**
     * @deprecated BC layer for eZ Publish 5 to fetch object state group name.
     *
     * Remove when support for eZ Publish 5 ends.
     */
    private function getGroupName(ObjectStateGroup $group): string
    {
        if (trait_exists(MultiLanguageNameTrait::class)) {
            return $group->getName() ?? '';
        }

        $groupNames = array_values($group->getNames());

        return $groupNames[0] ?? '';
    }
}
