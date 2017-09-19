<?php

namespace Netgen\BlockManager\Ez\Layout\Resolver\Form\TargetType\Mapper;

use Netgen\BlockManager\Layout\Resolver\Form\TargetType\Mapper;
use Netgen\ContentBrowser\Form\Type\ContentBrowserType;

class Subtree extends Mapper
{
    public function getFormType()
    {
        return ContentBrowserType::class;
    }

    public function getFormOptions()
    {
        return array(
            'item_type' => 'ezlocation',
        );
    }
}
