<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Parameters\Form\Mapper;

use Netgen\BlockManager\Parameters\Form\Mapper;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\ContentBrowser\Form\Type\ContentBrowserType;

final class ContentMapper extends Mapper
{
    public function getFormType(): string
    {
        return ContentBrowserType::class;
    }

    public function mapOptions(ParameterDefinition $parameterDefinition): array
    {
        $options = [
            'item_type' => 'ezcontent',
        ];

        $allowedTypes = $parameterDefinition->getOption('allowed_types') ?? [];
        if (count($allowedTypes) > 0) {
            $options['custom_params']['allowed_content_types'] = $allowedTypes;
        }

        return $options;
    }
}
