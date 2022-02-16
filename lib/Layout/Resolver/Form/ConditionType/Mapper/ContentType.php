<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Layout\Resolver\Form\ConditionType\Mapper;

use Netgen\Layouts\Ibexa\Form\ContentTypeType;
use Netgen\Layouts\Layout\Resolver\Form\ConditionType\Mapper;

final class ContentType extends Mapper
{
    public function getFormType(): string
    {
        return ContentTypeType::class;
    }

    public function getFormOptions(): array
    {
        return [
            'multiple' => true,
        ];
    }
}
