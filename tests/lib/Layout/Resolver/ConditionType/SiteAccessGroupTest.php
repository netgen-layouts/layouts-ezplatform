<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Tests\Layout\Resolver\ConditionType;

use Ibexa\Core\MVC\Symfony\SiteAccess as IbexaSiteAccess;
use Netgen\Layouts\Ibexa\Layout\Resolver\ConditionType\SiteAccessGroup;
use Netgen\Layouts\Ibexa\Tests\Validator\ValidatorFactory;
use Netgen\Layouts\Tests\TestCase\ValidatorFactory as BaseValidatorFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validation;

#[CoversClass(SiteAccessGroup::class)]
final class SiteAccessGroupTest extends TestCase
{
    private SiteAccessGroup $conditionType;

    protected function setUp(): void
    {
        $this->conditionType = new SiteAccessGroup(
            [
                'eng' => [
                    'frontend',
                ],
                'admin' => [
                    'backend',
                ],
            ],
        );
    }

    public function testGetType(): void
    {
        self::assertSame('ibexa_site_access_group', $this->conditionType::getType());
    }

    #[DataProvider('validationDataProvider')]
    public function testValidation(mixed $value, bool $isValid): void
    {
        $validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new ValidatorFactory($this, new BaseValidatorFactory($this)))
            ->getValidator();

        $errors = $validator->validate($value, $this->conditionType->getConstraints());
        self::assertSame($isValid, $errors->count() === 0);
    }

    #[DataProvider('matchesDataProvider')]
    public function testMatches(mixed $value, bool $matches): void
    {
        $request = Request::create('/');
        $request->attributes->set('siteaccess', new IbexaSiteAccess('eng'));

        self::assertSame($matches, $this->conditionType->matches($request, $value));
    }

    public function testMatchesWithSiteAccessWithNoGroups(): void
    {
        $request = Request::create('/');
        $request->attributes->set('siteaccess', new IbexaSiteAccess('cro'));

        self::assertFalse($this->conditionType->matches($request, ['frontend']));
    }

    public function testMatchesWithNoSiteAccess(): void
    {
        $request = Request::create('/');

        self::assertFalse($this->conditionType->matches($request, ['frontend']));
    }

    /**
     * Provider for testing condition type validation.
     */
    public static function validationDataProvider(): iterable
    {
        return [
            [['frontend'], true],
            [['backend', 'frontend'], true],
            [['frontend', 'unknown'], false],
            [['unknown'], false],
            [[], false],
            [null, false],
        ];
    }

    public static function matchesDataProvider(): iterable
    {
        return [
            ['not_array', false],
            [[], false],
            [['frontend'], true],
            [['backend'], false],
            [['frontend', 'backend'], true],
            [['frontend', 'other'], true],
            [['backend', 'other'], false],
        ];
    }
}
