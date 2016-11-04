<?php

namespace Netgen\BlockManager\Ez\Parameters\Form\Mapper;

use Netgen\BlockManager\Parameters\Form\Mapper;
use Netgen\BlockManager\Parameters\ParameterInterface;
use Netgen\Bundle\ContentBrowserBundle\Form\Type\ContentBrowserType;

class LocationMapper extends Mapper
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
     * Maps parameter options to Symfony form options.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterInterface $parameter
     * @param string $parameterName
     * @param array $formOptions
     *
     * @return array
     */
    public function mapOptions(ParameterInterface $parameter, $parameterName, array $formOptions)
    {
        return array(
            'item_type' => 'ezlocation',
        );
    }
}
