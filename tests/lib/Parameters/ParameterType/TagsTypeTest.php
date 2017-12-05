<?php

namespace Netgen\BlockManager\Ez\Tests\Parameters\ParameterType;

use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use Netgen\BlockManager\Ez\Parameters\ParameterType\TagsType;
use Netgen\BlockManager\Ez\Tests\Validator\TagsServiceValidatorFactory;
use Netgen\BlockManager\Tests\Parameters\Stubs\Parameter;
use Netgen\TagsBundle\Core\Repository\TagsService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

class TagsTypeTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $tagsServiceMock;

    public function setUp()
    {
        $this->tagsServiceMock = $this->createPartialMock(TagsService::class, array('loadTag'));
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Parameters\ParameterType\TagsType::getIdentifier
     */
    public function testGetIdentifier()
    {
        $type = new TagsType();
        $this->assertEquals('eztags', $type->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Parameters\ParameterType\TagsType::configureOptions
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
     * @covers \Netgen\BlockManager\Ez\Parameters\ParameterType\TagsType::configureOptions
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
     * @return \Netgen\BlockManager\Parameters\ParameterInterface
     */
    public function getParameter(array $options = array(), $required = false, $defaultValue = null)
    {
        return new Parameter(
            array(
                'name' => 'name',
                'type' => new TagsType(),
                'options' => $options,
                'isRequired' => $required,
                'defaultValue' => $defaultValue,
            )
        );
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
                array(
                ),
                array(
                    'max' => null,
                    'min' => null,
                    'allow_invalid' => false,
                ),
            ),
            array(
                array(
                    'max' => 5,
                ),
                array(
                    'max' => 5,
                    'min' => null,
                    'allow_invalid' => false,
                ),
            ),
            array(
                array(
                    'max' => null,
                ),
                array(
                    'max' => null,
                    'min' => null,
                    'allow_invalid' => false,
                ),
            ),
            array(
                array(
                    'min' => 5,
                ),
                array(
                    'min' => 5,
                    'max' => null,
                    'allow_invalid' => false,
                ),
            ),
            array(
                array(
                    'min' => null,
                ),
                array(
                    'max' => null,
                    'min' => null,
                    'allow_invalid' => false,
                ),
            ),
            array(
                array(
                    'min' => 5,
                    'max' => 10,
                    'allow_invalid' => false,
                ),
                array(
                    'min' => 5,
                    'max' => 10,
                    'allow_invalid' => false,
                ),
            ),
            array(
                array(
                    'min' => 5,
                    'max' => 3,
                ),
                array(
                    'min' => 5,
                    'max' => 5,
                    'allow_invalid' => false,
                ),
            ),
            array(
                array(
                    'allow_invalid' => false,
                ),
                array(
                    'min' => null,
                    'max' => null,
                    'allow_invalid' => false,
                ),
            ),
            array(
                array(
                    'allow_invalid' => true,
                ),
                array(
                    'min' => null,
                    'max' => null,
                    'allow_invalid' => true,
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
                    'min' => '0',
                ),
                array(
                    'min' => -5,
                ),
                array(
                    'min' => 0,
                ),
                array(
                    'max' => '0',
                ),
                array(
                    'max' => -5,
                ),
                array(
                    'max' => 0,
                ),
                array(
                    'allow_invalid' => 'false',
                ),
                array(
                    'allow_invalid' => 'true',
                ),
                array(
                    'allow_invalid' => 0,
                ),
                array(
                    'allow_invalid' => 1,
                ),
                array(
                    'undefined_value' => 'Value',
                ),
            ),
        );
    }

    /**
     * @param mixed $values
     * @param bool $required
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Ez\Parameters\ParameterType\TagsType::getValueConstraints
     * @dataProvider validationProvider
     */
    public function testValidation($values, $required, $isValid)
    {
        if ($values !== null) {
            foreach ($values as $i => $value) {
                if ($value !== null) {
                    $this->tagsServiceMock
                        ->expects($this->at($i))
                        ->method('loadTag')
                        ->with($this->equalTo($value))
                        ->will(
                            $this->returnCallback(
                                function () use ($value) {
                                    if (!is_int($value) || $value > 20) {
                                        throw new NotFoundException('tag', $value);
                                    }
                                }
                            )
                        );
                }
            }
        }

        $type = new TagsType();
        $parameter = $this->getParameter(array('min' => 1, 'max' => 3), $required);
        $validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new TagsServiceValidatorFactory($this->tagsServiceMock))
            ->getValidator();

        $errors = $validator->validate($values, $type->getConstraints($parameter, $values));
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
            array(array(12), false, true),
            array(array(12, 13, 14, 15), false, false),
            array(array(24), false, false),
            array(array(-12), false, false),
            array(array(0), false, false),
            array(array('12'), false, false),
            array(array(''), false, false),
            array(array(null), false, false),
            array(array(), false, false),
            array(null, false, true),
            array(array(12), true, true),
            array(array(12, 13, 14, 15), true, false),
            array(array(24), true, false),
            array(array(-12), true, false),
            array(array(0), true, false),
            array(array('12'), true, false),
            array(array(''), true, false),
            array(array(null), true, false),
            array(array(), true, false),
            array(null, true, false),
        );
    }
}
