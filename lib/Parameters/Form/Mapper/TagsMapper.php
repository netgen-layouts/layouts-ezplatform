<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Parameters\Form\Mapper;

use Netgen\BlockManager\Parameters\Form\Mapper;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\ContentBrowser\Form\Type\ContentBrowserMultipleType;

final class TagsMapper extends Mapper
{
    public function getFormType()
    {
        return ContentBrowserMultipleType::class;
    }

    public function mapOptions(ParameterDefinition $parameterDefinition)
    {
        $options = $parameterDefinition->getOptions();

        return [
            'item_type' => 'eztags',
            'min' => $options['min'],
            'max' => $options['max'],
        ];
    }
}
