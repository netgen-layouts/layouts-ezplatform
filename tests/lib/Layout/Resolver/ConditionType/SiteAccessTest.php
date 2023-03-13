<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Tests\Layout\Resolver\ConditionType;

use Ibexa\Core\MVC\Symfony\SiteAccess as IbexaSiteAccess;
use Netgen\Layouts\Ibexa\Layout\Resolver\ConditionType\SiteAccess;
use Netgen\Layouts\Ibexa\Tests\Validator\ValidatorFactory;
use Netgen\Layouts\Tests\TestCase\ValidatorFactory as BaseValidatorFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validation;

#[CoversClass(SiteAccess::class)]
final class SiteAccessTest extends TestCase
{
    private SiteAccess $conditionType;

    protected function setUp(): void
    {
        $this->conditionType = new SiteAccess();
    }

    public function testGetType(): void
    {
        self::assertSame('ibexa_site_access', $this->conditionType::getType());
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

    public function testMatchesWithNoSiteAccess(): void
    {
        $request = Request::create('/');

        self::assertFalse($this->conditionType->matches($request, ['eng']));
    }

    /**
     * Provider for testing condition type validation.
     */
    public static function validationDataProvider(): array
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

    public static function matchesDataProvider(): array
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
