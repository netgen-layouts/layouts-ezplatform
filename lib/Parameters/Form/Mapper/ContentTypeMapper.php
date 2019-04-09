<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Parameters\Form\Mapper;

use Netgen\BlockManager\Parameters\Form\Mapper;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\Layouts\Ez\Form\ContentTypeType;

final class ContentTypeMapper extends Mapper
{
    public function getFormType(): string
    {
        return ContentTypeType::class;
    }

    public function mapOptions(ParameterDefinition $parameterDefinition): array
    {
        return [
            'multiple' => $parameterDefinition->getOption('multiple'),
            'types' => $parameterDefinition->getOption('types'),
        ];
    }
}
