<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Ez\Tests\Parameters\ParameterType;

use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use Netgen\BlockManager\Ez\Parameters\ParameterType\TagsType;
use Netgen\BlockManager\Ez\Tests\Validator\TagsServiceValidatorFactory;
use Netgen\BlockManager\Tests\Parameters\ParameterType\ParameterTypeTestTrait;
use Netgen\TagsBundle\API\Repository\Values\Tags\Tag;
use Netgen\TagsBundle\Core\Repository\TagsService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

final class TagsTypeTest extends TestCase
{
    use ParameterTypeTestTrait;

    /**
     * @var \Netgen\TagsBundle\API\Repository\TagsService&\PHPUnit\Framework\MockObject\MockObject
     */
    private $tagsServiceMock;

    public function setUp(): void
    {
        $this->tagsServiceMock = $this->createPartialMock(TagsService::class, ['loadTag', 'loadTagByRemoteId']);

        $this->type = new TagsType($this->tagsServiceMock);
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Parameters\ParameterType\TagsType::__construct
     * @covers \Netgen\BlockManager\Ez\Parameters\ParameterType\TagsType::getIdentifier
     */
    public function testGetIdentifier(): void
    {
        $this->assertEquals('eztags', $this->type->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Parameters\ParameterType\TagsType::configureOptions
     * @dataProvider validOptionsProvider
     */
    public function testValidOptions(array $options, array $resolvedOptions): void
    {
        $parameter = $this->getParameterDefinition($options);
        $this->assertEquals($resolvedOptions, $parameter->getOptions());
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Parameters\ParameterType\TagsType::configureOptions
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidArgumentException
     * @dataProvider invalidOptionsProvider
     */
    public function testInvalidOptions(array $options): void
    {
        $this->getParameterDefinition($options);
    }

    /**
     * Provider for testing valid parameter attributes.
     */
    public function validOptionsProvider(): array
    {
        return [
            [
                [
                ],
                [
                    'max' => null,
                    'min' => null,
                    'allow_invalid' => false,
                ],
            ],
            [
                [
                    'max' => 5,
                ],
                [
                    'max' => 5,
                    'min' => null,
                    'allow_invalid' => false,
                ],
            ],
            [
                [
                    'max' => null,
                ],
                [
                    'max' => null,
                    'min' => null,
                    'allow_invalid' => false,
                ],
            ],
            [
                [
                    'min' => 5,
                ],
                [
                    'min' => 5,
                    'max' => null,
                    'allow_invalid' => false,
                ],
            ],
            [
                [
                    'min' => null,
                ],
                [
                    'max' => null,
                    'min' => null,
                    'allow_invalid' => false,
                ],
            ],
            [
                [
                    'min' => 5,
                    'max' => 10,
                    'allow_invalid' => false,
                ],
                [
                    'min' => 5,
                    'max' => 10,
                    'allow_invalid' => false,
                ],
            ],
            [
                [
                    'min' => 5,
                    'max' => 3,
                ],
                [
                    'min' => 5,
                    'max' => 5,
                    'allow_invalid' => false,
                ],
            ],
            [
                [
                    'allow_invalid' => false,
                ],
                [
                    'min' => null,
                    'max' => null,
                    'allow_invalid' => false,
                ],
            ],
            [
                [
                    'allow_invalid' => true,
                ],
                [
                    'min' => null,
                    'max' => null,
                    'allow_invalid' => true,
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
                    'min' => '0',
                ],
                [
                    'min' => -5,
                ],
                [
                    'min' => 0,
                ],
                [
                    'max' => '0',
                ],
                [
                    'max' => -5,
                ],
                [
                    'max' => 0,
                ],
                [
                    'allow_invalid' => 'false',
                ],
                [
                    'allow_invalid' => 'true',
                ],
                [
                    'allow_invalid' => 0,
                ],
                [
                    'allow_invalid' => 1,
                ],
                [
                    'undefined_value' => 'Value',
                ],
            ],
        ];
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Parameters\ParameterType\TagsType::export
     */
    public function testExport(): void
    {
        $this->tagsServiceMock
            ->expects($this->once())
            ->method('loadTag')
            ->with($this->equalTo(42))
            ->will($this->returnValue(new Tag(['remoteId' => 'abc'])));

        $this->assertEquals('abc', $this->type->export($this->getParameterDefinition(), 42));
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Parameters\ParameterType\TagsType::export
     */
    public function testExportWithNonExistingTag(): void
    {
        $this->tagsServiceMock
            ->expects($this->once())
            ->method('loadTag')
            ->with($this->equalTo(42))
            ->will($this->throwException(new NotFoundException('tag', 42)));

        $this->assertNull($this->type->export($this->getParameterDefinition(), 42));
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Parameters\ParameterType\TagsType::import
     */
    public function testImport(): void
    {
        $this->tagsServiceMock
            ->expects($this->once())
            ->method('loadTagByRemoteId')
            ->with($this->equalTo('abc'))
            ->will($this->returnValue(new Tag(['id' => 42])));

        $this->assertEquals(42, $this->type->import($this->getParameterDefinition(), 'abc'));
    }

    /**
     * @covers \Netgen\BlockManager\Ez\Parameters\ParameterType\TagsType::import
     */
    public function testImportWithNonExistingTag(): void
    {
        $this->tagsServiceMock
            ->expects($this->once())
            ->method('loadTagByRemoteId')
            ->with($this->equalTo('abc'))
            ->will($this->throwException(new NotFoundException('tag', 'abc')));

        $this->assertNull($this->type->import($this->getParameterDefinition(), 'abc'));
    }

    /**
     * @param mixed $values
     * @param bool $required
     * @param bool $isValid
     *
     * @covers \Netgen\BlockManager\Ez\Parameters\ParameterType\TagsType::getValueConstraints
     * @dataProvider validationProvider
     */
    public function testValidation($values, bool $required, bool $isValid): void
    {
        if ($values !== null) {
            foreach ($values as $i => $value) {
                if ($value !== null) {
                    $this->tagsServiceMock
                        ->expects($this->at($i))
                        ->method('loadTag')
                        ->with($this->equalTo($value))
                        ->will(
                            $this->returnCallback(
                                function () use ($value): void {
                                    if (!is_int($value) || $value > 20) {
                                        throw new NotFoundException('tag', $value);
                                    }
                                }
                            )
                        );
                }
            }
        }

        $parameter = $this->getParameterDefinition(['min' => 1, 'max' => 3], $required);
        $validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new TagsServiceValidatorFactory($this->tagsServiceMock))
            ->getValidator();

        $errors = $validator->validate($values, $this->type->getConstraints($parameter, $values));
        $this->assertEquals($isValid, $errors->count() === 0);
    }

    /**
     * Provider for testing valid parameter values.
     *
     * @return array
     */
    public function validationProvider(): array
    {
        return [
            [[12], false, true],
            [[12, 13, 14, 15], false, false],
            [[24], false, false],
            [[-12], false, false],
            [[0], false, false],
            [['12'], false, false],
            [[''], false, false],
            [[null], false, false],
            [[], false, false],
            [null, false, true],
            [[12], true, true],
            [[12, 13, 14, 15], true, false],
            [[24], true, false],
            [[-12], true, false],
            [[0], true, false],
            [['12'], true, false],
            [[''], true, false],
            [[null], true, false],
            [[], true, false],
            [null, true, false],
        ];
    }
}
