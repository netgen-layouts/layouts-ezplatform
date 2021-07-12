<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsEzPlatformBundle\Form\Admin\Type;

use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\Layout\Registry\LayoutTypeRegistry;
use Netgen\Layouts\Validator\Constraint\LayoutName;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;
use function array_key_first;

final class LayoutWizardType extends AbstractType
{
    public const ACTION_TYPE_NEW_LAYOUT = 'new_layout';

    public const ACTION_TYPE_COPY_LAYOUT = 'copy_layout';

    private LayoutService $layoutService;

    private LayoutTypeRegistry $layoutTypeRegistry;

    private bool $isEnterprise;

    public function __construct(
        LayoutService $layoutService,
        LayoutTypeRegistry $layoutTypeRegistry,
        bool $isEnterprise
    ) {
        $this->layoutService = $layoutService;
        $this->layoutTypeRegistry = $layoutTypeRegistry;
        $this->isEnterprise = $isEnterprise;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('translation_domain', 'nglayouts_ezadminui_forms');
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $layoutTypes = $this->layoutTypeRegistry->getLayoutTypes(true);

        $builder->add(
            'action',
            Type\ChoiceType::class,
            [
                'label' => false,
                'expanded' => true,
                'data' => self::ACTION_TYPE_NEW_LAYOUT,
                'choices' => [
                    'layout_wizard.action.new_layout' => self::ACTION_TYPE_NEW_LAYOUT,
                    'layout_wizard.action.copy_layout' => self::ACTION_TYPE_COPY_LAYOUT,
                ],
            ],
        );

        $builder->add(
            'layout_type',
            Type\ChoiceType::class,
            [
                'label' => 'layout_wizard.layout_type',
                'required' => true,
                'choices' => $layoutTypes,
                'choice_value' => 'identifier',
                'choice_name' => 'identifier',
                'choice_label' => 'name',
                'choice_translation_domain' => false,
                'expanded' => true,
                'data' => $layoutTypes[array_key_first($layoutTypes)],
                'constraints' => [
                    new Constraints\NotBlank(),
                ],
            ],
        );

        $builder->add(
            'layout',
            Type\ChoiceType::class,
            [
                'label' => 'layout_wizard.layout',
                'choices' => $this->layoutService->loadAllLayouts(),
                'choice_value' => 'id',
                'choice_label' => 'name',
            ],
        );

        $builder->add(
            'layout_name',
            Type\TextType::class,
            [
                'label' => 'layout_wizard.layout_name',
                'constraints' => [
                    new Constraints\NotBlank(),
                    new LayoutName(),
                ],
            ],
        );

        $builder->add(
            'layout_description',
            Type\TextareaType::class,
            [
                'label' => 'layout_wizard.layout_description',
                'required' => false,
                'constraints' => [
                    new Constraints\NotNull(),
                    new Constraints\Type(['type' => 'string']),
                ],
                'empty_data' => '',
            ],
        );

        if ($this->isEnterprise) {
            $builder->add(
                'rule_group',
                Type\HiddenType::class,
                [
                    'label' => 'layout_wizard.rule_group',
                    'constraints' => [
                        new Constraints\NotBlank(),
                        new Constraints\Uuid(['versions' => [Constraints\Uuid::V4_RANDOM]]),
                    ],
                ],
            );
        }

        $builder->add(
            'activate_rule',
            Type\CheckboxType::class,
            [
                'label' => 'layout_wizard.activate_rule',
                'data' => true,
                'constraints' => [
                    new Constraints\NotNull(),
                    new Constraints\Type(['type' => 'bool']),
                ],
            ],
        );
    }

    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        /** @var \Netgen\Layouts\Layout\Type\LayoutTypeInterface $layoutType */
        foreach ($this->layoutTypeRegistry->getLayoutTypes(true) as $layoutType) {
            if (!isset($view['layout_type'][$layoutType->getIdentifier()])) {
                continue;
            }

            $view['layout_type'][$layoutType->getIdentifier()]->vars['layout_type'] = $layoutType;
        }
    }
}
