<?php

namespace Netgen\Bundle\EzPublishBlockManagerBundle\Tests\Layout\Resolver\TargetType;

use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use eZ\Publish\Core\Repository\Repository;
use Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetType\Content;
use Netgen\Bundle\EzPublishBlockManagerBundle\Tests\Validator\RepositoryValidatorFactory;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

class ContentTest extends TestCase
{
    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    protected $requestStack;

    /**
     * @var \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetType\Content
     */
    protected $targetType;

    public function setUp()
    {
        $request = Request::create('/');
        $request->attributes->set('contentId', 42);

        $this->requestStack = new RequestStack();
        $this->requestStack->push($request);

        $this->targetType = new Content();
        $this->targetType->setRequestStack($this->requestStack);
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetType\Content::getType
     */
    public function testGetType()
    {
        self::assertEquals('ezcontent', $this->targetType->getType());
    }

    /**
     * @param mixed $value
     * @param bool $isValid
     *
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetType\Content::getConstraints
     * @dataProvider validationProvider
     */
    public function testValidation($value, $isValid)
    {
        $repositoryMock = $this->createMock(Repository::class);
        if ($value !== null) {
            $repositoryMock
                ->expects($this->once())
                ->method('sudo')
                ->will(
                    $this->returnCallback(
                        function () use ($value) {
                            if (!is_int($value) || $value > 20) {
                                throw new NotFoundException('location', $value);
                            }
                        }
                    )
                );
        }

        $validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new RepositoryValidatorFactory($repositoryMock))
            ->getValidator();

        $errors = $validator->validate($value, $this->targetType->getConstraints());
        self::assertEquals($isValid, $errors->count() == 0);
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetType\Content::provideValue
     */
    public function testProvideValue()
    {
        self::assertEquals(
            42,
            $this->targetType->provideValue()
        );
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetType\Content::provideValue
     */
    public function testProvideValueWithNoRequest()
    {
        // Make sure we have no request
        $this->requestStack->pop();

        self::assertNull($this->targetType->provideValue());
    }

    /**
     * @covers \Netgen\Bundle\EzPublishBlockManagerBundle\Layout\Resolver\TargetType\Content::provideValue
     */
    public function testProvideValueWithNoContentId()
    {
        // Make sure we have no content ID attribute
        $this->requestStack->getCurrentRequest()->attributes->remove('contentId');

        self::assertNull($this->targetType->provideValue());
    }

    /**
     * Provider for testing valid parameter values.
     *
     * @return array
     */
    public function validationProvider()
    {
        return array(
            array(12, true),
            array(24, false),
            array(-12, false),
            array(0, false),
            array('12', false),
            array('', false),
            array(null, false),
        );
    }
}
