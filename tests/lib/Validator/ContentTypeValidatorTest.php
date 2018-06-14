<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Tests\Validator;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use eZ\Publish\Core\Repository\Repository;
use eZ\Publish\Core\Repository\Values\ContentType\ContentType as EzContentType;
use eZ\Publish\Core\Repository\Values\ContentType\ContentTypeGroup;
use Netgen\BlockManager\Ez\Validator\Constraint\ContentType;
use Netgen\BlockManager\Ez\Validator\ContentTypeValidator;
use Netgen\BlockManager\Tests\TestCase\ValidatorTestCase;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintValidatorInterface;

final class ContentTypeValidatorTest extends ValidatorTestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $repositoryMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $contentTypeServiceMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->constraint = new ContentType();
    }

    public function getValidator(): ConstraintValidatorInterface
    {
        $this->contentTypeServiceMock = $this->createMock(ContentTypeService::class);
        $this->repositoryMock = $this->createPartialMock(Repository::class, ['sudo', 'getContentTypeService']);

        $this->repositoryMock
            ->expects($this->any())
            ->method('sudo')
            ->with($this->anything())
            ->will($this->returnCallback(function (callable $callback) {
                return $callback($this->repositoryMock);
            }));

        $this->repositoryMock
            ->expects($this->any())
            ->method('getContentTypeService')
            ->will($this->returnValue($this->contentTypeServiceMock));

        return new ContentTypeValidator($this->repositoryMock);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Validator\ContentTypeValidator::__construct
     * @covers \Netgen\BlockManager\Ez\Validator\ContentTypeValidator::validate
     * @dataProvider  validateDataProvider
     */
    public function testValidate(string $identifier, array $groups, array $allowedTypes, bool $isValid): void
    {
        $this->contentTypeServiceMock
            ->expects($this->once())
            ->method('loadContentTypeByIdentifier')
            ->with($this->equalTo($identifier))
            ->will(
                $this->returnValue(
                    new EzContentType(
                        [
                            'identifier' => $identifier,
                            'contentTypeGroups' => array_map(
                                function (string $group): ContentTypeGroup {
                                    return new ContentTypeGroup(
                                        [
                                            'identifier' => $group,
                                        ]
                                    );
                                },
                                $groups
                            ),
                        ]
                    )
                )
            );

        $this->constraint->allowedTypes = $allowedTypes;
        $this->assertValid($isValid, $identifier);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Validator\ContentTypeValidator::__construct
     * @covers \Netgen\BlockManager\Ez\Validator\ContentTypeValidator::validate
     */
    public function testValidateNull(): void
    {
        $this->contentTypeServiceMock
            ->expects($this->never())
            ->method('loadContentTypeByIdentifier');

        $this->assertValid(true, null);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Validator\ContentTypeValidator::__construct
     * @covers \Netgen\BlockManager\Ez\Validator\ContentTypeValidator::validate
     */
    public function testValidateInvalid(): void
    {
        $this->contentTypeServiceMock
            ->expects($this->once())
            ->method('loadContentTypeByIdentifier')
            ->with($this->equalTo('unknown'))
            ->will(
                $this->throwException(
                    new NotFoundException('content type', 'unknown')
                )
            );

        $this->assertValid(false, 'unknown');
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Validator\ContentTypeValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "Netgen\BlockManager\Ez\Validator\Constraint\ContentType", "Symfony\Component\Validator\Constraints\NotBlank" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidConstraint(): void
    {
        $this->constraint = new NotBlank();
        $this->assertValid(true, 'value');
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Validator\ContentTypeValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "string", "integer" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidValue(): void
    {
        $this->assertValid(true, 42);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Validator\ContentTypeValidator::validate
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "array", "integer" given
     */
    public function testValidateThrowsUnexpectedTypeExceptionWithInvalidAllowedTypes(): void
    {
        $this->constraint->allowedTypes = 42;
        $this->assertValid(true, 'article');
    }

    public function validateDataProvider(): array
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
}
