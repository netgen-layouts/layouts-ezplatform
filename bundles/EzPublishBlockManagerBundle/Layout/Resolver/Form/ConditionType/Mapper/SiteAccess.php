<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\Form\ConditionType\Mapper;

use Netgen\BlockManager\Layout\Resolver\Form\ConditionType\MapperInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class SiteAccess implements MapperInterface
{
    /**
     * @var array
     */
    protected $siteAccessList;

    /**
     * Constructor.
     *
     * @param array $siteAccessList
     */
    public function __construct(array $siteAccessList)
    {
        // We want the array to have the same
        // list for keys as well as values
        $this->siteAccessList = array_combine(
            $siteAccessList,
            $siteAccessList
        );
    }

    /**
     * Returns the form type that will be used to edit the value of this condition type.
     *
     * @return string
     */
    public function getFormType()
    {
        return ChoiceType::class;
    }

    /**
     * Returns the form type options.
     *
     * @return array
     */
    public function getOptions()
    {
        return array(
            'choices' => $this->siteAccessList,
            'choice_translation_domain' => false,
            'choices_as_values' => true,
            'label' => 'condition_type.siteaccess.label',
            'multiple' => true,
        );
    }

    /**
     * Handles the form for this condition type.
     *
     * This is the place where you will usually add data mappers and transformers to the form.
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return array
     */
    public function handleForm(FormBuilderInterface $builder)
    {
    }
}
