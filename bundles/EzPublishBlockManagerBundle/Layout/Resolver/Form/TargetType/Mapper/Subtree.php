<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\Form\TargetType\Mapper;

use Netgen\BlockManager\Layout\Resolver\Form\TargetType\Mapper;
use Netgen\BlockManager\Layout\Resolver\TargetTypeInterface;
use Netgen\Bundle\ContentBrowserBundle\Form\Type\ContentBrowserType;

class Subtree extends Mapper
{
    /**
     * Returns the form type that will be used to edit the value of this condition type.
     *
     * @return string
     */
    public function getFormType()
    {
        return ContentBrowserType::class;
    }

    /**
     * Returns the form type options.
     *
     * @param \Netgen\BlockManager\Layout\Resolver\TargetTypeInterface $targetType
     *
     * @return array
     */
    public function getOptions(TargetTypeInterface $targetType)
    {
        return array(
            'value_type' => 'ezlocation',
            'config_name' => 'ezlocation',
        ) + parent::getOptions($targetType);
    }
}
