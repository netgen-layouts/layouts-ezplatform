<?php

namespace Netgen\BlockManager\Ez\Block\BlockDefinition\Handler;

use Netgen\BlockManager\API\Values\Page\Block;
use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandler;
use Netgen\BlockManager\Ez\ContentProvider\ContentProviderInterface;
use Netgen\BlockManager\Parameters\ParameterType;
use Netgen\BlockManager\Parameters\ParameterBuilderInterface;

class ContentFieldHandler extends BlockDefinitionHandler
{
    /**
     * @var \Netgen\BlockManager\Ez\ContentProvider\ContentProviderInterface
     */
    protected $contentProvider;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Ez\ContentProvider\ContentProviderInterface $contentProvider
     */
    public function __construct(ContentProviderInterface $contentProvider)
    {
        $this->contentProvider = $contentProvider;
    }

    /**
     * Builds the parameters by using provided parameter builder.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterBuilderInterface $builder
     */
    public function buildParameters(ParameterBuilderInterface $builder)
    {
        $builder->add('field_identifier', ParameterType\IdentifierType::class);

        $this->buildCommonParameters($builder);
    }

    /**
     * Returns the array of dynamic parameters provided by this block definition.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     *
     * @return array
     */
    public function getDynamicParameters(Block $block)
    {
        return array(
            'content' => $this->contentProvider->provideContent(),
            'location' => $this->contentProvider->provideLocation(),
        );
    }
}
