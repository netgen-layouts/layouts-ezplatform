<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Tests\Layout\Resolver\ConditionType;

use eZ\Publish\Core\MVC\Symfony\SiteAccess as EzSiteAccess;
use Netgen\Layouts\Ez\Layout\Resolver\ConditionType\SiteAccessGroup;
use Netgen\Layouts\Ez\Tests\Validator\ValidatorFactory;
use Netgen\Layouts\Tests\TestCase\ValidatorFactory as BaseValidatorFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validation;

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

    /**
     * @covers \Netgen\Layouts\Ez\Layout\Resolver\ConditionType\SiteAccessGroup::__construct
     * @covers \Netgen\Layouts\Ez\Layout\Resolver\ConditionType\SiteAccessGroup::getType
     */
    public function testGetType(): void
    {
        self::assertSame('ez_site_access_group', $this->conditionType::getType());
    }

    /**
     * @param mixed $value
     *
     * @covers \Netgen\Layouts\Ez\Layout\Resolver\ConditionType\SiteAccessGroup::getConstraints
     *
     * @dataProvider validationDataProvider
     */
    public function testValidation($value, bool $isValid): void
    {
        $validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new ValidatorFactory($this, new BaseValidatorFactory($this)))
            ->getValidator();

        $errors = $validator->validate($value, $this->conditionType->getConstraints());
        self::assertSame($isValid, $errors->count() === 0);
    }

    /**
     * @covers \Netgen\Layouts\Ez\Layout\Resolver\ConditionType\SiteAccessGroup::matches
     *
     * @param mixed $value
     *
     * @dataProvider matchesDataProvider
     */
    public function testMatches($value, bool $matches): void
    {
        $request = Request::create('/');
        $request->attributes->set('siteaccess', new EzSiteAccess('eng'));

        self::assertSame($matches, $this->conditionType->matches($request, $value));
    }

    /**
     * @covers \Netgen\Layouts\Ez\Layout\Resolver\ConditionType\SiteAccessGroup::matches
     */
    public function testMatchesWithSiteAccessWithNoGroups(): void
    {
        $request = Request::create('/');
        $request->attributes->set('siteaccess', new EzSiteAccess('cro'));

        self::assertFalse($this->conditionType->matches($request, ['frontend']));
    }

    /**
     * @covers \Netgen\Layouts\Ez\Layout\Resolver\ConditionType\SiteAccessGroup::matches
     */
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
