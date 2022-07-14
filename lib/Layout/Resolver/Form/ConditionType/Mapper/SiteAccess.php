<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Layout\Resolver\Form\ConditionType\Mapper;

use Netgen\Layouts\Layout\Resolver\Form\ConditionType\Mapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use function array_combine;

final class SiteAccess extends Mapper
{
    /**
     * @var string[]
     */
    private array $siteAccessList;

    /**
     * @param string[] $siteAccessList
     */
    public function __construct(array $siteAccessList)
    {
        // We want the array to have the same list for keys as well as values
        $this->siteAccessList = array_combine($siteAccessList, $siteAccessList);
    }

    public function getFormType(): string
    {
        return ChoiceType::class;
    }

    public function getFormOptions(): array
    {
        return [
            'choices' => $this->siteAccessList,
            'choice_translation_domain' => false,
            'multiple' => true,
            'expanded' => true,
        ];
    }
}
