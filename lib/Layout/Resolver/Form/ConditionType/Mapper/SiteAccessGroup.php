<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Layout\Resolver\Form\ConditionType\Mapper;

use Netgen\Layouts\Layout\Resolver\Form\ConditionType\Mapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use function array_combine;
use function array_keys;

final class SiteAccessGroup extends Mapper
{
    /**
     * @param array<string, string[]> $siteAccessGroupList
     */
    public function __construct(private array $siteAccessGroupList)
    {
    }

    public function getFormType(): string
    {
        return ChoiceType::class;
    }

    public function getFormOptions(): array
    {
        $siteAccessGroupList = array_keys($this->siteAccessGroupList);

        return [
            'choices' => array_combine($siteAccessGroupList, $siteAccessGroupList),
            'choice_translation_domain' => false,
            'multiple' => true,
            'expanded' => true,
        ];
    }
}
