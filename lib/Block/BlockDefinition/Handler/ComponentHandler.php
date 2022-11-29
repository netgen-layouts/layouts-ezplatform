<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Block\BlockDefinition\Handler;

use Netgen\Layouts\Block\BlockDefinition\BlockDefinitionHandler;
use Netgen\Layouts\Ibexa\Parameters\ParameterType as IbexaParameterType;
use Netgen\Layouts\Parameters\ParameterBuilderInterface;
use Netgen\Layouts\Parameters\ParameterType;

final class ComponentHandler extends BlockDefinitionHandler
{
    public function buildParameters(ParameterBuilderInterface $builder): void
    {
        $builder->add(
            'content_type_identifier',
            ParameterType\HiddenType::class,
            [
                'required' => true,
                'readonly' => true,
            ],
        );

        $builder->add(
            'content',
            IbexaParameterType\ContentType::class,
            [
                'label' => 'block.ibexa_component.content',
                'allow_invalid' => true,
            ],
        );
    }
}
