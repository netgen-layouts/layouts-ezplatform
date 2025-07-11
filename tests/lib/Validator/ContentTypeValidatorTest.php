<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Tests\Validator;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use eZ\Publish\Core\Repository\Repository;
use eZ\Publish\Core\Repository\Values\ContentType\ContentType as EzContentType;
use eZ\Publish\Core\Repository\Values\ContentType\ContentTypeGroup;
use Netgen\Layouts\Ez\Validator\Constraint\ContentType;
use Netgen\Layouts\Ez\Validator\ContentTypeValidator;
use Netgen\Layouts\Tests\TestCase\ValidatorTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

use function array_map;

final class ContentTypeValidatorTest extends ValidatorTestCase
{
    private MockObject $repositoryMock;

    private MockObject $contentTypeServiceMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->constraint = new ContentType();
    }

    /**
     * @param string[] $groups
     * @param string[] $allowedTypes
     *
     * @covers \Netgen\Layouts\Ez\Validator\ContentTypeValidator::__construct
     * @covers \Netgen\Layouts\Ez\Validator\ContentTypeValidator::validate
     *
     * @dataProvider  validateDataProvider
     */
    public function testValidate(string $identifier, array $groups, array $allowedTypes, bool $isValid): void
    {
        $this->contentTypeServiceMock
            ->expects(self::once())
            ->method('loadContentTypeByIdentifier')
            ->with(self::identicalTo($identifier))
            ->willReturn(
                new EzContentType(
                    [
                        'identifier' => $identifier,
                        'contentTypeGroups' => array_map(
                            static fn (string $group): ContentTypeGroup => new ContentTypeGroup(
                                [
                                    'identifier' => $group,
                                ],
                            ),
                            $groups,
                        ),
                    ],
                ),
            );

        $this->constraint->allowedTypes = $allowedTypes;
        $this->assertValid($isValid, $identifier);
    }

    /**
     * @covers \Netgen\Layouts\Ez\Validator\ContentTypeValidator::__construct
     * @covers \Netgen\Layouts\Ez\Validator\ContentTypeValidator::validate
     */
    public function testValidateNull(): void
    {
        $this->contentTypeServiceMock
            ->expects(self::never())
            ->method('loadContentTypeByIdentifier');

        $this->assertValid(true, null);
    }

    /**
     * @covers \Netgen\Layouts\Ez\Validator\ContentTypeValidator::__construct
     * @covers \Netgen\Layouts\Ez\Validator\ContentTypeValidator::validate
     */
    public function testValidateInvalid(): void
    {
        $this->contentTypeServiceMock
            ->expects(self::once())
            ->method('loadContentTypeByIdentifier')
            ->with(self::identicalTo('unknown'))
            ->willThrowException(new NotFoundException('content type', 'unknown'));

        $this->assertValid(false, 'unknown');
    }

    /**
     * @covers \Netgen\Layouts\Ez\Validator\ContentTypeValidator::validate
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Expected argument of type "Netgen\Layouts\Ez\Validator\Constraint\ContentType", "Symfony\Component\Validator\Constraints\NotBlank" given');

        $this->constraint = new NotBlank();
        $this->assertValid(true, 'value');
    }

    /**
     * @covers \Netgen\Layouts\Ez\Validator\ContentTypeValidator::validate
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValue(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessageMatches('/^Expected argument of type "string", "int(eger)?" given$/');

        $this->assertValid(true, 42);
    }

    public static function validateDataProvider(): iterable
    {
        return [
            ['article', ['group1'], [], true],
            ['article', ['group1'], ['group2' => true], true],
            ['article', ['group1'], ['group1' => true], true],
            ['article', ['group1'], ['group1' => false], false],
            ['article', ['group1'], ['group1' => []], false],
            ['article', ['group1'], ['group1' => ['article']], true],
            ['article', ['group1'], ['group1' => ['news']], false],
            ['article', ['group1'], ['group1' => ['article', 'news']], true],
        ];
    }

    protected function getValidator(): ConstraintValidatorInterface
    {
        $this->contentTypeServiceMock = $this->createMock(ContentTypeService::class);
        $this->repositoryMock = $this->createPartialMock(Repository::class, ['sudo', 'getContentTypeService']);

        $this->repositoryMock
            ->method('sudo')
            ->with(self::anything())
            ->willReturnCallback(
                fn (callable $callback) => $callback($this->repositoryMock),
            );

        $this->repositoryMock
            ->method('getContentTypeService')
            ->willReturn($this->contentTypeServiceMock);

        return new ContentTypeValidator($this->repositoryMock);
    }
}
