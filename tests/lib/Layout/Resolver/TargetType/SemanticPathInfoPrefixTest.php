<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Tests\Layout\Resolver\TargetType;

use Netgen\BlockManager\Ez\Layout\Resolver\TargetType\SemanticPathInfoPrefix;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validation;

final class SemanticPathInfoPrefixTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Ez\Layout\Resolver\TargetType\SemanticPathInfoPrefix
     */
    private $targetType;

    public function setUp(): void
    {
        $this->targetType = new SemanticPathInfoPrefix();
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\TargetType\SemanticPathInfoPrefix::getType
     */
    public function testGetType(): void
    {
        $this->assertSame('ez_semantic_path_info_prefix', $this->targetType->getType());
    }

    /**
     * @param mixed $value
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\TargetType\SemanticPathInfoPrefix::getConstraints
     * @dataProvider validationProvider
     */
    public function testValidation($value, bool $isValid): void
    {
        $validator = Validation::createValidator();

        $errors = $validator->validate($value, $this->targetType->getConstraints());
        $this->assertSame($isValid, $errors->count() === 0);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\TargetType\SemanticPathInfoPrefix::provideValue
     */
    public function testProvideValue(): void
    {
        $request = Request::create('/the/answer');
        $request->attributes->set('semanticPathinfo', '/the/answer');

        $this->assertSame(
            '/the/answer',
            $this->targetType->provideValue($request)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\TargetType\SemanticPathInfoPrefix::provideValue
     */
    public function testProvideValueWithEmptySemanticPathInfo(): void
    {
        $request = Request::create('/the/answer');
        $request->attributes->set('semanticPathinfo', false);

        $this->assertSame(
            '/',
            $this->targetType->provideValue($request)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\TargetType\SemanticPathInfoPrefix::provideValue
     */
    public function testProvideValueWithNoSemanticPathInfo(): void
    {
        $request = Request::create('/the/answer');

        $this->assertNull($this->targetType->provideValue($request));
    }

    /**
     * Provider for testing target type validation.
     */
    public function validationProvider(): array
    {
        return [
            ['/some/route', true],
            ['/', true],
            ['', false],
            [null, false],
        ];
    }
}
