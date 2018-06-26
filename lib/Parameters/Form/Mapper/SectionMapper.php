<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Parameters\Form\Mapper;

use Netgen\BlockManager\Ez\Form\SectionType;
use Netgen\BlockManager\Parameters\Form\Mapper;
use Netgen\BlockManager\Parameters\ParameterDefinition;

final class SectionMapper extends Mapper
{
    public function getFormType(): string
    {
        return SectionType::class;
    }

    public function mapOptions(ParameterDefinition $parameterDefinition): array
    {
        return [
            'multiple' => $parameterDefinition->getOption('multiple'),
            'sections' => $parameterDefinition->getOption('sections'),
        ];
    }
}
