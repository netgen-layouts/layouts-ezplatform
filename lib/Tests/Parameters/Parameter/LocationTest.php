<?php

namespace Netgen\BlockManager\Ez\Tests\Parameters\Parameter;

use eZ\Publish\API\Repository\LocationService;
use Netgen\BlockManager\Ez\Parameters\Parameter\Location;
use eZ\Publish\Core\Repository\Repository;
use PHPUnit\Framework\TestCase;

class LocationTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $repositoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $locationServiceMock;

    public function setUp()
    {
        $this->locationServiceMock = $this->createMock(LocationService::class);
        $this->repositoryMock = $this->createPartialMock(Repository::class, array('sudo', 'getLocationService'));

        $this->repositoryMock
            ->expects($this->any())
            ->method('sudo')
            ->with($this->anything())
            ->will($this->returnCallback(function ($callback) {
                return $callback($this->repositoryMock);
            }));

        $this->repositoryMock
            ->expects($this->any())
            ->method('getLocationService')
            ->will($this->returnValue($this->locationServiceMock));
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Parameters\Parameter\Location::getType
     */
    public function testGetType()
    {
        $parameter = $this->getParameter();
        $this->assertEquals('ezlocation', $parameter->getType());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Parameters\Parameter\Location::getOptions
     * @covers \Netgen\BlockManager\Ez\Parameters\Parameter\Location::configureOptions
     * @dataProvider validOptionsProvider
     *
     * @param array $options
     * @param array $resolvedOptions
     */
    public function testValidOptions($options, $resolvedOptions)
    {
        $parameter = $this->getParameter($options);
        $this->assertEquals($resolvedOptions, $parameter->getOptions());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Parameters\Parameter\Location::getOptions
     * @covers \Netgen\BlockManager\Ez\Parameters\Parameter\Location::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidArgumentException
     * @dataProvider invalidOptionsProvider
     *
     * @param array $options
     */
    public function testInvalidOptions($options)
    {
        $this->getParameter($options);
    }

    /**
     * Returns the parameter under test.
     *
     * @param array $options
     * @param bool $required
     * @param mixed $defaultValue
     *
     * @return \Netgen\BlockManager\Ez\Parameters\Parameter\Location
     */
    public function getParameter(array $options = array(), $required = false, $defaultValue = null)
    {
        return new Location($options, $required, $defaultValue);
    }

    /**
     * Provider for testing valid parameter attributes.
     *
     * @return array
     */
    public function validOptionsProvider()
    {
        return array(
            array(
                array(),
                array(),
            ),
        );
    }

    /**
     * Provider for testing invalid parameter attributes.
     *
     * @return array
     */
    public function invalidOptionsProvider()
    {
        return array(
            array(
                array(
                    'undefined_value' => 'Value',
                ),
            ),
        );
    }
}
