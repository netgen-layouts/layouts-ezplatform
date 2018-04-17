<?php

namespace Netgen\BlockManager\Ez\Tests\Validator;

use Netgen\BlockManager\Ez\Validator\Constraint\SiteAccess;
use Netgen\BlockManager\Ez\Validator\SiteAccessValidator;
use Netgen\BlockManager\Tests\TestCase\ValidatorTestCase;
use Symfony\Component\Validator\Constraints\NotBlank;

final class SiteAccessValidatorTest extends ValidatorTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->constraint = new SiteAccess();
    }

    /**
     * @return \Symfony\Component\Validator\ConstraintValidatorInterface
     */
    public function getValidator()
    {
        return new SiteAccessValidator(['eng', 'cro']);
    }

    /**
     * @param int $identifier
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Ez\Validator\SiteAccessValidator::__construct
     * @covers \Netgen\BlockManager\Ez\Validator\SiteAccessValidator::validate
     * @dataProvider validateDataProvider
     */
    public function testValidate($identifier, $isValid)
    {
        $this->assertValid($isValid, $identifier);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Validator\SiteAccessValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "Netgen\BlockManager\Ez\Validator\Constraint\SiteAccess", "Symfony\Component\Validator\Constraints\NotBlank" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint()
    {
        $this->constraint = new NotBlank();
        $this->assertValid(true, 'value');
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Validator\SiteAccessValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "string", "integer" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValue()
    {
        $this->assertValid(true, 42);
    }

    public function validateDataProvider()
    {
        return [
            ['eng', true],
            ['fre', false],
            [null, true],
        ];
    }
}
