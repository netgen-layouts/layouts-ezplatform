<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Parameters\FormMapper\ParameterHandler;

use Netgen\BlockManager\Parameters\FormMapper\ParameterHandler;
use Netgen\BlockManager\Parameters\ParameterInterface;
use Netgen\Bundle\ContentBrowserBundle\Form\Type\ContentBrowserType;

class EzLocation extends ParameterHandler
{
    /**
     * Returns the form type for the parameter.
     *
     * @return string
     */
    protected function getFormType()
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
            'value_type' => 'ezlocation',
            'config_name' => 'ezlocation',
        );
    }
}
