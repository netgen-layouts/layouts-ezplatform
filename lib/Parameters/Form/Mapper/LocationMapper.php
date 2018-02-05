<?php

namespace Netgen\BlockManager\Ez\Parameters\Form\Mapper;

use Netgen\BlockManager\Parameters\Form\Mapper;
use Netgen\BlockManager\Parameters\ParameterDefinitionInterface;
use Netgen\ContentBrowser\Form\Type\ContentBrowserType;

final class LocationMapper extends Mapper
{
    public function getFormType()
    {
        return ContentBrowserType::class;
    }

    public function mapOptions(ParameterDefinitionInterface $parameterDefinition)
    {
        return array(
            'item_type' => 'ezlocation',
        );
    }
}
