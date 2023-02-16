<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Tests\Validator;

use Netgen\Layouts\Ibexa\Validator\Constraint\SiteAccessGroup;
use Netgen\Layouts\Ibexa\Validator\SiteAccessGroupValidator;
use Netgen\Layouts\Tests\TestCase\ValidatorTestCase;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class SiteAccessGroupValidatorTest extends ValidatorTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->constraint = new SiteAccessGroup();
    }

    /**
     * @covers \Netgen\Layouts\Ibexa\Validator\SiteAccessGroupValidator::__construct
     * @covers \Netgen\Layouts\Ibexa\Validator\SiteAccessGroupValidator::validate
     *
     * @dataProvider validateDataProvider
     */
    public function testValidate(?string $identifier, bool $isValid): void
    {
        $this->assertValid($isValid, $identifier);
    }

    /**
     * @covers \Netgen\Layouts\Ibexa\Validator\SiteAccessGroupValidator::validate
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Expected argument of type "Netgen\\Layouts\\Ibexa\\Validator\\Constraint\\SiteAccessGroup", "Symfony\\Component\\Validator\\Constraints\\NotBlank" given');

        $this->constraint = new NotBlank();
        $this->assertValid(true, 'value');
    }

    /**
     * @covers \Netgen\Layouts\Ibexa\Validator\SiteAccessGroupValidator::validate
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValue(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessageMatches('/^Expected argument of type "string", "int(eger)?" given$/');

        $this->assertValid(true, 42);
    }

    public static function validateDataProvider(): array
    {
        return [
            ['frontend', true],
            ['other', false],
            [null, true],
        ];
    }

    protected function getValidator(): ConstraintValidatorInterface
    {
        return new SiteAccessGroupValidator(
            [
                'frontend' => ['eng'],
                'backend' => ['admin'],
            ],
        );
    }
}
