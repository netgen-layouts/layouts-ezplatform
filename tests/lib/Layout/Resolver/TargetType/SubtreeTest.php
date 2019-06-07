<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Tests\Layout\Resolver\TargetType;

use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use eZ\Publish\Core\Repository\Repository;
use eZ\Publish\Core\Repository\Values\Content\Location;
use Netgen\Layouts\Ez\ContentProvider\ContentExtractorInterface;
use Netgen\Layouts\Ez\Layout\Resolver\TargetType\Subtree;
use Netgen\Layouts\Ez\Tests\Validator\RepositoryValidatorFactory;
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
     * @var \Netgen\Layouts\Ez\Layout\Resolver\TargetType\Subtree
     */
    private $targetType;

    protected function setUp(): void
    {
        $this->contentExtractorMock = $this->createMock(ContentExtractorInterface::class);
        $this->locationServiceMock = $this->createMock(LocationService::class);
        $this->repositoryMock = $this->createPartialMock(Repository::class, ['sudo', 'getLocationService']);

        $this->repositoryMock
            ->expects(self::any())
            ->method('sudo')
            ->with(self::anything())
            ->willReturnCallback(
                function (callable $callback) {
                    return $callback($this->repositoryMock);
                }
            );

        $this->repositoryMock
            ->expects(self::any())
            ->method('getLocationService')
            ->willReturn($this->locationServiceMock);

        $this->targetType = new Subtree($this->contentExtractorMock);
    }

    /**
     * @covers \Netgen\Layouts\Ez\Layout\Resolver\TargetType\Subtree::getType
     */
    public function testGetType(): void
    {
        self::assertSame('ez_subtree', $this->targetType::getType());
    }

    /**
     * @covers \Netgen\Layouts\Ez\Layout\Resolver\TargetType\Subtree::getConstraints
     */
    public function testValidation(): void
    {
        $this->locationServiceMock
            ->expects(self::once())
            ->method('loadLocation')
            ->with(self::identicalTo(42))
            ->willReturn(new Location());

        $validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new RepositoryValidatorFactory($this->repositoryMock))
            ->getValidator();

        $errors = $validator->validate(42, $this->targetType->getConstraints());
        self::assertCount(0, $errors);
    }

    /**
     * @covers \Netgen\Layouts\Ez\Layout\Resolver\TargetType\Subtree::getConstraints
     */
    public function testValidationWithInvalidValue(): void
    {
        $this->locationServiceMock
            ->expects(self::once())
            ->method('loadLocation')
            ->with(self::identicalTo(42))
            ->willThrowException(new NotFoundException('location', 42));

        $validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new RepositoryValidatorFactory($this->repositoryMock))
            ->getValidator();

        $errors = $validator->validate(42, $this->targetType->getConstraints());
        self::assertNotCount(0, $errors);
    }

    /**
     * @covers \Netgen\Layouts\Ez\Layout\Resolver\TargetType\Subtree::__construct
     * @covers \Netgen\Layouts\Ez\Layout\Resolver\TargetType\Subtree::provideValue
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
            ->expects(self::any())
            ->method('extractLocation')
            ->with(self::identicalTo($request))
            ->willReturn($location);

        self::assertSame([1, 2, 42], $this->targetType->provideValue($request));
    }

    /**
     * @covers \Netgen\Layouts\Ez\Layout\Resolver\TargetType\Subtree::__construct
     * @covers \Netgen\Layouts\Ez\Layout\Resolver\TargetType\Subtree::provideValue
     */
    public function testProvideValueWithNoLocation(): void
    {
        $request = Request::create('/');

        $this->contentExtractorMock
            ->expects(self::any())
            ->method('extractLocation')
            ->with(self::identicalTo($request))
            ->willReturn(null);

        self::assertNull($this->targetType->provideValue($request));
    }
}
