<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ez\Tests\Block\BlockDefinition\Integration;

use eZ\Publish\API\Repository\Repository;
use Netgen\Layouts\Block\BlockDefinition\BlockDefinitionHandlerInterface;
use Netgen\Layouts\Ez\Block\BlockDefinition\Handler\ComponentHandler;
use Netgen\Layouts\Ez\Parameters\ParameterType as EzParameterType;
use Netgen\Layouts\Ez\Tests\Validator\ValidatorFactory;
use Netgen\Layouts\Parameters\ParameterType;
use Netgen\Layouts\Parameters\Registry\ParameterTypeRegistry;
use Netgen\Layouts\Parameters\ValueObjectProviderInterface;
use Netgen\Layouts\Tests\Block\BlockDefinition\Integration\BlockTestCase;
use Netgen\Layouts\Tests\TestCase\ValidatorFactory as BaseValidatorFactory;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class ComponentTestBase extends BlockTestCase
{
    public static function parametersDataProvider(): iterable
    {
        return [
            [
                [
                    'content_type_identifier' => 'foo',
                ],
                [
                    'content_type_identifier' => 'foo',
                    'content' => null,
                ],
            ],
            [
                [
                    'content_type_identifier' => 'foo',
                    'content' => null,
                ],
                [
                    'content_type_identifier' => 'foo',
                    'content' => null,
                ],
            ],
            [
                [
                    'content_type_identifier' => 'foo',
                    'content' => 42,
                ],
                [
                    'content_type_identifier' => 'foo',
                    'content' => 42,
                ],
            ],
            [
                [
                    'unknown' => 'unknown',
                ],
                [],
            ],
        ];
    }

    public static function invalidParametersDataProvider(): iterable
    {
        return [
            [
                [],
                [
                    'content_type_identifier' => null,
                    'content' => null,
                ],
                [
                    'content_type_identifier' => '',
                    'content' => null,
                ],
                [
                    'content_type_identifier' => 42,
                    'content' => null,
                ],
                [
                    'content_type_identifier' => null,
                    'content' => '42',
                ],
            ],
        ];
    }

    protected function createParameterTypeRegistry(): ParameterTypeRegistry
    {
        return new ParameterTypeRegistry(
            [
                new ParameterType\HiddenType(),
                new EzParameterType\ContentType(
                    $this->createMock(Repository::class),
                    $this->createMock(ValueObjectProviderInterface::class),
                ),
            ],
        );
    }

    protected function createValidator(): ValidatorInterface
    {
        return Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new ValidatorFactory($this, new BaseValidatorFactory($this)))
            ->getValidator();
    }

    protected function createBlockDefinitionHandler(): BlockDefinitionHandlerInterface
    {
        return new ComponentHandler();
    }
}
