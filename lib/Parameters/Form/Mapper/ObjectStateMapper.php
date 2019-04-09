<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Parameters\Form\Mapper;

use Netgen\Layouts\Ez\Form\ObjectStateType;
use Netgen\BlockManager\Parameters\Form\Mapper;
use Netgen\BlockManager\Parameters\ParameterDefinition;

final class ObjectStateMapper extends Mapper
{
    public function getFormType(): string
    {
        return ObjectStateType::class;
    }

    public function mapOptions(ParameterDefinition $parameterDefinition): array
    {
        return [
            'multiple' => $parameterDefinition->getOption('multiple'),
            'states' => $parameterDefinition->getOption('states'),
        ];
    }
}
