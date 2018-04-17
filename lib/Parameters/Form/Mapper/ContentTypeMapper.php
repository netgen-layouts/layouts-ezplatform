<?php

namespace Netgen\BlockManager\Ez\Parameters\Form\Mapper;

use Netgen\BlockManager\Ez\Form\ContentTypeType;
use Netgen\BlockManager\Parameters\Form\Mapper;
use Netgen\BlockManager\Parameters\ParameterDefinitionInterface;

final class ContentTypeMapper extends Mapper
{
    public function getFormType()
    {
        return ContentTypeType::class;
    }

    public function mapOptions(ParameterDefinitionInterface $parameterDefinition)
    {
        return [
            'multiple' => $parameterDefinition->getOption('multiple'),
            'types' => $parameterDefinition->getOption('types'),
        ];
    }
}
