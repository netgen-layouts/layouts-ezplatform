<?php

namespace Netgen\BlockManager\Ez\Parameters\Form\Mapper;

use Netgen\BlockManager\Ez\Form\ObjectStateType;
use Netgen\BlockManager\Parameters\Form\Mapper;
use Netgen\BlockManager\Parameters\ParameterInterface;

final class ObjectStateMapper extends Mapper
{
    public function getFormType()
    {
        return ObjectStateType::class;
    }

    public function mapOptions(ParameterInterface $parameter)
    {
        return array(
            'multiple' => $parameter->getOption('multiple'),
            'states' => $parameter->getOption('states'),
        );
    }
}
