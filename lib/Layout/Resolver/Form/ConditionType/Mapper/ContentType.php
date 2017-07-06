<?php

namespace Netgen\BlockManager\Ez\Layout\Resolver\Form\ConditionType\Mapper;

use Netgen\BlockManager\Ez\Form\ContentTypeType;
use Netgen\BlockManager\Layout\Resolver\Form\ConditionType\Mapper;

class ContentType extends Mapper
{
    /**
     * Returns the form type that will be used to edit the value of this condition type.
     *
     * @return string
     */
    public function getFormType()
    {
        return ContentTypeType::class;
    }

    /**
     * Returns the form options.
     *
     * @return array
     */
    public function getFormOptions()
    {
        return array(
            'multiple' => true,
        );
    }
}
