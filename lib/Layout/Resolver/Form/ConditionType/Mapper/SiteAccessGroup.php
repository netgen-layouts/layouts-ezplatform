<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Layout\Resolver\Form\ConditionType\Mapper;

use Netgen\Layouts\Layout\Resolver\Form\ConditionType\Mapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use function array_combine;
use function array_keys;

final class SiteAccessGroup extends Mapper
{
    /**
     * @var array<string, string>
     */
    private array $siteAccessGroupList;

    /**
     * @param array<string, string[]> $siteAccessGroupList
     */
    public function __construct(array $siteAccessGroupList)
    {
        $siteAccessGroupList = array_keys($siteAccessGroupList);

        // We want the array to have the same list for keys as well as values
        $this->siteAccessGroupList = array_combine($siteAccessGroupList, $siteAccessGroupList);
    }

    public function getFormType(): string
    {
        return ChoiceType::class;
    }

    public function getFormOptions(): array
    {
        return [
            'choices' => $this->siteAccessGroupList,
            'choice_translation_domain' => false,
            'multiple' => true,
            'expanded' => true,
        ];
    }
}
