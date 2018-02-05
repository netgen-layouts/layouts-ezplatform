<?php

namespace Netgen\BlockManager\Ez\Parameters\Form\Mapper;

use Netgen\BlockManager\Parameters\Form\Mapper;
use Netgen\BlockManager\Parameters\ParameterDefinitionInterface;
use Netgen\ContentBrowser\Form\Type\ContentBrowserMultipleType;

final class TagsMapper extends Mapper
{
    public function getFormType()
    {
        return ContentBrowserMultipleType::class;
    }

    public function mapOptions(ParameterDefinitionInterface $parameterDefinition)
    {
        $options = $parameterDefinition->getOptions();

        return array(
            'item_type' => 'eztags',
            'min' => $options['min'],
            'max' => $options['max'],
        );
    }
}
