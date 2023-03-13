<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Block\BlockDefinition\Handler;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Block\BlockDefinition\BlockDefinitionHandler;
use Netgen\Layouts\Block\DynamicParameters;
use Netgen\Layouts\Ibexa\ContentProvider\ContentProviderInterface;
use Netgen\Layouts\Parameters\ParameterBuilderInterface;
use Netgen\Layouts\Parameters\ParameterType;

/**
 * Block definition handler for a block which renders a field specified by the parameter
 * from currently rendered Ibexa CMS content.
 */
final class ContentFieldHandler extends BlockDefinitionHandler
{
    public function __construct(private ContentProviderInterface $contentProvider)
    {
    }

    public function buildParameters(ParameterBuilderInterface $builder): void
    {
        $builder->add('field_identifier', ParameterType\IdentifierType::class);
    }

    public function getDynamicParameters(DynamicParameters $params, Block $block): void
    {
        $params['content'] = $this->contentProvider->provideContent();
        $params['location'] = $this->contentProvider->provideLocation();
    }

    public function isContextual(Block $block): bool
    {
        return true;
    }
}
