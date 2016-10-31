<?php

namespace Netgen\BlockManager\Ez\Tests\Parameters\FormMapper\ParameterHandler;

use Netgen\BlockManager\Ez\Parameters\Parameter\Tags as TagsParameter;
use Netgen\BlockManager\Ez\Parameters\FormMapper\ParameterHandler\TagsHandler;
use Netgen\Bundle\ContentBrowserBundle\Form\Type\ContentBrowserMultipleType;
use PHPUnit\Framework\TestCase;

class TagsHandlerTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Ez\Parameters\FormMapper\ParameterHandler\TagsHandler
     */
    protected $parameterHandler;

    public function setUp()
    {
        $this->parameterHandler = new TagsHandler();
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Parameters\FormMapper\ParameterHandler\TagsHandler::getFormType
     */
    public function testGetFormType()
    {
        $this->assertEquals(ContentBrowserMultipleType::class, $this->parameterHandler->getFormType());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Parameters\FormMapper\ParameterHandler\TagsHandler::convertOptions
     */
    public function testConvertOptions()
    {
        $this->assertEquals(
            array(
                'item_type' => 'eztags',
            ),
            $this->parameterHandler->convertOptions(new TagsParameter())
        );
    }
}
