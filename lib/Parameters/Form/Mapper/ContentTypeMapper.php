<?php

namespace Netgen\BlockManager\Ez\Parameters\Form\Mapper;

use Netgen\BlockManager\Parameters\Form\Mapper;
use Netgen\BlockManager\Parameters\ParameterInterface;
use Netgen\BlockManager\Ez\Form\ContentTypeType;

class ContentTypeMapper extends Mapper
{
    /**
     * Returns the form type for the parameter.
     *
     * @return string
     */
    public function getFormType()
    {
        return ContentTypeType::class;
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
            'multiple' => $parameter->getOptions()['multiple'],
        );
    }
}
