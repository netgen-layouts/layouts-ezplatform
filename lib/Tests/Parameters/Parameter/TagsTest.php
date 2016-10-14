<?php

namespace Netgen\BlockManager\Ez\Tests\Parameters\Parameter;

use Netgen\BlockManager\Ez\Tests\Validator\TagsServiceValidatorFactory;
use Netgen\TagsBundle\Core\Repository\TagsService;
use Netgen\BlockManager\Ez\Parameters\Parameter\Tags;
use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

class TagsTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $tagsServiceMock;

    public function setUp()
    {
        $this->tagsServiceMock = $this->createPartialMock(TagsService::class, array('loadTag'));
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Parameters\Parameter\Tags::getType
     */
    public function testGetType()
    {
        $parameter = $this->getParameter();
        $this->assertEquals('eztags', $parameter->getType());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Parameters\Parameter\Tags::getOptions
     * @covers \Netgen\BlockManager\Ez\Parameters\Parameter\Tags::configureOptions
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
     * @covers \Netgen\BlockManager\Ez\Parameters\Parameter\Tags::getOptions
     * @covers \Netgen\BlockManager\Ez\Parameters\Parameter\Tags::configureOptions
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
     * @return \Netgen\BlockManager\Ez\Parameters\Parameter\Tags
     */
    public function getParameter(array $options = array(), $required = false, $defaultValue = null)
    {
        return new Tags($options, $required, $defaultValue);
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
                ),
            ),
            array(
                array(
                    'max' => 5,
                ),
                array(
                    'max' => 5,
                    'min' => null,
                ),
            ),
            array(
                array(
                    'max' => null,
                ),
                array(
                    'max' => null,
                    'min' => null,
                ),
            ),
            array(
                array(
                    'min' => 5,
                ),
                array(
                    'min' => 5,
                    'max' => null,
                ),
            ),
            array(
                array(
                    'min' => null,
                ),
                array(
                    'max' => null,
                    'min' => null,
                ),
            ),
            array(
                array(
                    'min' => 5,
                    'max' => 10,
                ),
                array(
                    'min' => 5,
                    'max' => 10,
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
     * @covers \Netgen\BlockManager\Ez\Parameters\Parameter\Tags::getValueConstraints
     * @dataProvider validationProvider
     */
    public function testValidation($values, $required, $isValid)
    {
        if ($values !== null) {
            foreach ($values as $value) {
                if ($value !== null) {
                    $this->tagsServiceMock
                        ->expects($this->once())
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

        $parameter = $this->getParameter(array(), $required);
        $validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new TagsServiceValidatorFactory($this->tagsServiceMock))
            ->getValidator();

        $errors = $validator->validate($values, $parameter->getConstraints());
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
            array(array(12), false, true),
            array(array(24), false, false),
            array(array(-12), false, false),
            array(array(0), false, false),
            array(array('12'), false, false),
            array(array(''), false, false),
            array(array(null), false, false),
            array(null, false, true),
            array(array(12), true, true),
            array(array(24), true, false),
            array(array(-12), true, false),
            array(array(0), true, false),
            array(array('12'), true, false),
            array(array(''), true, false),
            array(array(null), true, false),
            array(null, true, false),
        );
    }
}
