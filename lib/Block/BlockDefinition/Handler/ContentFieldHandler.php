<?php

namespace Netgen\BlockManager\Ez\Block\BlockDefinition\Handler;

use Netgen\BlockManager\Ez\Block\BlockDefinition\ContentFieldDefinitionHandler;
use Netgen\BlockManager\Parameters\Parameter;

class ContentFieldHandler extends ContentFieldDefinitionHandler
{
    /**
     * Returns the array specifying block parameters.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterInterface[]
     */
    public function getParameters()
    {
        return array(
            'field_identifier' => new Parameter\Identifier(),
        ) + $this->getCommonParameters();
    }

    /**
     * Returns the name of the parameter which will provide the field identifier.
     *
     * @return string
     */
    public function getFieldIdentifierParameter()
    {
        return 'field_identifier';
    }
}
