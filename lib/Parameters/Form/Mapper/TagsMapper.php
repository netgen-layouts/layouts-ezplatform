<?php

namespace Netgen\BlockManager\Ez\Parameters\Form\Mapper;

use Netgen\BlockManager\Parameters\Form\Mapper;
use Netgen\BlockManager\Parameters\ParameterInterface;
use Netgen\Bundle\ContentBrowserBundle\Form\Type\ContentBrowserMultipleType;

class TagsMapper extends Mapper
{
    /**
     * Returns the form type for the parameter.
     *
     * @return string
     */
    public function getFormType()
    {
        return ContentBrowserMultipleType::class;
    }

    /**
     * Maps parameter options to Symfony form options.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterInterface $parameter
     *
     * @return array
     */
    public function mapOptions(ParameterInterface $parameter)
    {
        return array(
            'item_type' => 'eztags',
        );
    }
}
