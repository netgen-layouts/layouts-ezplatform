<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Tests\Layout\Resolver\TargetType;

use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use eZ\Publish\Core\Repository\Repository;
use eZ\Publish\Core\Repository\Values\Content\Location;
use Netgen\BlockManager\Ez\ContentProvider\ContentExtractorInterface;
use Netgen\BlockManager\Ez\Layout\Resolver\TargetType\Subtree;
use Netgen\BlockManager\Ez\Tests\Validator\RepositoryValidatorFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validation;

final class SubtreeTest extends TestCase
{
    /**
     * @var \eZ\Publish\API\Repository\Repository&\PHPUnit\Framework\MockObject\MockObject
     */
    private $repositoryMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $contentExtractorMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $locationServiceMock;

    /**
     * @var \Netgen\BlockManager\Ez\Layout\Resolver\TargetType\Subtree
     */
    private $targetType;

    public function setUp(): void
    {
        $this->contentExtractorMock = $this->createMock(ContentExtractorInterface::class);
        $this->locationServiceMock = $this->createMock(LocationService::class);
        $this->repositoryMock = $this->createPartialMock(Repository::class, ['sudo', 'getLocationService']);

        $this->repositoryMock
            ->expects($this->any())
            ->method('sudo')
            ->with($this->anything())
            ->will($this->returnCallback(function (callable $callback) {
                return $callback($this->repositoryMock);
            }));

        $this->repositoryMock
            ->expects($this->any())
            ->method('getLocationService')
            ->will($this->returnValue($this->locationServiceMock));

        $this->targetType = new Subtree($this->contentExtractorMock);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\TargetType\Subtree::getType
     */
    public function testGetType(): void
    {
        $this->assertSame('ezsubtree', $this->targetType::getType());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\TargetType\Subtree::getConstraints
     */
    public function testValidation(): void
    {
        $this->locationServiceMock
            ->expects($this->once())
            ->method('loadLocation')
            ->with($this->identicalTo(42))
            ->will($this->returnValue(new Location()));

        $validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new RepositoryValidatorFactory($this->repositoryMock))
            ->getValidator();

        $errors = $validator->validate(42, $this->targetType->getConstraints());
        $this->assertCount(0, $errors);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\TargetType\Subtree::getConstraints
     */
    public function testValidationWithInvalidValue(): void
    {
        $this->locationServiceMock
            ->expects($this->once())
            ->method('loadLocation')
            ->with($this->identicalTo(42))
            ->will($this->throwException(new NotFoundException('location', 42)));

        $validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new RepositoryValidatorFactory($this->repositoryMock))
            ->getValidator();

        $errors = $validator->validate(42, $this->targetType->getConstraints());
        $this->assertNotCount(0, $errors);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\TargetType\Subtree::__construct
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\TargetType\Subtree::provideValue
     */
    public function testProvideValue(): void
    {
        $location = new Location(
            [
                'path' => [1, 2, 42],
            ]
        );

        $request = Request::create('/');

        $this->contentExtractorMock
            ->expects($this->any())
            ->method('extractLocation')
            ->with($this->identicalTo($request))
            ->will($this->returnValue($location));

        $this->assertSame([1, 2, 42], $this->targetType->provideValue($request));
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\TargetType\Subtree::__construct
     * @covers \Netgen\BlockManager\Ez\Layout\Resolver\TargetType\Subtree::provideValue
     */
    public function testProvideValueWithNoLocation(): void
    {
        $request = Request::create('/');

        $this->contentExtractorMock
            ->expects($this->any())
            ->method('extractLocation')
            ->with($this->identicalTo($request))
            ->will($this->returnValue(null));

        $this->assertNull($this->targetType->provideValue($request));
    }
}
