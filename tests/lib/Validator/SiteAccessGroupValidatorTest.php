<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Tests\Validator;

use Netgen\BlockManager\Ez\Validator\Constraint\SiteAccessGroup;
use Netgen\BlockManager\Ez\Validator\SiteAccessGroupValidator;
use Netgen\BlockManager\Tests\TestCase\ValidatorTestCase;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintValidatorInterface;

final class SiteAccessGroupValidatorTest extends ValidatorTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->constraint = new SiteAccessGroup();
    }

    public function getValidator(): ConstraintValidatorInterface
    {
        return new SiteAccessGroupValidator(
            [
                'frontend' => ['eng'],
                'backend' => ['admin'],
            ]
        );
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Validator\SiteAccessGroupValidator::__construct
     * @covers \Netgen\BlockManager\Ez\Validator\SiteAccessGroupValidator::validate
     * @dataProvider validateDataProvider
     */
    public function testValidate(?string $identifier, bool $isValid): void
    {
        $this->assertValid($isValid, $identifier);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Validator\SiteAccessGroupValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "Netgen\BlockManager\Ez\Validator\Constraint\SiteAccessGroup", "Symfony\Component\Validator\Constraints\NotBlank" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint(): void
    {
        $this->constraint = new NotBlank();
        $this->assertValid(true, 'value');
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Validator\SiteAccessGroupValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "string", "integer" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValue(): void
    {
        $this->assertValid(true, 42);
    }

    public function validateDataProvider(): array
    {
        return [
            ['frontend', true],
            ['other', false],
            [null, true],
        ];
    }
}
