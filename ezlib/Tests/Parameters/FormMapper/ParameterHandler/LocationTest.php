<?php

namespace Netgen\BlockManager\Ez\Tests\Parameters\FormMapper\ParameterHandler;

use Netgen\BlockManager\Ez\Parameters\Parameter\Location as LocationParameter;
use Netgen\BlockManager\Ez\Parameters\FormMapper\ParameterHandler\Location;
use Netgen\BlockManager\Parameters\FormMapper\ParameterHandler;
use Netgen\Bundle\ContentBrowserBundle\Form\Type\ContentBrowserType;
use PHPUnit\Framework\TestCase;

class LocationTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Ez\Parameters\FormMapper\ParameterHandler\Location
     */
    protected $parameterHandler;

    public function setUp()
    {
        $this->parameterHandler = new Location();
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Parameters\FormMapper\ParameterHandler\Location::getFormType
     */
    public function testGetFormType()
    {
        $this->assertEquals(ContentBrowserType::class, $this->parameterHandler->getFormType());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Parameters\FormMapper\ParameterHandler\Location::convertOptions
     */
    public function testConvertOptions()
    {
        $this->assertEquals(
            array(
                'value_type' => 'ezlocation',
                'config_name' => 'ezlocation',
            ),
            $this->parameterHandler->convertOptions(new LocationParameter())
        );
    }
}
