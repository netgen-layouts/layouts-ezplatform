<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Layout\Resolver\Form\ConditionType\Mapper;

use Netgen\BlockManager\Ez\Form\ContentTypeType;
use Netgen\BlockManager\Layout\Resolver\Form\ConditionType\Mapper;

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
