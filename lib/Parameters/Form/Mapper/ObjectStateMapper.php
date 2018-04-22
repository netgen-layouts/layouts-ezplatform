<?php

namespace Netgen\BlockManager\Ez\Parameters\Form\Mapper;

use Netgen\BlockManager\Ez\Form\ObjectStateType;
use Netgen\BlockManager\Parameters\Form\Mapper;
use Netgen\BlockManager\Parameters\ParameterDefinition;

final class ObjectStateMapper extends Mapper
{
    public function getFormType()
    {
        return ObjectStateType::class;
    }

    public function mapOptions(ParameterDefinition $parameterDefinition)
    {
        return [
            'multiple' => $parameterDefinition->getOption('multiple'),
            'states' => $parameterDefinition->getOption('states'),
        ];
    }
}
