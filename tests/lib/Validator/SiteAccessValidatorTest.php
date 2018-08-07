<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Tests\Validator;

use Netgen\BlockManager\Ez\Validator\Constraint\SiteAccess;
use Netgen\BlockManager\Ez\Validator\SiteAccessValidator;
use Netgen\BlockManager\Tests\TestCase\ValidatorTestCase;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintValidatorInterface;

final class SiteAccessValidatorTest extends ValidatorTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->constraint = new SiteAccess();
    }

    public function getValidator(): ConstraintValidatorInterface
    {
        return new SiteAccessValidator(['eng', 'cro']);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Validator\SiteAccessValidator::__construct
     * @covers \Netgen\BlockManager\Ez\Validator\SiteAccessValidator::validate
     * @dataProvider validateDataProvider
     */
    public function testValidate(?string $identifier, bool $isValid): void
    {
        self::assertValid($isValid, $identifier);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Validator\SiteAccessValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "Netgen\BlockManager\Ez\Validator\Constraint\SiteAccess", "Symfony\Component\Validator\Constraints\NotBlank" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint(): void
    {
        $this->constraint = new NotBlank();
        self::assertValid(true, 'value');
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Validator\SiteAccessValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "string", "integer" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValue(): void
    {
        self::assertValid(true, 42);
    }

    public function validateDataProvider(): array
    {
        return [
            ['eng', true],
            ['fre', false],
            [null, true],
        ];
    }
}
