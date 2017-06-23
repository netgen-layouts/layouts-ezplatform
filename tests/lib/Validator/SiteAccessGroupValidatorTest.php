<?php

namespace Netgen\BlockManager\Ez\Tests\Validator;

use Netgen\BlockManager\Ez\Validator\Constraint\SiteAccessGroup;
use Netgen\BlockManager\Ez\Validator\SiteAccessGroupValidator;
use Netgen\BlockManager\Tests\TestCase\ValidatorTestCase;
use Symfony\Component\Validator\Constraints\NotBlank;

class SiteAccessGroupValidatorTest extends ValidatorTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->constraint = new SiteAccessGroup();
    }

    /**
     * @return \Symfony\Component\Validator\ConstraintValidatorInterface
     */
    public function getValidator()
    {
        return new SiteAccessGroupValidator(
            array(
                'frontend' => array('eng'),
                'backend' => array('admin'),
            )
        );
    }

    /**
     * @param int $identifier
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Ez\Validator\SiteAccessGroupValidator::__construct
     * @covers \Netgen\BlockManager\Ez\Validator\SiteAccessGroupValidator::validate
     * @dataProvider validateDataProvider
     */
    public function testValidate($identifier, $isValid)
    {
        $this->assertValid($isValid, $identifier);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Validator\SiteAccessGroupValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "Netgen\BlockManager\Ez\Validator\Constraint\SiteAccessGroup", "Symfony\Component\Validator\Constraints\NotBlank" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint()
    {
        $this->constraint = new NotBlank();
        $this->assertValid(true, 'value');
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Validator\SiteAccessGroupValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "string", "integer" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValue()
    {
        $this->assertValid(true, 42);
    }

    public function validateDataProvider()
    {
        return array(
            array('frontend', true),
            array('other', false),
            array(null, true),
        );
    }
}
