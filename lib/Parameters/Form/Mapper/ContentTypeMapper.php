<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Parameters\Form\Mapper;

use Netgen\Layouts\Ez\Form\ContentTypeType;
use Netgen\Layouts\Parameters\Form\Mapper;
use Netgen\Layouts\Parameters\ParameterDefinition;

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
