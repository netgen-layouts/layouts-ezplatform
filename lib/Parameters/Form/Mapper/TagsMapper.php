<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Parameters\Form\Mapper;

use Netgen\BlockManager\Parameters\Form\Mapper;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\ContentBrowser\Form\Type\ContentBrowserMultipleType;

final class TagsMapper extends Mapper
{
    public function getFormType(): string
    {
        return ContentBrowserMultipleType::class;
    }

    public function mapOptions(ParameterDefinition $parameterDefinition): array
    {
        return [
            'item_type' => 'eztags',
            'min' => $parameterDefinition->getOption('min'),
            'max' => $parameterDefinition->getOption('max'),
        ];
    }
}
