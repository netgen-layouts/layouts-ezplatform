<?php

namespace Netgen\BlockManager\Ez\Block\BlockDefinition\Handler;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandler;
use Netgen\BlockManager\Block\DynamicParameters;
use Netgen\BlockManager\Ez\ContentProvider\ContentProviderInterface;
use Netgen\BlockManager\Parameters\ParameterBuilderInterface;
use Netgen\BlockManager\Parameters\ParameterType;

/**
 * Block definition handler for a block which renders a field specified by the parameter
 * from currently rendered eZ Platform content.
 */
final class ContentFieldHandler extends BlockDefinitionHandler
{
    /**
     * @var \Netgen\BlockManager\Ez\ContentProvider\ContentProviderInterface
     */
    private $contentProvider;

    public function __construct(ContentProviderInterface $contentProvider)
    {
        $this->contentProvider = $contentProvider;
    }

    public function buildParameters(ParameterBuilderInterface $builder)
    {
        $builder->add('field_identifier', ParameterType\IdentifierType::class);
    }

    public function getDynamicParameters(DynamicParameters $params, Block $block)
    {
        $params['content'] = $this->contentProvider->provideContent();
        $params['location'] = $this->contentProvider->provideLocation();
    }

    public function isContextual(Block $block)
    {
        return true;
    }
}
