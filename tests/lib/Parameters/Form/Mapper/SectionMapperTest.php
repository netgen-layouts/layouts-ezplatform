<?php

declare(strict_types=1);

namespace Netgen\Layouts\Ibexa\Tests\Parameters\Form\Mapper;

use Netgen\Layouts\Ibexa\Form\SectionType;
use Netgen\Layouts\Ibexa\Parameters\Form\Mapper\SectionMapper;
use Netgen\Layouts\Ibexa\Parameters\ParameterType\SectionType as ParameterType;
use Netgen\Layouts\Parameters\ParameterDefinition;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(SectionMapper::class)]
final class SectionMapperTest extends TestCase
{
    private SectionMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new SectionMapper();
    }

    public function testGetFormType(): void
    {
        self::assertSame(SectionType::class, $this->mapper->getFormType());
    }

    public function testMapOptions(): void
    {
        self::assertSame(
            [
                'multiple' => true,
                'sections' => ['media'],
            ],
            $this->mapper->mapOptions(
                ParameterDefinition::fromArray(
                    [
                        'type' => new ParameterType(),
                        'options' => [
                            'multiple' => true,
                            'sections' => ['media'],
                        ],
                    ],
                ),
            ),
        );
    }
}
