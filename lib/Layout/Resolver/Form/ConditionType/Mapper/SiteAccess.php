<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Layout\Resolver\Form\ConditionType\Mapper;

use Netgen\Layouts\Layout\Resolver\Form\ConditionType\Mapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use function array_combine;

final class SiteAccess extends Mapper
{
    /**
     * @param string[] $siteAccessList
     */
    public function __construct(private array $siteAccessList)
    {
    }

    public function getFormType(): string
    {
        return ChoiceType::class;
    }

    public function getFormOptions(): array
    {
        return [
            'choices' => array_combine($this->siteAccessList, $this->siteAccessList),
            'choice_translation_domain' => false,
            'multiple' => true,
            'expanded' => true,
        ];
    }
}
