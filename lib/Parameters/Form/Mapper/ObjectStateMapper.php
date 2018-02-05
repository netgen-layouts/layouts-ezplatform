<?php

namespace Netgen\BlockManager\Ez\Parameters\Form\Mapper;

use Netgen\BlockManager\Ez\Form\ObjectStateType;
use Netgen\BlockManager\Parameters\Form\Mapper;
use Netgen\BlockManager\Parameters\ParameterDefinitionInterface;

final class ObjectStateMapper extends Mapper
{
    public function getFormType()
    {
        return ObjectStateType::class;
    }

    public function mapOptions(ParameterDefinitionInterface $parameterDefinition)
    {
        return array(
            'multiple' => $parameterDefinition->getOption('multiple'),
            'states' => $parameterDefinition->getOption('states'),
        );
    }
}
