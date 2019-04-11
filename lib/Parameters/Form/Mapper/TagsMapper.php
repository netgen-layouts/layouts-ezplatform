<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Parameters\Form\Mapper;

use Netgen\ContentBrowser\Form\Type\ContentBrowserMultipleType;
use Netgen\Layouts\Parameters\Form\Mapper;
use Netgen\Layouts\Parameters\ParameterDefinition;

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
