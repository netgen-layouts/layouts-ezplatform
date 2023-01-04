<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Parameters\Form\Mapper;

use Netgen\ContentBrowser\Form\Type\ContentBrowserType;
use Netgen\Layouts\Parameters\Form\Mapper;
use Netgen\Layouts\Parameters\ParameterDefinition;

use function count;

final class ContentMapper extends Mapper
{
    public function getFormType(): string
    {
        return ContentBrowserType::class;
    }

    public function mapOptions(ParameterDefinition $parameterDefinition): array
    {
        $options = [
            'item_type' => 'ibexa_content',
            'block_prefix' => 'ngcb_ibexa_content',
        ];

        $allowedTypes = $parameterDefinition->getOption('allowed_types') ?? [];
        if (count($allowedTypes) > 0) {
            $options['custom_params']['allowed_content_types'] = $allowedTypes;
        }

        return $options;
    }
}
