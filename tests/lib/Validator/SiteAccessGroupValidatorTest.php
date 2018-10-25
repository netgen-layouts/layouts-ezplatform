<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Tests\Validator;

use Netgen\BlockManager\Ez\Validator\Constraint\SiteAccessGroup;
use Netgen\BlockManager\Ez\Validator\SiteAccessGroupValidator;
use Netgen\BlockManager\Tests\TestCase\ValidatorTestCase;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class SiteAccessGroupValidatorTest extends ValidatorTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->constraint = new SiteAccessGroup();
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Validator\SiteAccessGroupValidator::__construct
     * @covers \Netgen\BlockManager\Ez\Validator\SiteAccessGroupValidator::validate
     * @dataProvider validateDataProvider
     */
    public function testValidate(?string $identifier, bool $isValid): void
    {
        self::assertValid($isValid, $identifier);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Validator\SiteAccessGroupValidator::validate
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Expected argument of type "Netgen\\BlockManager\\Ez\\Validator\\Constraint\\SiteAccessGroup", "Symfony\\Component\\Validator\\Constraints\\NotBlank" given');

        $this->constraint = new NotBlank();
        self::assertValid(true, 'value');
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Validator\SiteAccessGroupValidator::validate
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
            ]
        );
    }
}
