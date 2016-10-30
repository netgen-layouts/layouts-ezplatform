<?php

namespace Netgen\BlockManager\Ez\Tests\Parameters\FormMapper\ParameterHandler;

use Netgen\BlockManager\Ez\Parameters\Parameter\Tags as TagsParameter;
use Netgen\BlockManager\Ez\Parameters\FormMapper\ParameterHandler\Tags;
use Netgen\Bundle\ContentBrowserBundle\Form\Type\ContentBrowserMultipleType;
use PHPUnit\Framework\TestCase;

class TagsTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Ez\Parameters\FormMapper\ParameterHandler\Tags
     */
    protected $parameterHandler;

    public function setUp()
    {
        $this->parameterHandler = new Tags();
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Parameters\FormMapper\ParameterHandler\Tags::getFormType
     */
    public function testGetFormType()
    {
        $this->assertEquals(ContentBrowserMultipleType::class, $this->parameterHandler->getFormType());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Parameters\FormMapper\ParameterHandler\Tags::convertOptions
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
