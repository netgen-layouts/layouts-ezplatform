<?php

namespace Netgen\BlockManager\Ez\Tests\Parameters\Parameter;

use Netgen\BlockManager\Ez\Parameters\Parameter\Location;
use Netgen\BlockManager\Ez\Tests\Validator\RepositoryValidatorFactory;
use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use eZ\Publish\Core\Repository\Repository;
use Symfony\Component\Validator\Validation;
use PHPUnit\Framework\TestCase;

class LocationTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Ez\Parameters\Parameter\Location::getType
     */
    public function testGetType()
    {
        $parameter = $this->getParameter();
        self::assertEquals('ezlocation', $parameter->getType());
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
        self::assertEquals($resolvedOptions, $parameter->getOptions());
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

    /**
     * @param mixed $value
     * @param bool $required
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Ez\Parameters\Parameter\Location::getValueConstraints
     * @dataProvider validationProvider
     */
    public function testValidation($value, $required, $isValid)
    {
        $repositoryMock = $this->createMock(Repository::class);
        if ($value !== null) {
            $repositoryMock
                ->expects($this->once())
                ->method('sudo')
                ->will(
                    $this->returnCallback(
                        function () use ($value) {
                            if (!is_int($value) || $value > 20) {
                                throw new NotFoundException('location', $value);
                            }
                        }
                    )
                );
        }

        $parameter = $this->getParameter(array(), $required);
        $validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new RepositoryValidatorFactory($repositoryMock))
            ->getValidator();

        $errors = $validator->validate($value, $parameter->getConstraints());
        self::assertEquals($isValid, $errors->count() == 0);
    }

    /**
     * Provider for testing valid parameter values.
     *
     * @return array
     */
    public function validationProvider()
    {
        return array(
            array(12, false, true),
            array(24, false, false),
            array(-12, false, false),
            array(0, false, false),
            array('12', false, false),
            array('', false, false),
            array(null, false, true),
            array(12, true, true),
            array(24, true, false),
            array(-12, true, false),
            array(0, true, false),
            array('12', true, false),
            array('', true, false),
            array(null, true, false),
        );
    }
}
