<?php

namespace Netgen\BlockManager\Ez\Tests\Parameters\ParameterType;

use eZ\Publish\API\Repository\ContentTypeService;
use Netgen\BlockManager\Ez\Parameters\ParameterType\ContentTypeType;
use Netgen\BlockManager\Ez\Tests\Validator\RepositoryValidatorFactory;
use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use eZ\Publish\Core\Repository\Repository;
use Netgen\BlockManager\Tests\Parameters\Stubs\Parameter;
use Symfony\Component\Validator\Validation;
use PHPUnit\Framework\TestCase;

class ContentTypeTypeTest extends TestCase
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
     * @covers \Netgen\BlockManager\Ez\Parameters\ParameterType\ContentTypeType::getIdentifier
     */
    public function testGetIdentifier()
    {
        $type = new ContentTypeType();
        $this->assertEquals('ez_content_type', $type->getIdentifier());
    }

    /**
     * Returns the parameter under test.
     *
     * @param array $options
     * @param bool $required
     * @param mixed $defaultValue
     *
     * @return \Netgen\BlockManager\Parameters\ParameterInterface
     */
    public function getParameter(array $options = array(), $required = false, $defaultValue = null)
    {
        return new Parameter('name', new ContentTypeType(), $options, $required, $defaultValue);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Parameters\ParameterType\ContentTypeType::configureOptions
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
     * @covers \Netgen\BlockManager\Ez\Parameters\ParameterType\ContentTypeType::configureOptions
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

    /**
     * @param mixed $value
     * @param bool $required
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Ez\Parameters\ParameterType\ContentTypeType::getValueConstraints
     * @dataProvider validationProvider
     */
    public function testValidation($value, $required, $isValid)
    {
        $options = array();
        if ($value !== null) {
            $options = array('multiple' => is_array($value));
            foreach ((array)$value as $index => $identifier) {
                $this->contentTypeServiceMock
                    ->expects($this->at($index))
                    ->method('loadContentTypeByIdentifier')
                    ->with($this->equalTo($identifier))
                    ->will(
                        $this->returnCallback(
                            function () use ($identifier) {
                                if (!is_string($identifier) || !in_array($identifier, array('article', 'news'))) {
                                    throw new NotFoundException('content type', $identifier);
                                }
                            }
                        )
                    );
            }
        }

        $type = new ContentTypeType();
        $parameter = $this->getParameter($options, $required);
        $validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new RepositoryValidatorFactory($this->repositoryMock))
            ->getValidator();

        $errors = $validator->validate($value, $type->getConstraints($parameter, $value));
        $this->assertEquals($isValid, $errors->count() == 0);
    }

    /**
     * Provider for testing valid parameter values.
     *
     * @return array
     */
    public function validationProvider()
    {
        return array(
            array('news', false, true),
            array(array(), false, true),
            array(array('news'), false, true),
            array(array('article', 'news'), false, true),
            array(array('article', 'other'), false, false),
            array(array('other'), false, false),
            array(null, false, true),
            array('news', true, true),
            array(array(), true, false),
            array(array('news'), true, true),
            array(array('article', 'news'), true, true),
            array(array('article', 'other'), true, false),
            array(array('other'), true, false),
            array(null, true, false),
        );
    }
}
