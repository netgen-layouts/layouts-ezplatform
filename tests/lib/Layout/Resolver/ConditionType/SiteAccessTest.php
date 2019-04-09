<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Tests\Layout\Resolver\ConditionType;

use eZ\Publish\Core\MVC\Symfony\SiteAccess as EzSiteAccess;
use Netgen\Layouts\Ez\Layout\Resolver\ConditionType\SiteAccess;
use Netgen\Layouts\Ez\Tests\Validator\ValidatorFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validation;

final class SiteAccessTest extends TestCase
{
    /**
     * @var \Netgen\Layouts\Ez\Layout\Resolver\ConditionType\SiteAccess
     */
    private $conditionType;

    public function setUp(): void
    {
        $this->conditionType = new SiteAccess();
    }

    /**
     * @covers \Netgen\Layouts\Ez\Layout\Resolver\ConditionType\SiteAccess::getType
     */
    public function testGetType(): void
    {
        self::assertSame('ez_site_access', $this->conditionType::getType());
    }

    /**
     * @param mixed $value
     * @param bool $isValid
     *
     * @covers \Netgen\Layouts\Ez\Layout\Resolver\ConditionType\SiteAccess::getConstraints
     * @dataProvider validationProvider
     */
    public function testValidation($value, bool $isValid): void
    {
        $validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new ValidatorFactory())
            ->getValidator();

        $errors = $validator->validate($value, $this->conditionType->getConstraints());
        self::assertSame($isValid, $errors->count() === 0);
    }

    /**
     * @covers \Netgen\Layouts\Ez\Layout\Resolver\ConditionType\SiteAccess::matches
     *
     * @param mixed $value
     * @param bool $matches
     *
     * @dataProvider matchesProvider
     */
    public function testMatches($value, bool $matches): void
    {
        $request = Request::create('/');
        $request->attributes->set('siteaccess', new EzSiteAccess('eng'));

        self::assertSame($matches, $this->conditionType->matches($request, $value));
    }

    /**
     * @covers \Netgen\Layouts\Ez\Layout\Resolver\ConditionType\SiteAccess::matches
     */
    public function testMatchesWithNoSiteAccess(): void
    {
        $request = Request::create('/');

        self::assertFalse($this->conditionType->matches($request, ['eng']));
    }

    /**
     * Provider for testing condition type validation.
     */
    public function validationProvider(): array
    {
        return [
            [['cro'], true],
            [['cro', 'eng'], true],
            [['cro', 'unknown'], false],
            [['unknown'], false],
            [[], false],
            [null, false],
        ];
    }

    public function matchesProvider(): array
    {
        return [
            ['not_array', false],
            [[], false],
            [['eng'], true],
            [['cro'], false],
            [['eng', 'cro'], true],
            [['cro', 'eng'], true],
            [['cro', 'fre'], false],
        ];
    }
}
