<?php

namespace Netgen\BlockManager\Ez\Layout\Resolver\Form\ConditionType\Mapper;

use Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface;
use Netgen\BlockManager\Layout\Resolver\Form\ConditionType\Mapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class SiteAccessGroup extends Mapper
{
    /**
     * @var array
     */
    protected $siteAccessGroupList;

    /**
     * Constructor.
     *
     * @param array $siteAccessGroupList
     */
    public function __construct(array $siteAccessGroupList)
    {
        $siteAccessGroupList = array_keys($siteAccessGroupList);

        // We want the array to have the same
        // list for keys as well as values
        $this->siteAccessGroupList = array_combine(
            $siteAccessGroupList,
            $siteAccessGroupList
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
     * Maps the form type options from provided condition type.
     *
     * @param \Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface $conditionType
     *
     * @return array
     */
    public function mapOptions(ConditionTypeInterface $conditionType)
    {
        return array(
            'choices' => $this->siteAccessGroupList,
            'choice_translation_domain' => false,
            'choices_as_values' => true,
            'multiple' => true,
            'expanded' => true,
        );
    }
}
