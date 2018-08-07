<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Tests\Validator;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use eZ\Publish\Core\Repository\Repository;
use eZ\Publish\Core\Repository\Values\Content\Location as EzLocation;
use eZ\Publish\Core\Repository\Values\ContentType\ContentType;
use Netgen\BlockManager\Ez\Validator\Constraint\Location;
use Netgen\BlockManager\Ez\Validator\LocationValidator;
use Netgen\BlockManager\Tests\TestCase\ValidatorTestCase;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintValidatorInterface;

final class LocationValidatorTest extends ValidatorTestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $repositoryMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $locationServiceMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $contentTypeServiceMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->constraint = new Location(['allowedTypes' => ['user']]);
    }

    public function getValidator(): ConstraintValidatorInterface
    {
        $this->locationServiceMock = $this->createMock(LocationService::class);
        $this->contentTypeServiceMock = $this->createMock(ContentTypeService::class);

        $this->repositoryMock = $this->createPartialMock(Repository::class, ['sudo', 'getLocationService', 'getContentTypeService']);

        $this->repositoryMock
            ->expects(self::any())
            ->method('sudo')
            ->with(self::anything())
            ->will(self::returnCallback(function (callable $callback) {
                return $callback($this->repositoryMock);
            }));

        $this->repositoryMock
            ->expects(self::any())
            ->method('getLocationService')
            ->will(self::returnValue($this->locationServiceMock));

        $this->repositoryMock
            ->expects(self::any())
            ->method('getContentTypeService')
            ->will(self::returnValue($this->contentTypeServiceMock));

        $this->contentTypeServiceMock
            ->expects(self::any())
            ->method('loadContentType')
            ->will(
                self::returnCallback(
                    function (int $type): ContentType {
                        if ($type === 24) {
                            return new ContentType(['identifier' => 'user']);
                        }

                        return new ContentType(['identifier' => 'article']);
                    }
                )
            );

        return new LocationValidator($this->repositoryMock);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Validator\LocationValidator::__construct
     * @covers \Netgen\BlockManager\Ez\Validator\LocationValidator::validate
     */
    public function testValidateValid(): void
    {
        $this->locationServiceMock
            ->expects(self::once())
            ->method('loadLocation')
            ->with(self::identicalTo(42))
            ->will(
                self::returnValue(
                    new EzLocation(
                        [
                            'id' => 42,
                            'contentInfo' => new ContentInfo(['contentTypeId' => 24]),
                        ]
                    )
                )
            );

        self::assertValid(true, 42);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Validator\LocationValidator::__construct
     * @covers \Netgen\BlockManager\Ez\Validator\LocationValidator::validate
     */
    public function testValidateInvalidWithWrongType(): void
    {
        $this->locationServiceMock
            ->expects(self::once())
            ->method('loadLocation')
            ->with(self::identicalTo(42))
            ->will(
                self::returnValue(
                    new EzLocation(
                        [
                            'id' => 42,
                            'contentInfo' => new ContentInfo(['contentTypeId' => 52]),
                        ]
                    )
                )
            );

        self::assertValid(false, 42);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Validator\LocationValidator::__construct
     * @covers \Netgen\BlockManager\Ez\Validator\LocationValidator::validate
     */
    public function testValidateInvalidWithNonExistingLocation(): void
    {
        $this->locationServiceMock
            ->expects(self::once())
            ->method('loadLocation')
            ->with(self::identicalTo(42))
            ->will(self::throwException(new NotFoundException('location', 42)));

        self::assertValid(false, 42);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Validator\LocationValidator::__construct
     * @covers \Netgen\BlockManager\Ez\Validator\LocationValidator::validate
     */
    public function testValidateNull(): void
    {
        $this->locationServiceMock
            ->expects(self::never())
            ->method('loadLocation');

        self::assertValid(true, null);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Validator\LocationValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "Netgen\BlockManager\Ez\Validator\Constraint\Location", "Symfony\Component\Validator\Constraints\NotBlank" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint(): void
    {
        $this->constraint = new NotBlank();
        self::assertValid(true, 'value');
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Validator\LocationValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "scalar", "array" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValue(): void
    {
        self::assertValid(true, []);
    }
}
