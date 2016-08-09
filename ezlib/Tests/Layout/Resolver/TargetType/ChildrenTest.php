<?php

namespace Netgen\BlockManager\Ez\Tests\Layout\Resolver\TargetType;

use eZ\Publish\Core\MVC\Symfony\Routing\UrlAliasRouter;
use eZ\Publish\Core\Repository\Repository;
use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use eZ\Publish\Core\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\LocationService;
use Netgen\BlockManager\Ez\Layout\Resolver\TargetType\Children;
use Netgen\BlockManager\Ez\Tests\Validator\RepositoryValidatorFactory;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

class ChildrenTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $repositoryMock;

    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    protected $requestStack;

    /**
     * @var \Netgen\BlockManager\Ez\Layout\Resolver\TargetType\Children
     */
    protected $targetType;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $locationServiceMock;

    public function setUp()
    {
        $request = Request::create('/');
        $request->attributes->set('locationId', 42);
        $request->attributes->set('_route', UrlAliasRouter::URL_ALIAS_ROUTE_NAME);

        $this->requestStack = new RequestStack();
        $this->requestStack->push($request);

        $this->locationServiceMock = $this->createMock(LocationService::class);
        $this->repositoryMock = $this->createPartialMock(Repository::class, array('getLocationService'));

        $this->repositoryMock
            ->expects($this->any())
            ->method('getLocationService')
            ->will($this->returnValue($this->locationServiceMock));

        $this->targetType = new Children($this->locationServiceMock);
        $this->targetType->setRequestStack($this->requestStack);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\TargetType\Children::getType
     */
    public function testGetType()
    {
        $this->assertEquals('ezchildren', $this->targetType->getType());
    }

    /**
     * @param mixed $value
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\TargetType\Children::getConstraints
     * @dataProvider validationProvider
     */
    public function testValidation($value, $isValid)
    {
        if ($value !== null) {
            $this->locationServiceMock
                ->expects($this->once())
                ->method('loadLocation')
                ->with($this->equalTo($value))
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
            ->setConstraintValidatorFactory(new RepositoryValidatorFactory($this->repositoryMock))
            ->getValidator();

        $errors = $validator->validate($value, $this->targetType->getConstraints());
        $this->assertEquals($isValid, $errors->count() == 0);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\TargetType\Children::__construct
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\TargetType\Children::provideValue
     */
    public function testProvideValue()
    {
        $this->locationServiceMock
            ->expects($this->once())
            ->method('loadLocation')
            ->with($this->equalTo(42))
            ->will($this->returnValue(new Location(array('parentLocationId' => 84))));

        $this->assertEquals(
            84,
            $this->targetType->provideValue()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\TargetType\Children::provideValue
     */
    public function testProvideValueWithNoRequest()
    {
        $this->locationServiceMock
            ->expects($this->never())
            ->method('loadLocation');

        // Make sure we have no request
        $this->requestStack->pop();

        $this->assertNull($this->targetType->provideValue());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\TargetType\Children::provideValue
     */
    public function testProvideValueWithNoRoute()
    {
        // Make sure we have no URL alias route
        $this->requestStack->getCurrentRequest()->attributes->remove('_route');

        $this->assertNull($this->targetType->provideValue());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\TargetType\Children::provideValue
     */
    public function testProvideValueWithNoLocationId()
    {
        $this->locationServiceMock
            ->expects($this->never())
            ->method('loadLocation');

        // Make sure we have no location ID attribute
        $this->requestStack->getCurrentRequest()->attributes->remove('locationId');

        $this->assertNull($this->targetType->provideValue());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\TargetType\Children::provideValue
     */
    public function testProvideValueWithNoLocation()
    {
        $this->locationServiceMock
            ->expects($this->once())
            ->method('loadLocation')
            ->with($this->equalTo(42))
            ->will($this->throwException(new NotFoundException('location', 42)));

        $this->assertNull($this->targetType->provideValue());
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
