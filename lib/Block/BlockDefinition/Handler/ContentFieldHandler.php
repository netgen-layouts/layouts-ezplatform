<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Block\BlockDefinition\Handler;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandler;
use Netgen\BlockManager\Block\DynamicParameters;
use Netgen\Layouts\Ez\ContentProvider\ContentProviderInterface;
use Netgen\BlockManager\Parameters\ParameterBuilderInterface;
use Netgen\BlockManager\Parameters\ParameterType;

/**
 * Block definition handler for a block which renders a field specified by the parameter
 * from currently rendered eZ Platform content.
 */
final class ContentFieldHandler extends BlockDefinitionHandler
{
    /**
     * @var \Netgen\Layouts\Ez\ContentProvider\ContentProviderInterface
     */
    private $contentProvider;

    public function __construct(ContentProviderInterface $contentProvider)
    {
        $this->contentProvider = $contentProvider;
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
