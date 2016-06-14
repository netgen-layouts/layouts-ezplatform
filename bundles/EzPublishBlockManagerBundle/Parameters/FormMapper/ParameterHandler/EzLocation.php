<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Parameters\FormMapper\ParameterHandler;

use Netgen\BlockManager\Parameters\FormMapper\ParameterHandler;
use Netgen\BlockManager\Parameters\FormMapper\Type\ContentBrowserType;
use Netgen\BlockManager\Parameters\ParameterInterface;

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
            'item_type' => 'ezlocation',
            'config_name' => 'ezlocation',
        );
    }
}
