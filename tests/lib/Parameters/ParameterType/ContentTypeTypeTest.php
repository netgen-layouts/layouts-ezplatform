<?php

namespace Netgen\BlockManager\Ez\Tests\Parameters\ParameterType;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use eZ\Publish\Core\Repository\Repository;
use eZ\Publish\Core\Repository\Values\ContentType\ContentType as EzContentType;
use Netgen\BlockManager\Ez\Parameters\ParameterType\ContentTypeType;
use Netgen\BlockManager\Ez\Tests\Validator\RepositoryValidatorFactory;
use Netgen\BlockManager\Parameters\ParameterDefinition;
use Netgen\BlockManager\Tests\Parameters\ParameterType\ParameterTypeTestTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

final class ContentTypeTypeTest extends TestCase
{
    use ParameterTypeTestTrait;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $repositoryMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $contentTypeServiceMock;

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

        $this->type = new ContentTypeType();
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Parameters\ParameterType\ContentTypeType::getIdentifier
     */
    public function testGetIdentifier()
    {
        $this->assertEquals('ez_content_type', $this->type->getIdentifier());
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
        $parameter = $this->getParameterDefinition($options);
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
        $this->getParameterDefinition($options);
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
                    'types' => array(),
                ),
            ),
            array(
                array(
                    'multiple' => false,
                ),
                array(
                    'multiple' => false,
                    'types' => array(),
                ),
            ),
            array(
                array(
                    'multiple' => true,
                ),
                array(
                    'multiple' => true,
                    'types' => array(),
                ),
            ),
            array(
                array(
                    'types' => array(),
                ),
                array(
                    'multiple' => false,
                    'types' => array(),
                ),
            ),
            array(
                array(
                    'types' => array(42),
                ),
                array(
                    'multiple' => false,
                    'types' => array(42),
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
            foreach ((array) $value as $index => $identifier) {
                $this->contentTypeServiceMock
                    ->expects($this->at($index))
                    ->method('loadContentTypeByIdentifier')
                    ->with($this->equalTo($identifier))
                    ->will(
                        $this->returnCallback(
                            function () use ($identifier) {
                                if (!is_string($identifier) || !in_array($identifier, array('article', 'news'), true)) {
                                    throw new NotFoundException('content type', $identifier);
                                }

                                return new EzContentType(
                                    array(
                                        'identifier' => $identifier,
                                    )
                                );
                            }
                        )
                    );
            }
        }

        $parameter = $this->getParameterDefinition($options, $required);
        $validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new RepositoryValidatorFactory($this->repositoryMock))
            ->getValidator();

        $errors = $validator->validate($value, $this->type->getConstraints($parameter, $value));
        $this->assertEquals($isValid, $errors->count() === 0);
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

    /**
     * @param mixed $value
     * @param mixed $convertedValue
     * @param bool $multiple
     *
     * @covers \Netgen\BlockManager\Ez\Parameters\ParameterType\ContentTypeType::fromHash
     * @dataProvider fromHashProvider
     */
    public function testFromHash($value, $convertedValue, $multiple)
    {
        $this->assertEquals(
            $convertedValue,
            $this->type->fromHash(
                $this->getParameterDefinition(
                    array(
                        'multiple' => $multiple,
                    )
                ),
                $value
            )
        );
    }

    public function fromHashProvider()
    {
        return array(
            array(
                null,
                null,
                false,
            ),
            array(
                array(),
                null,
                false,
            ),
            array(
                42,
                42,
                false,
            ),
            array(
                array(42, 43),
                42,
                false,
            ),
            array(
                null,
                null,
                true,
            ),
            array(
                array(),
                null,
                true,
            ),
            array(
                42,
                array(42),
                true,
            ),
            array(
                array(42, 43),
                array(42, 43),
                true,
            ),
        );
    }

    /**
     * @param mixed $value
     * @param bool $isEmpty
     *
     * @covers \Netgen\BlockManager\Ez\Parameters\ParameterType\ContentTypeType::isValueEmpty
     * @dataProvider emptyProvider
     */
    public function testIsValueEmpty($value, $isEmpty)
    {
        $this->assertEquals($isEmpty, $this->type->isValueEmpty(new ParameterDefinition(), $value));
    }

    /**
     * Provider for testing if the value is empty.
     *
     * @return array
     */
    public function emptyProvider()
    {
        return array(
            array(null, true),
            array(array(), true),
            array(42, false),
            array(array(42), false),
            array(0, false),
            array('42', false),
            array('', false),
        );
    }
}
