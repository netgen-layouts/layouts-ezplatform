<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Tests\Layout\Resolver\TargetType;

use Netgen\BlockManager\Ez\Layout\Resolver\TargetType\SemanticPathInfo;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validation;

final class SemanticPathInfoTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Ez\Layout\Resolver\TargetType\SemanticPathInfo
     */
    private $targetType;

    public function setUp()
    {
        $this->targetType = new SemanticPathInfo();
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\TargetType\SemanticPathInfo::getType
     */
    public function testGetType()
    {
        $this->assertEquals('ez_semantic_path_info', $this->targetType->getType());
    }

    /**
     * @param mixed $value
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\TargetType\SemanticPathInfo::getConstraints
     * @dataProvider validationProvider
     */
    public function testValidation($value, $isValid)
    {
        $validator = Validation::createValidator();

        $errors = $validator->validate($value, $this->targetType->getConstraints());
        $this->assertEquals($isValid, $errors->count() === 0);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\TargetType\SemanticPathInfo::provideValue
     */
    public function testProvideValue()
    {
        $request = Request::create('/the/answer');
        $request->attributes->set('semanticPathinfo', '/the/answer');

        $this->assertEquals(
            '/the/answer',
            $this->targetType->provideValue($request)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\TargetType\SemanticPathInfo::provideValue
     */
    public function testProvideValueWithEmptySemanticPathInfo()
    {
        $request = Request::create('/the/answer');
        $request->attributes->set('semanticPathinfo', false);

        $this->assertEquals(
            '/',
            $this->targetType->provideValue($request)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\TargetType\SemanticPathInfo::provideValue
     */
    public function testProvideValueWithNoSemanticPathInfo()
    {
        $request = Request::create('/the/answer');

        $this->assertNull($this->targetType->provideValue($request));
    }

    /**
     * Extractor for testing target type validation.
     *
     * @return array
     */
    public function validationProvider()
    {
        return [
            ['/some/route', true],
            ['/', true],
            ['', false],
            [null, false],
        ];
    }
}
