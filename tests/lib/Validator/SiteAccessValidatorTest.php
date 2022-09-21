<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Tests\Validator;

use Netgen\Layouts\Ibexa\Validator\Constraint\SiteAccess;
use Netgen\Layouts\Ibexa\Validator\SiteAccessValidator;
use Netgen\Layouts\Tests\TestCase\ValidatorTestCase;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class SiteAccessValidatorTest extends ValidatorTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->constraint = new SiteAccess();
    }

    /**
     * @covers \Netgen\Layouts\Ibexa\Validator\SiteAccessValidator::__construct
     * @covers \Netgen\Layouts\Ibexa\Validator\SiteAccessValidator::validate
     *
     * @dataProvider validateDataProvider
     */
    public function testValidate(?string $identifier, bool $isValid): void
    {
        $this->assertValid($isValid, $identifier);
    }

    /**
     * @covers \Netgen\Layouts\Ibexa\Validator\SiteAccessValidator::validate
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Expected argument of type "Netgen\\Layouts\\Ibexa\\Validator\\Constraint\\SiteAccess", "Symfony\\Component\\Validator\\Constraints\\NotBlank" given');

        $this->constraint = new NotBlank();
        $this->assertValid(true, 'value');
    }

    /**
     * @covers \Netgen\Layouts\Ibexa\Validator\SiteAccessValidator::validate
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValue(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessageMatches('/^Expected argument of type "string", "int(eger)?" given$/');

        $this->assertValid(true, 42);
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
