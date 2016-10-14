<?php

namespace Netgen\BlockManager\Ez\Parameters\FormMapper\ParameterHandler;

use Netgen\BlockManager\Parameters\FormMapper\ParameterHandler;
use Netgen\BlockManager\Parameters\ParameterInterface;
use Netgen\Bundle\ContentBrowserBundle\Form\Type\ContentBrowserType;

class Location extends ParameterHandler
{
    /**
     * Returns the form type for the parameter.
     *
     * @return string
     */
    public function getFormType()
    {
        return ContentBrowserType::class;
    }

    /**
     * Converts parameter options to Symfony form options.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterInterface $parameter
     *
     * @return array
     */
    public function convertOptions(ParameterInterface $parameter)
    {
        return array(
            'item_type' => 'ezlocation',
            'required' => $parameter->isRequired(),
        );
    }
}
