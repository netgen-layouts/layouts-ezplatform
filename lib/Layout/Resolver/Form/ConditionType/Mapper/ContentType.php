<?php

namespace Netgen\BlockManager\Ez\Layout\Resolver\Form\ConditionType\Mapper;

use Netgen\BlockManager\Ez\Form\ContentTypeType;
use Netgen\BlockManager\Layout\Resolver\Form\ConditionType\Mapper;

final class ContentType extends Mapper
{
    public function getFormType()
    {
        return ContentTypeType::class;
    }

    public function getFormOptions()
    {
        return [
            'multiple' => true,
        ];
    }
}
