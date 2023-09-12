<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Tests\Validator;

use Netgen\Layouts\Ibexa\Validator\Constraint\SiteAccess;
use Netgen\Layouts\Ibexa\Validator\SiteAccessValidator;
use Netgen\Layouts\Tests\TestCase\ValidatorTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

#[CoversClass(SiteAccessValidator::class)]
final class SiteAccessValidatorTest extends ValidatorTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->constraint = new SiteAccess();
    }

    #[DataProvider('validateDataProvider')]
    public function testValidate(?string $identifier, bool $isValid): void
    {
        $this->assertValid($isValid, $identifier);
    }

    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Expected argument of type "Netgen\\Layouts\\Ibexa\\Validator\\Constraint\\SiteAccess", "Symfony\\Component\\Validator\\Constraints\\NotBlank" given');

        $this->constraint = new NotBlank();
        $this->assertValid(true, 'value');
    }

    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValue(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessageMatches('/^Expected argument of type "string", "int(eger)?" given$/');

        $this->assertValid(true, 42);
    }

    public static function validateDataProvider(): iterable
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
