<?php

namespace Netgen\BlockManager\Ez\Parameters\Form\Mapper;

use Netgen\BlockManager\Ez\Form\ContentTypeType;
use Netgen\BlockManager\Parameters\Form\Mapper;
use Netgen\BlockManager\Parameters\ParameterInterface;

class ContentTypeMapper extends Mapper
{
    public function getFormType()
    {
        return ContentTypeType::class;
    }

    public function mapOptions(ParameterInterface $parameter)
    {
        return array(
            'multiple' => $parameter->getOption('multiple'),
            'types' => $parameter->getOption('types'),
        );
    }
}
