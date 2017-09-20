<?php

namespace Netgen\BlockManager\Ez\Layout\Resolver\Form\ConditionType\Mapper;

use Netgen\BlockManager\Layout\Resolver\Form\ConditionType\Mapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class SiteAccess extends Mapper
{
    /**
     * @var array
     */
    private $siteAccessList;

    public function __construct(array $siteAccessList)
    {
        // We want the array to have the same
        // list for keys as well as values
        $this->siteAccessList = array_combine(
            $siteAccessList,
            $siteAccessList
        );
    }

    public function getFormType()
    {
        return ChoiceType::class;
    }

    public function getFormOptions()
    {
        return array(
            'choices' => $this->siteAccessList,
            'choice_translation_domain' => false,
            'choices_as_values' => true,
            'multiple' => true,
            'expanded' => true,
        );
    }
}
