<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Tests\Validator;

use Netgen\BlockManager\Ez\Validator\Constraint\SiteAccess;
use Netgen\BlockManager\Ez\Validator\SiteAccessValidator;
use Netgen\BlockManager\Tests\TestCase\ValidatorTestCase;
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
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Expected argument of type "Netgen\\BlockManager\\Ez\\Validator\\Constraint\\SiteAccess", "Symfony\\Component\\Validator\\Constraints\\NotBlank" given');

        $this->constraint = new NotBlank();
        self::assertValid(true, 'value');
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Validator\SiteAccessValidator::validate
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
