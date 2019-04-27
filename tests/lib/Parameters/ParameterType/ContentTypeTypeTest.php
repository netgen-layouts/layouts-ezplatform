<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Tests\Parameters\ParameterType;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use eZ\Publish\Core\Repository\Repository;
use eZ\Publish\Core\Repository\Values\ContentType\ContentType as EzContentType;
use Netgen\Layouts\Ez\Parameters\ParameterType\ContentTypeType;
use Netgen\Layouts\Ez\Tests\Validator\RepositoryValidatorFactory;
use Netgen\Layouts\Parameters\ParameterDefinition;
use Netgen\Layouts\Tests\Parameters\ParameterType\ParameterTypeTestTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidArgumentException;
use Symfony\Component\Validator\Validation;

final class ContentTypeTypeTest extends TestCase
{
    use ParameterTypeTestTrait;

    /**
     * @var \eZ\Publish\API\Repository\Repository&\PHPUnit\Framework\MockObject\MockObject
     */
    private $repositoryMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $contentTypeServiceMock;

    protected function setUp(): void
    {
        $this->contentTypeServiceMock = $this->createMock(ContentTypeService::class);
        $this->repositoryMock = $this->createPartialMock(Repository::class, ['sudo', 'getContentTypeService']);

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
            ->method('getContentTypeService')
            ->willReturn($this->contentTypeServiceMock);

        $this->type = new ContentTypeType();
    }

    /**
     * @covers \Netgen\Layouts\Ez\Parameters\ParameterType\ContentTypeType::getIdentifier
     */
    public function testGetIdentifier(): void
    {
        self::assertSame('ez_content_type', $this->type::getIdentifier());
    }

    /**
     * @covers \Netgen\Layouts\Ez\Parameters\ParameterType\ContentTypeType::configureOptions
     * @dataProvider validOptionsProvider
     */
    public function testValidOptions(array $options, array $resolvedOptions): void
    {
        $parameter = $this->getParameterDefinition($options);
        self::assertSame($resolvedOptions, $parameter->getOptions());
    }

    /**
     * @covers \Netgen\Layouts\Ez\Parameters\ParameterType\ContentTypeType::configureOptions
     * @dataProvider invalidOptionsProvider
     */
    public function testInvalidOptions(array $options): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->getParameterDefinition($options);
    }

    /**
     * Provider for testing valid parameter attributes.
     */
    public function validOptionsProvider(): array
    {
        return [
            [
                [],
                [
                    'multiple' => false,
                    'types' => [],
                ],
            ],
            [
                [
                    'multiple' => false,
                ],
                [
                    'multiple' => false,
                    'types' => [],
                ],
            ],
            [
                [
                    'multiple' => true,
                ],
                [
                    'multiple' => true,
                    'types' => [],
                ],
            ],
            [
                [
                    'types' => [],
                ],
                [
                    'multiple' => false,
                    'types' => [],
                ],
            ],
            [
                [
                    'types' => [42],
                ],
                [
                    'multiple' => false,
                    'types' => [42],
                ],
            ],
        ];
    }

    /**
     * Provider for testing invalid parameter attributes.
     */
    public function invalidOptionsProvider(): array
    {
        return [
            [
                [
                    'multiple' => 'true',
                ],
                [
                    'undefined_value' => 'Value',
                ],
            ],
        ];
    }

    /**
     * @param mixed $value
     * @param bool $required
     * @param bool $isValid
     *
     * @covers \Netgen\Layouts\Ez\Parameters\ParameterType\ContentTypeType::getValueConstraints
     * @dataProvider validationProvider
     */
    public function testValidation($value, bool $required, bool $isValid): void
    {
        $options = [];
        if ($value !== null) {
            $options = ['multiple' => is_array($value)];
            foreach ((array) $value as $index => $identifier) {
                $this->contentTypeServiceMock
                    ->expects(self::at($index))
                    ->method('loadContentTypeByIdentifier')
                    ->with(self::identicalTo($identifier))
                    ->willReturnCallback(
                        static function () use ($identifier): EzContentType {
                            if (!is_string($identifier) || !in_array($identifier, ['article', 'news'], true)) {
                                throw new NotFoundException('content type', $identifier);
                            }

                            return new EzContentType(
                                [
                                    'identifier' => $identifier,
                                ]
                            );
                        }
                    );
            }
        }

        $parameter = $this->getParameterDefinition($options, $required);
        $validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new RepositoryValidatorFactory($this->repositoryMock))
            ->getValidator();

        $errors = $validator->validate($value, $this->type->getConstraints($parameter, $value));
        self::assertSame($isValid, $errors->count() === 0);
    }

    /**
     * Provider for testing valid parameter values.
     */
    public function validationProvider(): array
    {
        return [
            ['news', false, true],
            [[], false, true],
            [['news'], false, true],
            [['article', 'news'], false, true],
            [['article', 'other'], false, false],
            [['other'], false, false],
            [null, false, true],
            ['news', true, true],
            [[], true, false],
            [['news'], true, true],
            [['article', 'news'], true, true],
            [['article', 'other'], true, false],
            [['other'], true, false],
            [null, true, false],
        ];
    }

    /**
     * @param mixed $value
     * @param mixed $convertedValue
     * @param bool $multiple
     *
     * @covers \Netgen\Layouts\Ez\Parameters\ParameterType\ContentTypeType::fromHash
     * @dataProvider fromHashProvider
     */
    public function testFromHash($value, $convertedValue, bool $multiple): void
    {
        self::assertSame(
            $convertedValue,
            $this->type->fromHash(
                $this->getParameterDefinition(
                    [
                        'multiple' => $multiple,
                    ]
                ),
                $value
            )
        );
    }

    public function fromHashProvider(): array
    {
        return [
            [
                null,
                null,
                false,
            ],
            [
                [],
                null,
                false,
            ],
            [
                42,
                42,
                false,
            ],
            [
                [42, 43],
                42,
                false,
            ],
            [
                null,
                null,
                true,
            ],
            [
                [],
                null,
                true,
            ],
            [
                42,
                [42],
                true,
            ],
            [
                [42, 43],
                [42, 43],
                true,
            ],
        ];
    }

    /**
     * @param mixed $value
     * @param bool $isEmpty
     *
     * @covers \Netgen\Layouts\Ez\Parameters\ParameterType\ContentTypeType::isValueEmpty
     * @dataProvider emptyProvider
     */
    public function testIsValueEmpty($value, bool $isEmpty): void
    {
        self::assertSame($isEmpty, $this->type->isValueEmpty(new ParameterDefinition(), $value));
    }

    /**
     * Provider for testing if the value is empty.
     */
    public function emptyProvider(): array
    {
        return [
            [null, true],
            [[], true],
            [42, false],
            [[42], false],
            [0, false],
            ['42', false],
            ['', false],
        ];
    }
}
