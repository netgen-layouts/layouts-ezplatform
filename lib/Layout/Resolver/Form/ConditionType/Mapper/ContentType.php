<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Layout\Resolver\Form\ConditionType\Mapper;

use Netgen\BlockManager\Layout\Resolver\Form\ConditionType\Mapper;
use Netgen\Layouts\Ez\Form\ContentTypeType;

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
