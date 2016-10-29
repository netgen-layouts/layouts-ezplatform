<?php

namespace Netgen\BlockManager\Ez\Tests\Parameters\ParameterDefinition;

use eZ\Publish\API\Repository\ContentTypeService;
use Netgen\BlockManager\Ez\Parameters\ParameterDefinition\ContentType;
use eZ\Publish\Core\Repository\Repository;
use PHPUnit\Framework\TestCase;

class ContentTypeTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $repositoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $contentTypeServiceMock;

    public function setUp()
    {
        $this->contentTypeServiceMock = $this->createMock(ContentTypeService::class);
        $this->repositoryMock = $this->createPartialMock(Repository::class, array('sudo', 'getContentTypeService'));

        $this->repositoryMock
            ->expects($this->any())
            ->method('sudo')
            ->with($this->anything())
            ->will($this->returnCallback(function ($callback) {
                return $callback($this->repositoryMock);
            }));

        $this->repositoryMock
            ->expects($this->any())
            ->method('getContentTypeService')
            ->will($this->returnValue($this->contentTypeServiceMock));
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Parameters\ParameterDefinition\ContentType::getType
     */
    public function testGetType()
    {
        $parameterDefinition = $this->getParameterDefinition();
        $this->assertEquals('ez_content_type', $parameterDefinition->getType());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Parameters\ParameterDefinition\ContentType::getOptions
     * @covers \Netgen\BlockManager\Ez\Parameters\ParameterDefinition\ContentType::configureOptions
     * @dataProvider validOptionsProvider
     *
     * @param array $options
     * @param array $resolvedOptions
     */
    public function testValidOptions($options, $resolvedOptions)
    {
        $parameterDefinition = $this->getParameterDefinition($options);
        $this->assertEquals($resolvedOptions, $parameterDefinition->getOptions());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Parameters\ParameterDefinition\ContentType::getOptions
     * @covers \Netgen\BlockManager\Ez\Parameters\ParameterDefinition\ContentType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidArgumentException
     * @dataProvider invalidOptionsProvider
     *
     * @param array $options
     */
    public function testInvalidOptions($options)
    {
        $this->getParameterDefinition($options);
    }

    /**
     * Returns the parameter under test.
     *
     * @param array $options
     * @param bool $required
     * @param mixed $defaultValue
     *
     * @return \Netgen\BlockManager\Ez\Parameters\ParameterDefinition\ContentType
     */
    public function getParameterDefinition(array $options = array(), $required = false, $defaultValue = null)
    {
        return new ContentType($options, $required, $defaultValue);
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
                array(
                    'multiple' => false,
                ),
            ),
            array(
                array(
                    'multiple' => false,
                ),
                array(
                    'multiple' => false,
                ),
            ),
            array(
                array(
                    'multiple' => true,
                ),
                array(
                    'multiple' => true,
                ),
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
                    'multiple' => 'true',
                ),
                array(
                    'undefined_value' => 'Value',
                ),
            ),
        );
    }
}
