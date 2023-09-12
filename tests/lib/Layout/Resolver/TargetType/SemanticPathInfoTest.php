<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Tests\Layout\Resolver\TargetType;

use Netgen\Layouts\Ez\Layout\Resolver\TargetType\SemanticPathInfo;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validation;

final class SemanticPathInfoTest extends TestCase
{
    private SemanticPathInfo $targetType;

    protected function setUp(): void
    {
        $this->targetType = new SemanticPathInfo();
    }

    /**
     * @covers \Netgen\Layouts\Ez\Layout\Resolver\TargetType\SemanticPathInfo::getType
     */
    public function testGetType(): void
    {
        self::assertSame('ez_semantic_path_info', $this->targetType::getType());
    }

    /**
     * @param mixed $value
     *
     * @covers \Netgen\Layouts\Ez\Layout\Resolver\TargetType\SemanticPathInfo::getConstraints
     *
     * @dataProvider validationDataProvider
     */
    public function testValidation($value, bool $isValid): void
    {
        $validator = Validation::createValidator();

        $errors = $validator->validate($value, $this->targetType->getConstraints());
        self::assertSame($isValid, $errors->count() === 0);
    }

    /**
     * @covers \Netgen\Layouts\Ez\Layout\Resolver\TargetType\SemanticPathInfo::provideValue
     */
    public function testProvideValue(): void
    {
        $request = Request::create('/the/answer');
        $request->attributes->set('semanticPathinfo', '/the/answer');

        self::assertSame(
            '/the/answer',
            $this->targetType->provideValue($request),
        );
    }

    /**
     * @covers \Netgen\Layouts\Ez\Layout\Resolver\TargetType\SemanticPathInfo::provideValue
     */
    public function testProvideValueWithEmptySemanticPathInfo(): void
    {
        $request = Request::create('/the/answer');
        $request->attributes->set('semanticPathinfo', false);

        self::assertSame(
            '/',
            $this->targetType->provideValue($request),
        );
    }

    /**
     * @covers \Netgen\Layouts\Ez\Layout\Resolver\TargetType\SemanticPathInfo::provideValue
     */
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
