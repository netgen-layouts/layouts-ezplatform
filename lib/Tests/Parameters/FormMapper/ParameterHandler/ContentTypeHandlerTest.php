<?php

namespace Netgen\BlockManager\Ez\Tests\Parameters\FormMapper\ParameterHandler;

use Netgen\BlockManager\Ez\Form\ContentTypeType;
use Netgen\BlockManager\Ez\Parameters\Parameter\ContentType as ContentTypeParameter;
use Netgen\BlockManager\Ez\Parameters\FormMapper\ParameterHandler\ContentTypeHandler;
use PHPUnit\Framework\TestCase;

class ContentTypeHandlerTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Ez\Parameters\FormMapper\ParameterHandler\ContentTypeHandler
     */
    protected $parameterHandler;

    public function setUp()
    {
        $this->parameterHandler = new ContentTypeHandler();
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Parameters\FormMapper\ParameterHandler\ContentTypeHandler::getFormType
     */
    public function testGetFormType()
    {
        $this->assertEquals(ContentTypeType::class, $this->parameterHandler->getFormType());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Parameters\FormMapper\ParameterHandler\ContentTypeHandler::convertOptions
     */
    public function testConvertOptions()
    {
        $this->assertEquals(
            array(
                'multiple' => true,
            ),
            $this->parameterHandler->convertOptions(
                new ContentTypeParameter(array('multiple' => true))
            )
        );
    }
}
