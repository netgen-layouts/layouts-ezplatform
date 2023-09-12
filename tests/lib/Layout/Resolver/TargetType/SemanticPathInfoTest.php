<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Tests\Layout\Resolver\TargetType;

use Netgen\Layouts\Ibexa\Layout\Resolver\TargetType\SemanticPathInfo;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validation;

#[CoversClass(SemanticPathInfo::class)]
final class SemanticPathInfoTest extends TestCase
{
    private SemanticPathInfo $targetType;

    protected function setUp(): void
    {
        $this->targetType = new SemanticPathInfo();
    }

    public function testGetType(): void
    {
        self::assertSame('ibexa_semantic_path_info', $this->targetType::getType());
    }

    #[DataProvider('validationDataProvider')]
    public function testValidation(mixed $value, bool $isValid): void
    {
        $validator = Validation::createValidator();

        $errors = $validator->validate($value, $this->targetType->getConstraints());
        self::assertSame($isValid, $errors->count() === 0);
    }

    public function testProvideValue(): void
    {
        $request = Request::create('/the/answer');
        $request->attributes->set('semanticPathinfo', '/the/answer');

        self::assertSame(
            '/the/answer',
            $this->targetType->provideValue($request),
        );
    }

    public function testProvideValueWithEmptySemanticPathInfo(): void
    {
        $request = Request::create('/the/answer');
        $request->attributes->set('semanticPathinfo', false);

        self::assertSame(
            '/',
            $this->targetType->provideValue($request),
        );
    }

    public function testProvideValueWithNoSemanticPathInfo(): void
    {
        $request = Request::create('/the/answer');

        self::assertNull($this->targetType->provideValue($request));
    }

    /**
     * Provider for testing target type validation.
     */
    public static function validationDataProvider(): iterable
    {
        return [
            ['/some/route', true],
            ['/', true],
            ['', false],
            [null, false],
        ];
    }
}
