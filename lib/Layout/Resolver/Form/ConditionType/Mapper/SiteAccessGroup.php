<?php

namespace Netgen\BlockManager\Ez\Layout\Resolver\Form\ConditionType\Mapper;

use Netgen\BlockManager\Form\ChoicesAsValuesTrait;
use Netgen\BlockManager\Layout\Resolver\Form\ConditionType\Mapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

final class SiteAccessGroup extends Mapper
{
    use ChoicesAsValuesTrait;

    /**
     * @var array
     */
    private $siteAccessGroupList;

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
        return [
            'choices' => $this->siteAccessGroupList,
            'choice_translation_domain' => false,
            'multiple' => true,
            'expanded' => true,
        ] + $this->getChoicesAsValuesOption();
    }
}
