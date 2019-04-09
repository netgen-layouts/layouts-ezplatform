<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Tests\Validator;

use Netgen\BlockManager\Tests\TestCase\ValidatorTestCase;
use Netgen\Layouts\Ez\Validator\Constraint\SiteAccess;
use Netgen\Layouts\Ez\Validator\SiteAccessValidator;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class SiteAccessValidatorTest extends ValidatorTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->constraint = new SiteAccess();
    }

    /**
     * @covers \Netgen\Layouts\Ez\Validator\SiteAccessValidator::__construct
     * @covers \Netgen\Layouts\Ez\Validator\SiteAccessValidator::validate
     * @dataProvider validateDataProvider
     */
    public function testValidate(?string $identifier, bool $isValid): void
    {
        self::assertValid($isValid, $identifier);
    }

    /**
     * @covers \Netgen\Layouts\Ez\Validator\SiteAccessValidator::validate
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Expected argument of type "Netgen\\Layouts\\Ez\\Validator\\Constraint\\SiteAccess", "Symfony\\Component\\Validator\\Constraints\\NotBlank" given');

        $this->constraint = new NotBlank();
        self::assertValid(true, 'value');
    }

    /**
     * @covers \Netgen\Layouts\Ez\Validator\SiteAccessValidator::validate
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValue(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Expected argument of type "string", "integer" given');

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

    protected function getValidator(): ConstraintValidatorInterface
    {
        return new SiteAccessValidator(['eng', 'cro']);
    }
}
