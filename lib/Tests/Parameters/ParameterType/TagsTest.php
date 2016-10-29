<?php

namespace Netgen\BlockManager\Ez\Tests\Parameters\ParameterType;

use Netgen\BlockManager\Ez\Tests\Validator\TagsServiceValidatorFactory;
use Netgen\TagsBundle\Core\Repository\TagsService;
use Netgen\BlockManager\Ez\Parameters\ParameterType\Tags as TagsType;
use Netgen\BlockManager\Ez\Parameters\ParameterDefinition\Tags;
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
     * @covers \Netgen\BlockManager\Ez\Parameters\ParameterDefinition\Tags::getType
     */
    public function testGetType()
    {
        $type = new TagsType();
        $this->assertEquals('eztags', $type->getType());
    }

    /**
     * Returns the parameter under test.
     *
     * @param array $options
     * @param bool $required
     * @param mixed $defaultValue
     *
     * @return \Netgen\BlockManager\Ez\Parameters\ParameterDefinition\Tags
     */
    public function getParameterDefinition(array $options = array(), $required = false, $defaultValue = null)
    {
        return new Tags($options, $required, $defaultValue);
    }

    /**
     * @param mixed $values
     * @param bool $required
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Ez\Parameters\ParameterDefinition\Tags::getValueConstraints
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

        $type = new TagsType();
        $parameterDefinition = $this->getParameterDefinition(array(), $required);
        $validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new TagsServiceValidatorFactory($this->tagsServiceMock))
            ->getValidator();

        $errors = $validator->validate($values, $type->getConstraints($parameterDefinition, $values));
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
