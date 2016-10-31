<?php

namespace Netgen\BlockManager\Ez\Tests\Parameters\FormMapper\ParameterHandler;

use Netgen\BlockManager\Ez\Parameters\Parameter\Location as LocationParameter;
use Netgen\BlockManager\Ez\Parameters\FormMapper\ParameterHandler\LocationHandler;
use Netgen\Bundle\ContentBrowserBundle\Form\Type\ContentBrowserType;
use PHPUnit\Framework\TestCase;

class LocationHandlerTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Ez\Parameters\FormMapper\ParameterHandler\LocationHandler
     */
    protected $parameterHandler;

    public function setUp()
    {
        $this->parameterHandler = new LocationHandler();
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Parameters\FormMapper\ParameterHandler\LocationHandler::getFormType
     */
    public function testGetFormType()
    {
        $this->assertEquals(ContentBrowserType::class, $this->parameterHandler->getFormType());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Parameters\FormMapper\ParameterHandler\LocationHandler::convertOptions
     */
    public function testConvertOptions()
    {
        $this->assertEquals(
            array(
                'item_type' => 'ezlocation',
                'required' => false,
            ),
            $this->parameterHandler->convertOptions(new LocationParameter())
        );
    }
}
