<?php

namespace Netgen\BlockManager\Ez\Parameters\Form\Mapper;

use Netgen\BlockManager\Parameters\Form\Mapper;
use Netgen\BlockManager\Parameters\ParameterInterface;
use Netgen\ContentBrowser\Form\Type\ContentBrowserType;

class LocationMapper extends Mapper
{
    public function getFormType()
    {
        return ContentBrowserType::class;
    }

    public function mapOptions(ParameterInterface $parameter)
    {
        return array(
            'item_type' => 'ezlocation',
        );
    }
}
