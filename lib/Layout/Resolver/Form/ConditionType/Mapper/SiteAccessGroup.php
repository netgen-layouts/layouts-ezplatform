<?php

namespace Netgen\BlockManager\Ez\Layout\Resolver\Form\ConditionType\Mapper;

use Netgen\BlockManager\Layout\Resolver\Form\ConditionType\Mapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class SiteAccessGroup extends Mapper
{
    /**
     * @var array
     */
    protected $siteAccessGroupList;

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

    public function getFormType()
    {
        return ChoiceType::class;
    }

    public function getFormOptions()
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
