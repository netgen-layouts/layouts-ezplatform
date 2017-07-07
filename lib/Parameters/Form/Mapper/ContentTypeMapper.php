<?php

namespace Netgen\BlockManager\Ez\Parameters\Form\Mapper;

use Netgen\BlockManager\Ez\Form\ContentTypeType;
use Netgen\BlockManager\Parameters\Form\Mapper;
use Netgen\BlockManager\Parameters\ParameterInterface;

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
     *
     * @return array
     */
    public function mapOptions(ParameterInterface $parameter)
    {
        return array(
            'multiple' => $parameter->getOption('multiple'),
            'types' => $parameter->getOption('types'),
        );
    }
}
