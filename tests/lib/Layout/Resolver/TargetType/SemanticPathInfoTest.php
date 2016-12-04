<?php

namespace Netgen\BlockManager\Ez\Tests\Layout\Resolver\TargetType;

use Netgen\BlockManager\Ez\Layout\Resolver\TargetType\SemanticPathInfo;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Validation;

class SemanticPathInfoTest extends TestCase
{
    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    protected $requestStack;

    /**
     * @var \Netgen\BlockManager\Ez\Layout\Resolver\TargetType\SemanticPathInfo
     */
    protected $targetType;

    public function setUp()
    {
        $request = Request::create('/the/answer');
        $request->attributes->set('semanticPathinfo', '/the/answer');

        $this->requestStack = new RequestStack();
        $this->requestStack->push($request);

        $this->targetType = new SemanticPathInfo();
        $this->targetType->setRequestStack($this->requestStack);
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
        $this->assertEquals(
            '/the/answer',
            $this->targetType->provideValue()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\TargetType\SemanticPathInfo::provideValue
     */
    public function testProvideValueWithEmptySemanticPathInfo()
    {
        $this->requestStack->getCurrentRequest()->attributes->set('semanticPathinfo', false);

        $this->assertEquals(
            '/',
            $this->targetType->provideValue()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\TargetType\SemanticPathInfo::provideValue
     */
    public function testProvideValueWithNoRequest()
    {
        // Make sure we have no request
        $this->requestStack->pop();

        $this->assertNull($this->targetType->provideValue());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\TargetType\SemanticPathInfo::provideValue
     */
    public function testProvideValueWithNoSemanticPathInfo()
    {
        // Make sure we have no semantic path info attribute
        $this->requestStack->getCurrentRequest()->attributes->remove('semanticPathinfo');

        $this->assertNull($this->targetType->provideValue());
    }

    /**
     * Provider for testing target type validation.
     *
     * @return array
     */
    public function validationProvider()
    {
        return array(
            array('/some/route', true),
            array('/', true),
            array('', false),
            array(null, false),
        );
    }
}
