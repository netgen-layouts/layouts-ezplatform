<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\Parameters\FormMapper\ParameterHandler;

use Netgen\Bundle\EzPublishBlockManagerBundle\Parameters\Parameter\Location as LocationParameter;
use Netgen\Bundle\EzPublishBlockManagerBundle\Parameters\FormMapper\ParameterHandler\Location;
use Netgen\BlockManager\Parameters\FormMapper\ParameterHandler;
use Netgen\Bundle\ContentBrowserBundle\Form\Type\ContentBrowserType;
use PHPUnit\Framework\TestCase;

class LocationTest extends TestCase
{
    /**
     * @var \Netgen\Bundle\EzPublishBlockManagerBundle\Parameters\FormMapper\ParameterHandler\Location
     */
    protected $parameterHandler;

    public function setUp()
    {
        $this->parameterHandler = new Location();
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Parameters\FormMapper\ParameterHandler\Location::getFormType
     */
    public function testGetFormType()
    {
        self::assertEquals(ContentBrowserType::class, $this->parameterHandler->getFormType());
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Parameters\FormMapper\ParameterHandler\Location::convertOptions
     */
    public function testConvertOptions()
    {
        self::assertEquals(
            array(
                'value_type' => 'ezlocation',
                'config_name' => 'ezlocation',
            ),
            $this->parameterHandler->convertOptions(new LocationParameter())
        );
    }
}
